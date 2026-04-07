<?php

namespace App\Models\Payment;

use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $table = 'payment';
    protected $primaryKey = 'payment_id';

    protected $allowedFields = [
        'applicant_id',
        'payment_fee_type_id',
        'payment_method_id',
        'payment_method_account_id',
        'reference_num',
        'transaction_id',
        'proof_of_payment',
        'amount',
        'payment_status',
        'payment_date',
        'remarks'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';


}