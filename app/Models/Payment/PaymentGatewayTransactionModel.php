<?php

namespace App\Models\Payment;

use CodeIgniter\Model;

class PaymentGatewayTransactionModel extends Model
{
    protected $table = 'payment_gateway_transactions';

    protected $primaryKey = 'gateway_transaction_id';

    protected $allowedFields = [
        'payment_id',
        'gateway_name',
        'gateway_reference',
        'service_reference',
        'checkout_url',
        'checkout_expires_at',
        'gateway_status',
        'settlement_status',
        'customer_fee',
        'gateway_fee',
        'paid_at',
        'webhook_received_at',
        'request_payload',
        'response_payload',
        'webhook_payload',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $returnType = 'array';
}