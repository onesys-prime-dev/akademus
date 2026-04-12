<?php

namespace App\Controllers\AdmissionOfficer;

use App\Controllers\BaseController;
use App\Models\ThemeModel;
use App\Models\ProgramModel;
use App\Services\ApplicantService;
use App\Services\ExamScheduleService;
use App\Models\FollowUpMessageModel;

class DashboardController extends BaseController
{
    public function index()
    {
        helper('theme');

        $themeModel   = new ThemeModel();
        $programModel = new ProgramModel();
        $db           = \Config\Database::connect();

        // --- Status counts (applicants + users + programs must exist) ---
        $statusCounts = [
            'all' => 0, 'in_progress' => 0, 'pending' => 0,
            'approved' => 0, 'rejected' => 0, 'completed' => 0,
        ];
        try {
            $applicantSvc = new ApplicantService();
            $statusCounts = $applicantSvc->getStatusCounts();
        } catch (\Throwable $e) {
            log_message('error', 'Dashboard statusCounts: ' . $e->getMessage());
        }

        // --- Passing rate ---
        $processedCount = $statusCounts['approved'] + $statusCounts['completed'];
        $ratedTotal     = $processedCount + $statusCounts['rejected'];
        $passingRate    = $ratedTotal > 0 ? round(($processedCount / $ratedTotal) * 100) : 0;

        // --- Applicants per program ---
        $perProgram = [];
        try {
            $perProgram = $db->table('applicants a')
                ->select('p.program_name, p.program_code, COUNT(*) as total')
                ->join('programs p', 'p.program_id = a.program_id')
                ->groupBy('a.program_id')
                ->orderBy('total', 'DESC')
                ->get()
                ->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', 'Dashboard perProgram: ' . $e->getMessage());
        }

        // --- Upcoming exam schedules (next 5 from today) ---
        $upcomingSchedules = [];
        try {
            $upcomingSchedules = $db->table('exam_schedules')
                ->where('exam_date >=', date('Y-m-d'))
                ->orderBy('exam_date', 'ASC')
                ->orderBy('start_time', 'ASC')
                ->limit(5)
                ->get()
                ->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', 'Dashboard upcomingSchedules: ' . $e->getMessage());
        }

        // --- Calendar: current month exam days ---
        $now             = new \DateTime();
        $calYear         = (int) $now->format('Y');
        $calMonth        = (int) $now->format('n');
        $calendarSummary = [];
        try {
            $examSvc         = new ExamScheduleService();
            $calendarSummary = $examSvc->getCalendarSummary($calMonth, $calYear);
        } catch (\Throwable $e) {
            log_message('error', 'Dashboard calendarSummary: ' . $e->getMessage());
        }

        // --- Recent applicants (last 10) ---
        $rows = [];
        try {
            $applicantSvc = new ApplicantService();
            $rows         = $applicantSvc->getApplicants([], 10, 0);
        } catch (\Throwable $e) {
            log_message('error', 'Dashboard rows: ' . $e->getMessage());
        }

        // --- Unread messages ---
        $unreadMessages = 0;
        try {
            $followUpModel  = new FollowUpMessageModel();
            $unreadMessages = $followUpModel->getTotalUnreadForOfficer();
        } catch (\Throwable $e) {
            log_message('error', 'Dashboard unreadMessages: ' . $e->getMessage());
        }

        // --- To-do list data ---
        $totalConversations = 0;
        try {
            $totalConversations = (int) $db->query(
                'SELECT COUNT(DISTINCT applicant_id) AS cnt FROM follow_up_messages'
            )->getRow()->cnt;
        } catch (\Throwable $e) {
            log_message('error', 'Dashboard totalConversations: ' . $e->getMessage());
        }

        $examSlots = null;
        try {
            $examSlots = $db->table('exam_schedules')
                ->select('SUM(booked_slots) as booked, SUM(max_slots) as total_slots')
                ->where('exam_date >=', date('Y-m-d'))
                ->where('status', 'open')
                ->get()
                ->getRow();
        } catch (\Throwable $e) {
            log_message('error', 'Dashboard examSlots: ' . $e->getMessage());
        }

        $todoItems = [
            [
                'task'  => 'Review Pending Applications',
                'done'  => $processedCount,
                'total' => max(1, $statusCounts['all']),
            ],
            [
                'task'  => 'Respond to Follow-up Messages',
                'done'  => max(0, $totalConversations - $unreadMessages),
                'total' => max(1, $totalConversations),
            ],
            [
                'task'  => 'Fill Open Exam Slots',
                'done'  => (int) ($examSlots->booked ?? 0),
                'total' => max(1, (int) ($examSlots->total_slots ?? 1)),
            ],
        ];

        return view('admission-officer/main', [
            'title'             => 'Dashboard',
            'page_content'      => 'admission-officer/dashboard/dashboard',
            'breadcrumb'        => [
                ['label' => 'Dashboard', 'url' => base_url('/admission-officer/dashboard')],
                ['label' => 'Overview'],
            ],
            'theme'             => $themeModel->getAllSettings(),
            'programs'          => $programModel->getAllPrograms(),
            'statusCounts'      => $statusCounts,
            'passingRate'       => $passingRate,
            'perProgram'        => $perProgram,
            'upcomingSchedules' => $upcomingSchedules,
            'calendarSummary'   => $calendarSummary,
            'calYear'           => $calYear,
            'calMonth'          => $calMonth,
            'rows'              => $rows,
            'unreadMessages'    => $unreadMessages,
            'todoItems'         => $todoItems,
        ]);
    }

