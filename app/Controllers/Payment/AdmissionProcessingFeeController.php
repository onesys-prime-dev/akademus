<?php

namespace App\Controllers\Payment;

use App\Controllers\BaseController;
use App\Models\ThemeModel;
use App\Models\Payment\PaymentModel;
use App\Models\Payment\PaymentFeeTypeModel;
use App\Models\Payment\PaymentMethodModel;

class AdmissionProcessingFeeController extends BaseController
{
    public function admissionProcessingFee()
    {
        helper('theme');

        $themeModel = new ThemeModel();

        return view('applicant/main', [
            'title'        => 'Admission Processing Fee',
            'page_content' => 'applicant/payments/admission-processing-fee',
            'theme'        => $themeModel->getAllSettings(),
            // 'paymentMethods' => getPaymentMethods()
        ]);

        
    }
}