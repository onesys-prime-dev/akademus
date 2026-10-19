<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePaymentGatewayTransactions extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('payment_gateway_transactions')) {
            return;
        }

        $this->forge->addField([
            'gateway_transaction_id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'payment_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'gateway_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'gateway_reference' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'service_reference' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'checkout_url' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'checkout_expires_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'gateway_status' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'settlement_status' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'customer_fee' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
            ],
            'gateway_fee' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
            ],
            'paid_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'webhook_received_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'request_payload' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'response_payload' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'webhook_payload' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('gateway_transaction_id', true);
        $this->forge->addKey('payment_id');
        $this->forge->addKey('gateway_name');
        $this->forge->addKey('gateway_reference');
        $this->forge->addKey('service_reference');

        $this->forge->addForeignKey(
            'payment_id',
            'payment',
            'payment_id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->createTable('payment_gateway_transactions', true);
    }

    public function down()
    {
        $this->forge->dropTable('payment_gateway_transactions', true);
    }
}