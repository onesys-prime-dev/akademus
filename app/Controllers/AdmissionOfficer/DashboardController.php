<?php

namespace App\Controllers\AdmissionOfficer;

use App\Controllers\BaseController;

use App\Models\ThemeModel;
use App\Models\ProgramModel;

class DashboardController extends BaseController
{
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
            'theme'        => $themeModel->getAllSettings(),
            'programs'     => $programModel->getAllPrograms()
        ]);
    }
}