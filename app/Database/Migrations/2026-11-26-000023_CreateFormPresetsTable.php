<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFormPresetsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true, 'null' => false],
            'name' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
            'description' => ['type' => 'TEXT', 'null' => true],
            'is_global' => ['type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => '0', 'comment' => '1 = applies to all programs, 0 = program-specific'],
            'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => '1'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('is_global', false, false, 'is_global');
        $this->forge->addKey('is_active', false, false, 'is_active');
        $this->forge->createTable('form_presets', false, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('form_presets', true);
    }
}
