<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFormFieldOptionConfigTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true, 'null' => false],
            'form_field_id' => ['type' => 'INT', 'constraint' => 11, 'null' => false],
            'option_name' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('form_field_id', false, false, 'form_field_id');
        $this->forge->addForeignKey('form_field_id', 'form_field_configs', 'id', 'CASCADE', 'CASCADE', 'form_field_option_config_form_field_id_foreign');
        $this->forge->createTable('form_field_option_config', false, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('form_field_option_config', true);
    }
}
