<?php
namespace App\Services;

use App\Core\Database;

/**
 * Small, persistent inbox and opt-in registry.
 * The idempotent CREATE TABLE guards existing local installations.
 * Production can also pre-run database/migration_contact_newsletter.sql.
 */
final class LeadCaptureService
{
    public static function saveContact(string $name, string $email, string $subject, string $body): void
    {
        $db = Database::getInstance();
        $db->query("CREATE TABLE IF NOT EXISTS contact_messages (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(120) NOT NULL,
            email VARCHAR(190) NOT NULL,
            subject VARCHAR(190) NOT NULL,
            body TEXT NOT NULL,
            status ENUM('new','read','archived') NOT NULL DEFAULT 'new',
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_contact_created (created_at),
            INDEX idx_contact_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $db->insert('contact_messages', [
            'name' => $name,
            'email' => $email,
            'subject' => $subject,
            'body' => $body,
        ]);
    }

    public static function subscribe(string $email, string $source): void
    {
        $db = Database::getInstance();
        $db->query("CREATE TABLE IF NOT EXISTS newsletter_subscribers (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(190) NOT NULL,
            source VARCHAR(32) NOT NULL DEFAULT 'footer',
            consent_at DATETIME NOT NULL,
            status ENUM('active','unsubscribed') NOT NULL DEFAULT 'active',
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uniq_newsletter_email (email),
            INDEX idx_newsletter_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $db->query("INSERT INTO newsletter_subscribers (email, source, consent_at, status)
                    VALUES (?, ?, NOW(), 'active')
                    ON DUPLICATE KEY UPDATE source = VALUES(source),
                    consent_at = VALUES(consent_at), status = 'active'",
                    [$email, $source]);
    }
}
