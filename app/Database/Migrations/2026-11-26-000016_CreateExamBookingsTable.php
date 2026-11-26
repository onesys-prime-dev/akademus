<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateExamBookingsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'exam_booking_id'    => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'auto_increment' => true, 'null' => false],
            'exam_schedule_id'   => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'null' => false],
            'applicant_id'       => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'null' => false],
            'status'             => ['type' => 'ENUM', 'constraint' => ['booked', 'attended', 'cancelled'], 'null' => false, 'default' => 'booked'],
            'booked_at'          => ['type' => 'DATETIME', 'null' => true],
            'checked_in_at'      => ['type' => 'DATETIME', 'null' => true],
            'result_status'      => ['type' => 'ENUM', 'constraint' => ['PENDING', 'PASSED', 'FAILED'], 'null' => true, 'default' => 'PENDING'],
            'score'              => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
            'result_remarks'     => ['type' => 'TEXT', 'null' => true],
            'result_recorded_at' => ['type' => 'DATETIME', 'null' => true],
            'result_recorded_by' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'null' => true],
        ]);
        $this->forge->addPrimaryKey('exam_booking_id');
        $this->forge->addUniqueKey(['exam_schedule_id', 'applicant_id'], 'exam_schedule_id_applicant_id');
        $this->forge->addKey('exam_schedule_id', false, false, 'exam_schedule_id');
        $this->forge->addKey('applicant_id', false, false, 'applicant_id');
        $this->forge->createTable('exam_bookings', false, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('exam_bookings', true);
    }
}
