<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProgramRequirementCategoriesTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'program_requirement_category_id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true, 'null' => false],
            'program_id'                      => ['type' => 'INT', 'constraint' => 11, 'null' => false],
            'category_id'                     => ['type' => 'INT', 'constraint' => 11, 'null' => false],
            'display_order'                   => ['type' => 'INT', 'constraint' => 11, 'null' => true, 'default' => '0'],
            'is_active'                       => ['type' => 'TINYINT', 'constraint' => 1, 'null' => true, 'default' => '1'],
            'created_at'                      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'                      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('program_requirement_category_id');
        $this->forge->addUniqueKey(['program_id', 'category_id'], 'program_id');
        $this->forge->addKey('category_id', false, false, 'category_id');
        $this->forge->addForeignKey('program_id', 'programs', 'program_id', 'RESTRICT', 'RESTRICT', 'program_requirement_categories_ibfk_1');
        $this->forge->addForeignKey('category_id', 'requirement_categories', 'category_id', 'RESTRICT', 'CASCADE', 'program_requirement_categories_ibfk_2');
        $this->forge->createTable('program_requirement_categories', false, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('program_requirement_categories', true);
    }
}
