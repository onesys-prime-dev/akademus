<?php

namespace App\Controllers\Applicant;

use App\Controllers\BaseController;
use App\Models\ThemeModel;
use App\Models\Applicant\ApplicantModel;
use App\Services\ExamScheduleService;

class ExamScheduleController extends BaseController
{
    private ExamScheduleService $service;

    public function __construct()
    {
        $this->service = new ExamScheduleService();
    }

    private function requireApplicant(): ?array
    {
        if (! session()->get('logged_in')) {
            return null;
        }

        $applicant = (new ApplicantModel())
            ->where('user_id', session()->get('user_id'))
            ->first();

        return $applicant ?: null;
    }

    public function index()
    {
        if (! session()->get('logged_in')) {
            return redirect()->to('/sign-in');
        }

        $themeModel  = new ThemeModel();
        $examEnabled = $themeModel->getSetting('exam_enabled', '1') === '1';

        if (! $examEnabled) {
            return redirect()->to('/dashboard');
        }

        $applicant = $this->requireApplicant();
        if (! $applicant) {
            return redirect()->to('/sign-in');
        }

        $applicantId = (int) $applicant['applicant_id'];
        $booking     = $this->getActiveBooking($applicantId);
        $available   = $booking ? [] : $this->service->getAvailableSchedules();

        return $this->renderApplicant('applicant/exam-schedule/exam-schedule', [
            'title'     => 'Exam Schedule',
            'theme'     => $themeModel->getAllSettings(),
            'booking'   => $booking,
            'available' => $available,
        ]);
    }

    // POST /exam-schedule/book
    public function book()
    {
        if (! session()->get('logged_in')) {
            return $this->jsonError('Unauthorized.');
        }

        $themeModel  = new ThemeModel();
        if ($themeModel->getSetting('exam_enabled', '1') !== '1') {
            return $this->jsonError('Exam scheduling is disabled.');
        }

        $applicant = $this->requireApplicant();
        if (! $applicant) {
            return $this->jsonError('Applicant not found.');
        }

        $scheduleId = (int) $this->request->getPost('schedule_id');
        if (! $scheduleId) {
            return $this->jsonError('No schedule selected.');
        }

        $result = $this->service->bookSlot((int) $applicant['applicant_id'], $scheduleId);

        if ($result === true) {
            return $this->jsonSuccess('Your exam slot has been booked successfully!');
        }

        return $this->jsonError($result);
    }

    // POST /exam-schedule/cancel
    public function cancel()
    {
        if (! session()->get('logged_in')) {
            return $this->jsonError('Unauthorized.');
        }

        $applicant = $this->requireApplicant();
        if (! $applicant) {
            return $this->jsonError('Applicant not found.');
        }

        $db = \Config\Database::connect();
        $booking = $db->table('exam_bookings')
            ->where('applicant_id', (int) $applicant['applicant_id'])
            ->whereIn('status', ['booked'])
            ->get()->getRowArray();

        if (! $booking) {
            return $this->jsonError('No active booking found to cancel.');
        }

        $ok = $this->service->cancelBooking((int) $booking['exam_booking_id']);

        if ($ok) {
            return $this->jsonSuccess('Your booking has been cancelled. You may now choose a new slot.');
        }

        return $this->jsonError('Cancellation failed. Please try again.');
    }

    private function getActiveBooking(int $applicantId): ?array
    {
        $db = \Config\Database::connect();

        return $db->table('exam_bookings eb')
            ->select([
                'eb.exam_booking_id',
                'eb.status AS booking_status',
                'eb.booked_at',
                'eb.checked_in_at',

                'eb.result_status',
                'eb.score',
                'eb.result_remarks',
                'eb.result_recorded_at',

                'es.exam_schedule_id',
                'es.exam_date',
                'es.start_time',
                'es.end_time',
                'es.location',
                'es.status AS schedule_status',
            ])
            ->join('exam_schedules es', 'es.exam_schedule_id = eb.exam_schedule_id')
            ->where('eb.applicant_id', $applicantId)
            ->whereIn('eb.status', ['booked', 'attended'])
            ->orderBy('es.exam_date', 'ASC')
            ->limit(1)
            ->get()
            ->getRowArray() ?: null;
    }

    private function jsonSuccess(string $message): \CodeIgniter\HTTP\ResponseInterface
    {
        return $this->response->setJSON(['success' => true, 'message' => $message]);
    }

    private function jsonError(string $message): \CodeIgniter\HTTP\ResponseInterface
    {
        return $this->response->setJSON(['success' => false, 'message' => $message]);
    }
}
