-- Netvera CRM migration: isolated NEW tables only. Idempotent, no ALTER to orders/payments/users.
CREATE TABLE IF NOT EXISTS nv_public_inquiries (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 source_type ENUM('chat','offer') NOT NULL DEFAULT 'chat',
 product_slug VARCHAR(250) NULL,
 visitor_name VARCHAR(140) NOT NULL,
 visitor_contact VARCHAR(190) NOT NULL,
 inquiry_text TEXT NOT NULL,
 status ENUM('new','open','replied','closed') NOT NULL DEFAULT 'new',
 admin_note TEXT NULL,
 session_hash CHAR(64) NOT NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 INDEX idx_status_created (status,created_at),
 INDEX idx_session (session_hash,id),
 INDEX idx_product (product_slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS nv_public_inquiry_replies (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 inquiry_id BIGINT UNSIGNED NOT NULL,
 sender ENUM('visitor','admin') NOT NULL,
 message TEXT NOT NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 INDEX idx_inquiry_time (inquiry_id,created_at),
 CONSTRAINT fk_nv_inquiry FOREIGN KEY (inquiry_id) REFERENCES nv_public_inquiries(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
