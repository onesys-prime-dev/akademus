<?php

namespace App\Controllers\Payment;

use App\Controllers\BaseController;

use App\Models\ThemeModel;
use App\Models\Payment\PaymentModel;
use App\Models\Payment\PaymentFeeTypeModel;
use App\Models\Payment\PaymentMethodModel;
use App\Models\Payment\PaymentMethodAccountModel;

class AdmissionProcessingFeeController extends BaseController
{
    protected $paymentModel;
    protected $methodModel;
    protected $accountModel;
    protected $feeModel;

    public function __construct()
    {    
        $this->paymentModel = new PaymentModel();
        $this->methodModel = new PaymentMethodModel();
        $this->accountModel = new PaymentMethodAccountModel();
        $this->feeModel = new PaymentFeeTypeModel();
    }

    // Show payment form
    public function create()
    {
        $data['methods'] = $this->methodModel
            ->where('is_active', 1)
            ->findAll();

        $data['fees'] = $this->feeModel
            ->where('is_active', 1)
            ->findAll();

        return view('payment/create', $data);
    }

    // Save payment
    public function store()
    {
        $file = $this->request->getFile('proof_of_payment');

        $fileName = null;
        if ($file && $file->isValid()) {
            $fileName = $file->getRandomName();
            $file->move('uploads/payments/', $fileName);
        }

        $this->paymentModel->save([
            'applicant_id' => session()->get('applicant_id'),
            'payment_fee_type_id' => $this->request->getPost('fee_type'),
            'payment_method_id' => $this->request->getPost('payment_method'),
            'payment_method_account_id' => $this->request->getPost('account'),
            'reference_num' => $this->request->getPost('reference_num'),
            'amount' => $this->request->getPost('amount'),
            'proof_of_payment' => $fileName,
            'payment_status' => 'PENDING',
            'payment_date' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/payment/success');
    }

    // APPROVAL by admin 
    public function approve($id)
    {
        $this->paymentModel->update($id, [
            'payment_status' => 'PAID'
        ]);

        return redirect()->back();
    }

    // REJECTION by admin
    public function reject($id)
    {
        $this->paymentModel->update($id, [
            'payment_status' => 'REJECTED'
        ]);

        return redirect()->back();
    }

    // public function index()
    // {
    //     $data['payments'] = $this->paymentModel
    //         ->select('payment.*, payment_method.name as method, payment_fee_type.name as fee')
    //         ->join('payment_method', 'payment_method.payment_method_id = payment.payment_method_id')
    //         ->join('payment_fee_type', 'payment_fee_type.payment_fee_type_id = payment.payment_fee_type_id')
    //         ->findAll();

    //     return view('payment/index', $data);
    // }

    public function admissionProcessingFee()
    {
        helper('theme');

        $themeModel = new ThemeModel();
        
        return $this->renderApplicant('applicant/payments/admission-processing-fee', ['title' => 'Admission Processing Fee']);
    }
}