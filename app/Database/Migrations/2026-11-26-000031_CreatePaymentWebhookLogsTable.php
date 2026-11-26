<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePaymentWebhookLogsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'webhook_log_id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true, 'null' => false],
            'gateway' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => false],
            'reference_number' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'payload' => ['type' => 'JSON', 'null' => true],
            'status' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => false, 'default' => 'RECEIVED'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('webhook_log_id');
        $this->forge->addKey('gateway', false, false, 'gateway');
        $this->forge->addKey('reference_number', false, false, 'reference_number');
        $this->forge->addKey('status', false, false, 'status');
        $this->forge->createTable('payment_webhook_logs', false, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('payment_webhook_logs', true);
    }
}
