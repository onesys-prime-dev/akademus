<?php

namespace App\Services;

use CodeIgniter\Database\BaseConnection;

class ExamScheduleService
{
    private BaseConnection $db;

    public function __construct()
    {
        $this->db = db_connect();
    }

    public function getSchedules(array $filters, int $page = 1, int $perPage = 5): array
    {
        $builder = $this->db->table('exam_schedules es')
            ->select("
                es.exam_schedule_id,
                es.exam_date AS date,
                CONCAT(es.start_time, ' - ', es.end_time) AS time,
                es.location,
                CONCAT(es.booked_slots, ' / ', es.max_slots) AS slots,
                es.status
            ");

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $builder->groupStart()
                ->like('es.location', $search)
                ->orLike('es.exam_date', $search)
                ->orLike("CONCAT(es.start_time, ' - ', es.end_time)", $search, 'both', null, true)
            ->groupEnd();
        }

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $status = $filters['status'];
            $builder->where('es.status', $this->normalizeStatus($status));
        }

        if (!empty($filters['day'])) {
            $builder->where('DAY(es.exam_date)', (int) $filters['day']);
        }

        if (!empty($filters['month'])) {
            $builder->where('MONTH(es.exam_date)', (int) $filters['month']);
        }

        if (!empty($filters['year'])) {
            $builder->where('YEAR(es.exam_date)', (int) $filters['year']);
        }

        $counts = $this->getCounts($filters);

        $countBuilder = clone $builder;
        $total = $countBuilder->countAllResults(false);

        $rows = $builder
            ->orderBy('es.exam_date', 'ASC')
            ->orderBy('es.start_time', 'ASC')
            ->limit($perPage, max(0, ($page - 1) * $perPage))
            ->get()
            ->getResultArray();

        return [
            'rows' => $rows,
            'total' => $total,
            'pages' => max(1, (int) ceil($total / $perPage)),
            'counts' => $counts,
        ];
    }

    public function getCalendarSummary(int $month, int $year): array
    {
        $rows = $this->db->table('exam_schedules es')
            ->select("
                DAY(es.exam_date) AS day_num,
                COUNT(*) AS total_schedules,
                SUM(CASE WHEN es.status = 'open' THEN 1 ELSE 0 END) AS open_count,
                SUM(CASE WHEN es.booked_slots >= es.max_slots THEN 1 ELSE 0 END) AS full_count
            ")
            ->where('MONTH(es.exam_date)', $month)
            ->where('YEAR(es.exam_date)', $year)
            ->groupBy('DAY(es.exam_date)')
            ->get()
            ->getResultArray();

        $map = [];
        foreach ($rows as $row) {
            $map[(int) $row['day_num']] = $row;
        }

        return $map;
    }

    public function getScheduleById(int $scheduleId): ?array
    {
        return $this->db->table('exam_schedules es')
            ->select("
                es.exam_schedule_id,
                es.exam_date,
                CONCAT(es.start_time, ' - ', es.end_time) AS time,
                es.start_time,
                es.end_time,
                es.location,
                es.max_slots,
                es.booked_slots,
                (es.max_slots - es.booked_slots) AS available_slots,
                es.status,
                es.created_at
            ")
            ->where('es.exam_schedule_id', $scheduleId)
            ->get()
            ->getRowArray() ?: null;
    }

    public function getApplicantsBySchedule(int $scheduleId): array
    {
        return $this->db->table('exam_schedules es')
            ->select("
                eb.exam_booking_id AS booking_id,
                CONCAT(u.lastname, ', ', u.firstname) AS applicant,
                u.mobile AS contact,
                eb.status,
                eb.booked_at,
                eb.checked_in_at,
                eb.applicant_id
            ")
            ->join('exam_bookings eb', 'eb.exam_schedule_id = es.exam_schedule_id')
            ->join('applicants a', 'a.applicant_id = eb.applicant_id')
            ->join('users u', 'u.user_id = a.user_id')
            ->where('es.exam_schedule_id', $scheduleId)
            ->orderBy('eb.booked_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function createSchedule(array $data): int
    {
        $this->db->table('exam_schedules')->insert([
            'exam_date'   => $data['exam_date'],
            'start_time'  => $data['start_time'],
            'end_time'    => $data['end_time'],
            'location'    => $data['location'],
            'max_slots'   => $data['max_slots'],
            'booked_slots'=> 0,
            'status'      => 'open',
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ]);

        return (int) $this->db->insertID();
    }

    public function updateSchedule(int $id, array $data): bool
    {
        $current = $this->getScheduleById($id);
        if (!$current) {
            return false;
        }

        if ((int) $data['max_slots'] < (int) $current['booked_slots']) {
            return false;
        }

        return $this->db->table('exam_schedules')
            ->where('exam_schedule_id', $id)
            ->update([
                'exam_date'  => $data['exam_date'],
                'start_time' => $data['start_time'],
                'end_time'   => $data['end_time'],
                'location'   => $data['location'],
                'max_slots'  => $data['max_slots'],
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
    }

    public function deleteSchedule(int $id): bool
    {
        return $this->db->table('exam_schedules')
            ->where('exam_schedule_id', $id)
            ->delete();
    }

    public function checkInBooking(int $bookingId): bool
    {
        return $this->db->table('exam_bookings')
            ->where('exam_booking_id', $bookingId)
            ->update([
                'checked_in_at' => date('Y-m-d H:i:s'),
                'status' => 'attended',
            ]);
    }

    public function cancelBooking(int $bookingId): bool
    {
        $booking = $this->db->table('exam_bookings')
            ->where('exam_booking_id', $bookingId)
            ->get()
            ->getRowArray();

        if (!$booking || $booking['status'] === 'cancelled') {
            return false;
        }

        $this->db->transStart();

        $this->db->table('exam_bookings')
            ->where('exam_booking_id', $bookingId)
            ->update(['status' => 'cancelled']);

        $this->db->table('exam_schedules')
            ->where('exam_schedule_id', $booking['exam_schedule_id'])
            ->set('booked_slots', 'GREATEST(booked_slots - 1, 0)', false)
            ->update();

        $this->db->transComplete();

        return $this->db->transStatus();
    }

    private function normalizeStatus(string $status): string
    {
        return $status === 'close' ? 'closed' : $status;
    }

    public function getCounts(array $filters): array
    {
        $builder = $this->db->table('exam_schedules es');

        // Apply SAME filters except status
        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $builder->groupStart()
                ->like('es.location', $search)
                ->orLike('es.exam_date', $search)
            ->groupEnd();
        }

        if (!empty($filters['day'])) {
            $builder->where('DAY(es.exam_date)', (int) $filters['day']);
        }

        if (!empty($filters['month'])) {
            $builder->where('MONTH(es.exam_date)', (int) $filters['month']);
        }

        if (!empty($filters['year'])) {
            $builder->where('YEAR(es.exam_date)', (int) $filters['year']);
        }

        // Clone for reuse
        $allBuilder = clone $builder;
        $openBuilder = clone $builder;
        $closedBuilder = clone $builder;

        return [
            'all' => $allBuilder->countAllResults(false),

            'open' => $openBuilder
                ->where('es.status', 'open')
                ->countAllResults(false),

            'closed' => $closedBuilder
                ->where('es.status', 'closed')
                ->countAllResults(false),
        ];
    }
}