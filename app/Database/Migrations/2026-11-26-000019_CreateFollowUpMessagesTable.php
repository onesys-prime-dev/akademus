<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFollowUpMessagesTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'message_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true, 'null' => false],
            'applicant_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => false],
            'sender_role'  => ['type' => 'ENUM', 'constraint' => ['applicant', 'officer'], 'null' => false],
            'message'      => ['type' => 'TEXT', 'null' => false],
            'is_read'      => ['type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => '0'],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('message_id');
        $this->forge->addKey('applicant_id', false, false, 'applicant_id');
        $this->forge->addKey(['applicant_id', 'sender_role', 'is_read'], false, false, 'idx_unread');
        $this->forge->addKey(['sender_role', 'is_read'], false, false, 'idx_officer_unread');
        $this->forge->addKey('created_at', false, false, 'idx_created');
        $this->forge->createTable('follow_up_messages', false, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('follow_up_messages', true);
    }
}
