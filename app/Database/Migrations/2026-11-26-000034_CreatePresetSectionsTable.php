<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePresetSectionsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true, 'null' => false],
            'preset_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => false],
            'form_page_id' => ['type' => 'INT', 'constraint' => 11, 'null' => false],
            'sort_order' => ['type' => 'INT', 'constraint' => 11, 'null' => false, 'default' => '0'],
            'is_required' => ['type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => '1', 'comment' => '1 = mandatory section, 0 = optional'],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('preset_id', false, false, 'preset_id');
        $this->forge->addKey('form_page_id', false, false, 'form_page_id');
        $this->forge->addForeignKey('preset_id', 'form_presets', 'id', 'CASCADE', 'CASCADE', 'preset_sections_preset_id_foreign');
        $this->forge->createTable('preset_sections', false, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('preset_sections', true);
    }
}
