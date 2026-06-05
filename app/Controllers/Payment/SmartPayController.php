<?php

namespace App\Controllers\Payment;

use App\Controllers\BaseController;
use App\Models\Payment\PaymentFeeTypeModel;
use App\Models\Payment\PaymentModel;
use App\Services\PaymentService;

class SmartPayController extends BaseController
{
    protected $paymentService;

    public function __construct()
    {
        $this->paymentService = new PaymentService();
    }

    public function getAdmissionFeePayment()
    {
        return $this->handlePayment(
            PaymentFeeTypeModel::CODE_ADMISSION
        );
    }

    public function getReservationFeePayment()
    {
        return $this->handlePayment(
            PaymentFeeTypeModel::CODE_RESERVATION
        );
    }

    public function webhook()
    {
        $rawBody = $this->request->getBody();

        /*
        |--------------------------------------------------------------------------
        | Verify the request is genuinely from SmartPay
        | SmartPay signs the raw body with HMAC-SHA256 using the secret key
        | and sends it as: X-SmartPay-Signature: sha256=<hex>
        |--------------------------------------------------------------------------
        */

        $signature = $this->request->getHeaderLine('X-SmartPay-Signature');
        $expected  = 'sha256=' . hash_hmac('sha256', $rawBody, env('SMARTPAY_SECRET_KEY'));

        if (! hash_equals($expected, $signature)) {
            log_message('warning', '[SmartPay] Webhook rejected — invalid signature.');
            return $this->response->setStatusCode(401)->setJSON(['success' => false]);
        }

        /*
        |--------------------------------------------------------------------------
        | Parse and validate required fields
        |--------------------------------------------------------------------------
        */

        $payload = json_decode($rawBody, true);

        if (! is_array($payload)) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false]);
        }

        log_message('info', '[SmartPay] Webhook received: ' . $rawBody);

        $serviceReference = $payload['service_reference'] ?? null;
        $status           = strtoupper($payload['status'] ?? '');

        if (! $serviceReference || ! $status) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false]);
        }

        /*
        |--------------------------------------------------------------------------
        | Look up the payment record
        |--------------------------------------------------------------------------
        */

        $paymentModel = new PaymentModel();
        $payment = $paymentModel->where('reference_num', $serviceReference)->first();

        if (! $payment) {
            log_message('error', '[SmartPay] Webhook: no payment found for reference ' . $serviceReference);
            return $this->response->setStatusCode(404)->setJSON(['success' => false]);
        }

        /*
        |--------------------------------------------------------------------------
        | Idempotency — ignore if already in a terminal state
        |--------------------------------------------------------------------------
        */

        if (in_array($payment['payment_status'], [PaymentModel::STATUS_PAID, PaymentModel::STATUS_REJECTED], true)) {
            return $this->response->setJSON(['success' => true]);
        }

        /*
        |--------------------------------------------------------------------------
        | Amount integrity check — block partial-payment fraud
        |--------------------------------------------------------------------------
        */

        if (isset($payload['amount'])) {
            $webhookAmount  = round((float) $payload['amount'], 2);
            $expectedAmount = round((float) $payment['amount_paid'], 2);

            if ($webhookAmount !== $expectedAmount) {
                log_message('critical',
                    '[SmartPay] Amount mismatch on webhook! '
                    . 'Expected: ' . $expectedAmount
                    . ', Got: ' . $webhookAmount
                    . ', Ref: ' . $serviceReference
                );
                return $this->response->setStatusCode(422)->setJSON(['success' => false]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Map SmartPay status to internal status and update
        |--------------------------------------------------------------------------
        */

        $newStatus = match ($status) {
            'PAID'                           => PaymentModel::STATUS_PAID,
            'FAILED', 'CANCELLED', 'EXPIRED' => PaymentModel::STATUS_REJECTED,
            default                          => null,
        };

        if ($newStatus !== null) {
            $paymentModel->updateStatus((int) $payment['payment_id'], $newStatus);
            log_message('info', '[SmartPay] Payment ' . $serviceReference . ' updated to ' . $newStatus);
        }

        return $this->response->setJSON(['success' => true]);
    }

    private function handlePayment(string $feeTypeCode)
    {
        try {

            /*
            |--------------------------------------------------------------------------
            | Get JSON payload
            |--------------------------------------------------------------------------
            */

            $payload = $this->request->getJSON(true);

            /*
            |--------------------------------------------------------------------------
            | Validate required fields
            |--------------------------------------------------------------------------
            */

            if (empty($payload['payment_method'])) {

                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Payment method is required.'
                ])->setStatusCode(400);
            }

            /*
            |--------------------------------------------------------------------------
            | Get applicant from session
            |--------------------------------------------------------------------------
            */

            $applicantId = session()->get('applicant_id');

            if (!$applicantId) {

                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Applicant session not found.'
                ])->setStatusCode(401);
            }

            /*
            |--------------------------------------------------------------------------
            | Call payment service
            |--------------------------------------------------------------------------
            */

            $result = $this->paymentService->getPayment(
                $applicantId,
                $payload,
                $feeTypeCode
            );

            return $this->response->setJSON([
                'success' => true,
                'data' => $result
            ]);

        } catch (\Throwable $e) {

            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }
}