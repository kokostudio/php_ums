<?php

namespace App\Core;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    public static function send(string $to, string $subject, string $body, bool $isHtml = true): bool
    {
        $mailerType = Config::get('MAIL_MAILER', 'smtp');

        // Log Mailer for local testing
        if ($mailerType === 'log') {
            return self::logEmail($to, $subject, $body, $isHtml);
        }

        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = Config::get('MAIL_HOST', 'localhost');
            
            $username = Config::get('MAIL_USERNAME');
            if ($username && $username !== 'null') {
                $mail->SMTPAuth = true;
                $mail->Username = $username;
                $mail->Password = Config::get('MAIL_PASSWORD');
            } else {
                $mail->SMTPAuth = false;
            }
            
            $mail->Port = Config::get('MAIL_PORT', 25);
            
            $encryption = Config::get('MAIL_ENCRYPTION');
            if ($encryption && $encryption !== 'null') {
                $mail->SMTPSecure = $encryption;
            }

            // Recipients
            $mail->setFrom(Config::get('MAIL_FROM_ADDRESS', 'no-reply@example.com'), Config::get('MAIL_FROM_NAME', 'System'));
            $mail->addAddress($to);

            // Content
            $mail->isHTML($isHtml);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $mail->send();
            return true;
        } catch (Exception $e) {
            Logger::activity("Email failed to {$to}: {$mail->ErrorInfo}");
            return false;
        }
    }

    private static function logEmail(string $to, string $subject, string $body, bool $isHtml): bool
    {
        $logDir = __DIR__ . '/../../storage/logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $logFile = $logDir . '/emails.log';
        $timestamp = date('Y-m-d H:i:s');
        $format = $isHtml ? 'HTML' : 'TEXT';

        $content = "=========================================================\n";
        $content .= "Time    : {$timestamp}\n";
        $content .= "To      : {$to}\n";
        $content .= "Subject : {$subject}\n";
        $content .= "Format  : {$format}\n";
        $content .= "---------------------------------------------------------\n";
        $content .= "{$body}\n";
        $content .= "=========================================================\n\n";

        if (file_put_contents($logFile, $content, FILE_APPEND) !== false) {
            Logger::activity("Email logged locally instead of sending to {$to}");
            return true;
        }

        return false;
    }
}
