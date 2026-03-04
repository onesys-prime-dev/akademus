<?php

namespace App\Controllers;

use App\Models\ApplicantModel;

class Dashboard extends BaseController
{
    public function index()
    {
        if (!session()->get('logged_in')) { 
            return redirect()->to('/sign_in');
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
        $data['page_content'] = 'dashboard/menu/dashboard';
        $data['applicant_number'] = $applicant['applicant_number'] ?? 'N/A';
        $data['firstname'] = session()->get('firstname');

        return view('dashboard/main', $data);

        // more add to this
        // REMINDER: CHECK THE DATABASE FIRST, CORRECT
        // RELATIONAL STRUCTURE OF DATABASE
        // CHECK IF applicant_num is fk in user or vice versa idk kajsdflkijas
    }
}