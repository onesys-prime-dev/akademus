<?php

namespace App\Controllers\Admin;

use App\Models\ThemeModel;
use App\Models\Payment\PaymentModel;
use App\Models\Payment\PaymentFeeTypeModel;

class PaymentFeesController extends BaseAdminController
{
    protected $paymentModel;
    protected $feeModel;

    public function __construct()
    {
        $this->paymentModel = new PaymentModel();
        $this->feeModel     = new PaymentFeeTypeModel();
    }

    // payment list
    public function index()
    {
        $payments = $this->paymentModel
            ->select('
                payment.*, 
                personal_informations.firstname, 
                personal_informations.lastname, 
                payment_fee_type.name as fee_name,
                payment_fee_type.code as fee_code
            ')
            ->join('applicants', 'applicants.applicant_id = payment.applicant_id')
            ->join('applications', 'applications.applicant_id = applicants.applicant_id')
            ->join('personal_informations', 'personal_informations.application_id = applications.application_id')
            ->join('payment_fee_type', 'payment_fee_type.payment_fee_type_id = payment.payment_fee_type_id')
            // ->wherein('payment_fee_type.name', [
            //         'Admission Fee',
            //         'Reservation Fee',
            // ])
            ->orderBy('payment.created_at', 'DESC')
            ->findAll();

        return $this->renderAdmin('admin/payments/index', [
            'payments' => $payments,
            'sidebarActive' => 'payments'
        ]);
    }

    // fee settings
    public function fees()
    {
        $fees = $this->feeModel->findAll();

        return $this->renderAdmin('admin/payments/fees', [
            'fees' => $fees,
            'sidebarActive' => 'payment-fees'
        ]);
    }

    public function updateFee($id)
    {
        $amount = $this->request->getPost('amount');

        $this->feeModel->update($id, [
            'amount' => $amount
        ]);

        return redirect()->back();
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