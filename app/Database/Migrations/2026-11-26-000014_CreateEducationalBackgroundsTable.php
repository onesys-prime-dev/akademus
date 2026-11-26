<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateEducationalBackgroundsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'educational_bg_id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true, 'null' => false],
            'application_id' => ['type' => 'INT', 'constraint' => 11, 'null' => false],
            'school_name' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'school_location' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'strand' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'school_year_start' => ['type' => 'YEAR', 'constraint' => 4, 'null' => true],
            'school_year_end' => ['type' => 'YEAR', 'constraint' => 4, 'null' => true],
            'gwa' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
            'school_type' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true, 'default' => new RawSql('CURRENT_TIMESTAMP')],
            '`updated_at` datetime NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
        ]);
        $this->forge->addPrimaryKey('educational_bg_id');
        $this->forge->addKey('application_id', false, false, 'fk_education_bg_application');
        $this->forge->addForeignKey('application_id', 'applications', 'application_id', 'CASCADE', 'CASCADE', 'fk_education_bg_application');
        $this->forge->createTable('educational_backgrounds', false, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('educational_backgrounds', true);
    }
}
