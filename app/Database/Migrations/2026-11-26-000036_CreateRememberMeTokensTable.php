<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateRememberMeTokensTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'null' => false],
            'user_id'    => ['type' => 'INT', 'constraint' => 11, 'null' => false],
            'token'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
            'expires_at' => ['type' => 'DATETIME', 'null' => false],
            'created_at' => ['type' => 'DATETIME', 'null' => true, 'default' => new RawSql('CURRENT_TIMESTAMP')],
        ]);

        $this->forge->createTable('remember_me_tokens', false, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('remember_me_tokens', true);
    }
}
