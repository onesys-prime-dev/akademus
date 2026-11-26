<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateApplicationsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'application_id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true, 'null' => false],
            'applicant_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'applicant_type' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'is_fast_track' => ['type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => '0'],
            'custom_fields' => ['type' => 'LONGTEXT', 'null' => true],
            'program_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'school_year' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'semester' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'application_status' => ['type' => 'ENUM', 'constraint' => ['draft', 'pending', 'approved', 'rejected', 'enrolled', 'needs_revision'], 'null' => false, 'default' => 'pending'],
            'revision_note' => ['type' => 'TEXT', 'null' => true],
            'revision_pages' => ['type' => 'TEXT', 'null' => true],
            'application_date' => ['type' => 'DATETIME', 'null' => true, 'default' => new RawSql('CURRENT_TIMESTAMP')],
            '`updated_at` datetime NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
        ]);
        $this->forge->addPrimaryKey('application_id');
        $this->forge->addKey('applicant_id', false, false, 'fk_application_applicant');
        $this->forge->addKey('program_id', false, false, 'fk_applications_program');
        $this->forge->addForeignKey('program_id', 'programs', 'program_id', 'CASCADE', 'CASCADE', 'fk_applications_program');
        $this->forge->addForeignKey('applicant_id', 'applicants', 'applicant_id', 'CASCADE', 'CASCADE', 'fk_application_applicant');
        $this->forge->createTable('applications', false, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('applications', true);
    }
}
