<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFollowUpMessagesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'message_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'applicant_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'sender_role' => [
                'type'       => 'ENUM',
                'constraint' => ['applicant', 'officer'],
            ],
            'message' => [
                'type' => 'TEXT',
            ],
            'is_read' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
            ],
        ]);

        $this->forge->addKey('message_id', true);
        $this->forge->addKey('applicant_id');
        $this->forge->createTable('follow_up_messages');
    }

    public function down()
    {
        $this->forge->dropTable('follow_up_messages');
    }
}
