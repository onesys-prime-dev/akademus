<?php

namespace App\Models\Payment;

use CodeIgniter\Model;

class PaymentFeeTypeModel extends Model
{
    protected $table      = 'payment_fee_type';
    protected $primaryKey = 'payment_fee_type_id';

    protected $allowedFields = [
        'code',
        'name',
        'description',
        'amount',
        'is_active',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    const CODE_ADMISSION   = 'ADMISSION_FEE';
    const CODE_RESERVATION = 'RESERVATION_FEE';
    const CODE_FAST_TRACK  = 'FAST_TRACK_FEE';

    public function getIdByCode(string $code): int
    {
        $row = $this->where('code', strtoupper($code))
                    ->where('is_active', 1)
                    ->first();

        if (! $row) {
            throw new \RuntimeException("Payment fee type '{$code}' not found or inactive.");
        }

        return (int) $row['payment_fee_type_id'];
    }
}