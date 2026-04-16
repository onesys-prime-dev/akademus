<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddReviewedByToPayment extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('reviewed_by', 'payment')) {
            $this->forge->addColumn('payment', [
                'reviewed_by' => [
                    'type'    => 'INT',
                    'null'    => true,
                    'default' => null,
                    'after'   => 'remarks',
                ],
                'reviewed_at' => [
                    'type'    => 'DATETIME',
                    'null'    => true,
                    'default' => null,
                    'after'   => 'reviewed_by',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('reviewed_by', 'payment')) {
            $this->forge->dropColumn('payment', 'reviewed_by');
        }
        if ($this->db->fieldExists('reviewed_at', 'payment')) {
            $this->forge->dropColumn('payment', 'reviewed_at');
        }
    }
}
