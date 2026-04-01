<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePresetRequirementCategoriesSystem extends Migration
{
    public function up()
    {
        // 1. Add is_required to requirements table
        $this->forge->addColumn('requirements', [
            'is_required' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'after'      => 'description',
            ],
        ]);

        // 2. Create preset_requirement_categories junction table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'preset_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'requirement_category_id' => [
                'type' => 'INT',
            ],
            'sort_order' => [
                'type'    => 'INT',
                'default' => 0,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('preset_id');
        $this->forge->addKey('requirement_category_id');
        $this->forge->addForeignKey('preset_id', 'form_presets', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('preset_requirement_categories');
    }

    public function down()
    {
        $this->forge->dropTable('preset_requirement_categories', true);
        $this->forge->dropColumn('requirements', 'is_required');
    }
}