    public function search()
    {
        $query = $this->request->getGet('q');
        if (!$query) {
            return redirect()->to(base_url('admission-officer/dashboard'));
        }

        $applicantService = new ApplicantService();
        $results          = $applicantService->dashboardSearchApplicants($query, 10);
        return $this->response->setJSON([
            'applicants' => $results
        ]);
    }

    public function data()
    {
        $program = $this->request->getGet('program');

        $filters = [];

        if (!empty($program)) {
            $filters['program'] = $program;
        }

        $applicantSvc = new ApplicantService();

        $data = $applicantSvc->getDashboardData($filters);

        return $this->response->setJSON($data);
    }

    public function calendarData()
    {
        $month = (int) $this->request->getGet('month');
        $year  = (int) $this->request->getGet('year');

        $examSvc = new \App\Services\ExamScheduleService();
        $calendarSummary = $examSvc->getCalendarSummary($month, $year);

        $monthName     = date('F', mktime(0, 0, 0, $month, 1, $year));
        $daysInMonth   = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $firstDayOfWeek = (int) date('w', mktime(0, 0, 0, $month, 1, $year));

        $today   = (int) date('j');
        $isCurrentMY = ((int) date('n') === $month && (int) date('Y') === $year);

        $cells = array_fill(0, $firstDayOfWeek, null);
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $cells[] = $d;
        }
        while (count($cells) % 7 !== 0) {
            $cells[] = null;
        }
        $weeks = array_chunk($cells, 7);

        // HEADER HTML
        $header = '
            <span class="calendar__current--month">'.$monthName.'</span>
            <span class="calendar__current--year">'.$year.'</span>
        ';

        // GRID HTML
        ob_start();
        ?>
        <div class="calendar__row">
            <div class="calendar__day calendar__day--label">Sun</div>
            <div class="calendar__day calendar__day--label">Mon</div>
            <div class="calendar__day calendar__day--label">Tue</div>
            <div class="calendar__day calendar__day--label">Wed</div>
            <div class="calendar__day calendar__day--label">Thu</div>
            <div class="calendar__day calendar__day--label">Fri</div>
            <div class="calendar__day calendar__day--label">Sat</div>
        </div>

        <?php foreach ($weeks as $week): ?>
        <div class="calendar__row">
            <?php foreach ($week as $day): ?>
                <?php if ($day === null): ?>
                    <div class="calendar__day calendar__day--inactive"></div>
                <?php else: ?>
                    <?php
                    $hasExam = isset($calendarSummary[$day]);
                    $isToday = $isCurrentMY && ($day === $today);

                    $cls = 'calendar__day';
                    if ($isToday) $cls .= ' calendar__day--active';
                    if ($hasExam) $cls .= ' calendar__day--has-exam';
                    ?>
                    <div class="<?= $cls ?>">
                        <?= $day ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>

        <?php
        $grid = ob_get_clean();

        return $this->response->setJSON([
            'header' => $header,
            'grid'   => $grid,
        ]);
    }
}
