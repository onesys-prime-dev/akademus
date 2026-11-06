<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeds the Playwright E2E accounts and baseline data into the TEST database (akademus_test).
 *
 *   php spark db:seed E2ETestSeeder        (or: npm run e2e:db:seed)
 *
 * - Refuses to run against any database other than `akademus_test`, so it can never touch dev data.
 * - Idempotent: users are matched by email and updated, everything else is created only when missing.
 * - Credentials come from .env.e2e (the same file Playwright reads) or real environment variables.
 *   A user whose variables are not set is skipped with a warning.
 * - Assumes the schema already exists and is migrated (it does not create tables or presets).
 *
 * It does NOT delete the QA leftovers earlier runs create (QA programs, payment methods, e2e+ sign-ups).
 */
class E2ETestSeeder extends Seeder
{
    protected $DBGroup = 'tests';

    private const SAFE_DATABASE = 'akademus_test';

    /** @var array<string,string> */
    private array $env = [];

    public function run(): void
    {
        $name = $this->db->getDatabase();
        if ($name !== self::SAFE_DATABASE) {
            throw new \RuntimeException("E2ETestSeeder only runs against '" . self::SAFE_DATABASE . "', but the connection is '{$name}'.");
        }

        $this->loadEnv();
        $now = date('Y-m-d H:i:s');

        // Reference data (both seeders are idempotent)
        $this->call(PaymentFeeTypeSeeder::class);
        $this->call(PaymentMethodAccountSeeder::class);

        $programs = $this->seedPrograms();
        $this->seedSettings($now);

        // Staff
        $this->seedAdmin($now);
        $this->seedOfficer($now);

        // Applicants
        $pw = $this->var('E2E_PASSWORD');

        // Main applicant: the shared account almost every applicant spec signs in as. Has a draft application.
        $this->seedApplicant('E2E_EMAIL', 'E2E_PASSWORD', 'Qa', 'Applicant', $programs['BSCS'], ['draft' => true, 'tour' => 0]);
        // Used by the "duplicate email" sign-up spec; never signs in.
        $this->seedApplicant('E2E_EXISTING_EMAIL', 'E2E_PASSWORD', 'Qa', 'Existing', $programs['BSCS'], ['draft' => true, 'tour' => 1]);
        // First-login applicant: temp_password stays 1 (reset on every run) so the forced password change can be tested.
        $this->seedApplicant('E2E_TEMP_EMAIL', 'E2E_TEMP_PASSWORD', 'Qa', 'Temp', $programs['BSCS'], ['temp' => 1, 'tour' => 0]);
        // Fresh applicant: no application at all (choose-path / Fast Track / empty states).
        $this->seedApplicant('E2E_FRESH_EMAIL', 'E2E_FRESH_PASSWORD', 'Qa', 'Fresh', $programs['BSCS'], ['tour' => 0]);
        // Second applicant in a different program (program-specific form / requirements).
        $this->seedApplicant('E2E_SECOND_EMAIL', 'E2E_SECOND_PASSWORD', 'Qa', 'Second', $programs['BSN'], ['draft' => true, 'tour' => 1]);

        // Disposable applicant carrying the "destructive" fixtures: a pending COUNTER payment and an exam booking.
        $payerApplicantId = $pw !== null
            ? $this->seedApplicant(null, null, 'Qa', 'Payer', $programs['BSCS'], [
                'email' => 'e2e.payer@example.com', 'password' => $pw, 'submitted' => true, 'tour' => 1,
            ])
            : null;
        if ($payerApplicantId) {
            $this->seedPendingPayment($payerApplicantId, $now);
            $this->seedExamSlotWithBooking($payerApplicantId, $now);
        }

        // Docs-verified applicants: every required document of the program is uploaded and approved, so the
        // applicant exam-schedule page is unlocked. They hold no booking: the exam specs have them book
        // a schedule they create themselves, and clean up afterwards, so no re-seed is needed between runs.
        //   e2e.docs      book / cancel an exam slot (applicant side)
        //   e2e.attendee  checked in by the officer
        //   e2e.dropout   booking cancelled by the officer
        if ($pw !== null) {
            foreach (['Docs', 'Attendee', 'Dropout'] as $last) {
                $id = $this->seedApplicant(null, null, 'Qa', $last, $programs['BSCS'], [
                    'email' => 'e2e.' . strtolower($last) . '@example.com', 'password' => $pw, 'draft' => true, 'tour' => 1,
                ]);
                if ($id) {
                    $this->seedApprovedDocuments($id, $programs['BSCS']);
                    $this->resetExamBookings($id);
                }
            }
        }

        echo "E2ETestSeeder finished on '{$name}'.\n";
    }

