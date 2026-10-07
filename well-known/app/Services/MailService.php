<?php
namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Core\Database;

class MailService
{
    public static function send(string $to, string $subject, string $body): bool
    {
        try {
            $config = SiteConfigService::getInstance();

            $host = $config->get('smtp_host');
            $port = (int) $config->get('smtp_port', '587');
            $username = $config->get('smtp_username');
            $password = $config->get('smtp_password');
            $encryption = $config->get('smtp_encryption', 'tls');
            $fromEmail = $config->get('smtp_from_email', 'noreply@yorumhizmeti.tr');
            $fromName = $config->get('smtp_from_name', 'Yorum Hizmeti');

            if (empty($host) || empty($username) || empty($password)) {
                self::log('SMTP ayarları eksik. Mail gönderilmedi: ' . $to);
                return false;
            }

            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = $host;
            $mail->SMTPAuth = true;
            $mail->Username = $username;
            $mail->Password = $password;
            $mail->SMTPSecure = $encryption;
            $mail->Port = $port;
            $mail->CharSet = 'UTF-8';

            $mail->setFrom($fromEmail, $fromName);
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = self::wrapInTemplate($body, $config);

            $mail->send();
            return true;
        } catch (Exception $e) {
            self::log('Mail gönderim hatası: ' . $e->getMessage() . ' | To: ' . $to);
            return false;
        }
    }

    /**
     * Şablon kullanarak mail gönder
     */
    public static function sendTemplate(string $templateKey, string $to, array $variables = []): bool
    {
        try {
            $db = Database::getInstance();
            $template = $db->fetch("SELECT * FROM email_templates WHERE template_key = ? AND status = 'active'", [$templateKey]);

            if (!$template) {
                self::log("Mail şablonu bulunamadı: {$templateKey}");
                return false;
            }

            $subject = self::replaceVariables($template['subject'], $variables);
            $body = self::replaceVariables($template['body'], $variables);

            return self::send($to, $subject, $body);
        } catch (\Exception $e) {
            self::log('Mail şablon hatası: ' . $e->getMessage());
            return false;
        }
    }

    private static function replaceVariables(string $text, array $variables): string
    {
        foreach ($variables as $key => $value) {
            $text = str_replace('{{' . $key . '}}', $value, $text);
        }
        // site_name varsayılan
        $text = str_replace('{{site_name}}', setting('site_name', 'Yorum Hizmeti'), $text);
        return $text;
    }

    private static function wrapInTemplate(string $body, SiteConfigService $config): string
    {
        $siteName = $config->get('site_name', 'Yorum Hizmeti');
        $siteUrl = $config->get('site_url', '');

        return '<!DOCTYPE html><html><head><meta charset="UTF-8"></head>
        <body style="margin:0;padding:0;background:#f8fafc;font-family:Arial,sans-serif;">
        <div style="max-width:600px;margin:0 auto;padding:24px;">
        <div style="background:#0F172A;padding:24px;text-align:center;border-radius:12px 12px 0 0;">
        <h1 style="color:#fff;margin:0;font-size:20px;">' . htmlspecialchars($siteName) . '</h1>
        </div>
        <div style="background:#fff;padding:32px;border:1px solid #E5E7EB;border-top:0;">
        ' . $body . '
        </div>
        <div style="background:#f1f5f9;padding:16px;text-align:center;border-radius:0 0 12px 12px;border:1px solid #E5E7EB;border-top:0;">
        <p style="margin:0;color:#64748B;font-size:12px;">' . htmlspecialchars($siteName) . ' | <a href="' . $siteUrl . '" style="color:#2563EB;">' . $siteUrl . '</a></p>
        </div>
        </div></body></html>';
    }

    private static function log(string $message): void
    {
        $logDir = BASE_PATH . '/storage/logs';
        if (!is_dir($logDir)) mkdir($logDir, 0755, true);
        $logFile = $logDir . '/mail_' . date('Y-m-d') . '.log';
        file_put_contents($logFile, '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL, FILE_APPEND);
    }
}
