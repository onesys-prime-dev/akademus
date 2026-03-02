<?php

namespace App\Controllers;

class ApplicantForm extends BaseController
{
    public function index()
    {
        /* UNCOMMENT THIS TO ENABLE AUTHENTICATION CHECK */
        /* 
        if (!session()->get('logged_in')) {
            return redirect()->to('/sign_in');
        }
        */

        $data['title'] = 'Applicant Form';
        $data['page_content'] = 'dashboard/menu/applicant_form';

        return view('dashboard/main', $data);
    }
}