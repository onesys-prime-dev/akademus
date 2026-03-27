<?php

namespace App\Controllers\Payment;

use App\Controllers\BaseController;
use App\Models\ThemeModel;
use App\Models\Payment\PaymentModel;
use App\Models\Payment\PaymentFeeTypeModel;
use App\Models\Payment\PaymentMethodModel;

class ReservationFeeController extends BaseController
{
    public function reservationFee()
    {
        helper('theme');

        $themeModel = new ThemeModel();

        return view('applicant/main', [
            'title'        => 'Reservation Fee',
            'page_content' => 'applicant/payments/reservation-fee',
            'theme'        => $themeModel->getAllSettings(),
            // 'paymentMethods' => getPaymentMethods()
        ]);

        
    }
}