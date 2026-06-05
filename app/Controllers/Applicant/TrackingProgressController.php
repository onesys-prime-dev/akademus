<?php

namespace App\Controllers\Applicant;

use App\Controllers\BaseController;
use App\Models\Applicant\ApplicantModel;
use App\Models\ThemeModel;
use App\Services\ApplicationProgressService;

class TrackingProgressController extends BaseController
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

        if (!$applicant) {
            return redirect()->to('/sign-in');
        }

        $db = \Config\Database::connect();

        $application = $db->table('applications')
            ->where('applicant_id', $applicant['applicant_id'])
            ->orderBy('application_date', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        // Shared progress builder
        $progress = (new ApplicationProgressService())
            ->build($applicant, $application);

        // View data
        $data = [
            'title'            => 'Track Submission Progress',
            'page_content'     => 'applicant/tracking-progress/tracking_progress',
            'theme'            => $themeModel->getAllSettings(),
            'applicant_number' => $applicant['applicant_number'] ?? 'N/A',
        ];

        $data = array_merge($data, $progress);

        return view('applicant/main', $data);
    }
}