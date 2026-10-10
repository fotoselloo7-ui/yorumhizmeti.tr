-- NetVera imported software catalogue: remove obsolete NEW-PROJECT internal links.
-- The site URL /kategori/hazir-yazilim-scriptleri was never a migrated
-- indexed NetVera URL. Only this exact old local path is rewritten.
-- No products, category IDs, orders, payment records, or imported /hazir-scriptler/*
-- URLs are changed. This migration is repeatable.
-- Back up the database before applying to a deployed environment.

START TRANSACTION;

UPDATE `settings`
SET `setting_value` = REPLACE(`setting_value`,
    '/kategori/hazir-yazilim-scriptleri', '/hazir-scriptler')
WHERE `setting_value` LIKE '%/kategori/hazir-yazilim-scriptleri%';

UPDATE `home_sections`
SET `button_url` = REPLACE(`button_url`,
    '/kategori/hazir-yazilim-scriptleri', '/hazir-scriptler')
WHERE `button_url` LIKE '%/kategori/hazir-yazilim-scriptleri%';

UPDATE `home_sections`
SET `content` = REPLACE(`content`,
    '/kategori/hazir-yazilim-scriptleri', '/hazir-scriptler')
WHERE `content` LIKE '%/kategori/hazir-yazilim-scriptleri%';

UPDATE `home_sections`
SET `extra_data` = REPLACE(CAST(`extra_data` AS CHAR),
    '/kategori/hazir-yazilim-scriptleri', '/hazir-scriptler')
WHERE CAST(`extra_data` AS CHAR) LIKE '%/kategori/hazir-yazilim-scriptleri%';

UPDATE `pages`
SET `content` = REPLACE(`content`,
    '/kategori/hazir-yazilim-scriptleri', '/hazir-scriptler')
WHERE `content` LIKE '%/kategori/hazir-yazilim-scriptleri%';

UPDATE `pages`
SET `canonical_url` = REPLACE(`canonical_url`,
    '/kategori/hazir-yazilim-scriptleri', '/hazir-scriptler')
WHERE `canonical_url` LIKE '%/kategori/hazir-yazilim-scriptleri%';

UPDATE `blog_posts`
SET `content` = REPLACE(`content`,
    '/kategori/hazir-yazilim-scriptleri', '/hazir-scriptler')
WHERE `content` LIKE '%/kategori/hazir-yazilim-scriptleri%';

UPDATE `blog_posts`
SET `canonical_url` = REPLACE(`canonical_url`,
    '/kategori/hazir-yazilim-scriptleri', '/hazir-scriptler')
WHERE `canonical_url` LIKE '%/kategori/hazir-yazilim-scriptleri%';

UPDATE `packages`
SET `short_description` = REPLACE(`short_description`,
    '/kategori/hazir-yazilim-scriptleri', '/hazir-scriptler')
WHERE `short_description` LIKE '%/kategori/hazir-yazilim-scriptleri%';

UPDATE `packages`
SET `description` = REPLACE(`description`,
    '/kategori/hazir-yazilim-scriptleri', '/hazir-scriptler')
WHERE `description` LIKE '%/kategori/hazir-yazilim-scriptleri%';

UPDATE `packages`
SET `canonical_url` = REPLACE(`canonical_url`,
    '/kategori/hazir-yazilim-scriptleri', '/hazir-scriptler')
WHERE `canonical_url` LIKE '%/kategori/hazir-yazilim-scriptleri%';

COMMIT;
