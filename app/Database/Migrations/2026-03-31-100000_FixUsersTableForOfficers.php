<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixUsersTableForOfficers extends Migration
{
    public function up()
    {
        // Add password column for admission_officer accounts
        if (!$this->db->fieldExists('password', 'users')) {
            $this->forge->addColumn('users', [
                'password' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                    'default'    => null,
                    'after'      => 'mobile',
                ],
            ]);
        }

        // Make mobile nullable so admin/officer accounts don't need it
        $this->forge->modifyColumn('users', [
            'mobile' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'default'    => null,
            ],
        ]);
    }

    public function down()
    {
        if ($this->db->fieldExists('password', 'users')) {
            $this->forge->dropColumn('users', 'password');
        }

        $this->forge->modifyColumn('users', [
            'mobile' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => false,
            ],
        ]);
    }
}
