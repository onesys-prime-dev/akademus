<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Outbox for the Admissions -> SIS handoff. One row per applicant (the unique
 * applicant_id makes queueing idempotent, mirroring the SIS side which keys on
 * applicant_id). Rows are created inside the same transaction that marks the
 * reservation fee PAID and delivered afterwards by App\Services\SisSyncService,
 * so a SIS outage never blocks or rolls back a payment.
 */
class CreateSisSync extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'sync_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'applicant_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'payment_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'processing', 'synced', 'failed'],
                'default'    => 'pending',
            ],
            'attempts' => [
                'type'       => 'TINYINT',
                'constraint' => 3,
                'unsigned'   => true,
                'default'    => 0,
            ],
            'next_attempt_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'last_error' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'student_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
            ],
            'synced_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('sync_id', true);
        $this->forge->addUniqueKey('applicant_id');
        $this->forge->addKey(['status', 'next_attempt_at']);
        $this->forge->createTable('sis_sync');
    }

    public function down()
    {
        $this->forge->dropTable('sis_sync');
    }
}
