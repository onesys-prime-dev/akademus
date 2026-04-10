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
}
