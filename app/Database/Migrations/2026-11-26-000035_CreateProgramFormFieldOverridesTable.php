<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateProgramFormFieldOverridesTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true, 'null' => false],
            'program_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => false],
            'field_config_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => false],
            'is_required' => ['type' => 'TINYINT', 'constraint' => 1, 'null' => true, 'comment' => 'NULL = use default, true/false = override'],
            'is_visible' => ['type' => 'TINYINT', 'constraint' => 1, 'null' => true, 'comment' => 'NULL = use default, true/false = override'],
            'sort_order' => ['type' => 'INT', 'constraint' => 11, 'null' => true, 'comment' => 'NULL = use default'],
            'created_at' => ['type' => 'DATETIME', 'null' => false, 'default' => new RawSql('CURRENT_TIMESTAMP')],
            'updated_at' => ['type' => 'DATETIME', 'null' => false, 'default' => new RawSql('CURRENT_TIMESTAMP')],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey(['program_id', 'field_config_id'], 'program_id_field_config_id');
        $this->forge->createTable('program_form_field_overrides', false, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('program_form_field_overrides', true);
    }
}
