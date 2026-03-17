<?php

namespace App\Controllers\Payment;

use App\Controllers\BaseController;
use App\Models\ThemeModel;

class BillingController extends BaseController
{
    public function billing()
    {
        helper('theme');

        $themeModel = new ThemeModel();

        return view('applicant/main', [
            'title'        => 'Billing',
            'page_content' => 'dashboard/payments/billing',
            'theme'        => $themeModel->getAllSettings()
        ]);
    }
}