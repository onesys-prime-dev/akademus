<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddGatewayFieldsToPayment extends Migration
{
    public function up()
    {
        $fields = $this->db->getFieldNames('payment');

        $columns = [];

        if (! in_array('payment_source', $fields, true)) {
            $columns['payment_source'] = [
                'type'       => 'ENUM',
                'constraint' => ['COUNTER', 'ONLINE'],
                'default'    => 'COUNTER',
                'null'       => false,
                'after'      => 'payment_method_account_id',
            ];
        }

        if (! in_array('external_reference', $fields, true)) {
            $columns['external_reference'] = [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'reference_num',
            ];
        }

        if (! empty($columns)) {
            $this->forge->addColumn('payment', $columns);
        }
    }

    public function down()
    {
        $fields = $this->db->getFieldNames('payment');

        if (in_array('external_reference', $fields, true)) {
            $this->forge->dropColumn('payment', 'external_reference');
        }

        if (in_array('payment_source', $fields, true)) {
            $this->forge->dropColumn('payment', 'payment_source');
        }
    }
}
