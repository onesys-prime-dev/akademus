<?php

namespace App\Controllers\Payment;

use App\Controllers\BaseController;
use App\Models\Payment\PaymentModel;
use App\Models\Payment\PaymentFeeTypeModel;
use App\Models\Payment\PaymentMethodModel;
use App\Models\Payment\PaymentMethodAccountModel;
use App\Services\NotificationService;

class FastTrackFeeController extends BaseController
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

        if (! session()->get('logged_in')) {
            return redirect()->to('/sign-in');
        }

        $applicantId = (int) session()->get('applicant_id');

        $fee = $this->feeModel->getByCode(PaymentFeeTypeModel::CODE_FAST_TRACK);

        $rawMethods     = $this->methodModel->getActiveWithAccounts();
        $paymentMethods = $this->buildMethodsMap($rawMethods);

        $existingPayment = $this->paymentModel->getByApplicantAndFeeType(
            $applicantId,
            PaymentFeeTypeModel::CODE_FAST_TRACK
        );

        $data = [
            'title'            => 'Fast Track Fee',
            'fee'              => $fee,
            'payment_methods'  => $paymentMethods,
            'existing_payment' => $existingPayment,
            'amount'           => $fee['amount'] ?? 0,
        ];

        return $this->renderApplicant(
            'applicant/payments/fast-track-fee',
            $data
        );
    }

    // POST /fast-track-fee/submit
    public function store()
    {
        if (! $this->request->isAJAX()) {
            return redirect()->to('/fast-track-fee');
        }

        if (! session()->get('logged_in')) {
            return $this->jsonError('You must be signed in to submit a payment.', 401);
        }

        $applicantId = (int) session()->get('applicant_id');

        if ($this->paymentModel->hasActivePayment($applicantId, PaymentFeeTypeModel::CODE_FAST_TRACK)) {
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

        $referenceNum = trim($this->request->getPost('reference_num'));

        if ($this->paymentModel->findByReferenceNum($referenceNum)) {
            return $this->jsonError(
                'This reference number has already been submitted. Please double-check your transaction details.'
            );
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

        $rawAccountId           = $this->request->getPost('payment_method_account_id');
        $paymentMethodAccountId = ($rawAccountId !== '' && $rawAccountId !== null)
            ? (int) $rawAccountId
            : null;

        $fee = $this->feeModel->getByCode(PaymentFeeTypeModel::CODE_FAST_TRACK);
        $feeTypeId = $fee['payment_fee_type_id'] ?? null;

        if (! $fee || ! $feeTypeId) {
            @unlink($uploadDir . $newFileName);

            return $this->jsonError('Fast Track fee is not available at the moment. Please try again later.');
        }

        $inserted = $this->paymentModel->insert([
            'applicant_id'              => $applicantId,
            'payment_fee_type_id'       => $feeTypeId,
            'payment_method_id'         => $paymentMethodId,
            'payment_method_account_id' => $paymentMethodAccountId,
            'payment_source'            => 'COUNTER',
            'reference_num'             => $referenceNum,
            'proof_of_payment'          => $proofPath,
            'amount_paid'               => $fee['amount'],
            'payment_status'            => PaymentModel::STATUS_PENDING,
        ]);

        if (! $inserted) {
            @unlink($uploadDir . $newFileName);
            return $this->jsonError('Could not record your payment. Please try again.');
        }

        $this->notificationService->trigger('fast_track_fee.pending', $applicantId, ['reference_num' => $referenceNum]);

        return $this->jsonSuccess(
            'Your payment has been submitted and is awaiting verification by the finance office.'
        );
    }

    public function proofFile(int $paymentId)
    {
        $applicantId = (int) session()->get('applicant_id');

        $payment = $this->paymentModel->find($paymentId);

        if (
            ! $payment ||
            (int) $payment['applicant_id'] !== $applicantId ||
            empty($payment['proof_of_payment'])
        ) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return $this->serveProofFile($payment['proof_of_payment']);
    }

    private function serveProofFile(string $proofPath)
    {
        $filePath = WRITEPATH . $proofPath;

        if (! file_exists($filePath)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $mime = mime_content_type($filePath);

        return $this->response
            ->setHeader('Content-Type', $mime)
            ->setHeader(
                'Content-Disposition',
                'inline; filename="' . basename($filePath) . '"'
            )
            ->setBody(file_get_contents($filePath));
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
