<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFormPresetsSystem extends Migration
{
    public function up()
    {
        // Create form_presets table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'is_global' => [
                'type'    => 'BOOLEAN',
                'default' => 0,
                'comment' => '1 = applies to all programs, 0 = program-specific',
            ],
            'is_active' => [
                'type'    => 'BOOLEAN',
                'default' => 1,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('is_global');
        $this->forge->addKey('is_active');
        $this->forge->createTable('form_presets');

        // Create preset_sections table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'preset_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'form_page_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'sort_order' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'is_required' => [
                'type'    => 'BOOLEAN',
                'default' => 1,
                'comment' => '1 = mandatory section, 0 = optional',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('preset_id');
        $this->forge->addKey('form_page_id');
        $this->forge->addForeignKey('preset_id', 'form_presets', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('preset_sections');

        // Add preset_id column to programs table
        $this->forge->addColumn('programs', [
            'preset_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'Foreign key to form_presets, null = use global preset',
            ],
        ]);
    }

    public function down()
    {
        // Drop foreign keys
        $this->forge->dropForeignKey('programs', 'programs_preset_id_foreign');
        
        // Drop columns
        $this->forge->dropColumn('programs', 'preset_id');

        // Drop tables
        $this->forge->dropTable('preset_sections');
        $this->forge->dropTable('form_presets');
    }
}
