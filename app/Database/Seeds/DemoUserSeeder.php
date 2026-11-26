<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Fictional demo / test accounts, one per role and applicant state. Contains no personal data:
 * names are role labels and every address uses the reserved `.test` domain, so no mail can ever reach a real inbox.
 *
 *   php spark db:seed DemoUserSeeder
 *
 * - Refuses to run when CI_ENVIRONMENT is `production`.
 * - Password: the DEMO_PASSWORD environment variable (or .env entry) when set; otherwise a random password is
 *   generated for this run and printed once. Never commit a demo password.
 * - Idempotent: users are matched by email. An existing account keeps its password unless DEMO_PASSWORD is set,
 *   in which case every demo account is reset to it.
 * - Needs ReferenceDataSeeder first (programs BSCS and BSIT).
 */
class DemoUserSeeder extends Seeder
{
    public const EMAIL_DOMAIN = 'akademus.test';

    /** local part => [first, last, role, program_code|null, application_status|null] */
    private const ACCOUNTS = [
        'demo.admin'               => ['Demo', 'Admin', 'admin', null, null],
        'demo.officer'             => ['Demo', 'Officer', 'admission_officer', null, null],
        'demo.applicant.new'       => ['Demo', 'New Applicant', 'applicant', 'BSCS', null],
        'demo.applicant.draft'     => ['Demo', 'Draft Applicant', 'applicant', 'BSCS', 'draft'],
        'demo.applicant.submitted' => ['Demo', 'Submitted Applicant', 'applicant', 'BSIT', 'pending'],
    ];

    public function run(): void
    {
        if (ENVIRONMENT === 'production') {
            throw new \RuntimeException('DemoUserSeeder must not run in production.');
        }

        $fixedPassword = env('DEMO_PASSWORD') ?: null;
        $password = $fixedPassword ?? bin2hex(random_bytes(8)) . 'Aa1!';
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $created = [];

        foreach (self::ACCOUNTS as $local => [$first, $last, $role, $programCode, $status]) {
            $email = $local . '@' . self::EMAIL_DOMAIN;
            [$userId, $isNew] = $this->upsertUser($email, $first, $last, $role);
            $setPassword = $isNew || $fixedPassword !== null;

            if ($role === 'admin') {
                $this->seedAdminCredential($userId, $setPassword ? $hash : null);
            } elseif ($role === 'admission_officer') {
                if ($setPassword) {
                    $this->db->table('users')->where('user_id', $userId)->update(['password' => $hash]);
                }
            } else {
                $programId = $this->programId($programCode);
                $applicantId = $this->seedApplicant($userId, $programId, $setPassword ? $hash : null);
                if ($status !== null) {
                    $this->seedApplication($applicantId, $programId, $status);
                }
            }

            if ($setPassword) {
                $created[] = $email;
            }
        }

        if ($created === []) {
            echo "DemoUserSeeder: demo accounts already exist; passwords unchanged (set DEMO_PASSWORD to reset them).\n";

            return;
        }

        echo "DemoUserSeeder: accounts with a new password:\n  " . implode("\n  ", $created) . "\n";
        echo $fixedPassword !== null
            ? "Password: the value of DEMO_PASSWORD.\n"
            : "Generated password (shown only now): {$password}\n";
    }

    /** @return array{0:int,1:bool} [user_id, created] */
    private function upsertUser(string $email, string $first, string $last, string $role): array
    {
        $row = $this->db->table('users')->where('email', $email)->get()->getRowArray();
        $data = ['firstname' => $first, 'lastname' => $last, 'role' => $role, 'mobile' => '09000000000'];
        if ($row !== null) {
            $this->db->table('users')->where('user_id', $row['user_id'])->update($data);

            return [(int) $row['user_id'], false];
        }
        // Admins and applicants keep their password elsewhere; users.password stays NULL for them.
        $this->db->table('users')->insert($data + ['email' => $email, 'password' => null]);

        return [(int) $this->db->insertID(), true];
    }

    private function seedAdminCredential(int $userId, ?string $hash): void
    {
        $exists = $this->db->table('admin_credentials')->where('user_id', $userId)->countAllResults() > 0;
        if (! $exists) {
            $this->db->table('admin_credentials')->insert(['user_id' => $userId, 'password' => $hash ?? password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT)]);
        } elseif ($hash !== null) {
            $this->db->table('admin_credentials')->where('user_id', $userId)->update(['password' => $hash]);
        }
    }

    private function programId(string $code): int
    {
        $row = $this->db->table('programs')->where('program_code', $code)->get()->getRowArray();
        if ($row === null) {
            throw new \RuntimeException("DemoUserSeeder: program {$code} is missing; run ReferenceDataSeeder first.");
        }

        return (int) $row['program_id'];
    }

    private function seedApplicant(int $userId, int $programId, ?string $hash): int
    {
        $row = $this->db->table('applicants')->where('user_id', $userId)->get()->getRowArray();
        if ($row !== null) {
            if ($hash !== null) {
                $this->db->table('applicants')->where('applicant_id', $row['applicant_id'])->update(['password' => $hash, 'temp_password' => 0]);
            }

            return (int) $row['applicant_id'];
        }

        $this->db->table('applicants')->insert([
            'user_id' => $userId, 'program_id' => $programId,
            'password' => $hash ?? password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT),
            'temp_password' => 0, 'application_status' => 'pending', 'has_seen_tour' => 0,
            'application_date' => date('Y-m-d H:i:s'), 'form_step' => 1,
        ]);
        $applicantId = (int) $this->db->insertID();
        $this->db->table('applicants')->where('applicant_id', $applicantId)
            ->update(['applicant_number' => sprintf('DEMO-%s-%05d', date('Y'), $applicantId)]);

        return $applicantId;
    }

    private function seedApplication(int $applicantId, int $programId, string $status): void
    {
        if ($this->db->table('applications')->where('applicant_id', $applicantId)->countAllResults() > 0) {
            return;
        }
        $this->db->table('applications')->insert([
            'applicant_id' => $applicantId, 'applicant_type' => 'freshmen', 'is_fast_track' => 0,
            'program_id' => $programId, 'school_year' => '2025-2026', 'semester' => '1st Semester',
            'application_status' => $status,
        ]);
    }
}
