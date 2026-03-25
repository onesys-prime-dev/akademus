<?php

namespace App\Models;

use CodeIgniter\Model;

class FollowUpMessageModel extends Model
{
    protected $table         = 'follow_up_messages';
    protected $primaryKey    = 'message_id';
    protected $allowedFields = ['applicant_id', 'sender_role', 'message', 'is_read', 'created_at'];
    protected $useTimestamps = false;

    public function getMessagesForApplicant(int $applicantId): array
    {
        return $this->where('applicant_id', $applicantId)
                    ->orderBy('created_at', 'ASC')
                    ->findAll();
    }

    public function getConversationsSummary(): array
    {
        $db = \Config\Database::connect();

        return $db->query("
            SELECT
                a.applicant_id,
                a.applicant_number,
                u.firstname,
                u.lastname,
                p.program_name,
                (SELECT message FROM follow_up_messages WHERE applicant_id = a.applicant_id ORDER BY created_at DESC LIMIT 1) AS last_message,
                (SELECT created_at FROM follow_up_messages WHERE applicant_id = a.applicant_id ORDER BY created_at DESC LIMIT 1) AS last_message_at,
                (SELECT COUNT(*) FROM follow_up_messages WHERE applicant_id = a.applicant_id AND sender_role = 'applicant' AND is_read = 0) AS unread_count
            FROM applicants a
            JOIN users u ON u.user_id = a.user_id
            JOIN programs p ON p.program_id = a.program_id
            WHERE a.applicant_id IN (SELECT DISTINCT applicant_id FROM follow_up_messages)
            ORDER BY last_message_at DESC
        ")->getResultArray();
    }

    public function markAsRead(int $applicantId, string $senderRole): void
    {
        $this->where('applicant_id', $applicantId)
             ->where('sender_role', $senderRole)
             ->where('is_read', 0)
             ->set('is_read', 1)
             ->update();
    }

    public function getUnreadCountForApplicant(int $applicantId): int
    {
        return $this->where('applicant_id', $applicantId)
                    ->where('sender_role', 'officer')
                    ->where('is_read', 0)
                    ->countAllResults();
    }

    public function getTotalUnreadForOfficer(): int
    {
        return $this->where('sender_role', 'applicant')
                    ->where('is_read', 0)
                    ->countAllResults();
    }
}
