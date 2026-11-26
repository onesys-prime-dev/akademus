<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSisSyncTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'sync_id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true, 'null' => false],
            'applicant_id'    => ['type' => 'INT', 'constraint' => 11, 'null' => false],
            'payment_id'      => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'status'          => ['type' => 'ENUM', 'constraint' => ['pending', 'processing', 'synced', 'failed'], 'null' => false, 'default' => 'pending'],
            'attempts'        => ['type' => 'TINYINT', 'constraint' => 3, 'unsigned' => true, 'null' => false, 'default' => '0'],
            'next_attempt_at' => ['type' => 'DATETIME', 'null' => true],
            'last_error'      => ['type' => 'TEXT', 'null' => true],
            'student_number'  => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'synced_at'       => ['type' => 'DATETIME', 'null' => true],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('sync_id');
        $this->forge->addUniqueKey('applicant_id', 'applicant_id');
        $this->forge->addKey(['status', 'next_attempt_at'], false, false, 'status_next_attempt_at');
        $this->forge->createTable('sis_sync', false, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('sis_sync', true);
    }
}
