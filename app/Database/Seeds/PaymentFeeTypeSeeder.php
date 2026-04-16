<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PaymentFeeTypeSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'code'        => 'ADMISSION_FEE',
                'name'        => 'Admission Processing Fee',
                'description' => 'One-time non-refundable fee required to submit the admission application.',
                'amount'      => 500.00,
                'is_active'   => 1,
            ],
            [
                'code'        => 'RESERVATION_FEE',
                'name'        => 'Reservation Fee',
                'description' => 'One-time non-refundable fee required after acceptance to reserve a slot.',
                'amount'      => 1000.00,
                'is_active'   => 1,
            ],
        ];

        foreach ($data as $row) {
            $exists = $this->db->table('payment_fee_type')
                ->where('code', $row['code'])
                ->countAllResults();

            if (! $exists) {
                $this->db->table('payment_fee_type')->insert($row);
            }
        }
    }
}
