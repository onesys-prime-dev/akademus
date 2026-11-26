<?php

namespace App\Database\Seeds;

use App\Database\Seeds\Concerns\InsertsMissingRows;
use CodeIgniter\Database\Seeder;

/**
 * Fee types referenced by code in the payment controllers (ADMISSION_FEE, RESERVATION_FEE, FAST_TRACK_FEE).
 * Amounts are defaults only; an existing row (and an amount changed in Fee Settings) is never overwritten.
 */
class PaymentFeeTypeSeeder extends Seeder
{
    use InsertsMissingRows;

    private const FEES = [
        'ADMISSION_FEE' => [
            'name'        => 'Admission Processing Fee',
            'description' => 'One-time non-refundable fee required to submit the admission application.',
            'amount'      => 1500.00,
        ],
        'RESERVATION_FEE' => [
            'name'        => 'Reservation Fee',
            'description' => 'One-time non-refundable fee required after acceptance to reserve a slot.',
            'amount'      => 1000.00,
        ],
        'FAST_TRACK_FEE' => [
            'name'        => 'Fast Track Fee',
            'description' => 'Additional fee for applicants who apply via the Fast Track option.',
            'amount'      => 1000.00,
        ],
    ];

    public function run(): void
    {
        foreach (self::FEES as $code => $fee) {
            $this->insertIfMissing('payment_fee_type', ['code' => $code], $fee + ['is_active' => 1], 'payment_fee_type_id');
        }
    }
}
