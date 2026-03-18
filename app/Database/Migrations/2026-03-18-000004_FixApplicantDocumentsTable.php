<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixApplicantDocumentsTable extends Migration
{
    public function up()
    {
        // Fix 1: document_id had no AUTO_INCREMENT, so every replace() used document_id=0
        // and hit a PRIMARY KEY conflict — deleting the previous upload before inserting the new one.
        $this->forge->modifyColumn('applicant_documents', [
            'document_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => false,
                'auto_increment' => true,
            ],
        ]);

        // Fix 2: Add UNIQUE constraint on (applicant_id, program_requirement_id)
        // so REPLACE correctly updates the existing row per requirement per applicant
        // instead of always inserting duplicates.
        $this->db->query('ALTER TABLE applicant_documents ADD UNIQUE KEY uq_applicant_requirement (applicant_id, program_requirement_id)');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE applicant_documents DROP INDEX uq_applicant_requirement');

        $this->forge->modifyColumn('applicant_documents', [
            'document_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
        ]);
    }
}
