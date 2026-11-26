<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateExamSchedulesTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'exam_schedule_id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'auto_increment' => true, 'null' => false],
            'exam_date'        => ['type' => 'DATE', 'null' => false],
            'start_time'       => ['type' => 'TIME', 'null' => false],
            'end_time'         => ['type' => 'TIME', 'null' => false],
            'location'         => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
            'max_slots'        => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'null' => false, 'default' => '30'],
            'booked_slots'     => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'null' => false, 'default' => '0'],
            'status'           => ['type' => 'ENUM', 'constraint' => ['open', 'closed'], 'null' => false, 'default' => 'open'],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('exam_schedule_id');
        $this->forge->addKey('exam_date', false, false, 'exam_date');
        $this->forge->createTable('exam_schedules', false, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('exam_schedules', true);
    }
}
