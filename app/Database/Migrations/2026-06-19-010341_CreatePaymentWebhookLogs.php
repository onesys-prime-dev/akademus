<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePaymentWebhookLogs extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'webhook_log_id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'gateway' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'reference_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'payload' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'RECEIVED',
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

        $this->forge->addKey('webhook_log_id', true);
        $this->forge->addKey('gateway');
        $this->forge->addKey('reference_number');
        $this->forge->addKey('status');

        $this->forge->createTable('payment_webhook_logs');
    }

    public function down()
    {
        $this->forge->dropTable('payment_webhook_logs', true);
    }
}
