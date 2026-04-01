<?php

namespace App\Controllers\Admin;

class DashboardController extends BaseAdminController
{
    public function index()
    {
        $this->pageTitle = 'Admin Dashboard';

        $data = [
            'sidebarActive' => 'dashboard',
            'stats' => [
                'total_courses' => model('CourseModel')->countAll(),
                'pending_applications' => model('ApplicationModel')
                    ->where('status', 'pending')
                    ->countAllResults(),
                'approved_applications' => model('ApplicationModel')
                    ->where('status', 'approved')
                    ->countAllResults(),
            ],
        ];

        return $this->renderAdmin('admin/dashboard', $data);
    }
}
