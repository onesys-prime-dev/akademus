<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateFormFieldConfigsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true, 'null' => false],
            'page_id' => ['type' => 'INT', 'constraint' => 11, 'null' => false, 'default' => '1'],
            'field_key' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
            'step' => ['type' => 'TINYINT', 'constraint' => 4, 'null' => false],
            'section' => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => false],
            'label' => ['type' => 'VARCHAR', 'constraint' => 200, 'null' => false],
            'field_type' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => false, 'default' => 'text'],
            'placeholder' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'is_visible' => ['type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => '1'],
            'is_required' => ['type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => '0'],
            'is_system' => ['type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => '1'],
            'sort_order' => ['type' => 'INT', 'constraint' => 11, 'null' => false, 'default' => '0'],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => false, 'default' => new RawSql('CURRENT_TIMESTAMP')],
            '`updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('form_field_configs', false, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('form_field_configs', true);
    }
}
