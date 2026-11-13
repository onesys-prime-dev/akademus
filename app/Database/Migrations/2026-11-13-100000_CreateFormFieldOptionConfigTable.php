<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Holds the selectable options of a `select`-type field in form_field_configs,
 * so admin-created select fields aren't limited to hardcoded <option> lists.
 */
class CreateFormFieldOptionConfigTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
            ],
            // Signed INT(11) to match form_field_configs.id exactly (required for the FK).
            'form_field_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'option_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('form_field_id');
        $this->forge->addForeignKey(
            'form_field_id',
            'form_field_configs',
            'id',
            'CASCADE', // ON UPDATE
            'CASCADE'  // ON DELETE: removing a field removes its options
        );

        $this->forge->createTable('form_field_option_config');
    }

    public function down()
    {
        $this->forge->dropTable('form_field_option_config');
    }
}
