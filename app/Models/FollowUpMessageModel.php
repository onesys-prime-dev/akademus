<?php

namespace App\Models;

use CodeIgniter\Model;

class FollowUpMessageModel extends Model
{
    protected $table         = 'follow_up_messages';
    protected $primaryKey    = 'message_id';
    protected $allowedFields = ['applicant_id', 'sender_role', 'message', 'is_read', 'created_at'];
    protected $useTimestamps = false;

    /**
     * Returns the last 100 messages for an applicant (initial page load).
     * Ordered by message_id so the index is used directly.
     */
    public function getMessagesForApplicant(int $applicantId): array
    {
        return $this->where('applicant_id', $applicantId)
                    ->orderBy('message_id', 'ASC')
                    ->findAll(100);
    }

    /**
     * Returns only messages newer than $sinceId — used by JS polling.
     * The query is trivially fast: PK range scan on an indexed column.
     */
    public function getMessagesSince(int $applicantId, int $sinceId): array
    {
        return $this->where('applicant_id', $applicantId)
                    ->where('message_id >', $sinceId)
                    ->orderBy('message_id', 'ASC')
                    ->findAll();
    }

    /**
     * Rewritten to use two derived-table subqueries (run once each) instead of
     * three correlated subqueries that ran once per applicant row.
     */
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
                lm.message    AS last_message,
                lm.created_at AS last_message_at,
                COALESCE(uc.unread_count, 0) AS unread_count
            FROM applicants a
            JOIN users u ON u.user_id = a.user_id
            JOIN programs p ON p.program_id = a.program_id
            JOIN (
                SELECT f1.applicant_id, f1.message, f1.created_at
                FROM follow_up_messages f1
                INNER JOIN (
                    SELECT applicant_id, MAX(message_id) AS max_id
                    FROM follow_up_messages
                    GROUP BY applicant_id
                ) f2 ON f1.applicant_id = f2.applicant_id AND f1.message_id = f2.max_id
            ) lm ON lm.applicant_id = a.applicant_id
            LEFT JOIN (
                SELECT applicant_id, COUNT(*) AS unread_count
                FROM follow_up_messages
                WHERE sender_role = 'applicant' AND is_read = 0
                GROUP BY applicant_id
            ) uc ON uc.applicant_id = a.applicant_id
            ORDER BY lm.created_at DESC
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

    /**
     * Returns unread count + top-5 notifications in a single method call,
     * avoiding the double model instantiation that was in the controller.
     */
    public function getUnreadSummaryForApplicant(int $applicantId): array
    {
        $db = \Config\Database::connect();

        $notifications = $db->table('follow_up_messages')
            ->where('applicant_id', $applicantId)
            ->where('sender_role', 'officer')
            ->where('is_read', 0)
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->get()->getResultArray();

        $count = $db->table('follow_up_messages')
            ->where('applicant_id', $applicantId)
            ->where('sender_role', 'officer')
            ->where('is_read', 0)
            ->countAllResults();

        return ['count' => $count, 'notifications' => $notifications];
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
