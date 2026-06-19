<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddGatewayFieldsToPayment extends Migration
{
    public function up()
    {
        $this->forge->addColumn('payment', [
            'payment_source' => [
                'type'       => 'ENUM',
                'constraint' => ['COUNTER', 'ONLINE'],
                'default'    => 'COUNTER',
                'null'       => false,
                'after'      => 'payment_method_account_id',
            ],
            'external_reference' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'reference_num',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('payment', [
            'payment_source',
            'external_reference',
        ]);
    }
}
