<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use App\Services\EmailService;

class SendEmails extends BaseCommand
{
    protected $group = 'Notifications';
    protected $name = 'emails:send';
    protected $description = 'Process email queue';

    public function run(array $params)
    {
        $db = db_connect();

        while (true) {

            $emails = $db->table('email_queue')
                ->where('status', 'pending')
                ->where('attempts <', 3)
                ->groupStart()
                    ->where('send_after <=', date('Y-m-d H:i:s'))
                    ->orWhere('send_after IS NULL') // 🔥 immediate emails
                ->groupEnd()
                ->limit(10)
                ->get()
                ->getResultArray();

            if (empty($emails)) {
                sleep(2);
                continue;
            }

            foreach ($emails as $email) {

                // 🔒 LOCK (avoid duplicate processing)
                $locked = $db->table('email_queue')
                    ->where('email_id', $email['email_id'])
                    ->where('status', 'pending')
                    ->update(['status' => 'processing']);

                if (!$locked || $db->affectedRows() === 0) {
                    continue;
                }

                $payload = json_decode($email['payload'], true) ?? [];

                $success = false;

                try {

                    switch ($email['type']) {

                        case 'auth.welcome':
                            $success = EmailService::sendWelcomeEmail(
                                $email['to_email'],
                                $payload['temp_password'] ?? ''
                            );
                            break;

                        case 'auth.password_reset':
                            $success = EmailService::sendPasswordReset(
                                $email['to_email'],
                                $payload['reset_url'] ?? ''
                            );
                            break;

                        case 'applicant.approved':
                            $success = EmailService::sendApplicationApproved($email['to_email']);
                            break;

                        case 'applicant.rejected':
                            $success = EmailService::sendApplicationDeclined($email['to_email']);
                            break;

                        case 'followup.new_message':
                            $messages = $payload['messages'] ?? [];
                            $success = EmailService::sendFollowUpNotification(
                                $email['to_email'],
                                $messages
                            );
                            break;
                        
                        case 'admission_fee.pending':
                            $success = EmailService::sendAdmissionFeePendingNotification($email['to_email'], $payload['reference_num'] ?? '');
                            break;

                        case 'reservation_fee.pending':
                            $success = EmailService::sendReservationFeePendingNotification($email['to_email'], $payload['reference_num'] ?? '');
                            break;

                        case 'payment.approved':
                            $success = EmailService::sendPaymentApprovedNotification($email['to_email'], $payload['reference_num'] ?? '', $payload['fee_type'] ?? '');
                            break;

                        case 'payment.rejected':
                            $success = EmailService::sendPaymentRejectedNotification($email['to_email'], $payload['reference_num'] ?? '', $payload['fee_type'] ?? '');
                            break;

                        case 'application.submitted':
                            $success = EmailService::sendApplicationSubmittedNotification($email['to_email']);
                            break;

                        case 'application.not_started':
                            $success = EmailService::sendApplicationNotStarted(
                                $email['to_email'],
                                $payload['level'] ?? 1
                            );
                            break;

                        case 'application.in_progress':
                            $success = EmailService::sendApplicationInProgress(
                                $email['to_email'],
                                $payload['level'] ?? 1
                            );
                            break;

                        case 'requirements.uploaded':
                            $success = EmailService::sendRequirementUploadedNotification(
                                $email['to_email'],
                                $payload['requirement_name'] ?? ''
                            );
                            break;
                        
                        case 'requirements.completed':
                            $success = EmailService::sendRequirementsCompletedNotification(
                                $email['to_email']
                            );
                            break;
                        
                        case 'requirements.not_started':
                            $success = EmailService::sendRequirementsNotStarted(
                                $email['to_email'],
                                $payload['level'] ?? 1
                            );
                            break;

                        case 'requirements.incomplete':
                            $success = EmailService::sendRequirementsIncomplete(
                                $email['to_email'],
                                $payload['level'] ?? 1
                            );
                            break;

                        default:
                            log_message('error', 'Unknown email type: ' . $email['type']);
                            $success = false;
                    }

                } catch (\Throwable $e) {
                    log_message('error', 'Email worker error: ' . $e->getMessage());
                    $success = false;
                }

                // ✅ UPDATE STATUS
                $db->table('email_queue')
                    ->where('email_id', $email['email_id'])
                    ->update([
                        'status' => $success ? 'sent' : 'failed',
                        'attempts' => $email['attempts'] + ($success ? 0 : 1),
                        'last_attempt_at' => date('Y-m-d H:i:s')
                    ]);
            }

            echo "Processed " . count($emails) . " emails\n";
        }
    }
}