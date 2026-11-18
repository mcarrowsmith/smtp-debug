<?php

namespace McArrowsmithPackages\SmtpDebug;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class SmtpDebugger
{
    /**
     * Send a test email and stream PHPMailer's SMTP debug output.
     *
     * @param string        $encryption 'tls' (STARTTLS), 'ssl' (SMTPS) or 'none'
     * @param int           $debugLevel 0 = off, 1 = commands, 2 = commands + data, 3/4 = extremely detailed
     * @param callable|null $output     Receives each line of output; defaults to echo
     */
    public static function send(
        string $host,
        int $port,
        string $username,
        string $password,
        string $from,
        string $to,
        string $encryption = 'tls',
        int $debugLevel = 3,
        ?callable $output = null
    ): bool {
        $output ??= function (string $line) {
            echo $line . PHP_EOL;
        };

        $mail = new PHPMailer(true);

        try {
            $mail->SMTPDebug = $debugLevel;
            $mail->Debugoutput = function ($str, $level) use ($output) {
                $output("Debug level $level; message: " . trim($str));
            };

            // SMTP configuration
            $mail->isSMTP();
            $mail->Host     = $host;
            $mail->Port     = $port;
            $mail->SMTPAuth = $username !== '';
            $mail->Username = $username;
            $mail->Password = $password;

            switch (strtolower($encryption)) {
                case 'ssl':
                case 'smtps':
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    break;
                case 'none':
                case '':
                    $mail->SMTPSecure = '';
                    $mail->SMTPAutoTLS = false;
                    break;
                default:
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            }

            // Email headers
            $mail->setFrom($from, 'SMTP Test');
            $mail->addAddress($to, 'Test User');

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'SMTP Test Email';
            $mail->Body    = 'This is a test email to confirm SMTP connection works.';

            $mail->send();
            $output('✅ Email has been sent successfully!');

            return true;
        } catch (Exception $e) {
            $output("❌ Mailer Error: {$mail->ErrorInfo}");

            return false;
        }
    }
}
