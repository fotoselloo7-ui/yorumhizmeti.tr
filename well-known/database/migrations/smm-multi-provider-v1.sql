-- SMM white-label integration v1. Safe for existing cPanel databases: additive only.
-- Apply from Admin > Sosyal Medya API; NEVER re-import the main SQL backup.
CREATE TABLE IF NOT EXISTS smm_providers (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(120) NOT NULL,
 endpoint VARCHAR(500) NOT NULL,
 api_key_enc TEXT NOT NULL,
 currency VARCHAR(8) NOT NULL DEFAULT 'USD',
 is_active TINYINT(1) NOT NULL DEFAULT 1,
 last_synced_at DATETIME NULL,
 last_error VARCHAR(255) NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS smm_services (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 provider_id INT UNSIGNED NOT NULL,
 external_service_id VARCHAR(80) NOT NULL,
 category VARCHAR(200) NOT NULL DEFAULT '',
 name VARCHAR(300) NOT NULL DEFAULT '',
 service_type VARCHAR(70) NOT NULL DEFAULT 'Default',
 rate_per_1000 DECIMAL(16,6) NOT NULL DEFAULT 0,
 min_quantity INT UNSIGNED NOT NULL DEFAULT 1,
 max_quantity INT UNSIGNED NOT NULL DEFAULT 1,
 can_refill TINYINT(1) NOT NULL DEFAULT 0,
 can_cancel TINYINT(1) NOT NULL DEFAULT 0,
 is_available TINYINT(1) NOT NULL DEFAULT 1,
 last_seen_at DATETIME NULL,
 UNIQUE KEY uniq_smm_provider_service (provider_id,external_service_id),
 KEY idx_smm_service_category (provider_id,category),
 KEY idx_smm_service_active (is_available)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS smm_package_links (
 package_id INT UNSIGNED NOT NULL PRIMARY KEY,
 service_id INT UNSIGNED NOT NULL,
 fulfillment_quantity INT UNSIGNED NOT NULL,
 field_key VARCHAR(50) NOT NULL DEFAULT 'smm_link',
 enabled TINYINT(1) NOT NULL DEFAULT 1,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 KEY idx_smm_link_service (service_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS smm_order_jobs (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 order_id INT UNSIGNED NOT NULL,
 order_item_id INT UNSIGNED NOT NULL,
 package_id INT UNSIGNED NOT NULL,
 provider_id INT UNSIGNED NOT NULL,
 external_service_id VARCHAR(80) NOT NULL,
 quantity INT UNSIGNED NOT NULL,
 target_link VARCHAR(2048) NOT NULL,
 upstream_order_id VARCHAR(120) NULL,
 state ENUM('queued','sending','submitted','in_progress','completed','partial','cancelled','failed','manual_review') NOT NULL DEFAULT 'queued',
 provider_status VARCHAR(80) NULL,
 remains INT NULL,
 attempts INT UNSIGNED NOT NULL DEFAULT 0,
 last_error VARCHAR(255) NULL,
 last_checked_at DATETIME NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 UNIQUE KEY uniq_smm_order_item (order_item_id),
 KEY idx_smm_order (order_id),
 KEY idx_smm_poll (state,last_checked_at),
 KEY idx_smm_provider (provider_id,upstream_order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS smm_job_events (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 job_id BIGINT UNSIGNED NOT NULL,
 event_type VARCHAR(40) NOT NULL,
 message VARCHAR(255) NOT NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 KEY idx_smm_job_events (job_id,created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
