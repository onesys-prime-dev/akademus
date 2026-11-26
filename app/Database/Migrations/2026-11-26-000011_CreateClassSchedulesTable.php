<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateClassSchedulesTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'schedule_id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true, 'null' => false],
            'program_id' => ['type' => 'INT', 'constraint' => 11, 'null' => false],
            'academic_year_id' => ['type' => 'INT', 'constraint' => 11, 'null' => false],
            'section_name' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'year_level' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => false, 'default' => new RawSql('CURRENT_TIMESTAMP')],
        ]);
        $this->forge->addPrimaryKey('schedule_id');
        $this->forge->addKey('program_id', false, false, 'fk_schedule_program');
        $this->forge->addKey('academic_year_id', false, false, 'fk_schedule_academic_year');
        $this->forge->addForeignKey('academic_year_id', 'academic_years', 'year_id', 'RESTRICT', 'CASCADE', 'fk_schedule_academic_year');
        $this->forge->addForeignKey('program_id', 'programs', 'program_id', 'RESTRICT', 'CASCADE', 'fk_schedule_program');
        $this->forge->createTable('class_schedules', false, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('class_schedules', true);
    }
}
