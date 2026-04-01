<?php

namespace App\Controllers\Applicant;

use App\Controllers\BaseController;

use App\Models\ThemeModel;

class FaqController extends BaseController
{
    public function index()
    {
        helper('theme');
        
        $themeModel = new ThemeModel();

        return view('applicant/main', [
            'title'        => 'FAQ & Chat Support',
            'page_content' => 'applicant/faq/faq',
            'theme'        => $themeModel->getAllSettings()
        ]);
    }
}