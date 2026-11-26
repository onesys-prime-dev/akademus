<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateApplicantDocumentsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'document_id'            => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true, 'null' => false],
            'applicant_id'           => ['type' => 'INT', 'constraint' => 11, 'null' => false],
            'program_requirement_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'document_name'          => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
            'mime_type'              => ['type' => 'VARCHAR', 'constraint' => 127, 'null' => true],
            'status'                 => ['type' => 'ENUM', 'constraint' => ['pending', 'approved', 'rejected'], 'null' => true, 'default' => 'pending'],
            'verified_by'            => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'remarks'                => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'uploaded_at'            => ['type' => 'DATETIME', 'null' => true, 'default' => new RawSql('CURRENT_TIMESTAMP')],
            '`updated_at` datetime NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
        ]);
        $this->forge->addPrimaryKey('document_id');
        $this->forge->addUniqueKey(['applicant_id', 'program_requirement_id'], 'uq_applicant_requirement');
        $this->forge->addKey('applicant_id', false, false, 'applicant_id');
        $this->forge->addKey('program_requirement_id', false, false, 'program_requirement_id');
        $this->forge->addForeignKey('program_requirement_id', 'program_requirements', 'program_requirement_id', 'CASCADE', 'SET NULL', 'fk_applicant_doc_requirement');
        $this->forge->createTable('applicant_documents', false, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('applicant_documents', true);
    }
}
