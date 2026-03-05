<?php

namespace App\Services;

// use this for emails
// EmailService::sendDocumentReceived($userEmail);
// EmailService::sendApplicationApproved($userEmail);
// EmailService::sendApplicationDeclined($userEmail);
// EmailService::sendChatReplyNotification($userEmail);

class EmailService {
    private static function send($to, $subject, $message) {
        $email = \Config\Services::email();

        $email->setFrom('yourgmail@gmail.com', 'Akademus Portal');
        $email->setTo($to);
        $email->setSubject($subject);

        $email->setMailType('html');

        $email->setMessage($message);

        if (!$email->send()) {
            log_message('error', $email->printDebugger(['headers']));
            return false;
        }

        return true;
    }

    public static function sendWelcomeEmail($email, $tempPassword) {
        $message = "
        <h2>Welcome to Akademus Portal </h2>
        
        <p>Your account has been successfully created.</p>
        
        <p><b>Login Portal:</b> ".base_url('sign_in')."</p

        <p><b>Email:</b> {$email}</p>
        <p><b>Temporary Password:</b> {$tempPassword}</p>
        
        <p>Please login</p>";

        return self::send($email, "Welcome to Akademus Portal", $message);
    }

    public static function sendDocumentReceived($email) {
        $message = "
        <h2>Document Received</h2>
        
        <p>Your uploaded document has been successfully received.</p>
        
        <p>ADMISSIONS will review review it shortly.</p>
        
        <p>Thank you and have a great day!</p>";

        return self::send($email, "Document Received - Akademus", $message);
    }

    public static function sendApplicationApproved($email) {
        $message = "
        <h2>Application Approved</h2>

        <p>Congratulations!</p>

        <p>Your application has been approved.</p>

        <p>You may now proceed with the enrollment process.</p>
        ";

        return self::send($email, "Application Approved", $message);
    }

    public static function sendApplicationDeclined($email) {
        $message = "
        <h2>Application Status Update</h2>

        <p>We regret to inform you that your application was not approved.</p>

        <p>If you have questions, please contact admissions.</p>
        ";

        return self::send($email, "Application Status Update", $message);
    }

    public static function sendChatReplyNotification($email) {
        $message = "
        <h2>New Reply from Admissions</h2>

        <p>You have received a reply to your inquiry.</p>

        <p>Please login to your portal to view the message.</p>

        <p><a href='".base_url('dashboard')."'>Open Portal</a></p>
        ";
        
        return self::send($email, "New Chat Reply", $message);
    }  
}