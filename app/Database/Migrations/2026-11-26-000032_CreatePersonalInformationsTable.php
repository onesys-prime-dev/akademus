<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreatePersonalInformationsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true, 'null' => false],
            'application_id' => ['type' => 'INT', 'constraint' => 11, 'null' => false],
            'firstname' => ['type' => 'TEXT', 'null' => true],
            'middlename' => ['type' => 'TEXT', 'null' => true],
            'lastname' => ['type' => 'TEXT', 'null' => true],
            'suffix' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'gender' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'date_of_birth' => ['type' => 'TEXT', 'null' => true],
            'place_of_birth' => ['type' => 'TEXT', 'null' => true],
            'citizenship' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'home_address' => ['type' => 'TEXT', 'null' => true],
            'phone_code' => ['type' => 'VARCHAR', 'constraint' => 5, 'null' => true, 'default' => '+63'],
            'contact_number' => ['type' => 'TEXT', 'null' => true],
            'email_address' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true, 'default' => new RawSql('CURRENT_TIMESTAMP')],
            '`updated_at` datetime NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('application_id', false, false, 'fk_personal_info_application');
        $this->forge->addForeignKey('application_id', 'applications', 'application_id', 'RESTRICT', 'CASCADE', 'fk_personal_info_application');
        $this->forge->createTable('personal_informations', false, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('personal_informations', true);
    }
}
