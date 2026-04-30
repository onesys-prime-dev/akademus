<?php

namespace App\Controllers\AdmissionOfficer;

use App\Controllers\BaseController;
use App\Models\ThemeModel;
use App\Models\ProgramModel;
use App\Services\ApplicantService;
use App\Services\ApplicantProfileService;
use App\Services\NotificationService;

class ApplicantsController extends BaseController
{
    protected ApplicantService $applicantService;
    protected ApplicantProfileService $applicantProfileService;
    protected NotificationService $notificationService;

    private const DEFAULT_TAB = 'applicant_form';

    public function __construct()
    {
        $this->applicantService = new ApplicantService();
        $this->applicantProfileService = new ApplicantProfileService();
        $this->notificationService = new NotificationService();
    }

    public function index()
    {
        helper('theme');

        $themeModel = new ThemeModel();
        $programModel = new ProgramModel();

        return view('admission-officer/main', [
            'title'        => 'Applicants',
            'page_content' => 'admission-officer/applicants/applicants',
            'breadcrumb'   => [
                [
                    'label' => 'Applicants',
                    'url'   => base_url('/admission-officer/applicants')
                ],
                [
                    'label' => 'List of Applicants'
                ]
            ],
            'theme'    => $themeModel->getAllSettings(),
            'programs' => $programModel->getAllPrograms()
        ]);
    }

