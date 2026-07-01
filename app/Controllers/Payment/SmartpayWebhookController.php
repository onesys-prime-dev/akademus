<?php

namespace App\Controllers\Payment;

use App\Controllers\BaseController;
use App\Models\Payment\PaymentModel;
use App\Models\Payment\PaymentGatewayTransactionModel;
use App\Models\Payment\PaymentWebhookLogModel;

class SmartpayWebhookController extends BaseController
{
    public function index()
    {
        $rawBody   = $this->request->getBody();
        $signature = $this->request->getHeaderLine('X-SmartPay-Signature');
        $expected  = 'sha256=' . hash_hmac('sha256', $rawBody, env('SMARTPAY_SECRET_KEY'));

        if (! hash_equals($expected, $signature)) {
            log_message('warning', '[SmartPay] Webhook rejected — invalid signature.');
            return $this->response->setStatusCode(401)->setJSON(['success' => false]);
        }

        $payload = $this->request->getJSON(true);

        if (! is_array($payload)) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'success' => false,
                    'message' => 'Invalid webhook payload.',
                ]);
        }

        $referenceNumber =
            $payload['reference_number']
            ?? $payload['data']['reference_number']
            ?? null;

        $webhookLogModel = new PaymentWebhookLogModel();

        $webhookLogId = $webhookLogModel->insert([
            'gateway'          => 'SMARTPAY',
            'reference_number' => $referenceNumber,
            'payload'          => json_encode($payload),
            'status'           => 'RECEIVED',
        ]);

        $gatewayTransactionModel = new PaymentGatewayTransactionModel();

        $transaction = $gatewayTransactionModel
            ->where('gateway_name', 'SMARTPAY')
            ->where('gateway_reference', $referenceNumber)
            ->first();

        if (! $transaction) {
            if ($webhookLogId) {
                $webhookLogModel->update($webhookLogId, [
                    'status' => 'TRANSACTION_NOT_FOUND',
                ]);
            }

            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'success' => false,
                    'message' => 'Transaction not found.',
                ]);
        }

        $gatewayStatus = strtoupper(
            $payload['payment_status']
            ?? $payload['status']
            ?? $payload['transaction_status']
            ?? $payload['data']['payment_status']
            ?? $payload['data']['status']
            ?? 'PENDING'
        );

        $paymentStatus = match ($gatewayStatus) {
            'SUCCESS',
            'PAID',
            'COMPLETED' => PaymentModel::STATUS_PAID,

            'FAILED',
            'EXPIRED',
            'CANCELLED' => PaymentModel::STATUS_REJECTED,

            default => PaymentModel::STATUS_PROCESSING,
        };

        if ($paymentStatus === PaymentModel::STATUS_PROCESSING) {
            log_message('warning', 'Unknown SmartPay payment status: ' . $gatewayStatus);
        }

        $paymentModel = new PaymentModel();

        $currentPayment = $paymentModel->find(
            $transaction['payment_id']
        );

        if (
            $currentPayment &&
            $currentPayment['payment_status'] === PaymentModel::STATUS_PAID
        ) {
            if ($webhookLogId) {
                $webhookLogModel->update($webhookLogId, [
                    'status' => 'ALREADY_PAID',
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Payment already marked as paid.',
            ]);
        }

        $db = db_connect();
        $db->transStart();

        $now = date('Y-m-d H:i:s');

        $paidAt =
            $payload['paid_at']
            ?? $payload['data']['paid_at']
            ?? null;

        $gatewayUpdate = [
            'gateway_status'      => $gatewayStatus,
            'settlement_status'   =>
                $payload['settlement_status']
                ?? $payload['data']['settlement_status']
                ?? 'PENDING',
            'customer_fee'        =>
                $payload['customer_fee']
                ?? $payload['data']['customer_fee']
                ?? null,
            'gateway_fee'         =>
                $payload['channel_fee']
                ?? $payload['gateway_fee']
                ?? $payload['data']['channel_fee']
                ?? $payload['data']['gateway_fee']
                ?? null,
            'webhook_received_at' => $now,
            'webhook_payload'     => json_encode($payload),
        ];

        if (
            $paymentStatus === PaymentModel::STATUS_PAID &&
            empty($transaction['paid_at'])
        ) {
            $gatewayUpdate['paid_at'] = $paidAt ?: $now;
        }

        $gatewayTransactionModel->update(
            $transaction['gateway_transaction_id'],
            $gatewayUpdate
        );

        $paymentData = [
            'payment_status' => $paymentStatus,
        ];

        if ($paymentStatus === PaymentModel::STATUS_PAID) {
            $paymentData['payment_date'] = $paidAt ?: $now;
        }

        $paymentModel->update(
            $transaction['payment_id'],
            $paymentData
        );

        if ($webhookLogId) {
            $webhookLogModel->update($webhookLogId, [
                'status' => 'PROCESSED',
            ]);
        }

        $db->transComplete();

        if (! $db->transStatus()) {
            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => 'Could not process webhook.',
                ]);
        }

        return $this->response->setJSON([
            'success' => true,
        ]);
    }
}