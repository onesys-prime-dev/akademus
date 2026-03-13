<?php

namespace App\Controllers\Applicant;

use App\Controllers\BaseController;

use App\Models\Applicant\ApplicantModel;
use App\Models\ThemeModel;

class DashboardController extends BaseController
{
    public function index()
    {
        if (!session()->get('logged_in')) { 
            return redirect()->to('/sign-in');
        }

        $applicantModel = new ApplicantModel();
        $themeModel     = new ThemeModel();

        $applicant = $applicantModel
            ->where('user_id', session()->get('user_id'))
            ->first();

        // Guard: no applicant record for this user
        if (!$applicant) {
            return redirect()->to('/sign-in');
        }

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
        $data['page_content']     = 'applicant/dashboard/dashboard';
        $data['applicant_number'] = $applicant['applicant_number'] ?? 'N/A';
        $data['firstname']        = session()->get('firstname');
        $data['theme']            = $themeModel->getAllSettings();
        $data['current_step']     = $step;
        $data['progress']         = $progress;

        return view('applicant/main', $data);
    }
}