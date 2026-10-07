-- =====================================================
-- YorumPanel Pro - Veritabanı Kurulum Scripti
-- 24 Tablo - UTF8MB4 - InnoDB
-- =====================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ─── Kullanıcılar ───
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(191) NOT NULL UNIQUE,
    `phone` VARCHAR(20) DEFAULT NULL,
    `password` VARCHAR(255) NOT NULL,
    `status` ENUM('active','inactive','banned') NOT NULL DEFAULT 'active',
    `email_verified_at` DATETIME DEFAULT NULL,
    `last_login_at` DATETIME DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_email` (`email`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Admin Kullanıcıları ───
CREATE TABLE IF NOT EXISTS `admins` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(191) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('super_admin','admin','editor') NOT NULL DEFAULT 'admin',
    `status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
    `must_change_password` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Kategoriler ───
CREATE TABLE IF NOT EXISTS `categories` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `parent_id` INT UNSIGNED DEFAULT NULL,
    `name` VARCHAR(200) NOT NULL,
    `slug` VARCHAR(200) NOT NULL UNIQUE,
    `description` TEXT DEFAULT NULL,
    `image` VARCHAR(500) DEFAULT NULL,
    `image_alt` VARCHAR(255) DEFAULT NULL,
    `icon_key` VARCHAR(50) DEFAULT 'package',
    `sort_order` INT NOT NULL DEFAULT 0,
    `status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
    `seo_title` VARCHAR(200) DEFAULT NULL,
    `seo_description` VARCHAR(500) DEFAULT NULL,
    `seo_focus_keyword` VARCHAR(100) DEFAULT NULL,
    `seo_score` INT DEFAULT 0,
    `canonical_url` VARCHAR(500) DEFAULT NULL,
    `og_title` VARCHAR(200) DEFAULT NULL,
    `og_description` VARCHAR(500) DEFAULT NULL,
    `og_image` VARCHAR(500) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_slug` (`slug`),
    INDEX `idx_parent` (`parent_id`),
    INDEX `idx_status` (`status`),
    INDEX `idx_sort` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Paketler / Ürünler ───
