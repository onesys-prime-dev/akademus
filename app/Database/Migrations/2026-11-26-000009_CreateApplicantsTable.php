<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateApplicantsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'applicant_id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true, 'null' => false],
            'user_id' => ['type' => 'INT', 'constraint' => 11, 'null' => false],
            'program_id' => ['type' => 'INT', 'constraint' => 11, 'null' => false],
            'applicant_number' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'password' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
            'temp_password' => ['type' => 'TINYINT', 'constraint' => 1, 'null' => true, 'default' => '1'],
            'application_status' => ['type' => 'ENUM', 'constraint' => ['pending', 'approved', 'rejected', 'enrolled'], 'null' => true, 'default' => 'pending'],
            'has_seen_tour' => ['type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => '0'],
            'decision_remarks' => ['type' => 'TEXT', 'null' => true],
            'application_date' => ['type' => 'DATETIME', 'null' => true],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => false, 'default' => new RawSql('CURRENT_TIMESTAMP')],
            'form_step' => ['type' => 'TINYINT', 'constraint' => 4, 'null' => true, 'default' => '1'],
        ]);
        $this->forge->addPrimaryKey('applicant_id');
        $this->forge->addUniqueKey('applicant_number', 'applicant_number');
        $this->forge->addKey('user_id', false, false, 'fk_applicant_user');
        $this->forge->createTable('applicants', false, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('applicants', true);
    }
}
