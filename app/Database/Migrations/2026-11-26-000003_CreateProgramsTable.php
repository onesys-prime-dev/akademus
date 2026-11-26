<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateProgramsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'program_id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true, 'null' => false],
            'program_code' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'program_name' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => false, 'default' => new RawSql('CURRENT_TIMESTAMP')],
            'preset_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'comment' => 'Foreign key to form_presets, null = use global preset'],
        ]);
        $this->forge->addPrimaryKey('program_id');
        $this->forge->addUniqueKey('program_name', 'uq_program_name');
        $this->forge->addUniqueKey('program_code', 'uq_program_code');
        $this->forge->createTable('programs', false, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('programs', true);
    }
}
