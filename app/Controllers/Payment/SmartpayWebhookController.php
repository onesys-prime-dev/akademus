<?php

namespace App\Controllers\Payment;

use App\Controllers\BaseController;

use App\Models\Payment\PaymentModel;
use App\Models\Payment\SmartpayTransactionModel;
use App\Models\Payment\PaymentWebhookLogModel;

class SmartpayWebhookController extends BaseController
{
    public function index()
    {
        $payload = $this->request->getJSON(true);

        /*
        |--------------------------------------------------------------------------
        | Save webhook log
        |--------------------------------------------------------------------------
        */

        $webhookLogModel = new PaymentWebhookLogModel();

        $webhookLogModel->insert([

            'gateway' => 'SMARTPAY',

            'reference_number' =>
                $payload['reference_number']
                ?? null,

            'payload' =>
                json_encode($payload),

            'status' => 'RECEIVED',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Find transaction
        |--------------------------------------------------------------------------
        */

        $smartpayTransactionModel =
            new SmartpayTransactionModel();

        $transaction =
            $smartpayTransactionModel
                ->where(
                    'reference_number',
                    $payload['reference_number'] ?? ''
                )
                ->first();

        if (!$transaction) {

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Transaction not found.'
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Update SmartPay transaction
        |--------------------------------------------------------------------------
        */

        $smartpayTransactionModel
            ->update(
                $transaction['smartpay_transaction_id'],
                [

                    'payment_status' =>
                        $payload['payment_status']
                        ?? 'PENDING',

                    'settlement_status' =>
                        $payload['settlement_status']
                        ?? 'PENDING',

                    'webhook_received_at' =>
                        date('Y-m-d H:i:s'),

                    'updated_at' =>
                        date('Y-m-d H:i:s'),
                ]
            );

        /*
        |--------------------------------------------------------------------------
        | Sync payment table
        |--------------------------------------------------------------------------
        */

        $paymentStatus = match (
            strtoupper(
                $payload['payment_status'] ?? ''
            )
        ) {

            'SUCCESS' => 'PAID',

            'FAILED',
            'EXPIRED' => 'REJECTED',

            default => 'PROCESSING',
        };

        $paymentModel = new PaymentModel();

        $paymentModel->update(
            $transaction['payment_id'],
            [

                'payment_status' =>
                    $paymentStatus,

                'payment_date' =>
                    date('Y-m-d H:i:s'),

                'updated_at' =>
                    date('Y-m-d H:i:s'),
            ]
        );

        return $this->response->setJSON([
            'success' => true
        ]);
    }
}