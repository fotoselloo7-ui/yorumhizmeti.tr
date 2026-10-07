-- =====================================================
-- Migration: is_featured + home_sections
-- Bu dosyayı mevcut veritabanında çalıştırın.
-- =====================================================

-- packages tablosuna is_featured sütunu ekle
ALTER TABLE `packages` ADD COLUMN `is_featured` TINYINT(1) NOT NULL DEFAULT 0 AFTER `badge`;

-- Mevcut paketleri öne çıkan yap
UPDATE `packages` SET `is_featured` = 1;

-- home_sections tablosunu oluştur (yoksa)
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

-- Home sections seed data
INSERT IGNORE INTO `home_sections` (`section_key`, `title`, `subtitle`, `content`, `button_text`, `button_url`, `icon_key`, `sort_order`, `status`, `seo_focus_keyword`) VALUES
('hero', 'Dijitalde Güven Oluşturur, Markanızı Büyütürüz.', 'Gerçek kullanıcılar, kaliteli etkileşimler ve hızlı teslimat ile dijital dünyada güçlü bir itibar inşa edin.', NULL, 'Hizmetleri İncele', '/kategoriler', 'arrow-right', 1, 'active', 'dijital hizmet'),
('google_services', 'Google Hizmetleri', 'Google işletme profilinizi güçlendirin', 'Google haritalar yorumları, Google işletme puanı yükseltme, Google SEO destekli yorum hizmetleri ile işletmenizin dijital görünürlüğünü artırın.', 'Google Paketlerini İncele', '/kategori/google-hizmetleri', 'search', 10, 'active', 'google yorum hizmeti'),
('instagram_services', 'Instagram Hizmetleri', 'Instagram hesabınızı büyütün', 'Instagram takipçi, beğeni, yorum ve etkileşim hizmetleri ile markanızı sosyal medyada öne çıkarın.', 'Instagram Paketlerini İncele', '/kategori/instagram-hizmetleri', 'instagram', 11, 'active', 'instagram takipçi hizmeti'),
('tiktok_services', 'TikTok Hizmetleri', 'TikTok içeriklerinizi öne çıkarın', 'TikTok takipçi, beğeni, izlenme ve paylaşım hizmetleri ile viral içerikler oluşturun.', 'TikTok Paketlerini İncele', '/kategori/tiktok-hizmetleri', 'zap', 12, 'active', 'tiktok hizmeti'),
('youtube_services', 'YouTube Hizmetleri', 'YouTube kanalınızı büyütün', 'YouTube abone, izlenme, beğeni ve yorum hizmetleri ile kanalınızın performansını artırın.', 'YouTube Paketlerini İncele', '/kategori/youtube-hizmetleri', 'youtube', 13, 'active', 'youtube abone hizmeti'),
('seo_services', 'SEO ve Dijital Reklam', 'Arama motorlarında üst sıralara çıkın', 'Profesyonel SEO hizmetleri, Google Ads yönetimi ve dijital reklam çözümleri.', 'SEO Paketlerini İncele', '/kategori/seo-hizmetleri', 'bar-chart', 14, 'active', 'SEO hizmeti'),
('reputation', 'İtibar Yönetimi', 'Dijital itibarınızı profesyonelce yönetin', 'Olumsuz yorumların etkisini azaltın, olumlu müşteri deneyimlerini öne çıkarın.', 'İtibar Yönetimi Paketleri', '/kategoriler', 'shield', 15, 'active', 'itibar yönetimi'),
('how_it_works', 'Nasıl Çalışır?', '4 basit adımda dijital hizmetinizi alın', NULL, NULL, NULL, 'zap', 20, 'active', NULL),
('trust', 'Neden Bizi Tercih Etmelisiniz?', 'Binlerce müşterinin güvendiği profesyonel hizmet', NULL, NULL, NULL, 'shield', 30, 'active', NULL),
('testimonials', 'Müşterilerimiz Ne Diyor?', 'Gerçek müşteri deneyimleri ve değerlendirmeler', NULL, NULL, NULL, 'users', 40, 'active', NULL),
('info_center', 'Bilgi Merkezi', 'Dijital hizmetler hakkında merak ettikleriniz', NULL, 'Tüm Rehberleri Gör', '/blog', 'book-open', 50, 'active', 'dijital hizmet rehberi'),
('seo_text', 'Yorum Hizmeti - Türkiye''nin Güvenilir Dijital Hizmet Platformu', NULL, 'Yorum Hizmeti olarak Google, Instagram, TikTok, YouTube ve diğer dijital platformlarda markanızın görünürlüğünü artırıyoruz. Profesyonel ekibimiz, gerçek kullanıcı etkileşimleri ve hızlı teslimat ile işletmenizin dijital dünyada öne çıkmasını sağlıyor.', NULL, NULL, 'info', 90, 'active', 'yorum hizmeti'),
('cta', 'Hemen Sipariş Verin, Farkı Hissedin!', 'Markanız için doğru adım atın, dijitalde bir adım öne geçin.', NULL, 'Hizmetleri Keşfet', '/kategoriler', 'zap', 95, 'active', NULL);

