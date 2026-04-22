<?php

namespace App\Services;

class EmailService
{
    private static function send(string $to, string $subject, string $message): bool
    {
        $email = \Config\Services::email();
        $email->clear();

        $email->setFrom('testakademusmentora@gmail.com', 'Akademus Portal');
        $email->setTo($to);
        $email->setSubject($subject);
        $email->setMailType('html');
        $email->setMessage($message);

        if (!$email->send()) {
            log_message('error', 'EmailService failed: ' . $email->printDebugger(['headers']));
            return false;
        }

        return true;
    }
    public static function sendWelcomeEmail(string $to, string $tempPassword): bool
    {
        $loginUrl = base_url('sign-in');

        $message = "
        <table width='100%' cellpadding='0' cellspacing='0' style='background-color:#f3f4f6;padding:30px 0;font-family:Arial,sans-serif;'>
            <tr>
                <td align='center'>
                    
                    <table width='100%' cellpadding='0' cellspacing='0' style='max-width:600px;background:#ffffff;border-radius:10px;padding:30px;'>
                        
                        <!-- Header -->
                        <tr>
                            <td style='font-size:22px;font-weight:bold;color:#111827;padding-bottom:10px;'>
                                Welcome to Akademus Portal 🎓
                            </td>
                        </tr>

                        <!-- Intro -->
                        <tr>
                            <td style='font-size:14px;color:#4b5563;padding-bottom:20px;'>
                                Your account has been successfully created.
                            </td>
                        </tr>

                        <!-- Credentials Box -->
                        <tr>
                            <td style='background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:15px;'>
                                
                                <table width='100%' cellpadding='0' cellspacing='0'>
                                    <tr>
                                        <td style='font-size:13px;color:#6b7280;padding-bottom:5px;'>Email</td>
                                    </tr>
                                    <tr>
                                        <td style='font-size:14px;color:#111827;font-weight:500;padding-bottom:10px;'>
                                            {$to}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td style='font-size:13px;color:#6b7280;padding-bottom:5px;'>Temporary Password</td>
                                    </tr>
                                    <tr>
                                        <td style='font-size:14px;color:#111827;font-weight:600;background:#ffffff;border:1px dashed #d1d5db;padding:10px;border-radius:6px;letter-spacing:1px;'>
                                            {$tempPassword}
                                        </td>
                                    </tr>
                                </table>

                            </td>
                        </tr>

                        <!-- CTA Button -->
                        <tr>
                            <td align='center' style='padding:25px 0 10px 0;'>
                                <a href='{$loginUrl}' 
                                style='background:#3b82f6;color:#ffffff;text-decoration:none;padding:12px 24px;border-radius:8px;font-size:14px;font-weight:600;display:inline-block;'>
                                    Sign In to Your Account
                                </a>
                            </td>
                        </tr>

                        <!-- Fallback Link -->
                        <tr>
                            <td align='center' style='font-size:12px;color:#6b7280;padding-bottom:20px;'>
                                or copy and paste this link:<br>
                                <a href='{$loginUrl}' style='color:#3b82f6;text-decoration:none;'>{$loginUrl}</a>
                            </td>
                        </tr>

                        <!-- Instruction -->
                        <tr>
                            <td style='font-size:13px;color:#6b7280;padding-bottom:20px;text-align:center;'>
                                For security, please change your password after logging in.
                            </td>
                        </tr>

                        <!-- Divider -->
                        <tr>
                            <td style='border-top:1px solid #e5e7eb;padding-top:15px;'></td>
                        </tr>

                        <!-- Footer -->
                        <tr>
                            <td style='font-size:11px;color:#9ca3af;text-align:center;padding-top:10px;'>
                                Akademus Portal • This is an automated message
                            </td>
                        </tr>

                    </table>

                </td>
            </tr>
        </table>
        ";

        return self::send($to, 'Welcome to Akademus Portal', $message);
    }

    public static function sendDocumentReceived(string $to): bool
    {
        $message = "
            <h2>Document Received</h2>
            <p>Your uploaded document has been successfully received.</p>
            <p>Admissions will review it shortly.</p>
            <p>Thank you and have a great day!</p>
        ";

        return self::send($to, 'Document Received - Akademus', $message);
    }

    public static function sendApplicationApproved(string $to): bool
    {
        $message = "
            <h2>Application Approved</h2>
            <p>Congratulations! Your application has been approved.</p>
            <p>You may now proceed with the enrollment process.</p>
        ";

        return self::send($to, 'Application Approved - Akademus', $message);
    }

    public static function sendApplicationDeclined(string $to): bool
    {
        $message = "
            <h2>Application Status Update</h2>
            <p>We regret to inform you that your application was not approved.</p>
            <p>If you have questions, please contact admissions.</p>
        ";

        return self::send($to, 'Application Status Update - Akademus', $message);
    }

    public static function sendPasswordReset(string $to, string $resetUrl): bool
    {
        $message = "
            <h2>Password Reset Request</h2>
            <p>We received a request to reset your Akademus Portal password.</p>
            <p>Click the button below to set a new password. This link expires in <strong>1 hour</strong>.</p>
            <p style='margin:24px 0;'>
                <a href='{$resetUrl}' style='
                    background:#0040a6;color:#fff;padding:12px 24px;
                    border-radius:8px;text-decoration:none;font-weight:600;
                '>Reset My Password</a>
            </p>
            <p>If you didn't request this, you can safely ignore this email.</p>
            <p style='color:#718096;font-size:12px;'>Link: {$resetUrl}</p>
        ";

        return self::send($to, 'Reset Your Password - Akademus', $message);
    }

    public static function sendChatReplyNotification(string $to): bool
    {
        $dashboardUrl = base_url('dashboard');
        $message      = "
            <h2>New Reply from Admissions</h2>
            <p>You have received a reply to your inquiry.</p>
            <p><a href='{$dashboardUrl}'>Open Portal</a></p>
        ";

        return self::send($to, 'New Chat Reply - Akademus', $message);
    }
}