<?php

namespace App\Controllers\Payment;

use App\Controllers\BaseController;
use App\Models\ThemeModel;
use App\Models\Payment\PaymentModel;
use App\Models\Payment\PaymentFeeTypeModel;
use App\Models\Payment\PaymentMethodModel;
use App\Models\Payment\PaymentMethodAccountModel;
use App\Services\NotificationService;

class ReservationFeeController extends BaseController
{
    protected $paymentModel;
    protected $methodModel;
    protected $accountModel;
    protected $feeModel;
    protected $notificationService;

    public function __construct()
    {
        $this->paymentModel = new PaymentModel();
        $this->methodModel  = new PaymentMethodModel();
        $this->accountModel = new PaymentMethodAccountModel();
        $this->feeModel     = new PaymentFeeTypeModel();
        $this->notificationService = new NotificationService();
    }

    public function index()
    {
        helper('theme');

        $applicantId = (int) session()->get('applicant_id');

        // reservation fee
        $fee = $this->feeModel
            ->where('code', PaymentFeeTypeModel::CODE_RESERVATION)
            ->where('is_active', 1)
            ->first();

        $rawMethods     = $this->methodModel->getActiveWithAccounts();
        $paymentMethods = $this->buildMethodsMap($rawMethods);

        $existingPayment = $this->paymentModel->getByApplicantAndFeeType(
            $applicantId,
            PaymentFeeTypeModel::CODE_RESERVATION
        );

        $data = [
            'title'            => 'Reservation Fee',
            'fee'              => $fee,
            'payment_methods'  => $paymentMethods,
            'existing_payment' => $existingPayment,
        ];

        return $this->renderApplicant(
            'applicant/payments/reservation-fee',
            $data
        );
    }

    public function store()
    {
        if (! $this->request->isAJAX()) {
            return redirect()->to('/reservation-fee');
        }

        $applicantId = (int) session()->get('applicant_id');

        if ($this->paymentModel->hasPaidOrProcessing(
            $applicantId,
            PaymentFeeTypeModel::CODE_RESERVATION
        )) {
            return $this->jsonError(
                'You have already submitted a reservation fee payment.'
            );
        }

        // validation same as admission
        $rules = [
            'payment_method_id' => 'required|is_natural_no_zero',
            'reference_num'     => 'required|min_length[3]|max_length[50]',
            'proof_of_payment'  => 'uploaded[proof_of_payment]|max_size[proof_of_payment,10240]|ext_in[proof_of_payment,jpg,jpeg,png,pdf]',
        ];

        if (! $this->validate($rules)) {
            return $this->jsonError(
                implode(' ', $this->validator->getErrors())
            );
        }

        $referenceNum = trim($this->request->getPost('reference_num'));

        if ($this->paymentModel->findByReferenceNum($referenceNum)) {
            return $this->jsonError('Reference number already used.');
        }

        $file = $this->request->getFile('proof_of_payment');

        if (! $file->isValid() || $file->hasMoved()) {
            return $this->jsonError('File upload failed. Please try again.');
        }

        $uploadDir   = WRITEPATH . 'uploads/payments/';
        $newFileName = $file->getRandomName();

        if (! $file->move($uploadDir, $newFileName)) {
            return $this->jsonError('Could not save the uploaded file. Please try again.');
        }

        $proofPath = 'uploads/payments/' . $newFileName;

        $paymentMethodId = (int) $this->request->getPost('payment_method_id');

        $accountId = $this->request->getPost('payment_method_account_id');
        $accountId = $accountId ? (int) $accountId : null;

        $feeTypeId = $this->feeModel->getIdByCode(
            PaymentFeeTypeModel::CODE_RESERVATION
        );

        $fee = $this->feeModel
            ->where('code', PaymentFeeTypeModel::CODE_RESERVATION)
            ->where('is_active', 1)
            ->first();

        $inserted = $this->paymentModel->insert([
            'applicant_id'              => $applicantId,
            'payment_fee_type_id'       => $feeTypeId,
            'payment_method_id'         => $paymentMethodId,
            'payment_method_account_id' => $accountId,
            'reference_num'             => $referenceNum,
            'proof_of_payment'          => $proofPath,
            'amount_paid'               => $fee['amount'] ?? 0,
            'payment_status'            => PaymentModel::STATUS_PENDING,
        ]);

        if (! $inserted) {
            @unlink($uploadDir . $newFileName);
            return $this->jsonError('Could not record your payment. Please try again.');
        }

        $this->notificationService->trigger('reservation_fee.pending', $applicantId, ['reference_num' => $referenceNum]);

        return $this->jsonSuccess(
            'Reservation fee submitted successfully.'
        );
    }

    private function buildMethodsMap(array $rows): array
    {
        $map = [];

        foreach ($rows as $row) {
            if (isset($map[$row['code']])) continue;

            $map[$row['code']] = [
                'payment_method_id' => (int) $row['payment_method_id'],
                'code'              => $row['code'],
                'name'              => $row['name'],
                'payment_method_account_id' => $row['payment_method_account_id'],
                'account_number'    => $row['account_number'],
                'account_name'      => $row['account_name'],
                'label'             => 'Send payment via ' . $row['name'],
            ];
        }

        return array_values($map);
    }

    private function jsonSuccess($msg)
    {
        return $this->response->setJSON(['success'=>true,'message'=>$msg]);
    }

    private function jsonError($msg)
    {
        return $this->response->setJSON(['success'=>false,'message'=>$msg]);
    }
}