    // ------------------------------------------------------------------ helpers

    private function loadEnv(): void
    {
        $file = ROOTPATH . '.env.e2e';
        if (is_file($file)) {
            foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                $line = trim($line);
                if ($line === '' || $line[0] === '#' || ! str_contains($line, '=')) {
                    continue;
                }
                [$k, $v] = explode('=', $line, 2);
                $this->env[trim($k)] = trim(trim($v), "\"'");
            }
        }
    }

    private function var(string $key): ?string
    {
        $real = getenv($key);
        $value = ($real !== false && $real !== '') ? $real : ($this->env[$key] ?? '');

        return $value === '' ? null : $value;
    }

    /** @return array<string,int> program_code => program_id */
    private function seedPrograms(): array
    {
        $wanted = [
            'BSCS' => 'Bachelor of Science in Computer Science',
            'BSIT' => 'Bachelor of Science in Information Technology',
            'BSN'  => 'Bachelor of Science in Nursing',
        ];
        $ids = [];
        foreach ($wanted as $code => $name) {
            $row = $this->db->table('programs')->where('program_code', $code)->get()->getRowArray();
            if (! $row) {
                $this->db->table('programs')->insert(['program_code' => $code, 'program_name' => $name]);
                $ids[$code] = (int) $this->db->insertID();
            } else {
                $ids[$code] = (int) $row['program_id'];
            }
        }

        return $ids;
    }

    /** Baseline feature flags: exam scheduling ON (specs that need it OFF switch it off and restore it). */
    private function seedSettings(string $now): void
    {
        foreach (['exam_enabled' => '1', 'fast_track_enabled' => '1', 'tuition_calculator_enabled' => '1'] as $key => $value) {
            $exists = $this->db->table('theme_settings')->where('setting_key', $key)->countAllResults();
            if ($exists) {
                $this->db->table('theme_settings')->where('setting_key', $key)->update(['setting_value' => $value, 'updated_at' => $now]);
            } else {
                $this->db->table('theme_settings')->insert(['setting_key' => $key, 'setting_value' => $value, 'created_at' => $now, 'updated_at' => $now]);
            }
        }
    }

    private function upsertUser(string $email, string $first, string $last, string $role, ?string $hash): int
    {
        $row = $this->db->table('users')->where('email', $email)->get()->getRowArray();
        $data = ['firstname' => $first, 'lastname' => $last, 'role' => $role, 'password' => $hash, 'mobile' => '09171234567'];
        if ($row) {
            $this->db->table('users')->where('user_id', $row['user_id'])->update($data);

            return (int) $row['user_id'];
        }
        $this->db->table('users')->insert($data + ['email' => $email]);

        return (int) $this->db->insertID();
    }

    private function seedAdmin(string $now): void
    {
        $email = $this->var('ADMIN_EMAIL');
        $pw = $this->var('ADMIN_PASSWORD');
        if (! $email || ! $pw) {
            echo "  skip admin: ADMIN_EMAIL / ADMIN_PASSWORD not set\n";

            return;
        }
        $hash = password_hash($pw, PASSWORD_DEFAULT);
        // Admin passwords live in admin_credentials; users.password stays NULL.
        $userId = $this->upsertUser($email, 'QA', 'Admin', 'admin', null);
        $exists = $this->db->table('admin_credentials')->where('user_id', $userId)->countAllResults();
        if ($exists) {
            $this->db->table('admin_credentials')->where('user_id', $userId)->update(['password' => $hash]);
        } else {
            $this->db->table('admin_credentials')->insert(['user_id' => $userId, 'password' => $hash, 'created_at' => $now]);
        }
    }

    private function seedOfficer(string $now): void
    {
        $email = $this->var('OFFICER_EMAIL');
        $pw = $this->var('OFFICER_PASSWORD');
        if (! $email || ! $pw) {
            echo "  skip officer: OFFICER_EMAIL / OFFICER_PASSWORD not set\n";

            return;
        }
        $this->upsertUser($email, 'QA', 'Officer', 'admission_officer', password_hash($pw, PASSWORD_DEFAULT));
    }

    /**
     * @param array{draft?:bool,submitted?:bool,temp?:int,tour?:int,email?:string,password?:string} $opts
     */
    private function seedApplicant(?string $emailVar, ?string $pwVar, string $first, string $last, int $programId, array $opts = []): ?int
    {
        $email = $opts['email'] ?? ($emailVar ? $this->var($emailVar) : null);
        $pw = $opts['password'] ?? ($pwVar ? $this->var($pwVar) : null);
        if (! $email || ! $pw) {
            echo '  skip applicant ' . ($emailVar ?? $last) . ": credentials not set\n";

            return null;
        }

        $userId = $this->upsertUser($email, $first, $last, 'applicant', null);
        $applicant = $this->db->table('applicants')->where('user_id', $userId)->get()->getRowArray();
        $fields = [
            'program_id'    => $programId,
            'password'      => password_hash($pw, PASSWORD_DEFAULT),
            'temp_password' => $opts['temp'] ?? 0,
        ];

        if ($applicant) {
            $applicantId = (int) $applicant['applicant_id'];
            $this->db->table('applicants')->where('applicant_id', $applicantId)->update($fields);
        } else {
            $this->db->table('applicants')->insert($fields + [
                'user_id' => $userId, 'application_status' => 'pending', 'has_seen_tour' => $opts['tour'] ?? 0,
                'application_date' => date('Y-m-d H:i:s'), 'form_step' => 1,
            ]);
            $applicantId = (int) $this->db->insertID();
            $this->db->table('applicants')->where('applicant_id', $applicantId)
                ->update(['applicant_number' => sprintf('APP-%s-%05d', date('Y'), $applicantId)]);
        }

        if (! empty($opts['draft']) || ! empty($opts['submitted'])) {
            $has = $this->db->table('applications')->where('applicant_id', $applicantId)->countAllResults();
            if (! $has) {
                $this->db->table('applications')->insert([
                    'applicant_id' => $applicantId, 'applicant_type' => 'freshmen', 'is_fast_track' => 0,
                    'program_id' => $programId, 'school_year' => '2025-2026', 'semester' => '1st Semester',
                    'application_status' => ! empty($opts['submitted']) ? 'pending' : 'draft',
                ]);
            }
        }

        return $applicantId;
    }

    /** Uploads + approves a document for every active, mandatory requirement of the program. */
    private function seedApprovedDocuments(int $applicantId, int $programId): void
    {
        $reqs = $this->db->table('program_requirements pr')
            ->select('pr.program_requirement_id')
            ->join('requirements r', 'r.requirement_id = pr.requirement_id')
            ->join('program_requirement_categories prc', 'prc.program_requirement_category_id = pr.program_requirement_category_id')
            ->where('prc.program_id', $programId)->where('prc.is_active', 1)
            ->where('pr.is_active', 1)->where('r.is_active', 1)->where('pr.is_mandatory', 1)
            ->get()->getResultArray();
        foreach ($reqs as $req) {
            $where = ['applicant_id' => $applicantId, 'program_requirement_id' => $req['program_requirement_id']];
            $data = ['document_name' => 'e2e-seed.pdf', 'mime_type' => 'application/pdf', 'status' => 'approved'];
            if ($this->db->table('applicant_documents')->where($where)->countAllResults()) {
                $this->db->table('applicant_documents')->where($where)->update($data);
            } else {
                $this->db->table('applicant_documents')->insert($where + $data);
            }
        }
    }

    /** Removes the applicant's exam bookings and recounts the slots they held, so a booking spec starts clean. */
    private function resetExamBookings(int $applicantId): void
    {
        $slotIds = array_column($this->db->table('exam_bookings')->select('exam_schedule_id')->where('applicant_id', $applicantId)->get()->getResultArray(), 'exam_schedule_id');
        $this->db->table('exam_bookings')->where('applicant_id', $applicantId)->delete();
        foreach (array_unique($slotIds) as $slotId) {
            $booked = $this->db->table('exam_bookings')->where(['exam_schedule_id' => $slotId, 'status !=' => 'cancelled'])->countAllResults();
            $this->db->table('exam_schedules')->where('exam_schedule_id', $slotId)->update(['booked_slots' => $booked]);
        }
    }

    /** A pending COUNTER admission-fee payment an officer can approve / reject. */
    private function seedPendingPayment(int $applicantId, string $now): void
    {
        $ref = 'E2E-SEED-PENDING';
        if ($this->db->table('payment')->where('reference_num', $ref)->countAllResults()) {
            // Keep it pending on every run so approve / reject specs can use it again.
            $this->db->table('payment')->where('reference_num', $ref)->update(['payment_status' => 'PENDING', 'remarks' => null, 'reviewed_by' => null, 'reviewed_at' => null]);

            return;
        }
        $fee = $this->db->table('payment_fee_type')->where('code', 'ADMISSION_FEE')->get()->getRowArray();
        $method = $this->db->table('payment_method')->where('code', 'GCASH')->get()->getRowArray();
        if (! $fee || ! $method) {
            echo "  skip pending payment: ADMISSION_FEE or GCASH missing\n";

            return;
        }
        $this->db->table('payment')->insert([
            'applicant_id' => $applicantId, 'payment_fee_type_id' => $fee['payment_fee_type_id'],
            'payment_method_id' => $method['payment_method_id'], 'payment_source' => 'COUNTER',
            'reference_num' => $ref, 'amount_paid' => $fee['amount'], 'payment_status' => 'PENDING',
            'payment_date' => $now, 'created_at' => $now, 'updated_at' => $now,
        ]);
    }

    /** An open exam slot far in the future, with one booking, for check-in / cancel / result specs. */
    private function seedExamSlotWithBooking(int $applicantId, string $now): void
    {
        $location = 'E2E Seed Exam Hall';
        $slot = $this->db->table('exam_schedules')->where('location', $location)->get()->getRowArray();
        if (! $slot) {
            $this->db->table('exam_schedules')->insert([
                'exam_date' => date('Y-m-d', strtotime('+60 days')), 'start_time' => '09:00:00', 'end_time' => '12:00:00',
                'location' => $location, 'max_slots' => 10, 'booked_slots' => 0, 'status' => 'open',
                'created_at' => $now, 'updated_at' => $now,
            ]);
            $slotId = (int) $this->db->insertID();
        } else {
            $slotId = (int) $slot['exam_schedule_id'];
            // Slot dates in the past are not bookable; keep it ahead of today.
            if (strtotime($slot['exam_date']) < time()) {
                $this->db->table('exam_schedules')->where('exam_schedule_id', $slotId)->update(['exam_date' => date('Y-m-d', strtotime('+60 days'))]);
            }
        }

        $booking = $this->db->table('exam_bookings')->where(['exam_schedule_id' => $slotId, 'applicant_id' => $applicantId])->get()->getRowArray();
        $data = ['status' => 'booked', 'booked_at' => $now, 'checked_in_at' => null, 'result_status' => 'PENDING', 'score' => null];
        if ($booking) {
            $this->db->table('exam_bookings')->where('exam_booking_id', $booking['exam_booking_id'])->update($data);
        } else {
            $this->db->table('exam_bookings')->insert($data + ['exam_schedule_id' => $slotId, 'applicant_id' => $applicantId]);
        }
        $booked = $this->db->table('exam_bookings')->where(['exam_schedule_id' => $slotId, 'status !=' => 'cancelled'])->countAllResults();
        $this->db->table('exam_schedules')->where('exam_schedule_id', $slotId)->update(['booked_slots' => $booked]);
    }
}
