<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddProgramsUniqueIndexes extends Migration
{
    public function up()
    {
        // Add unique indexes so the DB enforces no duplicate program names or codes.
        // Safe to run even if the indexes already exist — we catch the exception.
        try {
            $this->db->query('ALTER TABLE programs ADD UNIQUE INDEX uq_program_name (program_name)');
        } catch (\Throwable $e) {
            // Index already exists — skip
        }

        try {
            $this->db->query('ALTER TABLE programs ADD UNIQUE INDEX uq_program_code (program_code)');
        } catch (\Throwable $e) {
            // Index already exists — skip
        }
    }

    public function down()
    {
        try {
            $this->db->query('ALTER TABLE programs DROP INDEX uq_program_name');
        } catch (\Throwable $e) {}

        try {
            $this->db->query('ALTER TABLE programs DROP INDEX uq_program_code');
        } catch (\Throwable $e) {}
    }
}
