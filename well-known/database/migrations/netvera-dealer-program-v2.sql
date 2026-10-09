-- NetVera dealer v2: new operational application/review tables, no changes to native orders.
-- Import source affiliate history separately. NEVER copy private credentials to this SQL.
CREATE TABLE IF NOT EXISTS nv_dealer_accounts (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 user_id INT UNSIGNED NOT NULL,
 legacy_affiliate_id BIGINT UNSIGNED NULL,
 referral_code VARCHAR(32) NOT NULL,
 status ENUM('pending','approved','suspended','rejected') NOT NULL DEFAULT 'pending',
 tier ENUM('starter','pro','agency') NOT NULL DEFAULT 'starter',
 commission_rate DECIMAL(5,2) NOT NULL DEFAULT 0.00,
 note VARCHAR(500) NOT NULL DEFAULT '',
 reviewed_by INT UNSIGNED NULL,
 reviewed_at DATETIME NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 UNIQUE KEY uq_nvdealer_user (user_id),
 UNIQUE KEY uq_nvdealer_code (referral_code),
 UNIQUE KEY uq_nvdealer_legacy (legacy_affiliate_id),
 KEY idx_nvdealer_status (status,created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS nv_dealer_audit (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 dealer_id BIGINT UNSIGNED NOT NULL,
 actor_type ENUM('customer','admin','system') NOT NULL,
 actor_id INT UNSIGNED NOT NULL,
 action VARCHAR(40) NOT NULL,
 old_status VARCHAR(20) NULL,
 new_status VARCHAR(20) NULL,
 details VARCHAR(255) NOT NULL DEFAULT '',
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 KEY idx_nvdealer_audit (dealer_id,created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
-- Referral clicks are anonymous, not paid sales. No orders are attributed automatically.
CREATE TABLE IF NOT EXISTS nv_dealer_referral_events (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 dealer_id BIGINT UNSIGNED NOT NULL,
 day DATE NOT NULL,
 visitor_hash CHAR(64) NOT NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY uq_nvdealer_visit (dealer_id,day,visitor_hash),
 KEY idx_nvdealer_event_day (dealer_id,day)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
