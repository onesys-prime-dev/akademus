<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDecisionRemarksToApplicants extends Migration
{
    public function up()
    {
        $this->forge->addColumn('applicants', [
            'decision_remarks' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'application_status',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('applicants', 'decision_remarks');
    }
}
