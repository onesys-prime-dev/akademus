<?php

namespace App\Controllers;

use App\Models\ApplicantModel;
use App\Models\ThemeModel;

class Dashboard extends BaseController
{
    public function index()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/sign_in');
        }

        $applicantModel = new ApplicantModel();
        $themeModel     = new ThemeModel();

        $applicant = $applicantModel
            ->where('user_id', session()->get('user_id'))
            ->first();

        // Get application row
        $db          = \Config\Database::connect();
        $application = $db->table('applications')
            ->where('applicant_id', $applicant['applicant_id'])
            ->get()->getRowArray();

        // Determine current step
        $step = 1; // Account always done
        if ($application) {
            $step = 2; // Form started
            if ($application['application_status'] === 'approved') {
                $step = 4;
            }
            if ($application['application_status'] === 'enrolled') {
                $step = 5;
            }
        }

        $progress = ($step / 5) * 100;

        $data['title']            = 'Dashboard';
        $data['page_content']     = 'dashboard/menu/dashboard';
        $data['applicant_number'] = $applicant['applicant_number'] ?? 'N/A';
        $data['firstname']        = session()->get('firstname');
        $data['theme']            = $themeModel->getAllSettings();
        $data['current_step']     = $step;
        $data['progress']         = $progress;

        return view('dashboard/main', $data);
    }
    public function tuitionCalculator()
    {
        $themeModel = new \App\Models\ThemeModel();
        $data['theme']        = $themeModel->getAllSettings();
        $data['title']        = 'Tuition Fee Calculator';
        $data['active_menu']  = 'tuition_calculator';
        $data['page_content'] = 'dashboard/support/tuition_fee_calculator';
        return view('dashboard/main', $data);
    }

    public function admissionProcessingFee()
    {
        $themeModel = new \App\Models\ThemeModel();
        $data['theme']        = $themeModel->getAllSettings();
        $data['title']        = 'Admission Processing Fee';
        $data['active_menu']  = 'admission_processing_fee';
        $data['page_content'] = 'dashboard/payments/admission_processing_fee';
        return view('dashboard/main', $data);
    }
}