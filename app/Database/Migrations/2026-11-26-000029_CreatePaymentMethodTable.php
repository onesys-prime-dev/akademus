<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreatePaymentMethodTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'payment_method_id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true, 'null' => false],
            'code' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => false],
            'name' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => false],
            'description' => ['type' => 'TEXT', 'null' => true],
            'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'null' => true, 'default' => '1'],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => false, 'default' => new RawSql('CURRENT_TIMESTAMP')],
            '`updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP',
        ]);
        $this->forge->addPrimaryKey('payment_method_id');
        $this->forge->addUniqueKey('code', 'code');
        $this->forge->createTable('payment_method', false, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('payment_method', true);
    }
}
