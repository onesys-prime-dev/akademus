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

    // const FEE_AMOUNT = 500.00;

    public function __construct()
    {
        $this->paymentModel = new PaymentModel();
        $this->methodModel  = new PaymentMethodModel();
        $this->accountModel = new PaymentMethodAccountModel();
        $this->feeModel     = new PaymentFeeTypeModel();
    }

    public function admissionProcessingFee()
    {
        helper('theme');

        $applicantId = (int) session()->get('applicant_id');

        // Fee type row (used for its ID on form submit)
        $fee = $this->feeModel
            ->where('code', PaymentFeeTypeModel::CODE_ADMISSION)
            ->where('is_active', 1)
            ->first();

        // $this->paymentModel->insert([
        //     'amount_paid' => $fee['amount'] ?? self::FEE_AMOUNT,
        // ]);

        $rawMethods     = $this->methodModel->getActiveWithAccounts();
        $paymentMethods = $this->buildMethodsMap($rawMethods);

        $existingPayment = $this->paymentModel->getByApplicantAndFeeType(
            $applicantId,
            PaymentFeeTypeModel::CODE_ADMISSION
        );

        $data = [
            'title'            => 'Admission Processing Fee',
            'fee'              => $fee,
            'payment_methods'  => $paymentMethods,   
            'existing_payment' => $existingPayment,  // pass existing payment to view for conditional display
            'amount_paid'           => $fee['amount'],
        ];

        return $this->renderApplicant(
            'applicant/payments/admission-processing-fee',
            $data
        );
    }

    // POST /admission-processing-fee/submit
    public function store()
    {
        //   Prevents direct browser form POSTs from bypassing the JS
        //   flow. If someone navigates to the URL directly they get
        //   redirected instead of hitting a half-processed action.
        if (! $this->request->isAJAX()) {
            return redirect()->to('/admission-processing-fee');
        }

        $applicantId = (int) session()->get('applicant_id');

        //   If the applicant already has a PAID or PROCESSING record
        if ($this->paymentModel->hasPaidOrProcessing($applicantId, PaymentFeeTypeModel::CODE_ADMISSION)) {
            return $this->jsonError(
                'You have already submitted a payment that is being processed or has been approved.'
            );
        }

        $rules = [
            'payment_method_id' => [
                'label' => 'Payment Method',
                'rules' => 'required|is_natural_no_zero',
            ],
            'reference_num' => [
                'label'  => 'Transaction Reference',
                'rules'  => 'required|min_length[3]|max_length[50]',
                'errors' => [
                    'required'   => 'Please enter your transaction / reference number.',
                    'min_length' => 'Reference number is too short.',
                ],
            ],
            'proof_of_payment' => [
                'label'  => 'Proof of Payment',
                'rules'  => 'uploaded[proof_of_payment]'
                          . '|max_size[proof_of_payment,10240]'
                          . '|ext_in[proof_of_payment,jpg,jpeg,png,pdf]',
                'errors' => [
                    'uploaded' => 'Please upload your proof of payment.',
                    'max_size' => 'File size must not exceed 10 MB.',
                    'ext_in'   => 'Only JPG, PNG, and PDF files are allowed.',
                ],
            ],
        ];

        if (! $this->validate($rules)) {
            return $this->jsonError(
                implode(' ', $this->validator->getErrors())
            );
        }

        // duplicate reference number check.
        $referenceNum = trim($this->request->getPost('reference_num'));

        if ($this->paymentModel->findByReferenceNum($referenceNum)) {
            return $this->jsonError(
                'This reference number has already been submitted. Please double-check your transaction details.'
            );
        }

        // file upload 
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

        // resolving ids
        $paymentMethodId = (int) $this->request->getPost('payment_method_id');

        $rawAccountId           = $this->request->getPost('payment_method_account_id');
        $paymentMethodAccountId = ($rawAccountId !== '' && $rawAccountId !== null)
            ? (int) $rawAccountId
            : null;

        $feeTypeId = $this->feeModel->getIdByCode(PaymentFeeTypeModel::CODE_ADMISSION);

        $fee = $this->feeModel
            ->where('code', PaymentFeeTypeModel::CODE_ADMISSION)
            ->where('is_active', 1)
            ->first();

        $inserted = $this->paymentModel->insert([
            'applicant_id'              => $applicantId,
            'payment_fee_type_id'       => $feeTypeId,
            'payment_method_id'         => $paymentMethodId,
            'payment_method_account_id' => $paymentMethodAccountId,
            'reference_num'             => $referenceNum,
            'proof_of_payment'          => $proofPath,
            'amount_paid'               => $fee['amount'],
            'payment_status'            => PaymentModel::STATUS_PENDING,
        ]);

        if (! $inserted) {
            @unlink($uploadDir . $newFileName);
            return $this->jsonError('Could not record your payment. Please try again.');
        }

        return $this->jsonSuccess(
            'Your payment has been submitted and is awaiting verification by the finance office.'
        );
    }

    // admin approve
    public function approve($id)
    {
        $this->paymentModel->updateStatus((int) $id, PaymentModel::STATUS_PAID);
        return redirect()->back();
    }

    // admin reject
    public function reject($id)
    {
        $this->paymentModel->updateStatus((int) $id, PaymentModel::STATUS_REJECTED);
        return redirect()->back();
    }

    private function buildMethodsMap(array $rows): array
    {
        $map = [];

        foreach ($rows as $row) {
            $code = $row['code'];

            if (isset($map[$code])) {
                continue; // first active account per method wins
            }

            $map[$code] = [
                'payment_method_id'         => (int) $row['payment_method_id'],
                'code'                      => $code,
                'name'                      => $row['name'],
                'payment_method_account_id' => ! empty($row['payment_method_account_id'])
                                                ? (int) $row['payment_method_account_id']
                                                : null,
                'account_number'            => $row['account_number'] ?? null,
                'account_name'              => $row['account_name']   ?? null,
                'label'                     => 'Send payment via ' . $row['name'],
            ];
        }

        return array_values($map);
    }

    private function jsonSuccess(string $message, array $payload = [])
    {
        return $this->response
            ->setContentType('application/json')
            ->setJSON(array_merge(['success' => true, 'message' => $message], $payload));
    }

    private function jsonError(string $message, int $statusCode = 200)
    {
        return $this->response
            ->setStatusCode($statusCode)
            ->setContentType('application/json')
            ->setJSON(['success' => false, 'message' => $message]);
    }
}