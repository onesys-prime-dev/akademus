<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseAdminController;
use App\Models\ThemeModel;
use App\Models\Payment\PaymentModel;
use App\Models\Payment\PaymentFeeTypeModel;
use App\Models\Payment\PaymentMethodModel;
use App\Models\Payment\PaymentMethodAccountModel;

class PaymentFeesController extends BaseAdminController
{
    protected $paymentModel;

    public function __construct()
    {
        $this->paymentModel = new PaymentModel();
    }

    public function index()
    {
        $payments = $this->paymentModel
            ->select('payment.*, 
                      payment_fee_type.name AS fee_name,
                      payment_method.name AS method_name,
                      applicants.applicant_number')
            ->join('payment_fee_type', 'payment_fee_type.payment_fee_type_id = payment.payment_fee_type_id')
            ->join('payment_method', 'payment_method.payment_method_id = payment.payment_method_id')
            ->join('applicants', 'applicants.applicant_id = payment.applicant_id')
            ->orderBy('payment.created_at', 'DESC')
            ->findAll();

        return view('admin/payment-fees/index', [
            'payments' => $payments
        ]);
    }

    public function approve($id)
    {
        $this->paymentModel->updateStatus($id, PaymentModel::STATUS_PAID);
        return redirect()->back();
    }

    public function reject($id)
    {
        $this->paymentModel->updateStatus($id, PaymentModel::STATUS_REJECTED);
        return redirect()->back();
    }
}