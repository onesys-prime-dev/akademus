<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateEmailQueueTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'email_id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true, 'null' => false],
            'to_email' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
            'type' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
            'payload' => ['type' => 'JSON', 'null' => true],
            'status' => ['type' => 'ENUM', 'constraint' => ['pending', 'processing', 'sent', 'failed'], 'null' => true, 'default' => 'pending'],
            'attempts' => ['type' => 'INT', 'constraint' => 11, 'null' => true, 'default' => '0'],
            'last_attempt_at' => ['type' => 'DATETIME', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true, 'default' => new RawSql('CURRENT_TIMESTAMP')],
            'related_applicant_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'send_after' => ['type' => 'DATETIME', 'null' => true],
            'notification_key' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
        ]);
        $this->forge->addPrimaryKey('email_id');
        $this->forge->addKey('status', false, false, 'status');
        $this->forge->addKey('type', false, false, 'type');
        $this->forge->createTable('email_queue', false, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('email_queue', true);
    }
}
