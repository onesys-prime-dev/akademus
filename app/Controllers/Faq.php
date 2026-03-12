<?php

namespace App\Controllers;

use App\Models\ThemeModel;

class Faq extends BaseController
{
    public function index()
    {
        helper('theme');
        
        $themeModel = new ThemeModel();

        return view('dashboard/main', [
            'title'        => 'FAQ & Chat Support',
            'page_content' => 'dashboard/menu/faq',
            'theme'        => $themeModel->getAllSettings()
        ]);
    }
}