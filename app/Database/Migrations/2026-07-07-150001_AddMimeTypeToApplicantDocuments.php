<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Document files are encrypted at rest, so the on-disk bytes no longer carry
 * a detectable MIME signature — the original type must be recorded at
 * upload time so it can be replayed as the Content-Type header on view.
 */
class AddMimeTypeToApplicantDocuments extends Migration
{
    public function up()
    {
        $this->forge->addColumn('applicant_documents', [
            'mime_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 127,
                'null'       => true,
                'after'      => 'document_name',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('applicant_documents', 'mime_type');
    }
}
