<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateUsersTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'user_id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true, 'null' => false],
            'firstname' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
            'lastname' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
            'email' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => false],
            'mobile' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'password' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'role' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'default' => 'applicant'],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => false, 'default' => new RawSql('CURRENT_TIMESTAMP')],
        ]);
        $this->forge->addPrimaryKey('user_id');
        $this->forge->addUniqueKey('email', 'email');
        $this->forge->createTable('users', false, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('users', true);
    }
}
