-- NetVera inquiry v2. Run on installations that already applied v1.
SET @nv_add_important = (
 SELECT IF(COUNT(*)=0,
   'ALTER TABLE nv_public_inquiries ADD COLUMN is_important TINYINT(1) NOT NULL DEFAULT 0 AFTER admin_note',
   'SELECT 1')
 FROM information_schema.COLUMNS
 WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='nv_public_inquiries'
   AND COLUMN_NAME='is_important'
);
PREPARE nv_stmt FROM @nv_add_important;
EXECUTE nv_stmt;
DEALLOCATE PREPARE nv_stmt;
