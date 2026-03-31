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
        $message  = "
            <h2>Welcome to Akademus Portal</h2>
            <p>Your account has been successfully created.</p>
            <p><b>Login Portal:</b> <a href='{$loginUrl}'>{$loginUrl}</a></p>
            <p><b>Email:</b> {$to}</p>
            <p><b>Temporary Password:</b> {$tempPassword}</p>
            <p>Please login and change your password immediately.</p>
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