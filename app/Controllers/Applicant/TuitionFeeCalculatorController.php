<?php

namespace App\Controllers\Applicant;

use App\Controllers\BaseController;

use App\Models\ThemeModel;

class TuitionFeeCalculatorController extends BaseController
{
    public function tuitionCalculator()
    {
        $themeModel = new ThemeModel();

        if ($themeModel->getSetting('tuition_calculator_enabled', '1') !== '1') {
            return redirect()->to(base_url('/dashboard'));
        }

        $data['theme']        = $themeModel->getAllSettings();
        $data['title']        = 'Tuition Fee Calculator';
        $data['active_menu']  = 'tuition_calculator';
        $data['page_content'] = 'applicant/tuition-fee-calculator/tuition-fee-calculator';
        return view('applicant/main', $data);
    }
}