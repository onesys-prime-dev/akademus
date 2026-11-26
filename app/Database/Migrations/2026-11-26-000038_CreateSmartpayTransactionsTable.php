<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateSmartpayTransactionsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'smartpay_transaction_id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true, 'null' => false],
            'payment_id'              => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'null' => false],
            'reference_number'        => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
            'service_reference'       => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'merchant'                => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'merchant_service'        => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'payment_channel'         => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'amount'                  => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => false, 'default' => '0.00'],
            'customer_fee'            => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true, 'default' => '0.00'],
            'channel_fee'             => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true, 'default' => '0.00'],
            'customer_email'          => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'customer_mobile'         => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'customer_name'           => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'payment_status'          => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'default' => 'PENDING'],
            'settlement_status'       => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'default' => 'PENDING'],
            'gateway_response'        => ['type' => 'JSON', 'null' => true],
            'created_at'              => ['type' => 'DATETIME', 'null' => true, 'default' => new RawSql('CURRENT_TIMESTAMP')],
            '`updated_at` datetime NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
        ]);
        $this->forge->addPrimaryKey('smartpay_transaction_id');
        $this->forge->addUniqueKey('reference_number', 'uniq_reference_number');
        $this->forge->addKey('payment_id', false, false, 'idx_payment_id');
        $this->forge->addForeignKey('payment_id', 'payment', 'payment_id', 'RESTRICT', 'CASCADE', 'fk_smartpay_payment');
        $this->forge->createTable('smartpay_transactions', false, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('smartpay_transactions', true);
    }
}
