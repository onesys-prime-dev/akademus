<?php

namespace App\Models\Payment;

use CodeIgniter\Model;

class PaymentMethodModel extends Model
{
    protected $table = 'payment_method';
    protected $primaryKey = 'payment_method_id';

    protected $allowedFields = [
        'code',
        'name',
        'description',
        'is_active'
    ];
}