<?php

namespace App\Controllers\AdmissionOfficer;

use App\Controllers\BaseController;

use App\Models\ThemeModel;
use App\Models\ProgramModel;

use App\Services\OfficerDashboardService;

class DashboardController extends BaseController
{
    protected OfficerDashboardService $officerDashboardService;

    public function __construct() {
        $this->officerDashboardService = new OfficerDashboardService();
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
                    'label' => 'Overview'
                ]
            ],
            'theme'               => $themeModel->getAllSettings(),
            'programs'            => $programModel->getAllPrograms(),
            'key_metrics'         => $this->officerDashboardService->getKeyMetrics(),
            'recent_applicants'   => $this->officerDashboardService->getApplicants($limit = 5),
        ]);
    }
}