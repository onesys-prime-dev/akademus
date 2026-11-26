<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateCourseFormFieldsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true, 'null' => false],
            'course_id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => false],
            'field_name'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
            'field_type'       => ['type' => 'ENUM', 'constraint' => ['text', 'textarea', 'email', 'number', 'file', 'select', 'checkbox', 'radio'], 'null' => false],
            'label'            => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
            'placeholder'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'is_required'      => ['type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => '0'],
            'validation_rules' => ['type' => 'JSON', 'null' => true],
            'options'          => ['type' => 'JSON', 'null' => true],
            'display_order'    => ['type' => 'INT', 'constraint' => 11, 'null' => false, 'default' => '0'],
            'override_global'  => ['type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => '0', 'comment' => 'If true, hides global field with same name'],
            'is_active'        => ['type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => '1'],
            'created_at'       => ['type' => 'DATETIME', 'null' => false, 'default' => new RawSql('CURRENT_TIMESTAMP')],
            'updated_at'       => ['type' => 'DATETIME', 'null' => false, 'default' => new RawSql('CURRENT_TIMESTAMP')],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('course_id', false, false, 'course_form_fields_course_id_foreign');
        $this->forge->addForeignKey('course_id', 'courses', 'id', 'CASCADE', 'CASCADE', 'course_form_fields_course_id_foreign');
        $this->forge->createTable('course_form_fields', false, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('course_form_fields', true);
    }
}
