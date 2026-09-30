<?php

namespace App\Controllers\AdmissionOfficer;

use App\Controllers\BaseController;
use App\Libraries\EncryptedFileStorage;
use App\Models\ThemeModel;
use App\Models\ProgramModel;
use App\Services\ApplicantService;
use App\Services\ApplicantProfileService;
use App\Services\NotificationService;
use App\Services\SisSyncService;
use CodeIgniter\Events\Events;
use Throwable;

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
            $this->queueSisHandoff($applicantId);
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

    /**
     * Queues the just-approved applicant for handoff to the SIS. Only
     * inserts a sis_sync row (safe alongside the decision write above); the
     * actual HTTP call runs after the response is sent, and
     * `php spark sis:sync` retries anything that fails. Must never break
     * the approval decision itself.
     */
    private function queueSisHandoff(int $applicantId): void
    {
        try {
            $service = new SisSyncService();
            $syncId  = $service->queueForApplicant($applicantId);

            if ($syncId !== null) {
                Events::on('post_system', static function () use ($service, $syncId): void {
                    try {
                        $service->attempt($syncId);
                    } catch (Throwable $e) {
                        log_message('error', '[SIS sync] post-response attempt failed: ' . $e->getMessage());
                    }
                });
            }
        } catch (Throwable $e) {
            log_message('error', '[SIS sync] could not queue handoff: ' . $e->getMessage());
        }
    }

    public function requestRevision(int $applicantId)
    {
        $pageIds = $this->request->getPost('page_ids');
        $note    = trim($this->request->getPost('note') ?? '');

        if (empty($pageIds) || !is_array($pageIds) || $note === '' || !$applicantId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Select at least one step and provide a note.',
            ]);
        }

        $pageIds = array_values(array_unique(array_map('intval', $pageIds)));

        $db = \Config\Database::connect();

        $application = $db->table('applications')
            ->where('applicant_id', $applicantId)
            ->orderBy('application_date', 'DESC')
            ->get()
            ->getRowArray();

        if (!$application) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No submitted application found.',
            ]);
        }

        if (!in_array($application['application_status'], ['pending', 'needs_revision'], true)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'This application cannot be sent back for revision in its current state.',
            ]);
        }

        $updated = $db->table('applications')
            ->where('application_id', $application['application_id'])
            ->update([
                'application_status' => 'needs_revision',
                'revision_note'      => $note,
                'revision_pages'     => json_encode($pageIds),
            ]);

        if (!$updated) {
            log_message('error', 'Failed to set needs_revision for application ID: ' . $application['application_id']);

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to save revision request.',
            ]);
        }

        $pageTitles = (new \App\Models\FormPageModel())
            ->whereIn('id', $pageIds)
            ->orderBy('sort_order', 'ASC')
            ->findColumn('title');

        $stepList    = implode(', ', $pageTitles ?: []);
        $messageBody = "Your application requires revision on: {$stepList}.\n\n" . $note;

        (new \App\Models\FollowUpMessageModel())->insert([
            'applicant_id' => $applicantId,
            'sender_role'  => 'officer',
            'message'      => $messageBody,
            'is_read'      => 0,
            'created_at'   => date('Y-m-d H:i:s'),
        ]);

        $this->notificationService->trigger('followup.new_message', $applicantId, ['message' => $messageBody]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Revision requested successfully.',
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
                $this->queueSisHandoff($applicantId);
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

        $limit  = 20;
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
            'formPages'      => (new \App\Models\FormPageModel())->getEnabledOrdered(),
        ]);
    }

    public function decideDocument(int $id)
    {
        $data = $this->request->getJSON(true);

        $status = $data['status'] ?? null;

        $allowed = ['approved', 'rejected'];

        if (!in_array($status, $allowed, true)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid status'
            ]);
        }

        $db = \Config\Database::connect();

        $updated = $db->table('applicant_documents')
            ->where('document_id', $id)
            ->update([
                'status' => $status,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

        if (!$updated) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update document status'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Document updated successfully',
            'status'  => $status
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

        $mime = $doc['mime_type'] ?? 'application/octet-stream';

        return $this->response
            ->setHeader('Content-Type', $mime)
            ->setHeader('Content-Disposition', 'inline; filename="' . esc($doc['document_name']) . '"')
            ->setBody(EncryptedFileStorage::read($filePath));
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

        $limit  = 20;
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

    public function saveExamResult(int $bookingId)
    {
        if (! session()->get('ao_logged_in')) {
            return $this->response->setStatusCode(401)->setJSON([
                'success' => false,
                'message' => 'Unauthorized.',
            ]);
        }

        $resultStatus = strtoupper(trim($this->request->getPost('result_status') ?? ''));
        $scoreRaw     = $this->request->getPost('score');
        $remarks      = trim($this->request->getPost('result_remarks') ?? '');
        $recordedBy   = (int) session()->get('ao_id');

        if (! in_array($resultStatus, ['PENDING', 'PASSED', 'FAILED'], true)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Result status is required.',
            ]);
        }

        $score = $scoreRaw !== '' && $scoreRaw !== null
            ? (float) $scoreRaw
            : null;

        $service = new \App\Services\ExamScheduleService();

        $saved = $service->saveResult(
            $bookingId,
            $resultStatus,
            $score,
            $remarks !== '' ? $remarks : null,
            $recordedBy
        );

        if (! $saved) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to save exam result.',
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Exam result saved successfully.',
        ]);
    }
}