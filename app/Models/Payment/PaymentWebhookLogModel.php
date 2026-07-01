<?php

namespace App\Models\Payment;

use CodeIgniter\Model;

class PaymentWebhookLogModel extends Model
{
    protected $table = 'payment_webhook_logs';

    protected $primaryKey = 'webhook_log_id';

    protected $allowedFields = [
        'gateway',
        'reference_number',
        'payload',
        'status',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $returnType = 'array';
}