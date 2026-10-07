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

