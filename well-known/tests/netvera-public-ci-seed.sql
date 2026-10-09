-- Only the isolated GitHub Actions DB. Not a production import.
INSERT INTO nv_legacy_script_categories
 (legacy_id,parent_legacy_id,slug,name,description,sort_order,active)
 VALUES
 (11,NULL,'sektorel-yonetim-yazilimlari','Sektörel Yönetim Yazılımları','Test kategorisi',1,1),
 (12,NULL,'dijital-araclar-ve-otomasyon','Dijital Araçlar ve Otomasyon','Test kategorisi',2,1);
INSERT INTO nv_legacy_script_products
 (legacy_id,category_legacy_id,slug,name,short_desc,description,price,old_price,meta_title,meta_description,public_json,sort_order,active)
 VALUES
 (47,11,'haber-sitesi-scripti','NetVera Haber Scripti — QA Örneği','Test amaçlı demo verisi','<p>Test ürün açıklaması</p>',4000,5000,
 'Haber Sitesi Scripti QA','Staging test yazılımı','{"current_version":"1.0.0","is_featured":1,"modules_json":"[]","demo_is_active":0,"demo_is_public":0}',1,1),
 (49,11,'netvera-temizlik-firmasi-script-yazilimi-pro','Temizlik Firması Scripti — QA','Test amaçlı','<p>Test</p>',3000,NULL,
 'Temizlik Scripti QA','Test','{"current_version":"1.0.0","is_featured":0}',2,1),
 (50,11,'netvera-emlak-script-yazilimi-pro','Emlak Scripti — QA','Test amaçlı','<p>Test</p>',3500,NULL,
 'Emlak Scripti QA','Test','{"current_version":"1.0.0","is_featured":1}',3,1);

-- Synthetic approved-review fixtures for safe rating filters. Never real customers.
INSERT INTO nv_legacy_public_reviews
 (legacy_id,product_legacy_id,rating,comment,created_at)
 VALUES
 (801,47,5,'Yalnızca QA otomasyon verisi',NOW()),
 (802,49,4,'Yalnızca QA otomasyon verisi',NOW());

-- QA ONLY: validate the exact legacy format that caused unlabeled values
-- (numeric JSON string list) and opt-in public demo account rendering.
UPDATE nv_legacy_script_products
SET public_json=JSON_SET(
    public_json,
    '$.specs_json','["PHP 8.2 ve üstü","MySQL 8+","Linux / cPanel / LiteSpeed"]',
    '$.demo_url','https://qa-demo.example.test',
    '$.demo_admin_url','https://qa-demo.example.test/yonetim',
    '$.demo_is_active',1,
    '$.demo_is_public',1,
    '$.demo_credentials_public',1,
    '$.demo_username','qa-public-demo-user',
    '$.demo_password','qa-public-demo-only-no-real-account',
    '$.demo_note','Bu yalnızca otomatik test hesabıdır.'
)
WHERE legacy_id=47;
