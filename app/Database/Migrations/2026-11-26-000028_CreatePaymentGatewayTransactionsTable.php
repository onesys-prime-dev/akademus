<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePaymentGatewayTransactionsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'gateway_transaction_id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true, 'null' => false],
            'payment_id'             => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'null' => false],
            'gateway_name'           => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => false],
            'gateway_reference'      => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'service_reference'      => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'checkout_url'           => ['type' => 'TEXT', 'null' => true],
            'checkout_expires_at'    => ['type' => 'DATETIME', 'null' => true],
            'gateway_status'         => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'settlement_status'      => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'customer_fee'           => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true],
            'gateway_fee'            => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true],
            'paid_at'                => ['type' => 'DATETIME', 'null' => true],
            'webhook_received_at'    => ['type' => 'DATETIME', 'null' => true],
            'request_payload'        => ['type' => 'JSON', 'null' => true],
            'response_payload'       => ['type' => 'JSON', 'null' => true],
            'webhook_payload'        => ['type' => 'JSON', 'null' => true],
            'created_at'             => ['type' => 'DATETIME', 'null' => true],
            'updated_at'             => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('gateway_transaction_id');
        $this->forge->addKey('payment_id', false, false, 'payment_id');
        $this->forge->addKey('gateway_name', false, false, 'gateway_name');
        $this->forge->addKey('gateway_reference', false, false, 'gateway_reference');
        $this->forge->addKey('service_reference', false, false, 'service_reference');
        $this->forge->addForeignKey('payment_id', 'payment', 'payment_id', 'CASCADE', 'CASCADE', 'payment_gateway_transactions_payment_id_foreign');
        $this->forge->createTable('payment_gateway_transactions', false, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('payment_gateway_transactions', true);
    }
}
