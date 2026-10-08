-- Netvera public catalog only, isolated from YorumHizmeti services and payment.
-- Staging use ONLY. Never DROP or ALTER orders, payments, gateway or users.
CREATE TABLE IF NOT EXISTS nv_legacy_script_categories (
 legacy_id INT UNSIGNED NOT NULL PRIMARY KEY,
 parent_legacy_id INT UNSIGNED NULL,
 slug VARCHAR(220) NOT NULL UNIQUE,
 name VARCHAR(240) NOT NULL,
 description TEXT NULL,
 meta_title VARCHAR(255) NULL,
 meta_description TEXT NULL,
 sort_order INT NOT NULL DEFAULT 0,
 active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS nv_legacy_script_products (
 legacy_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 category_legacy_id INT UNSIGNED NOT NULL,
 slug VARCHAR(250) NOT NULL UNIQUE,
 name VARCHAR(300) NOT NULL,
 short_desc TEXT NULL,
 description LONGTEXT NULL,
 cover_image VARCHAR(500) NULL,
 price DECIMAL(12,2) NOT NULL DEFAULT 0,
 old_price DECIMAL(12,2) NULL,
 badge VARCHAR(70) NULL,
 meta_title VARCHAR(255) NULL,
 meta_description TEXT NULL,
 focus_keyword VARCHAR(255) NULL,
 public_json LONGTEXT NOT NULL,
 sort_order INT NOT NULL DEFAULT 0,
 active TINYINT(1) NOT NULL DEFAULT 1,
 INDEX idx_cat (category_legacy_id),
 INDEX idx_public (active,sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS nv_legacy_script_images (
 legacy_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 product_legacy_id INT UNSIGNED NOT NULL,
 image_path VARCHAR(600) NOT NULL,
 alt_text VARCHAR(300) NULL,
 caption TEXT NULL,
 sort_order INT NOT NULL DEFAULT 0,
 active TINYINT(1) NOT NULL DEFAULT 1,
 INDEX idx_product (product_legacy_id, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS nv_legacy_public_reviews (
 legacy_id INT UNSIGNED NOT NULL PRIMARY KEY,
 product_legacy_id INT UNSIGNED NOT NULL,
 rating TINYINT NOT NULL,
 comment TEXT NOT NULL,
 created_at DATETIME NULL,
 INDEX idx_product (product_legacy_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS nv_legacy_blog_meta (
 blog_post_id INT UNSIGNED NOT NULL PRIMARY KEY,
 original_slug VARCHAR(300) NOT NULL,
 original_author VARCHAR(255) NULL,
 robots VARCHAR(150) NULL,
 focus_keyword VARCHAR(255) NULL,
 secondary_keywords TEXT NULL,
 source_json LONGTEXT NOT NULL,
 INDEX idx_slug (original_slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
