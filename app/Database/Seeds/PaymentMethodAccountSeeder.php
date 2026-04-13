<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PaymentMethodAccountSeeder extends Seeder
{
    public function run()
    {
        // Map method codes to placeholder account details.
        // Update these with the school's real account numbers.
        $accounts = [
            'GCASH' => ['account_number' => '09XX-XXX-XXXX', 'account_name' => 'School Cashier'],
            'MAYA'  => ['account_number' => '09XX-XXX-XXXX', 'account_name' => 'School Cashier'],
            'BDO'   => ['account_number' => '0000-0000-0000', 'account_name' => 'School Name'],
            'BPI'   => ['account_number' => '0000-0000-0000', 'account_name' => 'School Name'],
        ];

        foreach ($accounts as $code => $info) {
            $method = $this->db->table('payment_method')
                ->where('code', $code)
                ->where('is_active', 1)
                ->get()
                ->getRowArray();

            if (! $method) {
                continue;
            }

            $methodId = $method['payment_method_id'];

            $exists = $this->db->table('payment_method_account')
                ->where('payment_method_id', $methodId)
                ->countAllResults();

            if (! $exists) {
                $this->db->table('payment_method_account')->insert([
                    'payment_method_id' => $methodId,
                    'account_number'    => $info['account_number'],
                    'account_name'      => $info['account_name'],
                    'is_active'         => 1,
                    'created_at'        => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }
}
