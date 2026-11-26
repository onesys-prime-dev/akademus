<?php

namespace App\Controllers\AdmissionOfficer;

use App\Controllers\BaseController;
use App\Models\ThemeModel;
use App\Services\ExamScheduleService;
use App\Services\NotificationService;

class ExamScheduleController extends BaseController
{
    private ExamScheduleService $service;
    protected NotificationService $notificationService;

    public function __construct()
    {
        $this->service             = new ExamScheduleService();
        $this->notificationService = new NotificationService();
    }

    private function resolveState(): array
    {
        $month = (int) ($this->request->getGet('month') ?: date('n'));
        $year  = (int) ($this->request->getGet('year') ?: date('Y'));
        $view  = $this->request->getGet('view') ?: 'calendar';

        return [
            'month'  => ($month >= 1 && $month <= 12) ? $month : (int) date('n'),
            'year'   => $year,
            'view'   => in_array($view, ['calendar', 'list'], true) ? $view : 'calendar',
            'day'    => (int) ($this->request->getGet('day') ?: 0),
            'status' => $this->request->getGet('status') ?: 'all',
            'search' => trim((string) ($this->request->getGet('search') ?: '')),
            'page'   => max(1, (int) ($this->request->getGet('page') ?: 1)),
        ];
    }

    public function index()
    {
        helper('theme');

        $themeModel  = new ThemeModel();
        $examEnabled = $themeModel->getSetting('exam_enabled', '1') === '1';

        if (! $examEnabled) {
            return view('admission-officer/main', [
                'title'        => 'Exam Schedule',
                'page_content' => 'admission-officer/exam-schedule/disabled',
                'breadcrumb'   => [
                    ['label' => 'Exam Schedule'],
                ],
                'theme' => $themeModel->getAllSettings(),
            ]);
        }

        $state = $this->resolveState();

        return view('admission-officer/main', [
            'title'        => 'Exam Schedule',
            'page_content' => 'admission-officer/exam-schedule/exam-schedule',
            'breadcrumb'   => [
                ['label' => 'Exam Schedule', 'url' => base_url('/admission-officer/exam-schedule')],
                ['label' => ucfirst($state['view'])],
            ],
            'theme'          => $themeModel->getAllSettings(),
            'activeView'     => $state['view'],
            'selectedMonth'  => $state['month'],
            'selectedYear'   => $state['year'],
            'initialContent' => $this->renderContent($state),
        ]);
    }

    public function content()
    {
        $examEnabled = (new ThemeModel())->getSetting('exam_enabled', '1') === '1';

        if (! $examEnabled) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Exam scheduling is currently disabled.',
            ]);
        }

        $state = $this->resolveState();

        return $this->response->setJSON([
            'html'  => $this->renderContent($state),
            'state' => $state,
        ]);
    }

    private function renderContent(array $state): string
    {
        if ($state['view'] === 'calendar') {
            return view('admission-officer/exam-schedule/components/content/calendar', [
                'month'           => $state['month'],
                'year'            => $state['year'],
                'calendarSummary' => $this->service->getCalendarSummary($state['month'], $state['year']),
            ]);
        }

        $result = $this->service->getSchedules($state, $state['page'], 5);

        return view('admission-officer/exam-schedule/components/content/list', [
            'rows'        => $result['rows'],
            'totalPages'  => $result['pages'],
            'currentPage' => $state['page'],
            'filters'     => $state,
            'counts'      => $result['counts'],
        ]);
    }

    public function show($id)
    {
        return $this->response->setJSON([
            'schedule'   => $this->service->getScheduleById($id),
            'applicants' => $this->service->getApplicantsBySchedule($id),
        ]);
    }

    public function create()
    {
        $data  = $this->request->getPost();
        $error = $this->validateSchedule($data, null);
        if ($error !== null) {
            return $this->response->setJSON(['success' => false, 'message' => $error]);
        }

        $id = $this->service->createSchedule($data);

        return $this->response->setJSON(['success' => true, 'id' => $id]);
    }

    public function update($id)
    {
        $current = $this->service->getScheduleById((int) $id);
        if (! $current) {
            return $this->response->setJSON(['success' => false, 'message' => 'Exam schedule not found.']);
        }

        $data  = $this->request->getPost();
        $error = $this->validateSchedule($data, $current['raw_date'] ?? null);
        if ($error !== null) {
            return $this->response->setJSON(['success' => false, 'message' => $error]);
        }

        $ok = $this->service->updateSchedule((int) $id, $data);

        return $this->response->setJSON([
            'success' => $ok,
            'message' => $ok ? null : 'Capacity cannot be lower than the number of booked slots.',
        ]);
    }

    /**
     * Server-side validation for create/update. Returns an error message, or null when the data is valid.
     *
     * @param string|null $currentDate the schedule's saved date when updating: keeping an existing past date is allowed
     */
    private function validateSchedule(array $data, ?string $currentDate): ?string
    {
        $validation = service('validation')->reset();
        $validation->setRules([
            'exam_date' => ['label' => 'Exam date', 'rules' => 'required|valid_date[Y-m-d]'],
            // Array form: the regex contains "|", which a rule string would split on.
            'start_time' => ['label' => 'Start time', 'rules' => ['required', 'regex_match[/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/]']],
            'end_time'   => ['label' => 'End time', 'rules' => ['required', 'regex_match[/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/]']],
            'location'   => ['label' => 'Location', 'rules' => 'required|max_length[255]'],
            'max_slots'  => ['label' => 'Capacity', 'rules' => 'required|is_natural_no_zero|less_than_equal_to[10000]'],
        ]);

        if (! $validation->run($data)) {
            return implode(' ', $validation->getErrors());
        }

        if ($data['exam_date'] < date('Y-m-d') && $data['exam_date'] !== $currentDate) {
            return 'The exam date cannot be in the past.';
        }

        if (substr($data['end_time'], 0, 5) <= substr($data['start_time'], 0, 5)) {
            return 'The end time must be after the start time.';
        }

        return null;
    }

    public function delete($id)
    {
        $id = (int) $id;

        // Read these before deleting: the schedule row and the 'booked' status are gone afterwards.
        $schedule     = $this->service->getScheduleById($id);
        $applicantIds = $schedule ? $this->service->getBookedApplicantIds($id) : [];

        $ok = $this->service->deleteSchedule($id);

        if ($ok && $schedule) {
            foreach ($applicantIds as $applicantId) {
                $this->notificationService->trigger('exam.schedule_cancelled', $applicantId, [
                    'exam_date'  => $schedule['raw_date'],
                    'start_time' => $schedule['start_time'],
                    'end_time'   => $schedule['end_time'],
                    'location'   => $schedule['location'],
                ]);
            }
        }

        return $this->response->setJSON(['success' => $ok]);
    }

    public function checkIn($bookingId)
    {
        return $this->response->setJSON([
            'success' => $this->service->checkInBooking($bookingId),
        ]);
    }

    public function cancelBooking($bookingId)
    {
        return $this->response->setJSON([
            'success' => $this->service->cancelBooking($bookingId),
        ]);
    }
}
