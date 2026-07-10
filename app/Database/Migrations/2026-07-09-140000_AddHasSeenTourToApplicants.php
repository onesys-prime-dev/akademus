<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddHasSeenTourToApplicants extends Migration
{
    public function up()
    {
        $this->forge->addColumn('applicants', [
            'has_seen_tour' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => false,
                'after'      => 'application_status',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('applicants', 'has_seen_tour');
    }
}
