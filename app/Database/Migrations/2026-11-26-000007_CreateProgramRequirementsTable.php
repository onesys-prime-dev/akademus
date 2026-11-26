<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateProgramRequirementsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'program_requirement_id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true, 'null' => false],
            'program_requirement_category_id' => ['type' => 'INT', 'constraint' => 11, 'null' => false],
            'requirement_id' => ['type' => 'INT', 'constraint' => 11, 'null' => false],
            'display_order' => ['type' => 'INT', 'constraint' => 11, 'null' => true, 'default' => '0'],
            'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'null' => true, 'default' => '1'],
            'is_mandatory' => ['type' => 'TINYINT', 'constraint' => 1, 'null' => true, 'default' => '1'],
            'is_fast_track_required' => ['type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => '0'],
            'created_at' => ['type' => 'DATETIME', 'null' => true, 'default' => new RawSql('CURRENT_TIMESTAMP')],
            '`updated_at` datetime NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
        ]);
        $this->forge->addPrimaryKey('program_requirement_id');
        $this->forge->addUniqueKey(['program_requirement_category_id', 'requirement_id'], 'program_requirement_category_id');
        $this->forge->addKey('requirement_id', false, false, 'requirement_id');
        $this->forge->addForeignKey('program_requirement_category_id', 'program_requirement_categories', 'program_requirement_category_id', 'RESTRICT', 'RESTRICT', 'program_requirements_ibfk_1');
        $this->forge->addForeignKey('requirement_id', 'requirements', 'requirement_id', 'RESTRICT', 'CASCADE', 'program_requirements_ibfk_2');
        $this->forge->createTable('program_requirements', false, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('program_requirements', true);
    }
}
