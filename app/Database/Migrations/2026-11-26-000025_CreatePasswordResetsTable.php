<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePasswordResetsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true, 'null' => false],
            'email' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
            'token' => ['type' => 'VARCHAR', 'constraint' => 64, 'null' => false],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'used' => ['type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => '0'],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('token', false, false, 'token');
        $this->forge->createTable('password_resets', false, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('password_resets', true);
    }
}
