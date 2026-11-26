<?php

namespace App\Database\Seeds;

use App\Database\Seeds\Concerns\InsertsMissingRows;
use CodeIgniter\Database\Seeder;

/**
 * Counter payment methods applicants can choose. Run before PaymentMethodAccountSeeder,
 * which attaches the (placeholder) account details to these codes.
 */
class PaymentMethodSeeder extends Seeder
{
    use InsertsMissingRows;

    private const METHODS = [
        'GCASH' => 'GCash',
        'MAYA'  => 'Maya',
        'BDO'   => 'BDO',
        'BPI'   => 'BPI',
    ];

    public function run(): void
    {
        foreach (self::METHODS as $code => $name) {
            $this->insertIfMissing('payment_method', ['code' => $code], ['name' => $name, 'is_active' => 1], 'payment_method_id');
        }
    }
}
