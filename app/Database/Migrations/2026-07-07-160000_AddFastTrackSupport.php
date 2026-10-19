<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFastTrackSupport extends Migration
{
    public function up()
    {
        $this->forge->addColumn('applications', [
            'is_fast_track' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => false,
                'after'      => 'applicant_type',
            ],
        ]);

        $this->forge->addColumn('requirements', [
            'is_fast_track_required' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => false,
                'after'      => 'is_required',
            ],
        ]);

        $this->forge->addColumn('program_requirements', [
            'is_fast_track_required' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => false,
                'after'      => 'is_mandatory',
            ],
        ]);

        $exists = $this->db->table('payment_fee_type')->where('code', 'FAST_TRACK_FEE')->get()->getRowArray();

        if (!$exists) {
            $this->db->table('payment_fee_type')->insert([
                'code'       => 'FAST_TRACK_FEE',
                'name'       => 'Fast Track Fee',
                'description' => 'Additional fee for applicants who apply via the Fast Track option.',
                'amount'     => 1000.00,
                'is_active'  => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    public function down()
    {
        $this->db->table('payment_fee_type')->where('code', 'FAST_TRACK_FEE')->delete();

        $this->forge->dropColumn('program_requirements', 'is_fast_track_required');
        $this->forge->dropColumn('requirements', 'is_fast_track_required');
        $this->forge->dropColumn('applications', 'is_fast_track');
    }
}
