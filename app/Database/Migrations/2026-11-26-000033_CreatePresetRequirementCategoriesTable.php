<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePresetRequirementCategoriesTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'auto_increment' => true, 'null' => false],
            'preset_id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'null' => false],
            'requirement_category_id' => ['type' => 'INT', 'constraint' => 11, 'null' => false],
            'sort_order' => ['type' => 'INT', 'constraint' => 11, 'null' => false, 'default' => '0'],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('preset_id', false, false, 'preset_id');
        $this->forge->addKey('requirement_category_id', false, false, 'requirement_category_id');
        $this->forge->addForeignKey('preset_id', 'form_presets', 'id', 'CASCADE', 'CASCADE', 'preset_requirement_categories_preset_id_foreign');
        $this->forge->createTable('preset_requirement_categories', false, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('preset_requirement_categories', true);
    }
}
