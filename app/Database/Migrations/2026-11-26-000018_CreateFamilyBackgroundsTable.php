<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateFamilyBackgroundsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true, 'null' => false],
            'application_id' => ['type' => 'INT', 'constraint' => 11, 'null' => false],
            'father_firstname' => ['type' => 'TEXT', 'null' => true],
            'father_middlename' => ['type' => 'TEXT', 'null' => true],
            'father_lastname' => ['type' => 'TEXT', 'null' => true],
            'father_occupation' => ['type' => 'TEXT', 'null' => true],
            'father_contact_number' => ['type' => 'TEXT', 'null' => true],
            'mother_firstname' => ['type' => 'TEXT', 'null' => true],
            'mother_middlename' => ['type' => 'TEXT', 'null' => true],
            'mother_lastname' => ['type' => 'TEXT', 'null' => true],
            'mother_occupation' => ['type' => 'TEXT', 'null' => true],
            'mother_contact_number' => ['type' => 'TEXT', 'null' => true],
            'guardian_name' => ['type' => 'TEXT', 'null' => true],
            'guardian_relationship' => ['type' => 'TEXT', 'null' => true],
            'guardian_contact_number' => ['type' => 'TEXT', 'null' => true],
            'annual_family_income' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true, 'default' => new RawSql('CURRENT_TIMESTAMP')],
            '`updated_at` datetime NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('application_id', false, false, 'fk_fam_bg_application');
        $this->forge->addForeignKey('application_id', 'applications', 'application_id', 'CASCADE', 'CASCADE', 'fk_fam_bg_application');
        $this->forge->createTable('family_backgrounds', false, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('family_backgrounds', true);
    }
}
