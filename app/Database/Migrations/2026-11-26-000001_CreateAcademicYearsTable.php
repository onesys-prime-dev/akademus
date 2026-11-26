<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateAcademicYearsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'year_id'               => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true, 'null' => false],
            'year_name'             => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => false],
            'start_date'            => ['type' => 'DATE', 'null' => true],
            'end_date'              => ['type' => 'DATE', 'null' => true],
            'is_current'            => ['type' => 'TINYINT', 'constraint' => 1, 'null' => true, 'default' => '0'],
            'start_date_admission'  => ['type' => 'DATE', 'null' => true],
            'end_date_admission'    => ['type' => 'DATE', 'null' => true],
            'start_date_enrollment' => ['type' => 'DATE', 'null' => true],
            'end_date_enrollment'   => ['type' => 'DATE', 'null' => true],
            'created_at'            => ['type' => 'TIMESTAMP', 'null' => false, 'default' => new RawSql('CURRENT_TIMESTAMP')],
        ]);
        $this->forge->addPrimaryKey('year_id');
        $this->forge->createTable('academic_years', false, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('academic_years', true);
    }
}
