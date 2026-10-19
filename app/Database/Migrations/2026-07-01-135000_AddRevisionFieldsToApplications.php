<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRevisionFieldsToApplications extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('applications', [
            'application_status' => [
                'type'       => 'ENUM',
                'constraint' => ['draft', 'pending', 'approved', 'rejected', 'enrolled', 'needs_revision'],
                'default'    => 'pending',
                'null'       => false,
            ],
        ]);

        $fields = $this->db->getFieldNames('applications');

        $columns = [];

        if (! in_array('revision_note', $fields, true)) {
            $columns['revision_note'] = [
                'type'  => 'TEXT',
                'null'  => true,
                'after' => 'application_status',
            ];
        }

        if (! in_array('revision_pages', $fields, true)) {
            $columns['revision_pages'] = [
                'type'  => 'TEXT',
                'null'  => true,
                'after' => 'revision_note',
            ];
        }

        if (! empty($columns)) {
            $this->forge->addColumn('applications', $columns);
        }
    }

    public function down()
    {
        $fields = $this->db->getFieldNames('applications');

        foreach (['revision_pages', 'revision_note'] as $column) {
            if (in_array($column, $fields, true)) {
                $this->forge->dropColumn('applications', $column);
            }
        }

        $this->forge->modifyColumn('applications', [
            'application_status' => [
                'type'       => 'ENUM',
                'constraint' => ['draft', 'pending', 'approved', 'rejected', 'enrolled'],
                'default'    => 'pending',
                'null'       => false,
            ],
        ]);
    }
}
