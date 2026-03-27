<?php

namespace App\Models\Payment;

use CodeIgniter\Model;

class PaymentFeeTypeModel extends Model
{
    protected $table = 'payment_fee_type';
    protected $primaryKey = 'payment_fee_type_id';

    protected $allowedFields = [
        'code',
        'name',
        'description',
        'is_active'
    ];
}