    public function fetchApplicants()
    {
        $filters = [
            'status'  => $this->request->getGet('status'),
            'program' => $this->request->getGet('program'),
            'search'  => $this->request->getGet('search'),
        ];

        $page  = max(1, (int) ($this->request->getGet('page') ?? 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $applicants = $this->applicantService->getApplicants($filters, $limit, $offset);
        $totalCounts = $this->applicantService->countApplicants($filters);
        $statusCounts = $this->applicantService->getStatusCounts($filters);

        return $this->response->setJSON([
            'data'   => $applicants,
            'page'   => $page,
            'total'  => $totalCounts,
            'limit'  => $limit,
            'counts' => $statusCounts
        ]);
    }

    public function decide(int $applicantId)
    {
        $allowed = ['approved', 'rejected'];
        $status  = $this->request->getPost('status');
        $remarks = trim($this->request->getPost('remarks') ?? '');

        if (!in_array($status, $allowed) || !$applicantId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request.'
            ]);
        }

        $db = \Config\Database::connect();

        // 🔍 Get current status FIRST (important)
        $current = $db->table('applicants')
            ->select('application_status')
            ->where('applicant_id', $applicantId)
            ->get()
            ->getRowArray();

        if (!$current) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Applicant not found.'
            ]);
        }

        // ❗ Prevent unnecessary update + email spam
        if ($current['application_status'] === $status) {
            return $this->response->setJSON([
                'success' => true,
                'status'  => $status,
                'message' => 'No changes made.'
            ]);
        }

        // 🔄 Perform update
        $updated = $db->table('applicants')
            ->where('applicant_id', $applicantId)
            ->update([
                'application_status' => $status,
                'decision_remarks'   => $remarks,
            ]);

        // ❗ CHECK if update query failed
        if (!$updated) {
            log_message('error', 'Failed to update applicant decision ID: ' . $applicantId);

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update decision.'
            ]);
        }

        // ❗ Optional: double check affected rows
        if ($db->affectedRows() === 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No changes applied.'
            ]);
        }

        // ✅ SAFE TO TRIGGER NOW
        if ($status === 'approved') {
            $this->notificationService->trigger('applicant.approved', $applicantId);
        }

        if ($status === 'rejected') {
            $this->notificationService->trigger('applicant.rejected', $applicantId);
        }

        return $this->response->setJSON([
            'success' => true,
            'status'  => $status,
            'message' => 'Decision saved successfully.',
        ]);
    }

    public function bulkUpdate()
    {
        $data = $this->request->getJSON(true);

        $ids    = $data['ids'] ?? [];
        $status = $data['status'] ?? null;

        $allowed = ['approved', 'rejected', 'pending', 'in_progress'];

        if (empty($ids) || !$status || !in_array($status, $allowed, true)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid payload'
            ]);
        }

        $db = \Config\Database::connect();

        // 🔍 Get current statuses
        $applicants = $db->table('applicants')
            ->select('applicant_id, application_status')
            ->whereIn('applicant_id', $ids)
            ->get()
            ->getResultArray();

        if (empty($applicants)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No valid applicants found'
            ]);
        }

        $updatedCount = 0;

        foreach ($applicants as $applicant) {

            $applicantId = $applicant['applicant_id'];
            $currentStatus = $applicant['application_status'];

            // ❗ Skip if same status (prevents duplicate emails)
            if ($currentStatus === $status) {
                continue;
            }

            // 🔄 Update per applicant
            $updated = $db->table('applicants')
                ->where('applicant_id', $applicantId)
                ->update([
                    'application_status' => $status
                ]);

            if (!$updated || $db->affectedRows() === 0) {
                log_message('error', 'Bulk update failed for applicant ID: ' . $applicantId);
                continue;
            }

            $updatedCount++;

            // 🔥 Trigger ONLY for relevant statuses
            if ($status === 'approved') {
                $this->notificationService->trigger('applicant.approved', $applicantId);
            }

            if ($status === 'rejected') {
                $this->notificationService->trigger('applicant.rejected', $applicantId);
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'updated' => $updatedCount,
            'message' => "{$updatedCount} applicant(s) updated successfully."
        ]);
    }

    public function profile($applicantId)
    {
        helper('theme');

        $applicantId = (int) $applicantId;

        if (!$applicantId) {
            return redirect()->to(base_url('/admission-officer/applicants'));
        }

        $themeModel = new ThemeModel();

        $profile = $this->applicantProfileService->getApplicantProfile($applicantId);

        if (empty($profile)) {
            return redirect()->to(base_url('/admission-officer/applicants'));
        }

        $activeTab = self::DEFAULT_TAB;

        $filters = [
            'status' => 'all',
            'search' => '',
        ];

        $limit  = 10;
        $page   = 1;
        $offset = 0;

        $payload = $this->applicantProfileService->getTabPayload(
            $applicantId,
            $activeTab,
            $filters,
            $limit,
            $offset
        );

        $viewMap = $this->applicantProfileService->getViewMap();

        $initialContent = view(
            $viewMap[$activeTab],
            array_merge($payload, [
                'applicantId' => $applicantId,
                'activeTab'   => $activeTab,
            ])
        );

        return view('admission-officer/main', [
            'title'         => 'Applicants',
            'page_content'  => 'admission-officer/applicants/detailed-applicant/detailed-applicant',
            'breadcrumb'    => [
                [
                    'label' => 'Applicants',
                    'url'   => base_url('/admission-officer/applicants')
                ],
                [
                    'label' => 'Applicant Profile'
                ]
            ],
            'theme'          => $themeModel->getAllSettings(),
            'applicantId'    => $applicantId,
            'profile'        => $profile,
            'activeTab'      => $activeTab,
            'initialContent' => $initialContent,
        ]);
    }

    public function viewDocument(int $applicantId, int $requirementId)
    {
        $db  = \Config\Database::connect();
        $doc = $db->table('applicant_documents')
            ->where('applicant_id', $applicantId)
            ->where('program_requirement_id', $requirementId)
            ->get()->getRowArray();

        if (!$doc) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $filePath = WRITEPATH . "uploads/applicants/{$applicantId}/" . basename($doc['document_name']);

        if (!file_exists($filePath)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $mime = mime_content_type($filePath);

        return $this->response
            ->setHeader('Content-Type', $mime)
            ->setHeader('Content-Disposition', 'inline; filename="' . esc($doc['document_name']) . '"')
            ->setBody(file_get_contents($filePath));
    }

    public function fetchContent()
    {
        $applicantId = (int) ($this->request->getGet('applicant_id') ?? 0);
        $tab         = (string) ($this->request->getGet('tab') ?? self::DEFAULT_TAB);
        $status      = (string) ($this->request->getGet('status') ?? 'all');
        $search      = (string) ($this->request->getGet('search') ?? '');
        $page        = max(1, (int) ($this->request->getGet('page') ?? 1));

        if (!$applicantId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid applicant ID'
            ]);
        }

        $limit  = 10;
        $offset = ($page - 1) * $limit;

        $filters = compact('status', 'search');

        try {
            $payload = $this->applicantProfileService->getTabPayload(
                $applicantId,
                $tab,
                $filters,
                $limit,
                $offset
            );
        } catch (\Throwable $e) {
            log_message('error', 'fetchContent getTabPayload error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error loading tab data: ' . $e->getMessage(),
            ]);
        }

        $viewMap = $this->applicantProfileService->getViewMap();

        if (!isset($viewMap[$tab])) {
            $tab = self::DEFAULT_TAB;
        }

        try {
            $html = view($viewMap[$tab], array_merge($payload, [
                'applicantId' => $applicantId,
                'activeTab'   => $tab,
                'filters'     => $filters,
                'page'        => $page,
                'limit'       => $limit,
            ]));
        } catch (\Throwable $e) {
            log_message('error', 'fetchContent view error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error rendering tab: ' . $e->getMessage(),
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'tab'     => $tab,
            'html'    => $html,
            'page'    => $page,
            'limit'   => $limit,
            'total'   => $payload['total'] ?? 0,
            'counts'  => $payload['counts'] ?? [],
        ]);
    }
}