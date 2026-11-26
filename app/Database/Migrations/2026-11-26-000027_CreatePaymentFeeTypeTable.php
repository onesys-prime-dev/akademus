<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreatePaymentFeeTypeTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'payment_fee_type_id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true, 'null' => false],
            'code' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => false],
            'name' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
            'description' => ['type' => 'TEXT', 'null' => true],
            'amount' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => false, 'default' => '500.00'],
            'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'null' => true, 'default' => '1'],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => false, 'default' => new RawSql('CURRENT_TIMESTAMP')],
            '`updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP',
        ]);
        $this->forge->addPrimaryKey('payment_fee_type_id');
        $this->forge->addUniqueKey('code', 'code');
        $this->forge->createTable('payment_fee_type', false, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('payment_fee_type', true);
    }
}
