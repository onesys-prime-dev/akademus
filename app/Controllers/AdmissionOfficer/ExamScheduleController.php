<?php

namespace App\Controllers\AdmissionOfficer;

use App\Controllers\BaseController;
use App\Models\ThemeModel;
use App\Services\ExamScheduleService;

class ExamScheduleController extends BaseController
{
    private ExamScheduleService $service;

    public function __construct()
    {
        $this->service = new ExamScheduleService();
    }

    private function resolveState(): array
    {
        $month = (int) ($this->request->getGet('month') ?: date('n'));
        $year  = (int) ($this->request->getGet('year') ?: date('Y'));
        $view  = $this->request->getGet('view') ?: 'calendar';

        return [
            'month'   => ($month >= 1 && $month <= 12) ? $month : (int) date('n'),
            'year'    => $year,
            'view'    => in_array($view, ['calendar', 'list'], true) ? $view : 'calendar',
            'day'     => (int) ($this->request->getGet('day') ?: 0),
            'status'  => $this->request->getGet('status') ?: 'all',
            'search'  => trim((string) ($this->request->getGet('search') ?: '')),
            'page'    => max(1, (int) ($this->request->getGet('page') ?: 1)),
        ];
    }

    public function index()
    {
        helper('theme');

        $themeModel = new ThemeModel();
        $state = $this->resolveState();

        return view('admission-officer/main', [
            'title'         => 'Exam Schedule',
            'page_content'  => 'admission-officer/exam-schedule/exam-schedule',
            'breadcrumb'    => [
                ['label' => 'Exam Schedule', 'url' => base_url('/admission-officer/exam-schedule')],
                ['label' => ucfirst($state['view'])],
            ],
            'theme'         => $themeModel->getAllSettings(),
            'activeView'    => $state['view'],
            'selectedMonth' => $state['month'],
            'selectedYear'  => $state['year'],
            'initialContent'=> $this->renderContent($state),
        ]);
    }

    public function content()
    {
        $state = $this->resolveState();

        return $this->response->setJSON([
            'html'   => $this->renderContent($state),
            'state'  => $state, // cleaner than repeating fields
        ]);
    }

    private function renderContent(array $state): string
    {
        if ($state['view'] === 'calendar') {
            return view('admission-officer/exam-schedule/components/content/calendar', [
                'month' => $state['month'],
                'year'  => $state['year'],
                'calendarSummary' => $this->service->getCalendarSummary($state['month'], $state['year']),
            ]);
        }

        $result = $this->service->getSchedules($state, $state['page'], 5);

        return view('admission-officer/exam-schedule/components/content/list', [
            'rows'         => $result['rows'],
            'totalPages'   => $result['pages'],
            'currentPage'  => $state['page'],
            'filters'      => $state,
            'counts'       => $result['counts'],
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
        $id = $this->service->createSchedule($this->request->getPost());
        return $this->response->setJSON(['success' => true, 'id' => $id]);
    }

    public function update($id)
    {
        $ok = $this->service->updateSchedule($id, $this->request->getPost());
        return $this->response->setJSON(['success' => $ok]);
    }

    public function delete($id)
    {
        $ok = $this->service->deleteSchedule($id);
        return $this->response->setJSON(['success' => $ok]);
    }

    public function checkIn($bookingId)
    {
        return $this->response->setJSON([
            'success' => $this->service->checkInBooking($bookingId)
        ]);
    }

    public function cancelBooking($bookingId)
    {
        return $this->response->setJSON([
            'success' => $this->service->cancelBooking($bookingId)
        ]);
    }
}