<?php

namespace App\Services;

use App\Models\Applicant\ApplicantModel;

class NotificationService
{
    public function trigger(string $type, int $applicantId, array $data = [], ?string $key = null): void
    {
        $db = db_connect();

        $applicant = (new ApplicantModel())
            ->select('users.email')
            ->join('users', 'users.user_id = applicants.user_id')
            ->where('applicants.applicant_id', $applicantId)
            ->first();

        if (!$applicant || empty($applicant['email'])) {
            log_message('error', 'NotificationService: email not found for applicant ID ' . $applicantId);
            return;
        }

        // =========================================================
        // 🔥 FOLLOW-UP MESSAGE (WITH BATCHING + DELAY)
        // =========================================================
        if ($type === 'followup.new_message') {

            $cooldownMinutes = 15;

            $existing = $db->table('email_queue')
                ->where('type', 'followup.new_message')
                ->where('related_applicant_id', $applicantId)
                ->where('status', 'pending')
                ->where('send_after >', date('Y-m-d H:i:s')) // 🔥 prevent race condition
                ->where('created_at >=', date('Y-m-d H:i:s', strtotime("-{$cooldownMinutes} minutes")))
                ->orderBy('email_id', 'DESC')
                ->get()
                ->getRowArray();

            if ($existing) {

                $payload = json_decode($existing['payload'], true) ?? [];
                $messages = $payload['messages'] ?? [];

                $messages[] = $data['message'] ?? '';

                // 🔥 SLIDING DELAY WITH MAX CAP
                $delayMinutes = 2;
                $maxDelayMinutes = 15;

                $originalCreated = strtotime($existing['created_at']);
                $maxSendTime = strtotime("+{$maxDelayMinutes} minutes", $originalCreated);

                $newSendAfter = strtotime("+{$delayMinutes} minutes");

                if ($newSendAfter > $maxSendTime) {
                    $newSendAfter = $maxSendTime;
                }

                $newSendAfter = date('Y-m-d H:i:s', $newSendAfter);

                $db->table('email_queue')
                    ->where('email_id', $existing['email_id'])
                    ->update([
                        'payload' => json_encode([
                            'messages' => $messages
                        ]),
                        'send_after' => $newSendAfter
                    ]);

                return;
            }

            // 🔥 FIRST MESSAGE → CREATE NEW QUEUE
            $delayMinutes = 2;

            $db->table('email_queue')->insert([
                'to_email' => $applicant['email'],
                'type'     => $type,
                'payload'  => json_encode([
                    'messages' => [$data['message'] ?? '']
                ]),
                'related_applicant_id' => $applicantId,
                'status'   => 'pending',
                'send_after' => date('Y-m-d H:i:s', strtotime("+{$delayMinutes} minutes")),
            ]);

            return;
        }

        // =========================================================
        // ✅ DEFAULT (NO DELAY / NO BATCHING)
        // =========================================================
        $db->table('email_queue')->insert([
            'to_email' => $applicant['email'],
            'type'     => $type,
            'payload'  => json_encode($data),
            'related_applicant_id' => $applicantId,
            'status'   => 'pending',
            'send_after' => null,
            'notification_key' => $key,
        ]);
    }
}