CREATE TABLE IF NOT EXISTS `packages` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `category_id` INT UNSIGNED NOT NULL,
    `name` VARCHAR(300) NOT NULL,
    `slug` VARCHAR(300) NOT NULL UNIQUE,
    `short_description` VARCHAR(500) DEFAULT NULL,
    `description` TEXT DEFAULT NULL,
    `image` VARCHAR(500) DEFAULT NULL,
    `image_alt` VARCHAR(255) DEFAULT NULL,
    `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `discount_price` DECIMAL(10,2) DEFAULT NULL,
    `delivery_time` VARCHAR(100) DEFAULT NULL,
    `min_quantity` INT NOT NULL DEFAULT 1,
    `max_quantity` INT NOT NULL DEFAULT 1,
    `badge` VARCHAR(50) DEFAULT NULL,
    `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
    `status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
    `sort_order` INT NOT NULL DEFAULT 0,
    `seo_title` VARCHAR(200) DEFAULT NULL,
    `seo_description` VARCHAR(500) DEFAULT NULL,
    `seo_focus_keyword` VARCHAR(100) DEFAULT NULL,
    `seo_score` INT DEFAULT 0,
    `canonical_url` VARCHAR(500) DEFAULT NULL,
    `og_title` VARCHAR(200) DEFAULT NULL,
    `og_description` VARCHAR(500) DEFAULT NULL,
    `og_image` VARCHAR(500) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_slug` (`slug`),
    INDEX `idx_category` (`category_id`),
    INDEX `idx_status` (`status`),
    INDEX `idx_sort` (`sort_order`),
    INDEX `idx_price` (`price`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Paket Dinamik Alanları ───
CREATE TABLE IF NOT EXISTS `package_fields` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `package_id` INT UNSIGNED NOT NULL,
    `field_key` VARCHAR(50) NOT NULL,
    `field_label` VARCHAR(100) NOT NULL,
    `field_type` ENUM('text','textarea','url','email','number','select') NOT NULL DEFAULT 'text',
    `is_required` TINYINT(1) NOT NULL DEFAULT 1,
    `placeholder` VARCHAR(200) DEFAULT NULL,
    `sort_order` INT NOT NULL DEFAULT 0,
    INDEX `idx_package` (`package_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Siparişler ───
CREATE TABLE IF NOT EXISTS `orders` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `order_number` VARCHAR(20) NOT NULL UNIQUE,
    `total_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `payment_method` VARCHAR(50) DEFAULT NULL,
    `payment_gateway` VARCHAR(50) DEFAULT NULL,
    `payment_status` ENUM('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending',
    `order_status` ENUM('payment_pending','paid','preparing','processing','completed','cancelled','refunded','waiting_customer_info') NOT NULL DEFAULT 'payment_pending',
    `customer_note` TEXT DEFAULT NULL,
    `admin_note` TEXT DEFAULT NULL,
    `customer_visible_note` TEXT DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_user` (`user_id`),
    INDEX `idx_order_number` (`order_number`),
    INDEX `idx_status` (`order_status`),
    INDEX `idx_payment` (`payment_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Sipariş Kalemleri ───
CREATE TABLE IF NOT EXISTS `order_items` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT UNSIGNED NOT NULL,
    `package_id` INT UNSIGNED DEFAULT NULL,
    `package_name` VARCHAR(300) NOT NULL,
    `quantity` INT NOT NULL DEFAULT 1,
    `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `total` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_order` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Sipariş Alanları (Dinamik) ───
CREATE TABLE IF NOT EXISTS `order_fields` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT UNSIGNED NOT NULL,
    `order_item_id` INT UNSIGNED DEFAULT NULL,
    `field_key` VARCHAR(50) NOT NULL,
    `field_label` VARCHAR(100) NOT NULL,
    `field_value` TEXT DEFAULT NULL,
    INDEX `idx_order` (`order_id`),
    INDEX `idx_item` (`order_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Sipariş Durum Logları ───
CREATE TABLE IF NOT EXISTS `order_status_logs` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT UNSIGNED NOT NULL,
    `old_status` VARCHAR(50) DEFAULT NULL,
    `new_status` VARCHAR(50) NOT NULL,
    `note` TEXT DEFAULT NULL,
    `created_by` VARCHAR(50) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_order` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Ödemeler ───
CREATE TABLE IF NOT EXISTS `payments` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT UNSIGNED NOT NULL,
    `gateway_key` VARCHAR(50) NOT NULL,
    `transaction_id` VARCHAR(200) DEFAULT NULL,
    `amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `currency` VARCHAR(3) NOT NULL DEFAULT 'TRY',
    `status` ENUM('pending','completed','failed','refunded') NOT NULL DEFAULT 'pending',
    `raw_response` TEXT DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_order` (`order_id`),
    INDEX `idx_gateway` (`gateway_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Ödeme Modülleri ───
CREATE TABLE IF NOT EXISTS `payment_gateways` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `gateway_key` VARCHAR(50) NOT NULL UNIQUE,
    `name` VARCHAR(100) NOT NULL,
    `description` VARCHAR(500) DEFAULT NULL,
    `type` ENUM('online','manual') NOT NULL DEFAULT 'online',
    `is_active` TINYINT(1) NOT NULL DEFAULT 0,
    `is_default` TINYINT(1) NOT NULL DEFAULT 0,
    `settings` JSON DEFAULT NULL,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Banka Hesapları ───
CREATE TABLE IF NOT EXISTS `bank_accounts` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `bank_name` VARCHAR(100) NOT NULL,
    `account_holder` VARCHAR(200) NOT NULL,
    `iban` VARCHAR(50) NOT NULL,
    `branch` VARCHAR(100) DEFAULT NULL,
    `description` TEXT DEFAULT NULL,
    `status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Havale Bildirimleri ───
CREATE TABLE IF NOT EXISTS `bank_transfer_notifications` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `bank_name` VARCHAR(100) NOT NULL,
    `sender_name` VARCHAR(200) NOT NULL,
    `amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `receipt_file` VARCHAR(500) DEFAULT NULL,
    `note` TEXT DEFAULT NULL,
    `status` ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    `admin_note` TEXT DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_order` (`order_id`),
    INDEX `idx_user` (`user_id`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Destek Talepleri ───
CREATE TABLE IF NOT EXISTS `support_tickets` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `order_id` INT UNSIGNED DEFAULT NULL,
    `ticket_number` VARCHAR(20) NOT NULL UNIQUE,
    `subject` VARCHAR(300) NOT NULL,
    `priority` ENUM('low','medium','high','urgent') NOT NULL DEFAULT 'medium',
    `status` ENUM('open','answered','customer_reply','closed') NOT NULL DEFAULT 'open',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_user` (`user_id`),
    INDEX `idx_status` (`status`),
    INDEX `idx_ticket` (`ticket_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Destek Mesajları ───
CREATE TABLE IF NOT EXISTS `support_messages` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `ticket_id` INT UNSIGNED NOT NULL,
    `sender_type` ENUM('user','admin') NOT NULL,
    `sender_id` INT UNSIGNED NOT NULL,
    `message` TEXT NOT NULL,
    `attachment` VARCHAR(500) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_ticket` (`ticket_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Sayfalar ───
CREATE TABLE IF NOT EXISTS `pages` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(300) NOT NULL,
    `slug` VARCHAR(300) NOT NULL UNIQUE,
    `content` LONGTEXT DEFAULT NULL,
    `status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
    `seo_title` VARCHAR(200) DEFAULT NULL,
    `seo_description` VARCHAR(500) DEFAULT NULL,
    `seo_focus_keyword` VARCHAR(100) DEFAULT NULL,
    `seo_score` INT DEFAULT 0,
    `canonical_url` VARCHAR(500) DEFAULT NULL,
    `og_title` VARCHAR(200) DEFAULT NULL,
    `og_description` VARCHAR(500) DEFAULT NULL,
    `og_image` VARCHAR(500) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_slug` (`slug`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Blog Kategorileri ───
CREATE TABLE IF NOT EXISTS `blog_categories` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(200) NOT NULL,
    `slug` VARCHAR(200) NOT NULL UNIQUE,
    `description` TEXT DEFAULT NULL,
    `image` VARCHAR(500) DEFAULT NULL,
    `image_alt` VARCHAR(255) DEFAULT NULL,
    `icon_key` VARCHAR(50) DEFAULT 'file-text',
    `status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
    `sort_order` INT NOT NULL DEFAULT 0,
    `seo_title` VARCHAR(200) DEFAULT NULL,
    `seo_description` VARCHAR(500) DEFAULT NULL,
    `seo_focus_keyword` VARCHAR(100) DEFAULT NULL,
    `seo_score` INT DEFAULT 0,
    `canonical_url` VARCHAR(500) DEFAULT NULL,
    `og_title` VARCHAR(200) DEFAULT NULL,
    `og_description` VARCHAR(500) DEFAULT NULL,
    `og_image` VARCHAR(500) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_slug` (`slug`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Blog Yazıları ───
CREATE TABLE IF NOT EXISTS `blog_posts` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `blog_category_id` INT UNSIGNED DEFAULT NULL,
    `title` VARCHAR(300) NOT NULL,
    `slug` VARCHAR(300) NOT NULL UNIQUE,
    `excerpt` TEXT DEFAULT NULL,
    `content` LONGTEXT DEFAULT NULL,
    `image` VARCHAR(500) DEFAULT NULL,
    `image_alt` VARCHAR(255) DEFAULT NULL,
    `status` ENUM('active','inactive','draft') NOT NULL DEFAULT 'draft',
    `published_at` DATETIME DEFAULT NULL,
    `seo_title` VARCHAR(200) DEFAULT NULL,
    `seo_description` VARCHAR(500) DEFAULT NULL,
    `seo_focus_keyword` VARCHAR(100) DEFAULT NULL,
    `seo_score` INT DEFAULT 0,
    `canonical_url` VARCHAR(500) DEFAULT NULL,
    `og_title` VARCHAR(200) DEFAULT NULL,
    `og_description` VARCHAR(500) DEFAULT NULL,
    `og_image` VARCHAR(500) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_slug` (`slug`),
    INDEX `idx_category` (`blog_category_id`),
    INDEX `idx_status` (`status`),
    INDEX `idx_published` (`published_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── SSS ───
CREATE TABLE IF NOT EXISTS `faqs` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `question` VARCHAR(500) NOT NULL,
    `answer` TEXT NOT NULL,
    `sort_order` INT NOT NULL DEFAULT 0,
    `status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_status` (`status`),
    INDEX `idx_sort` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Site Ayarları ───
CREATE TABLE IF NOT EXISTS `settings` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `setting_key` VARCHAR(100) NOT NULL UNIQUE,
    `setting_value` TEXT DEFAULT NULL,
    `setting_group` VARCHAR(50) NOT NULL DEFAULT 'general',
    INDEX `idx_key` (`setting_key`),
    INDEX `idx_group` (`setting_group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Mail Şablonları ───
CREATE TABLE IF NOT EXISTS `email_templates` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `template_key` VARCHAR(100) NOT NULL UNIQUE,
    `name` VARCHAR(200) NOT NULL,
    `subject` VARCHAR(300) NOT NULL,
    `body` TEXT NOT NULL,
    `variables` TEXT DEFAULT NULL,
    `status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Aktivite Logları ───
CREATE TABLE IF NOT EXISTS `activity_logs` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `admin_id` INT UNSIGNED DEFAULT NULL,
    `action` VARCHAR(100) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `user_agent` VARCHAR(500) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_admin` (`admin_id`),
    INDEX `idx_action` (`action`),
    INDEX `idx_date` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Şifre Sıfırlama ───
CREATE TABLE IF NOT EXISTS `password_resets` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(191) NOT NULL,
    `token` VARCHAR(100) NOT NULL,
    `type` ENUM('user','admin') NOT NULL DEFAULT 'user',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_email` (`email`),
    INDEX `idx_token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── SEO Redirect ───
CREATE TABLE IF NOT EXISTS `seo_redirects` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `old_url` VARCHAR(500) NOT NULL,
    `new_url` VARCHAR(500) NOT NULL,
    `status_code` INT NOT NULL DEFAULT 301,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_old_url` (`old_url`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Ana Sayfa Bölümleri ───
CREATE TABLE IF NOT EXISTS `home_sections` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `section_key` VARCHAR(50) NOT NULL UNIQUE,
    `title` VARCHAR(255) DEFAULT NULL,
    `subtitle` VARCHAR(500) DEFAULT NULL,
    `content` TEXT DEFAULT NULL,
    `button_text` VARCHAR(100) DEFAULT NULL,
    `button_url` VARCHAR(255) DEFAULT NULL,
    `image` VARCHAR(255) DEFAULT NULL,
    `image_alt` VARCHAR(255) DEFAULT NULL,
    `icon_key` VARCHAR(50) DEFAULT 'package',
    `sort_order` INT DEFAULT 0,
    `status` ENUM('active','inactive') DEFAULT 'active',
    `seo_focus_keyword` VARCHAR(100) DEFAULT NULL,
    `extra_data` JSON DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
-- Gelişmiş Blog Sistemi Migration

-- 1. `blog_posts` tablosuna eksik sütunların eklenmesi
SET @dbname = DATABASE();

-- faqs
SET @tablename = 'blog_posts';
SET @columnname = 'faqs';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = @tablename AND table_schema = @dbname AND column_name = @columnname) > 0,
  'SELECT 1',
  'ALTER TABLE blog_posts ADD COLUMN faqs JSON DEFAULT NULL AFTER content'
));
PREPARE addColumn FROM @preparedStatement;
EXECUTE addColumn;
DEALLOCATE PREPARE addColumn;

-- schema_type
SET @columnname = 'schema_type';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = @tablename AND table_schema = @dbname AND column_name = @columnname) > 0,
  'SELECT 1',
  'ALTER TABLE blog_posts ADD COLUMN schema_type VARCHAR(50) DEFAULT ''Article'' AFTER faqs'
));
PREPARE addColumn FROM @preparedStatement;
EXECUTE addColumn;
DEALLOCATE PREPARE addColumn;

-- reading_time
SET @columnname = 'reading_time';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = @tablename AND table_schema = @dbname AND column_name = @columnname) > 0,
  'SELECT 1',
  'ALTER TABLE blog_posts ADD COLUMN reading_time INT DEFAULT 1 AFTER schema_type'
));
PREPARE addColumn FROM @preparedStatement;
EXECUTE addColumn;
DEALLOCATE PREPARE addColumn;

-- noindex
SET @columnname = 'noindex';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = @tablename AND table_schema = @dbname AND column_name = @columnname) > 0,
  'SELECT 1',
  'ALTER TABLE blog_posts ADD COLUMN noindex TINYINT(1) DEFAULT 0 AFTER reading_time'
));
PREPARE addColumn FROM @preparedStatement;
EXECUTE addColumn;
DEALLOCATE PREPARE addColumn;

-- views
SET @columnname = 'views';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = @tablename AND table_schema = @dbname AND column_name = @columnname) > 0,
  'SELECT 1',
  'ALTER TABLE blog_posts ADD COLUMN views INT DEFAULT 0 AFTER noindex'
));
PREPARE addColumn FROM @preparedStatement;
EXECUTE addColumn;
DEALLOCATE PREPARE addColumn;

-- update status enum to include 'scheduled' if it does not exist
-- First check current enum values. If we can't easily check, we can just ALTER the column safely.
ALTER TABLE `blog_posts` MODIFY COLUMN `status` ENUM('active','inactive','draft','scheduled') NOT NULL DEFAULT 'draft';


-- 2. `blog_tags` tablosu
CREATE TABLE IF NOT EXISTS `blog_tags` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- 3. `blog_post_tags` pivot tablosu
CREATE TABLE IF NOT EXISTS `blog_post_tags` (
    `post_id` INT UNSIGNED NOT NULL,
    `tag_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`post_id`, `tag_id`),
    INDEX `idx_post_id` (`post_id`),
    INDEX `idx_tag_id` (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


