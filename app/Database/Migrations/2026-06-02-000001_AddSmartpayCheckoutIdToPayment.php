<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSmartpayCheckoutIdToPayment extends Migration
{
    public function up()
    {
        if ($this->db->fieldExists('smartpay_checkout_id', 'payment')) {
            return;
        }

        $this->forge->addColumn('payment', [
            'smartpay_checkout_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
                'after'      => 'reference_num',
            ],
        ]);
    }

    public function down()
    {
        if ($this->db->fieldExists('smartpay_checkout_id', 'payment')) {
            $this->forge->dropColumn('payment', 'smartpay_checkout_id');
        }
    }
}
