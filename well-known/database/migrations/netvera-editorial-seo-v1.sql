CREATE TABLE IF NOT EXISTS nv_editorial_seo (
 entity_type VARCHAR(30) NOT NULL,
 entity_id INT UNSIGNED NOT NULL,
 extra_json LONGTEXT NOT NULL,
 PRIMARY KEY(entity_type,entity_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
