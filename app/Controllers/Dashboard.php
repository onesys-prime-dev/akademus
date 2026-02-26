<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        /* UNCOMMENT THIS TO ENABLE AUTHENTICATION CHECK */
        /* 
        if (!session()->get('logged_in')) {
            return redirect()->to('/sign_in');
        }
        */

        $data['title'] = 'Dashboard';
        $data['page_content'] = 'dashboard/menu/dashboard/dashboard';

        return view('dashboard/main', $data);
    }
}