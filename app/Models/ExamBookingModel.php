<?php

namespace App\Models;

use CodeIgniter\Model;

class ExamBookingModel extends Model
{
    protected $table      = 'exam_bookings';
    protected $primaryKey = 'exam_booking_id';

    protected $allowedFields = [
        'exam_schedule_id',
        'applicant_id',
        'status',
        'booked_at',
        'checked_in_at',
        'result_status',
        'score',
        'result_remarks',
        'result_recorded_at',
        'result_recorded_by',
    ];

    protected $useTimestamps = false;

    /**
     * Get a field from the applicant's latest exam booking.
     */
    public function getResultStatus(
        string $field,
        $default = null
    ) {
        $applicantId = (int) session()->get('applicant_id');

        if (! $applicantId) {
            return $default;
        }

        $booking = $this
            ->where('applicant_id', $applicantId)
            ->orderBy('exam_booking_id', 'DESC')
            ->first();

        if (! $booking) {
            return $default;
        }

        return $booking[$field] ?? $default;
    }

    /**
     * Get latest booking of applicant.
     */
    public function getLatestBooking(int $applicantId): ?array
    {
        return $this
            ->where('applicant_id', $applicantId)
            ->orderBy('exam_booking_id', 'DESC')
            ->first();
    }
}