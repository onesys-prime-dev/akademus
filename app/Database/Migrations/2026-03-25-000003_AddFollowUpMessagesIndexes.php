<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFollowUpMessagesIndexes extends Migration
{
    public function up()
    {
        // Composite index for per-applicant unread count queries
        $this->db->query('ALTER TABLE follow_up_messages ADD INDEX idx_unread (applicant_id, sender_role, is_read)');

        // Composite index for officer-wide unread count
        $this->db->query('ALTER TABLE follow_up_messages ADD INDEX idx_officer_unread (sender_role, is_read)');

        // Index for sorting/fetching latest message per conversation
        $this->db->query('ALTER TABLE follow_up_messages ADD INDEX idx_created (created_at)');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE follow_up_messages DROP INDEX idx_unread');
        $this->db->query('ALTER TABLE follow_up_messages DROP INDEX idx_officer_unread');
        $this->db->query('ALTER TABLE follow_up_messages DROP INDEX idx_created');
    }
}
