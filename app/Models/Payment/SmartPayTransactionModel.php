<?php

namespace App\Models\Payment;

use CodeIgniter\Model;

class SmartPayTransactionModel extends Model
{
    protected $table = 'smartpay_transactions';

    protected $primaryKey =
        'smartpay_transaction_id';

    protected $allowedFields = [
        'payment_id',
        'reference_number',
        'service_reference',
        'merchant',
        'merchant_service',
        'payment_channel',
        'amount',
        'customer_fee',
        'channel_fee',
        'customer_email',
        'customer_mobile',
        'customer_name',
        'payment_status',
        'settlement_status',
        'checkout_url',
        'checkout_expires_at',
        'paid_at',
        'webhook_received_at',
        'gateway_response',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;

    protected $createdField = 'created_at';

    protected $updatedField = 'updated_at';

    protected $returnType = 'array';
}
