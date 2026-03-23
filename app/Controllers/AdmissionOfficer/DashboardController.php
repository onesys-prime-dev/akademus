<?php

namespace App\Controllers\AdmissionOfficer;

use App\Controllers\BaseController;

use App\Models\ThemeModel;
use App\Models\ProgramModel;

use App\Services\ApplicantService;

class DashboardController extends BaseController
{
    protected $applicantService;

    public function __construct() {
        $this->applicantService = new ApplicantService();
    }

    public function index() {
        helper('theme');
        
        $themeModel = new ThemeModel();
        $programModel = new ProgramModel();

        return view('admission-officer/main', [
            'title'        => 'Dashboard',
            'page_content' => 'admission-officer/dashboard/dashboard',
            'breadcrumb'   => [
                [
                    'label' => 'Dashboard',
                    'url'   => base_url('/admission-officer/dashboard')
                ],
                [
                    'label' => 'List of Applicants'
                ]
            ],
            'theme'        => $themeModel->getAllSettings(),
            'programs'     => $programModel->getAllPrograms()
        ]);
    }

    public function fetchApplicants() {
        $filters = [
            'status'  => $this->request->getGet('status'),
            'program' => $this->request->getGet('program'),
            'search'  => $this->request->getGet('search'),
        ];

        $page  = (int) ($this->request->getGet('page') ?? 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $applicants = $this->applicantService->getApplicants($filters, $limit, $offset);
        $totalCounts = $this->applicantService->countApplicants($filters);
        $statusCounts = $this->applicantService->getStatusCounts($filters);

        return $this->response->setJSON([
            'data' => $applicants,
            'page' => $page,
            'total' => $totalCounts,
            'limit' => $limit,
            'counts' => $statusCounts
        ]);
    }

    public function bulkUpdate() {
        $data = $this->request->getJSON(true);

        $ids = $data['ids'] ?? [];
        $status = $data['status'] ?? null;

        if (empty($ids) || !$status) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid payload']);
        }

        $db = \Config\Database::connect();

        // IMPORTANT: must match data-id from the table rows
        $db->table('applicants')
            ->whereIn('applicant_id', $ids)
            ->update(['application_status' => $status]);

        return $this->response->setJSON(['success' => true]);
    }

    public function fetchApplicant($id) {
        
    }
}