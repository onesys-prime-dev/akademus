<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateRequirementsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'requirement_id'         => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true, 'null' => false],
            'category_id'            => ['type' => 'INT', 'constraint' => 11, 'null' => false],
            'requirement_name'       => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => false],
            'description'            => ['type' => 'TEXT', 'null' => true],
            'is_required'            => ['type' => 'TINYINT', 'constraint' => 1, 'null' => true, 'default' => '1'],
            'is_fast_track_required' => ['type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => '0'],
            'display_order'          => ['type' => 'INT', 'constraint' => 11, 'null' => true, 'default' => '0'],
            'is_active'              => ['type' => 'TINYINT', 'constraint' => 1, 'null' => true, 'default' => '1'],
            'created_at'             => ['type' => 'DATETIME', 'null' => true, 'default' => new RawSql('CURRENT_TIMESTAMP')],
            '`updated_at` datetime NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
        ]);
        $this->forge->addPrimaryKey('requirement_id');
        $this->forge->addKey('category_id', false, false, 'category_id');
        $this->forge->addForeignKey('category_id', 'requirement_categories', 'category_id', 'RESTRICT', 'RESTRICT', 'requirements_ibfk_1');
        $this->forge->createTable('requirements', false, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('requirements', true);
    }
}
