<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateExamSchedulesTables extends Migration
{
    public function up()
    {
        // exam_schedules
        $this->forge->addField([
            'exam_schedule_id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'exam_date'        => ['type' => 'DATE', 'null' => false],
            'start_time'       => ['type' => 'TIME', 'null' => false],
            'end_time'         => ['type' => 'TIME', 'null' => false],
            'location'         => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
            'max_slots'        => ['type' => 'INT', 'unsigned' => true, 'default' => 30],
            'booked_slots'     => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'status'           => ['type' => 'ENUM', 'constraint' => ['open', 'closed'], 'default' => 'open'],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('exam_schedule_id');
        $this->forge->addKey('exam_date');
        $this->forge->createTable('exam_schedules', true);

        // exam_bookings
        $this->forge->addField([
            'exam_booking_id'   => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'exam_schedule_id'  => ['type' => 'INT', 'unsigned' => true, 'null' => false],
            'applicant_id'      => ['type' => 'INT', 'unsigned' => true, 'null' => false],
            'status'            => ['type' => 'ENUM', 'constraint' => ['booked', 'attended', 'cancelled'], 'default' => 'booked'],
            'booked_at'         => ['type' => 'DATETIME', 'null' => true],
            'checked_in_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('exam_booking_id');
        $this->forge->addKey('exam_schedule_id');
        $this->forge->addKey('applicant_id');
        $this->forge->addUniqueKey(['exam_schedule_id', 'applicant_id']);
        $this->forge->createTable('exam_bookings', true);
    }

    public function down()
    {
        $this->forge->dropTable('exam_bookings', true);
        $this->forge->dropTable('exam_schedules', true);
    }
}
