-- PRIVATE CUSTOMER DATA TABLES: create only in isolated staging MySQL.
-- Never commit actual customer rows, license keys or source DB credentials.
CREATE TABLE IF NOT EXISTS nv_private_user_map (
  old_customer_id INT UNSIGNED NOT NULL PRIMARY KEY,
  new_user_id INT UNSIGNED NOT NULL,
  is_agency TINYINT(1) NOT NULL DEFAULT 0,
  want_dealer TINYINT(1) NOT NULL DEFAULT 0,
  source_status VARCHAR(20) NOT NULL,
  imported_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_nv_user_target (new_user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS nv_private_orders (
  old_order_id BIGINT UNSIGNED NOT NULL PRIMARY KEY,
  old_customer_id INT UNSIGNED NULL,
  new_user_id INT UNSIGNED NULL,
  order_no VARCHAR(40) NOT NULL,
  product_id INT UNSIGNED NULL,
  product_name VARCHAR(190) NULL,
  amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  currency VARCHAR(8) NOT NULL DEFAULT 'TRY',
  payment_status VARCHAR(25) NOT NULL,
  order_status VARCHAR(25) NOT NULL,
  order_type VARCHAR(25) NOT NULL,
  entitlement_json LONGTEXT NULL,
  license_key_encrypted TEXT NULL,
  paid_at DATETIME NULL,
  created_at DATETIME NULL,
  imported_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_nv_order_no (order_no),
  KEY idx_nv_order_user (new_user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS nv_private_order_items (
  old_item_id BIGINT UNSIGNED NOT NULL PRIMARY KEY,
  old_order_id BIGINT UNSIGNED NOT NULL,
  item_type VARCHAR(20) NOT NULL,
  item_id INT UNSIGNED NOT NULL,
  item_name VARCHAR(190) NOT NULL,
  item_slug VARCHAR(190) NULL,
  sale_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  entitlement_json LONGTEXT NULL,
  KEY idx_nv_item_order (old_order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS nv_private_affiliates (
  old_affiliate_id BIGINT UNSIGNED NOT NULL PRIMARY KEY,
  old_customer_id INT UNSIGNED NOT NULL,
  new_user_id INT UNSIGNED NULL,
  referral_code VARCHAR(32) NULL,
  status VARCHAR(25) NOT NULL,
  commission_rate DECIMAL(5,2) NULL,
  imported_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY idx_nv_aff_user (new_user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS nv_private_affiliate_commissions (
  old_commission_id BIGINT UNSIGNED NOT NULL PRIMARY KEY,
  old_affiliate_id BIGINT UNSIGNED NOT NULL,
  old_order_id BIGINT UNSIGNED NOT NULL,
  amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  rate DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  status VARCHAR(25) NOT NULL,
  paid_at DATETIME NULL,
  created_at DATETIME NULL,
  KEY idx_nv_commission_affiliate (old_affiliate_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS nv_private_admin_map (
  old_admin_id INT UNSIGNED NOT NULL PRIMARY KEY,
  new_admin_id INT UNSIGNED NOT NULL,
  imported_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
