<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Puts the exam tables of the TEST database back to the state E2ETestSeeder leaves them in:
 *
 *   php spark db:seed E2EExamResetSeeder
 *
 * Playwright runs it before and after the suite (e2e/global-setup.js), so a crashed or interrupted run
 * never leaves bookings, check-ins, exam results or throwaway schedules behind.
 *
 * - Deletes every exam booking of the seeded e2e.* applicants (book / check in / result specs), except the
 *   fixed booking of e2e.payer, which is put back to "booked, result pending, not checked in".
 * - Deletes the throwaway schedules the specs create (location starting with "QA ") and their bookings.
 * - Recounts booked_slots on every schedule.
 *
 * Refuses to run against any database other than `akademus_test`.
 */
class E2EExamResetSeeder extends Seeder
{
    protected $DBGroup = 'tests';

    private const SAFE_DATABASE = 'akademus_test';
    private const PAYER_EMAIL = 'e2e.payer@example.com';

    public function run(): void
    {
        $name = $this->db->getDatabase();
        if ($name !== self::SAFE_DATABASE) {
            throw new \RuntimeException("E2EExamResetSeeder only runs against '" . self::SAFE_DATABASE . "', but the connection is '{$name}'.");
        }

        // Throwaway schedules created by the specs, with whatever booked them.
        $qaIds = array_column($this->db->table('exam_schedules')->select('exam_schedule_id')->like('location', 'QA ', 'after')->get()->getResultArray(), 'exam_schedule_id');
        if ($qaIds) {
            $this->db->table('exam_bookings')->whereIn('exam_schedule_id', $qaIds)->delete();
            $this->db->table('exam_schedules')->whereIn('exam_schedule_id', $qaIds)->delete();
        }

        // Bookings of the seeded applicants (everything but the payer's fixed booking).
        $applicants = $this->db->table('applicants a')
            ->select('a.applicant_id, u.email')
            ->join('users u', 'u.user_id = a.user_id')
            ->like('u.email', 'e2e.', 'after')->like('u.email', '@example.com', 'before')
            ->get()->getResultArray();
        $payerId = null;
        $others = [];
        foreach ($applicants as $row) {
            if ($row['email'] === self::PAYER_EMAIL) {
                $payerId = (int) $row['applicant_id'];
            } else {
                $others[] = (int) $row['applicant_id'];
            }
        }
        if ($others) {
            $this->db->table('exam_bookings')->whereIn('applicant_id', $others)->delete();
        }
        if ($payerId) {
            $this->db->table('exam_bookings')->where('applicant_id', $payerId)->update([
                'status' => 'booked', 'checked_in_at' => null, 'result_status' => 'PENDING', 'score' => null,
                'result_remarks' => null, 'result_recorded_at' => null, 'result_recorded_by' => null,
            ]);
        }

        // Recount seats.
        foreach ($this->db->table('exam_schedules')->select('exam_schedule_id')->get()->getResultArray() as $s) {
            $booked = $this->db->table('exam_bookings')->where(['exam_schedule_id' => $s['exam_schedule_id'], 'status !=' => 'cancelled'])->countAllResults();
            $this->db->table('exam_schedules')->where('exam_schedule_id', $s['exam_schedule_id'])->update(['booked_slots' => $booked]);
        }

        echo "E2EExamResetSeeder finished on '{$name}'.\n";
    }
}
