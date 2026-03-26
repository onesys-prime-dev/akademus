<?php

namespace App\Controllers\AdmissionOfficer;

use App\Controllers\BaseController;
use App\Models\ThemeModel;
use App\Services\ApplicantProfileService;

class ApplicantsController extends BaseController
{
    protected ApplicantProfileService $applicantProfileService;

    public function __construct()
    {
        $this->applicantProfileService = new ApplicantProfileService();
    }

    public function index($applicantId = null)
    {
        helper('theme');

        $applicantId = (int) $applicantId;

        if (!$applicantId) {
            return redirect()->to(base_url('/admission-officer/dashboard'));
        }

        $themeModel = new ThemeModel();

        $profile = $this->applicantProfileService->getApplicantProfile($applicantId);

        if (empty($profile)) {
            return redirect()->to(base_url('/admission-officer/dashboard'));
        }

        $activeTab = 'requirements';

        $filters = [
            'status' => 'all',
            'search' => '',
        ];

        $limit  = 10;
        $page   = 1;
        $offset = 0;

        $tabPayload = $this->applicantProfileService->getTabPayload(
            $applicantId,
            $activeTab,
            $filters,
            $limit,
            $offset
        );

        $initialContent = view(
            'admission-officer/applicants/components/content/' . $activeTab,
            array_merge($tabPayload, [
                'applicantId' => $applicantId,
                'activeTab'   => $activeTab,
            ])
        );

        return view('admission-officer/main', [
            'title'          => 'Applicants',
            'page_content'    => 'admission-officer/applicants/applicants',
            'breadcrumb'     => [
                [
                    'label' => 'Applicants',
                    'url'   => base_url('/admission-officer/applicants')
                ],
                [
                    'label' => 'Applicant Profile'
                ],
                [
                    'label' => 'Applicant Form'
                ]
            ],
            'theme'           => $themeModel->getAllSettings(),
            'applicantId'     => $applicantId,
            'profile'         => $profile,
            'activeTab'       => $activeTab,
            'initialContent'  => $initialContent,
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

        $filePath = WRITEPATH . "uploads/applicants/{$applicantId}/" . $doc['document_name'];

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
        $tab         = (string) ($this->request->getGet('tab') ?? 'requirements');
        $status      = (string) ($this->request->getGet('status') ?? 'all');
        $search      = (string) ($this->request->getGet('search') ?? '');
        $page        = max(1, (int) ($this->request->getGet('page') ?? 1));

        if (!$applicantId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid applicant ID'
            ]);
        }

        $limit  = 3;
        $offset = ($page - 1) * $limit;

        $filters = [
            'status' => $status,
            'search' => $search,
        ];

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

        $viewMap = [
            'applicant_form'    => 'admission-officer/applicants/components/content/applicant-form',
            'requirements'      => 'admission-officer/applicants/components/content/requirements',
            'exam'              => 'admission-officer/applicants/components/content/exam',
            'reservation_fee'   => 'admission-officer/applicants/components/content/reservation-fee',
            'clearance'         => 'admission-officer/applicants/components/content/clearance',
        ];

        $tab = array_key_exists($tab, $viewMap) ? $tab : 'requirements';

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