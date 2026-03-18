<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProgramFormFieldOverridesTable extends Migration
{
    public function up()
    {
        // This table allows programs to override/customize global form fields
        // without changing the original form_field_configs
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'program_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'field_config_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'is_required' => [
                'type' => 'BOOLEAN',
                'null' => true,
                'comment' => 'NULL = use default, true/false = override',
            ],
            'is_visible' => [
                'type' => 'BOOLEAN',
                'null' => true,
                'comment' => 'NULL = use default, true/false = override',
            ],
            'sort_order' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'comment' => 'NULL = use default',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP'),
                'on_update' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['program_id', 'field_config_id']);
        $this->forge->createTable('program_form_field_overrides');
    }

    public function down()
    {
        $this->forge->dropTable('program_form_field_overrides');
    }
}
