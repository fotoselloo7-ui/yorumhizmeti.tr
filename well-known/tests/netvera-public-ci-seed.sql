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
 'Haber Sitesi Scripti QA','Staging test yazılımı','{"current_version":"1.0.0","modules_json":"[]","demo_is_active":0,"demo_is_public":0}',1,1),
 (49,11,'netvera-temizlik-firmasi-script-yazilimi-pro','Temizlik Firması Scripti — QA','Test amaçlı','<p>Test</p>',3000,NULL,
 'Temizlik Scripti QA','Test','{"current_version":"1.0.0"}',2,1),
 (50,11,'netvera-emlak-script-yazilimi-pro','Emlak Scripti — QA','Test amaçlı','<p>Test</p>',3500,NULL,
 'Emlak Scripti QA','Test','{"current_version":"1.0.0"}',3,1);

-- Synthetic approved-review fixtures for safe rating filters. Never real customers.
INSERT INTO nv_legacy_public_reviews
 (legacy_id,product_legacy_id,rating,comment,created_at)
 VALUES
 (801,47,5,'Yalnızca QA otomasyon verisi',NOW()),
 (802,49,4,'Yalnızca QA otomasyon verisi',NOW());
