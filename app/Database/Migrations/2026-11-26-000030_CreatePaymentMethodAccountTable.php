<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreatePaymentMethodAccountTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'payment_method_account_id' => ['type' => 'INT', 'constraint' => 11, 'null' => false],
            'payment_method_id'         => ['type' => 'INT', 'constraint' => 11, 'null' => false],
            'account_number'            => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
            'account_name'              => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
            'is_active'                 => ['type' => 'TINYINT', 'constraint' => 1, 'null' => true, 'default' => '1'],
            'created_at'                => ['type' => 'TIMESTAMP', 'null' => false, 'default' => new RawSql('CURRENT_TIMESTAMP')],
        ]);

        $this->forge->createTable('payment_method_account', false, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('payment_method_account', true);
    }
}
