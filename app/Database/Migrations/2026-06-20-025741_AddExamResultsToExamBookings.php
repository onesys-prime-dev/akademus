<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddExamResultsToExamBookings extends Migration
{
    public function up()
    {
        $fields = $this->db->getFieldNames('exam_bookings');

        $columns = [];

        if (! in_array('result_status', $fields, true)) {
            $columns['result_status'] = [
                'type'       => 'ENUM',
                'constraint' => ['PENDING', 'PASSED', 'FAILED'],
                'default'    => 'PENDING',
                'after'      => 'checked_in_at',
            ];
        }

        if (! in_array('score', $fields, true)) {
            $columns['score'] = [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'       => true,
                'after'      => 'result_status',
            ];
        }

        if (! in_array('result_remarks', $fields, true)) {
            $columns['result_remarks'] = [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'score',
            ];
        }

        if (! in_array('result_recorded_at', $fields, true)) {
            $columns['result_recorded_at'] = [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'result_remarks',
            ];
        }

        if (! in_array('result_recorded_by', $fields, true)) {
            $columns['result_recorded_by'] = [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'result_recorded_at',
            ];
        }

        if (! empty($columns)) {
            $this->forge->addColumn('exam_bookings', $columns);
        }
    }

    public function down()
    {
        $fields = $this->db->getFieldNames('exam_bookings');

        foreach ([
            'result_recorded_by',
            'result_recorded_at',
            'result_remarks',
            'score',
            'result_status',
        ] as $column) {
            if (in_array($column, $fields, true)) {
                $this->forge->dropColumn('exam_bookings', $column);
            }
        }
    }
}