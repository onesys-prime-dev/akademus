<?php

namespace App\Models\Payment;

use CodeIgniter\Model;

class PaymentMethodAccountModel extends Model
{
    protected $table      = 'payment_method_account';
    protected $primaryKey = 'payment_method_account_id';

    protected $allowedFields = [
        'payment_method_id',
        'account_number',
        'account_name',
        'is_active',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';   // table has no updated_at column
}