UPDATE `home_sections` SET `extra_data` = '[{"title":"Google yorum hizmeti nedir?","desc":"Google haritalar ve işletme profilinize gerçek kullanıcı yorumları ekleyerek puanınızı yükseltin.","link":"/blog"},{"title":"Instagram etkileşimi nasıl artırılır?","desc":"Takipçi, beğeni ve yorum hizmetleri ile Instagram hesabınızı büyütün.","link":"/blog"},{"title":"Sosyal medya etkileşimi neden önemlidir?","desc":"Güçlü sosyal medya varlığı, marka bilinirliği ve müşteri güvenini artırır.","link":"/blog"},{"title":"İtibar yönetimi neden önemlidir?","desc":"Online itibarınızı yöneterek müşteri güvenini koruyun ve satışlarınızı artırın.","link":"/blog"},{"title":"SEO ile dijital görünürlük nasıl artırılır?","desc":"Arama motoru optimizasyonu ile web sitenizin organik trafiğini artırın.","link":"/blog"}]' WHERE `section_key` = 'info_center';

UPDATE `home_sections` SET `extra_data` = '[{"name":"Ahmet K.","role":"Restoran Sahibi","text":"Google yorum hizmeti sayesinde işletmemizin puanı 4.9''a yükseldi. Müşteri sayımız arttı!","stars":5},{"name":"Elif Y.","role":"E-Ticaret Uzmanı","text":"Instagram etkileşim paketleri gerçekten sonuç veriyor. Takipçi ve satışlarımız arttı.","stars":5},{"name":"Murat D.","role":"Dijital Pazarlama Uzmanı","text":"Hızlı teslimat ve kaliteli destek için teşekkürler. Gönül rahatlığıyla tavsiye ederim.","stars":5}]' WHERE `section_key` = 'testimonials';

-- 3. blog yazısı (eksikse)
INSERT IGNORE INTO `blog_posts` (`blog_category_id`, `title`, `slug`, `excerpt`, `content`, `status`, `published_at`, `seo_title`, `seo_description`, `seo_focus_keyword`) VALUES
(3, 'Sosyal Medya Pazarlamasında 2024 Trendleri', 'sosyal-medya-pazarlamasi-2024-trendleri', 'Sosyal medya pazarlamasında öne çıkan trendleri ve stratejileri keşfedin.', '<h2>Sosyal Medya Pazarlaması Trendleri</h2><p>Dijital pazarlama dünyası hızla değişiyor. Sosyal medya platformları, markaların hedef kitleleriyle etkileşim kurmasının en önemli araçları haline geldi.</p><h3>1. Kısa Video İçerikleri</h3><p>TikTok ve Instagram Reels ile kısa video içerikler, en yüksek etkileşim oranlarını yakalıyor.</p><h3>2. Topluluk Yönetimi</h3><p>Sadece takipçi sayısı değil, aktif ve bağlı bir topluluk oluşturmak uzun vadeli başarı için şart.</p><h3>3. Etkileyici Pazarlama</h3><p>Mikro ve nano etkileyicilerle yapılan işbirlikleri, daha yüksek dönüşüm oranları sağlıyor.</p><h3>4. Sosyal Ticaret</h3><p>Instagram Shop ve TikTok Shop gibi özellikler, doğrudan sosyal medya üzerinden satışı mümkün kılıyor.</p>', 'active', NOW(), 'Sosyal Medya Pazarlaması 2024 Trendleri', 'Sosyal medya pazarlamasında 2024 yılının öne çıkan trendlerini ve stratejilerini keşfedin.', 'sosyal medya pazarlama');
