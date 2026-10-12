-- NetVera coupon / discount engine adapted for the unified storefront.
-- Safe to run repeatedly.
CREATE TABLE IF NOT EXISTS discount_campaigns (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(160) NOT NULL,
  code VARCHAR(64) DEFAULT NULL,
  kind ENUM('coupon','automatic') NOT NULL DEFAULT 'coupon',
  discount_type ENUM('percentage','fixed') NOT NULL,
  discount_value DECIMAL(10,2) NOT NULL,
  starts_at DATETIME DEFAULT NULL,
  ends_at DATETIME DEFAULT NULL,
  min_subtotal DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  max_discount DECIMAL(10,2) DEFAULT NULL,
  total_usage_limit INT UNSIGNED DEFAULT NULL,
  per_customer_limit INT UNSIGNED DEFAULT NULL,
  customer_id INT UNSIGNED DEFAULT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  deleted_at DATETIME DEFAULT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_discount_campaign_code (code),
  KEY idx_discount_active (kind,is_active,starts_at,ends_at,deleted_at),
  KEY idx_discount_customer (customer_id,is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS discount_targets (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  campaign_id BIGINT UNSIGNED NOT NULL,
  target_type ENUM('script','service','script_category','service_category','package') NOT NULL,
  target_id INT UNSIGNED NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_discount_target (campaign_id,target_type,target_id),
  KEY idx_discount_target_lookup (target_type,target_id,campaign_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS discount_usages (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  campaign_id BIGINT UNSIGNED NOT NULL,
  order_id BIGINT UNSIGNED NOT NULL,
  customer_id INT UNSIGNED DEFAULT NULL,
  customer_email VARCHAR(190) DEFAULT NULL,
  discount_amount DECIMAL(10,2) NOT NULL,
  final_amount DECIMAL(10,2) NOT NULL,
  used_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_discount_usage_order (order_id),
  KEY idx_discount_usage_campaign (campaign_id,used_at),
  KEY idx_discount_usage_customer (campaign_id,customer_id,customer_email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
