-- Akıllı Editör Veritabanı Güncellemesi
SET @dbname = DATABASE();

SET @tablename = 'blog_posts';

-- raw_content
SET @columnname = 'raw_content';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = @tablename AND table_schema = @dbname AND column_name = @columnname) > 0,
  'SELECT 1',
  'ALTER TABLE blog_posts ADD COLUMN raw_content LONGTEXT DEFAULT NULL AFTER content'
));
PREPARE addColumn FROM @preparedStatement;
EXECUTE addColumn;
DEALLOCATE PREPARE addColumn;

-- toc
SET @columnname = 'toc';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = @tablename AND table_schema = @dbname AND column_name = @columnname) > 0,
  'SELECT 1',
  'ALTER TABLE blog_posts ADD COLUMN toc JSON DEFAULT NULL AFTER faqs'
));
PREPARE addColumn FROM @preparedStatement;
EXECUTE addColumn;
DEALLOCATE PREPARE addColumn;
