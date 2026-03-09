<?php

namespace App\Controllers\Applicant;

use App\Controllers\BaseController;

use App\Models\Applicant\ApplicantModel;

class DashboardController extends BaseController
{
    public function index()
    {
        if (!session()->get('logged_in')) { 
            return redirect()->to('/sign-in');
        }

        // ADMIN accounts
        // if (!session()->get('logged_in') || session()->get('role') !== 'applicant') {
        //     return redirect()->to('/sign_in');
        // }

        $applicantModel = new ApplicantModel();

        $applicant = $applicantModel
            ->where('user_id', session()->get('user_id'))
            ->first();

        $data['title'] = 'Dashboard';
        $data['page_content'] = 'applicant/dashboard/dashboard';
        $data['applicant_number'] = $applicant['applicant_number'] ?? 'N/A';
        $data['firstname'] = session()->get('firstname');

        return view('applicant/main', $data);

        // more add to this
        // REMINDER: CHECK THE DATABASE FIRST, CORRECT
        // RELATIONAL STRUCTURE OF DATABASE
        // CHECK IF applicant_num is fk in user or vice versa idk kajsdflkijas
    }
}