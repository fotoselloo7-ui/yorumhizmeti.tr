-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Anamakine: localhost:3306
-- Üretim Zamanı: 28 Ağu 2026, 16:41:07
-- Sunucu sürümü: 10.11.14-MariaDB-cll-lve
-- PHP Sürümü: 8.4.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `yorumhizmeti_yorumhizmeti`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `admin_id` int(10) UNSIGNED DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `admins`
--

CREATE TABLE `admins` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(191) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('super_admin','admin','editor') NOT NULL DEFAULT 'admin',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `must_change_password` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `password`, `role`, `status`, `must_change_password`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@yorumhizmeti.tr', '$2y$10$kijA4bMSuQGyZ1/erH9/4uIkl1UDYJNlKEck7pniTdxZJhYxo2IGq', 'super_admin', 'active', 1, '2026-06-30 23:57:17', '2026-06-30 23:57:17');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `bank_accounts`
--

CREATE TABLE `bank_accounts` (
  `id` int(10) UNSIGNED NOT NULL,
  `bank_name` varchar(100) NOT NULL,
  `account_holder` varchar(200) NOT NULL,
  `iban` varchar(50) NOT NULL,
  `branch` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `bank_accounts`
--

INSERT INTO `bank_accounts` (`id`, `bank_name`, `account_holder`, `iban`, `branch`, `description`, `status`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Ziraat Bankası', 'Yorum Hizmeti', 'TR00 0000 0000 0000 0000 0000 00', 'Online', NULL, 'active', 1, '2026-06-30 23:57:17', '2026-06-30 23:57:17'),
(2, 'Garanti BBVA', 'Yorum Hizmeti', 'TR00 0000 0000 0000 0000 0000 00', 'Online', NULL, 'active', 2, '2026-06-30 23:57:17', '2026-06-30 23:57:17');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `bank_transfer_notifications`
--

CREATE TABLE `bank_transfer_notifications` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `bank_name` varchar(100) NOT NULL,
  `sender_name` varchar(200) NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `receipt_file` varchar(500) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `admin_note` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `blog_categories`
--

CREATE TABLE `blog_categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(200) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(500) DEFAULT NULL,
  `image_alt` varchar(255) DEFAULT NULL,
  `icon_key` varchar(50) DEFAULT 'file-text',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `seo_title` varchar(200) DEFAULT NULL,
  `seo_description` varchar(500) DEFAULT NULL,
  `seo_focus_keyword` varchar(100) DEFAULT NULL,
  `seo_score` int(11) DEFAULT 0,
  `canonical_url` varchar(500) DEFAULT NULL,
  `og_title` varchar(200) DEFAULT NULL,
  `og_description` varchar(500) DEFAULT NULL,
  `og_image` varchar(500) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `blog_categories`
--

INSERT INTO `blog_categories` (`id`, `name`, `slug`, `description`, `image`, `image_alt`, `icon_key`, `status`, `sort_order`, `seo_title`, `seo_description`, `seo_focus_keyword`, `seo_score`, `canonical_url`, `og_title`, `og_description`, `og_image`, `created_at`, `updated_at`) VALUES
(1, 'Google İşletme Profili', 'google-isletme-profili', 'Google İşletme Profili hakkında rehber ve ipuçları', NULL, NULL, 'globe', 'active', 1, 'Google İşletme Profili Rehberi', 'Google İşletme Profili hakkında bilmeniz gereken her şey.', NULL, 0, NULL, NULL, NULL, NULL, '2026-06-30 23:57:17', '2026-06-30 23:57:17'),
(2, 'SEO Rehberi', 'seo-rehberi', 'SEO ve arama motoru optimizasyonu hakkında yazılar', NULL, NULL, 'search', 'active', 2, 'SEO Rehberi', 'SEO hakkında kapsamlı rehber ve ipuçları.', NULL, 0, NULL, NULL, NULL, NULL, '2026-06-30 23:57:17', '2026-06-30 23:57:17'),
(3, 'Sosyal Medya', 'sosyal-medya', 'Sosyal medya pazarlaması hakkında yazılar', NULL, NULL, 'share-2', 'active', 3, 'Sosyal Medya Rehberi', 'Sosyal medya pazarlaması hakkında ipuçları.', NULL, 0, NULL, NULL, NULL, NULL, '2026-06-30 23:57:17', '2026-06-30 23:57:17');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `blog_posts`
--

CREATE TABLE `blog_posts` (
  `id` int(10) UNSIGNED NOT NULL,
  `blog_category_id` int(10) UNSIGNED DEFAULT NULL,
  `title` varchar(300) NOT NULL,
  `slug` varchar(300) NOT NULL,
  `excerpt` text DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `raw_content` longtext DEFAULT NULL,
  `faqs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`faqs`)),
  `toc` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`toc`)),
  `schema_type` varchar(50) DEFAULT 'Article',
  `reading_time` int(11) DEFAULT 1,
  `noindex` tinyint(1) DEFAULT 0,
  `views` int(11) DEFAULT 0,
  `image` varchar(500) DEFAULT NULL,
  `image_alt` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive','draft','scheduled') NOT NULL DEFAULT 'draft',
  `published_at` datetime DEFAULT NULL,
  `seo_title` varchar(200) DEFAULT NULL,
  `seo_description` varchar(500) DEFAULT NULL,
  `seo_focus_keyword` varchar(100) DEFAULT NULL,
  `seo_score` int(11) DEFAULT 0,
  `canonical_url` varchar(500) DEFAULT NULL,
  `og_title` varchar(200) DEFAULT NULL,
  `og_description` varchar(500) DEFAULT NULL,
  `og_image` varchar(500) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `blog_posts`
--

INSERT INTO `blog_posts` (`id`, `blog_category_id`, `title`, `slug`, `excerpt`, `content`, `raw_content`, `faqs`, `toc`, `schema_type`, `reading_time`, `noindex`, `views`, `image`, `image_alt`, `status`, `published_at`, `seo_title`, `seo_description`, `seo_focus_keyword`, `seo_score`, `canonical_url`, `og_title`, `og_description`, `og_image`, `created_at`, `updated_at`) VALUES
(1, 1, 'Google İşletme Profili Nasıl Optimize Edilir?', 'google-isletme-profili-nasil-optimize-edilir', 'Google İşletme Profilinizi optimize ederek yerel aramalarda üst sıralara çıkmanın yollarını öğrenin.', '<h2>Google İşletme Profili Optimizasyonu Rehberi</h2><p>Google İşletme Profili, yerel işletmeler için en önemli dijital pazarlama araçlarından biridir. Doğru optimize edildiğinde, potansiyel müşterilerinizin sizi kolayca bulmasını sağlar.</p><h3>1. Profil Bilgilerinizi Eksiksiz Doldurun</h3><p>İşletme adı, adres, telefon numarası, web sitesi ve çalışma saatlerinizi eksiksiz ve doğru bir şekilde girin.</p><h3>2. Doğru Kategori Seçimi</h3><p>İşletmenizi en iyi tanımlayan kategoriyi seçin. Birincil ve ikincil kategorileri doğru belirleyin.</p><h3>3. Fotoğraf ve Video Ekleyin</h3><p>Kaliteli fotoğraflar ve videolar ekleyerek profilinizi zenginleştirin.</p><h3>4. Düzenli Paylaşım Yapın</h3><p>Google İşletme Profilinizde düzenli olarak güncelleme ve paylaşım yapın.</p><h3>5. Müşteri Yorumlarını Yönetin</h3><p>Gelen yorumlara profesyonel ve zamanında yanıt verin.</p>', NULL, NULL, NULL, 'Article', 1, 0, 1, NULL, NULL, 'active', '2026-06-30 23:57:17', 'Google İşletme Profili Nasıl Optimize Edilir?', 'Google İşletme Profilinizi optimize ederek yerel aramalarda üst sıralara çıkın.', 'google işletme profili optimizasyonu', 0, NULL, NULL, NULL, NULL, '2026-06-30 23:57:17', '2026-07-01 04:10:15'),
(2, 2, 'SEO Nedir? Başlangıç Rehberi', 'seo-nedir-baslangic-rehberi', 'SEO nedir, nasıl çalışır ve neden önemlidir? Kapsamlı başlangıç rehberimizi okuyun.', '<h2>SEO Nedir?</h2><p>SEO (Search Engine Optimization), web sitenizin arama motorlarında daha üst sıralarda yer almasını sağlayan teknik ve stratejik çalışmaların bütünüdür.</p><h3>SEO Neden Önemlidir?</h3><p>İnternet kullanıcılarının büyük çoğunluğu arama motorları üzerinden bilgi arar. İlk sayfada yer almak, organik trafik ve müşteri kazanımı için kritiktir.</p><h3>SEO\'nun Temel Bileşenleri</h3><ul><li><strong>Teknik SEO:</strong> Site hızı, mobil uyumluluk, site haritası</li><li><strong>İçerik SEO:</strong> Kaliteli ve optimize edilmiş içerik</li><li><strong>Off-Page SEO:</strong> Backlink ve dış referanslar</li></ul>', NULL, NULL, NULL, 'Article', 1, 0, 1, NULL, NULL, 'active', '2026-06-30 23:57:17', 'SEO Nedir? Başlangıç Rehberi', 'SEO nedir, nasıl yapılır? Kapsamlı başlangıç rehberi.', 'seo nedir', 0, NULL, NULL, NULL, NULL, '2026-06-30 23:57:17', '2026-07-01 04:10:14'),
(3, 3, 'Sosyal Medya Pazarlamasında 2024 Trendleri', 'sosyal-medya-pazarlamasi-2024-trendleri', 'Sosyal medya pazarlamasında öne çıkan trendleri ve stratejileri keşfedin.', '<h2>Sosyal Medya Pazarlaması Trendleri</h2><p>Dijital pazarlama dünyası hızla değişiyor. Sosyal medya platformları, markaların hedef kitleleriyle etkileşim kurmasının en önemli araçları haline geldi.</p><h3>1. Kısa Video İçerikleri</h3><p>TikTok ve Instagram Reels ile kısa video içerikler, en yüksek etkileşim oranlarını yakalıyor. Markaların bu formata yatırım yapması kritik önem taşıyor.</p><h3>2. Topluluk Yönetimi</h3><p>Sadece takipçi sayısı değil, aktif ve bağlı bir topluluk oluşturmak uzun vadeli başarı için şart.</p><h3>3. Etkileyici Pazarlama</h3><p>Mikro ve nano etkileyicilerle yapılan işbirlikleri, daha yüksek dönüşüm oranları sağlıyor.</p><h3>4. Sosyal Ticaret</h3><p>Instagram Shop ve TikTok Shop gibi özellikler, doğrudan sosyal medya üzerinden satışı mümkün kılıyor.</p>', NULL, NULL, NULL, 'Article', 1, 0, 1, NULL, NULL, 'active', '2026-06-30 23:57:17', 'Sosyal Medya Pazarlaması 2024 Trendleri', 'Sosyal medya pazarlamasında 2024 yılının öne çıkan trendlerini ve stratejilerini keşfedin.', 'sosyal medya pazarlama', 0, NULL, NULL, NULL, NULL, '2026-06-30 23:57:17', '2026-07-01 04:10:14');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `blog_post_tags`
--

CREATE TABLE `blog_post_tags` (
  `post_id` int(10) UNSIGNED NOT NULL,
  `tag_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `blog_tags`
--

CREATE TABLE `blog_tags` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `categories`
--

CREATE TABLE `categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `parent_id` int(10) UNSIGNED DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(500) DEFAULT NULL,
  `image_alt` varchar(255) DEFAULT NULL,
  `icon_key` varchar(50) DEFAULT 'package',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `seo_title` varchar(200) DEFAULT NULL,
  `seo_description` varchar(500) DEFAULT NULL,
  `seo_focus_keyword` varchar(100) DEFAULT NULL,
  `seo_score` int(11) DEFAULT 0,
  `canonical_url` varchar(500) DEFAULT NULL,
  `og_title` varchar(200) DEFAULT NULL,
  `og_description` varchar(500) DEFAULT NULL,
  `og_image` varchar(500) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `categories`
--

INSERT INTO `categories` (`id`, `parent_id`, `name`, `slug`, `description`, `image`, `image_alt`, `icon_key`, `sort_order`, `status`, `seo_title`, `seo_description`, `seo_focus_keyword`, `seo_score`, `canonical_url`, `og_title`, `og_description`, `og_image`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Google Hizmetleri', 'google-hizmetleri', 'Google İşletme Profili, Google Harita ve Google SEO hizmetleri', '/uploads/categories/6a45913f3cd55_1782944063.png', '', 'globe', 1, 'active', 'Google Hizmetleri - Yorum Hizmeti', 'Profesyonel Google hizmetleri ile işletmenizi dijitalde öne çıkarın.', '', 32, '', '', '', NULL, '2026-06-30 23:57:17', '2026-07-02 01:14:24'),
(2, NULL, 'Instagram Hizmetleri', 'instagram-hizmetleri', 'Instagram büyüme, etkileşim ve yönetim hizmetleri', NULL, NULL, 'camera', 2, 'active', 'Instagram Hizmetleri - Yorum Hizmeti', 'Instagram hesabınızı profesyonel olarak büyütün.', NULL, 0, NULL, NULL, NULL, NULL, '2026-06-30 23:57:17', '2026-06-30 23:57:17'),
(3, NULL, 'TikTok Hizmetleri', 'tiktok-hizmetleri', 'TikTok büyüme ve etkileşim hizmetleri', NULL, NULL, 'video', 3, 'active', 'TikTok Hizmetleri - Yorum Hizmeti', 'TikTok hesabınızı hızla büyütün.', NULL, 0, NULL, NULL, NULL, NULL, '2026-06-30 23:57:17', '2026-06-30 23:57:17'),
(4, NULL, 'YouTube Hizmetleri', 'youtube-hizmetleri', 'YouTube kanal büyüme ve video tanıtım hizmetleri', NULL, NULL, 'play-circle', 4, 'active', 'YouTube Hizmetleri - Yorum Hizmeti', 'YouTube kanalınızı profesyonel olarak büyütün.', NULL, 0, NULL, NULL, NULL, NULL, '2026-06-30 23:57:17', '2026-06-30 23:57:17'),
(5, NULL, 'Facebook Hizmetleri', 'facebook-hizmetleri', 'Facebook sayfa yönetimi ve büyüme hizmetleri', NULL, NULL, 'thumbs-up', 5, 'active', 'Facebook Hizmetleri - Yorum Hizmeti', 'Facebook sayfanızı güçlendirin.', NULL, 0, NULL, NULL, NULL, NULL, '2026-06-30 23:57:17', '2026-06-30 23:57:17'),
(6, NULL, 'SEO Hizmetleri', 'seo-hizmetleri', 'Arama motoru optimizasyonu ve site içi SEO hizmetleri', NULL, NULL, 'search', 6, 'active', 'SEO Hizmetleri - Yorum Hizmeti', 'Profesyonel SEO hizmetleri ile Google\'da üst sıralara çıkın.', NULL, 0, NULL, NULL, NULL, NULL, '2026-06-30 23:57:17', '2026-06-30 23:57:17'),
(7, NULL, 'Dijital Reklam', 'dijital-reklam', 'Google Ads, Facebook Ads ve dijital reklam yönetimi', NULL, NULL, 'target', 7, 'active', 'Dijital Reklam Hizmetleri - Yorum Hizmeti', 'Etkili dijital reklam kampanyaları ile müşterilerinize ulaşın.', NULL, 0, NULL, NULL, NULL, NULL, '2026-06-30 23:57:17', '2026-06-30 23:57:17'),
(8, NULL, 'İtibar Yönetimi', 'itibar-yonetimi', 'Online itibar yönetimi ve olumsuz yorum temizleme', NULL, NULL, 'shield', 8, 'active', 'İtibar Yönetimi - Yorum Hizmeti', 'Online itibarınızı profesyonel olarak yönetin.', NULL, 0, NULL, NULL, NULL, NULL, '2026-06-30 23:57:17', '2026-06-30 23:57:17'),
(9, 1, 'Google Yorum', 'google-yorum', NULL, NULL, NULL, 'package', 1, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(10, 1, 'Google 5 Yıldız', 'google-5-yildiz', NULL, NULL, NULL, 'package', 2, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(11, 1, 'Google Harita', 'google-harita', NULL, NULL, NULL, 'package', 3, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(12, 1, 'Google İşletme Profili', 'google-isletme-profili', NULL, NULL, NULL, 'package', 4, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(13, 1, 'Google Yorum Beğeni', 'google-yorum-begeni', NULL, NULL, NULL, 'package', 5, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(14, 2, 'Instagram Takipçi', 'instagram-takipci', NULL, NULL, NULL, 'package', 1, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(15, 2, 'Instagram Beğeni', 'instagram-begeni', NULL, NULL, NULL, 'package', 2, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(16, 2, 'Instagram Yorum', 'instagram-yorum', NULL, NULL, NULL, 'package', 3, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(17, 2, 'Instagram Yorum Beğeni', 'instagram-yorum-begeni', NULL, NULL, NULL, 'package', 4, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(18, 2, 'Instagram Reels İzlenme', 'instagram-reels-izlenme', NULL, NULL, NULL, 'package', 5, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(19, 2, 'Instagram Hikaye İzlenme', 'instagram-hikaye-izlenme', NULL, NULL, NULL, 'package', 6, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(20, 2, 'Instagram Kaydetme', 'instagram-kaydetme', NULL, NULL, NULL, 'package', 7, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(21, 2, 'Instagram Profil Ziyareti', 'instagram-profil-ziyareti', NULL, NULL, NULL, 'package', 8, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(22, 3, 'TikTok Takipçi', 'tiktok-takipci', NULL, NULL, NULL, 'package', 1, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(23, 3, 'TikTok Beğeni', 'tiktok-begeni', NULL, NULL, NULL, 'package', 2, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(24, 3, 'TikTok İzlenme', 'tiktok-izlenme', NULL, NULL, NULL, 'package', 3, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(25, 3, 'TikTok Yorum', 'tiktok-yorum', NULL, NULL, NULL, 'package', 4, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(26, 3, 'TikTok Canlı Yayın', 'tiktok-canli-yayin', NULL, NULL, NULL, 'package', 5, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(27, 4, 'YouTube Abone', 'youtube-abone', NULL, NULL, NULL, 'package', 1, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(28, 4, 'YouTube İzlenme', 'youtube-izlenme', NULL, NULL, NULL, 'package', 2, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(29, 4, 'YouTube Beğeni', 'youtube-begeni', NULL, NULL, NULL, 'package', 3, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(30, 4, 'YouTube Yorum', 'youtube-yorum', NULL, NULL, NULL, 'package', 4, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(31, 4, 'YouTube Shorts İzlenme', 'youtube-shorts-izlenme', NULL, NULL, NULL, 'package', 5, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(32, 5, 'Facebook Sayfa Beğeni', 'facebook-sayfa-begeni', NULL, NULL, NULL, 'package', 1, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(33, 5, 'Facebook Gönderi Beğeni', 'facebook-gonderi-begeni', NULL, NULL, NULL, 'package', 2, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(34, 5, 'Facebook Yorum', 'facebook-yorum', NULL, NULL, NULL, 'package', 3, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(35, 5, 'Facebook Video İzlenme', 'facebook-video-izlenme', NULL, NULL, NULL, 'package', 4, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(36, NULL, 'X / Twitter Hizmetleri', 'x-twitter-hizmetleri', NULL, NULL, NULL, 'package', 6, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(37, 36, 'X Takipçi', 'x-takipci', NULL, NULL, NULL, 'package', 1, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(38, 36, 'X Beğeni', 'x-begeni', NULL, NULL, NULL, 'package', 2, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(39, 36, 'X Retweet', 'x-retweet', NULL, NULL, NULL, 'package', 3, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(40, 36, 'X Görüntülenme', 'x-goruntulenme', NULL, NULL, NULL, 'package', 4, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(41, 36, 'X Yorum', 'x-yorum', NULL, NULL, NULL, 'package', 5, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(42, NULL, 'Threads Hizmetleri', 'threads-hizmetleri', NULL, NULL, NULL, 'package', 7, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(43, 42, 'Threads Takipçi', 'threads-takipci', NULL, NULL, NULL, 'package', 1, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(44, 42, 'Threads Beğeni', 'threads-begeni', NULL, NULL, NULL, 'package', 2, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(45, 42, 'Threads Yorum', 'threads-yorum', NULL, NULL, NULL, 'package', 3, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(46, NULL, 'Telegram Hizmetleri', 'telegram-hizmetleri', NULL, NULL, NULL, 'package', 8, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(47, 46, 'Telegram Üye', 'telegram-uye', NULL, NULL, NULL, 'package', 1, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(48, 46, 'Telegram Kanal Üyesi', 'telegram-kanal-uyesi', NULL, NULL, NULL, 'package', 2, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(49, 46, 'Telegram Grup Üyesi', 'telegram-grup-uyesi', NULL, NULL, NULL, 'package', 3, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(50, 46, 'Telegram Görüntülenme', 'telegram-goruntulenme', NULL, NULL, NULL, 'package', 4, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(51, 46, 'Telegram Reaksiyon', 'telegram-reaksiyon', NULL, NULL, NULL, 'package', 5, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(52, NULL, 'Spotify Hizmetleri', 'spotify-hizmetleri', NULL, NULL, NULL, 'package', 9, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(53, 52, 'Spotify Dinlenme', 'spotify-dinlenme', NULL, NULL, NULL, 'package', 1, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(54, 52, 'Spotify Takipçi', 'spotify-takipci', NULL, NULL, NULL, 'package', 2, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(55, 52, 'Spotify Playlist', 'spotify-playlist', NULL, NULL, NULL, 'package', 3, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(56, 52, 'Spotify Kaydetme', 'spotify-kaydetme', NULL, NULL, NULL, 'package', 4, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(57, NULL, 'Discord Hizmetleri', 'discord-hizmetleri', NULL, NULL, NULL, 'package', 10, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(58, 57, 'Discord Üye', 'discord-uye', NULL, NULL, NULL, 'package', 1, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(59, 57, 'Discord Online Üye', 'discord-online-uye', NULL, NULL, NULL, 'package', 2, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(60, 57, 'Discord Sunucu Boost', 'discord-sunucu-boost', NULL, NULL, NULL, 'package', 3, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(61, NULL, 'LinkedIn Hizmetleri', 'linkedin-hizmetleri', NULL, NULL, NULL, 'package', 11, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(62, 61, 'LinkedIn Takipçi', 'linkedin-takipci', NULL, NULL, NULL, 'package', 1, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(63, 61, 'LinkedIn Beğeni', 'linkedin-begeni', NULL, NULL, NULL, 'package', 2, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(64, 61, 'LinkedIn Bağlantı', 'linkedin-baglanti', NULL, NULL, NULL, 'package', 3, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(65, 61, 'LinkedIn Görüntülenme', 'linkedin-goruntulenme', NULL, NULL, NULL, 'package', 4, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(66, NULL, 'Twitch Hizmetleri', 'twitch-hizmetleri', NULL, NULL, NULL, 'package', 12, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(67, 66, 'Twitch Takipçi', 'twitch-takipci', NULL, NULL, NULL, 'package', 1, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(68, 66, 'Twitch İzlenme', 'twitch-izlenme', NULL, NULL, NULL, 'package', 2, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(69, 66, 'Twitch Canlı İzleyici', 'twitch-canli-izleyici', NULL, NULL, NULL, 'package', 3, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(70, 6, 'SEO Analizi', 'seo-analizi', NULL, NULL, NULL, 'package', 1, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(71, 6, 'Backlink Paketi', 'backlink-paketi', NULL, NULL, NULL, 'package', 2, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(72, 6, 'Yerel SEO', 'yerel-seo', NULL, NULL, NULL, 'package', 3, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(73, 6, 'Teknik SEO', 'teknik-seo', NULL, NULL, NULL, 'package', 4, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(74, 6, 'İçerik SEO', 'icerik-seo', NULL, NULL, NULL, 'package', 5, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(75, 7, 'Google Ads', 'google-ads', NULL, NULL, NULL, 'package', 1, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(76, 7, 'Meta Reklam', 'meta-reklam', NULL, NULL, NULL, 'package', 2, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(77, 7, 'Instagram Reklam', 'instagram-reklam', NULL, NULL, NULL, 'package', 3, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(78, 7, 'TikTok Reklam', 'tiktok-reklam', NULL, NULL, NULL, 'package', 4, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(79, 7, 'YouTube Reklam', 'youtube-reklam', NULL, NULL, NULL, 'package', 5, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(80, 8, 'Marka İtibar Yönetimi', 'marka-itibar-yonetimi', NULL, NULL, NULL, 'package', 1, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(81, 8, 'Şikayet Yönetimi', 'sikayet-yonetimi', NULL, NULL, NULL, 'package', 2, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(82, 8, 'Olumsuz Yorum Yönetimi', 'olumsuz-yorum-yonetimi', NULL, NULL, NULL, 'package', 3, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(83, 8, 'Online İtibar Paketi', 'online-itibar-paketi', NULL, NULL, NULL, 'package', 4, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 22:54:55', '2026-07-01 22:54:55'),
(84, NULL, 'Web Site Hizmetleri', 'web-site-hizmetleri', NULL, NULL, NULL, 'package', 16, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 23:22:43', '2026-07-01 23:22:43'),
(85, NULL, 'E-Ticaret Hizmetleri', 'e-ticaret-hizmetleri', NULL, NULL, NULL, 'package', 17, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 23:22:43', '2026-07-01 23:22:43'),
(86, NULL, 'Mobil Uygulama Hizmetleri', 'mobil-uygulama-hizmetleri', NULL, NULL, NULL, 'package', 18, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 23:22:43', '2026-07-01 23:22:43'),
(87, NULL, 'İçerik Üretimi', 'icerik-uretimi', NULL, NULL, NULL, 'package', 19, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 23:22:43', '2026-07-01 23:22:43'),
(88, NULL, 'Grafik Tasarım', 'grafik-tasarim', NULL, NULL, NULL, 'package', 20, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 23:22:43', '2026-07-01 23:22:43'),
(89, NULL, 'Yerel İşletme Hizmetleri', 'yerel-isletme-hizmetleri', NULL, NULL, NULL, 'package', 21, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-01 23:22:43', '2026-07-01 23:22:43'),
(90, 84, 'Web Tasarım', 'web-tasarim', NULL, NULL, NULL, 'package', 1, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:11', '2026-07-02 00:20:11'),
(91, 84, 'Landing Page', 'landing-page', NULL, NULL, NULL, 'package', 2, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:11', '2026-07-02 00:20:11'),
(92, 84, 'WordPress Site', 'wordpress-site', NULL, NULL, NULL, 'package', 3, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:11', '2026-07-02 00:20:11'),
(93, 85, 'E-Ticaret Site', 'eticaret-site', NULL, NULL, NULL, 'package', 1, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:11', '2026-07-02 00:20:11'),
(94, 85, 'Pazaryeri Entegrasyonu', 'pazaryeri-entegrasyonu', NULL, NULL, NULL, 'package', 2, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:11', '2026-07-02 00:20:11'),
(95, 85, 'OpenCart / WooCommerce', 'opencart-woocommerce', NULL, NULL, NULL, 'package', 3, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:11', '2026-07-02 00:20:11'),
(96, 86, 'Mobil Uygulama', 'mobil-uygulama', NULL, NULL, NULL, 'package', 1, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:11', '2026-07-02 00:20:11'),
(97, 86, 'iOS / Android App', 'ios-android-app', NULL, NULL, NULL, 'package', 2, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:11', '2026-07-02 00:20:11'),
(98, 86, 'App Store Yayınlama', 'app-store-yayinlama', NULL, NULL, NULL, 'package', 3, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:11', '2026-07-02 00:20:11'),
(99, 87, 'Blog Makale', 'blog-makale', NULL, NULL, NULL, 'package', 1, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:11', '2026-07-02 00:20:11'),
(100, 87, 'Sosyal Medya İçerik', 'sosyal-medya-icerik', NULL, NULL, NULL, 'package', 2, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:11', '2026-07-02 00:20:11'),
(101, 87, 'Ürün Açıklama', 'urun-aciklama', NULL, NULL, NULL, 'package', 3, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:11', '2026-07-02 00:20:11'),
(102, 88, 'Logo Tasarım', 'logo-tasarim', NULL, NULL, NULL, 'package', 1, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:11', '2026-07-02 00:20:11'),
(103, 88, 'Sosyal Medya Görsel', 'sosyal-medya-gorsel', NULL, NULL, NULL, 'package', 2, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:11', '2026-07-02 00:20:11'),
(104, 88, 'Reklam Banner', 'reklam-banner', NULL, NULL, NULL, 'package', 3, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:11', '2026-07-02 00:20:11'),
(105, 89, 'Google Profil Kurulum', 'google-profil-kurulum', NULL, NULL, NULL, 'package', 1, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:11', '2026-07-02 00:20:11'),
(106, 89, 'QR Yorum Kartı', 'qr-yorum-karti', NULL, NULL, NULL, 'package', 2, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:11', '2026-07-02 00:20:11'),
(107, 89, 'Harita Optimizasyon', 'harita-optimizasyon', NULL, NULL, NULL, 'package', 3, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:11', '2026-07-02 00:20:11'),
(108, 89, 'Yerel Reklam', 'yerel-reklam', NULL, NULL, NULL, 'package', 4, 'active', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:11', '2026-07-02 00:20:11');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `email_templates`
--

CREATE TABLE `email_templates` (
  `id` int(10) UNSIGNED NOT NULL,
  `template_key` varchar(100) NOT NULL,
  `name` varchar(200) NOT NULL,
  `subject` varchar(300) NOT NULL,
  `body` text NOT NULL,
  `variables` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `email_templates`
--

INSERT INTO `email_templates` (`id`, `template_key`, `name`, `subject`, `body`, `variables`, `status`, `created_at`, `updated_at`) VALUES
(1, 'user_registered', 'Üye Kayıt', 'Hoş Geldiniz - {{site_name}}', '<h2>Hoş Geldiniz, {{user_name}}!</h2><p>{{site_name}} ailesine katıldığınız için teşekkür ederiz.</p><p>Hesabınız başarıyla oluşturuldu. Hemen giriş yaparak hizmetlerimizi inceleyebilirsiniz.</p>', 'user_name,user_email,site_name', 'active', '2026-06-30 23:57:17', '2026-06-30 23:57:17'),
(2, 'password_reset', 'Şifre Sıfırlama', 'Şifre Sıfırlama - {{site_name}}', '<h2>Şifre Sıfırlama</h2><p>Merhaba {{user_name}},</p><p>Şifrenizi sıfırlamak için aşağıdaki bağlantıya tıklayın:</p><p><a href=\"{{reset_link}}\">Şifremi Sıfırla</a></p><p>Bu bağlantı 1 saat geçerlidir.</p>', 'user_name,reset_link,site_name', 'active', '2026-06-30 23:57:17', '2026-06-30 23:57:17'),
(3, 'order_created', 'Sipariş Oluşturuldu', 'Siparişiniz Alındı #{{order_number}} - {{site_name}}', '<h2>Siparişiniz Alındı</h2><p>Merhaba {{user_name}},</p><p>#{{order_number}} numaralı siparişiniz başarıyla oluşturuldu.</p><p>Toplam: {{total_amount}}</p><p>Ödeme yöntemi: {{payment_method}}</p>', 'user_name,order_number,total_amount,payment_method,site_name', 'active', '2026-06-30 23:57:17', '2026-06-30 23:57:17'),
(4, 'payment_received', 'Ödeme Alındı', 'Ödemeniz Alındı #{{order_number}} - {{site_name}}', '<h2>Ödemeniz Alındı</h2><p>Merhaba {{user_name}},</p><p>#{{order_number}} numaralı siparişiniz için ödeme başarıyla alınmıştır.</p>', 'user_name,order_number,site_name', 'active', '2026-06-30 23:57:17', '2026-06-30 23:57:17'),
(5, 'order_completed', 'Sipariş Tamamlandı', 'Siparişiniz Tamamlandı #{{order_number}} - {{site_name}}', '<h2>Siparişiniz Tamamlandı</h2><p>Merhaba {{user_name}},</p><p>#{{order_number}} numaralı siparişiniz başarıyla tamamlanmıştır.</p><p>Bizi tercih ettiğiniz için teşekkür ederiz.</p>', 'user_name,order_number,site_name', 'active', '2026-06-30 23:57:17', '2026-06-30 23:57:17'),
(6, 'order_cancelled', 'Sipariş İptal', 'Siparişiniz İptal Edildi #{{order_number}} - {{site_name}}', '<h2>Sipariş İptal Edildi</h2><p>Merhaba {{user_name}},</p><p>#{{order_number}} numaralı siparişiniz iptal edilmiştir.</p>', 'user_name,order_number,site_name', 'active', '2026-06-30 23:57:17', '2026-06-30 23:57:17'),
(7, 'support_ticket_created', 'Destek Talebi Oluşturuldu', 'Destek Talebiniz Alındı #{{ticket_number}} - {{site_name}}', '<h2>Destek Talebiniz Alındı</h2><p>Merhaba {{user_name}},</p><p>#{{ticket_number}} numaralı destek talebiniz oluşturuldu. En kısa sürede yanıt verilecektir.</p>', 'user_name,ticket_number,subject,site_name', 'active', '2026-06-30 23:57:17', '2026-06-30 23:57:17'),
(8, 'support_ticket_answered', 'Destek Talebi Yanıtlandı', 'Destek Talebiniz Yanıtlandı #{{ticket_number}} - {{site_name}}', '<h2>Destek Talebiniz Yanıtlandı</h2><p>Merhaba {{user_name}},</p><p>#{{ticket_number}} numaralı destek talebinize yanıt verildi. Panelinizdencevabı görebilirsiniz.</p>', 'user_name,ticket_number,site_name', 'active', '2026-06-30 23:57:17', '2026-06-30 23:57:17'),
(9, 'admin_new_order', 'Yeni Sipariş (Admin)', 'Yeni Sipariş #{{order_number}}', '<h2>Yeni Sipariş Geldi</h2><p>Sipariş No: #{{order_number}}</p><p>Müşteri: {{user_name}}</p><p>Toplam: {{total_amount}}</p>', 'user_name,order_number,total_amount,site_name', 'active', '2026-06-30 23:57:17', '2026-06-30 23:57:17'),
(10, 'bank_transfer_received', 'Havale Bildirimi Alındı', 'Havale Bildiriminiz Alındı - {{site_name}}', '<h2>Havale Bildirimi</h2><p>Merhaba {{user_name}},</p><p>#{{order_number}} numaralı siparişiniz için havale bildiriminiz alınmıştır. En kısa sürede kontrol edilecektir.</p>', 'user_name,order_number,site_name', 'active', '2026-06-30 23:57:17', '2026-06-30 23:57:17'),
(11, 'payment_pending', 'Ödeme Bekliyor', 'Ödemeniz Bekleniyor #{{order_number}} - {{site_name}}', '<h2>Ödeme Bekleniyor</h2><p>Merhaba {{user_name}},</p><p>#{{order_number}} numaralı siparişiniz için ödeme beklenmektedir.</p>', 'user_name,order_number,site_name', 'active', '2026-06-30 23:57:17', '2026-06-30 23:57:17'),
(12, 'order_processing', 'Sipariş İşlemde', 'Siparişiniz İşleme Alındı #{{order_number}} - {{site_name}}', '<h2>Siparişiniz İşlemde</h2><p>Merhaba {{user_name}},</p><p>#{{order_number}} numaralı siparişiniz işleme alınmıştır.</p>', 'user_name,order_number,site_name', 'active', '2026-06-30 23:57:17', '2026-06-30 23:57:17'),
(13, 'waiting_customer_info', 'Eksik Bilgi', 'Eksik Bilgi - Sipariş #{{order_number}} - {{site_name}}', '<h2>Eksik Bilgi Bekleniyor</h2><p>Merhaba {{user_name}},</p><p>#{{order_number}} numaralı siparişinizde eksik bilgi bulunmaktadır. Lütfen panelinizden eksik bilgileri tamamlayın.</p>', 'user_name,order_number,site_name', 'active', '2026-06-30 23:57:17', '2026-06-30 23:57:17');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `faqs`
--

CREATE TABLE `faqs` (
  `id` int(10) UNSIGNED NOT NULL,
  `question` varchar(500) NOT NULL,
  `answer` text NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `faqs`
--

INSERT INTO `faqs` (`id`, `question`, `answer`, `sort_order`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Siparişim ne kadar sürede teslim edilir?', 'Teslim süresi seçtiğiniz pakete göre değişiklik göstermektedir. Her paketin detay sayfasında tahmini teslim süresi belirtilmiştir. Genellikle 1-10 iş günü arasında siparişler tamamlanmaktadır.', 1, 'active', '2026-06-30 23:57:17', '2026-06-30 23:57:17'),
(2, 'Ödeme yöntemleri nelerdir?', 'Kredi kartı/banka kartı (PayTR/iyzico) ve Havale/EFT ile ödeme yapabilirsiniz. Tüm ödemeleriniz güvenli altyapılar üzerinden gerçekleştirilmektedir.', 2, 'active', '2026-06-30 23:57:17', '2026-06-30 23:57:17'),
(3, 'Sipariş verdikten sonra nasıl takip edebilirim?', 'Üye girişi yaptıktan sonra \"Siparişlerim\" sayfasından tüm siparişlerinizi ve durumlarını takip edebilirsiniz.', 3, 'active', '2026-06-30 23:57:17', '2026-06-30 23:57:17'),
(4, 'Destek talebi nasıl oluşturabilirim?', 'Üye panelinizden \"Destek\" bölümüne giderek yeni bir destek talebi oluşturabilirsiniz. Ekibimiz en kısa sürede yanıt verecektir.', 4, 'active', '2026-06-30 23:57:17', '2026-06-30 23:57:17'),
(5, 'İade politikanız nedir?', 'Hizmet başlamadan önce tam iade yapılmaktadır. Hizmet başladıktan sonra iade koşulları, hizmetin durumuna göre değerlendirilmektedir. Detaylı bilgi için İade Politikası sayfamızı inceleyebilirsiniz.', 5, 'active', '2026-06-30 23:57:17', '2026-06-30 23:57:17'),
(6, 'Bilgilerim güvende mi?', 'Evet, tüm kişisel bilgileriniz KVKK kapsamında korunmaktadır. SSL sertifikası ile şifrelenmiş güvenli bağlantı kullanılmaktadır.', 6, 'active', '2026-06-30 23:57:17', '2026-06-30 23:57:17');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `home_sections`
--

CREATE TABLE `home_sections` (
  `id` int(11) NOT NULL,
  `section_key` varchar(50) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `subtitle` varchar(500) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `button_text` varchar(100) DEFAULT NULL,
  `button_url` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `image_alt` varchar(255) DEFAULT NULL,
  `icon_key` varchar(50) DEFAULT 'package',
  `sort_order` int(11) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  `seo_focus_keyword` varchar(100) DEFAULT NULL,
  `extra_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`extra_data`)),
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `home_sections`
--

INSERT INTO `home_sections` (`id`, `section_key`, `title`, `subtitle`, `content`, `button_text`, `button_url`, `image`, `image_alt`, `icon_key`, `sort_order`, `status`, `seo_focus_keyword`, `extra_data`, `created_at`, `updated_at`) VALUES
(1, 'hero', 'Dijitalde Güven Oluşturur, Markanızı Büyütürüz.', 'Gerçek kullanıcılar, kaliteli etkileşimler ve hızlı teslimat ile dijital dünyada güçlü bir itibar inşa edin.', NULL, 'Hizmetleri İncele', '/kategoriler', NULL, NULL, 'arrow-right', 1, 'active', 'dijital hizmet', NULL, '2026-06-30 20:56:24', '2026-06-30 20:56:24'),
(2, 'google_services', 'Google Hizmetleri', 'Google işletme profilinizi güçlendirin', 'Google haritalar yorumları, Google işletme puanı yükseltme, Google SEO destekli yorum hizmetleri ile işletmenizin dijital görünürlüğünü artırın. Gerçek kullanıcı etkileşimleri ile Google sıralamanızda fark yaratın.', 'Google Paketlerini İncele', '/kategori/google-hizmetleri', NULL, NULL, 'search', 10, 'active', 'google yorum hizmeti', NULL, '2026-06-30 20:56:24', '2026-06-30 20:56:24'),
(3, 'instagram_services', 'Instagram Hizmetleri', 'Instagram hesabınızı büyütün', 'Instagram takipçi, beğeni, yorum ve etkileşim hizmetleri ile markanızı sosyal medyada öne çıkarın. Organik büyüme destekli, güvenilir ve hızlı teslimat.', 'Instagram Paketlerini İncele', '/kategori/instagram-hizmetleri', NULL, NULL, 'instagram', 11, 'active', 'instagram takipçi hizmeti', NULL, '2026-06-30 20:56:24', '2026-06-30 20:56:24'),
(4, 'tiktok_services', 'TikTok Hizmetleri', 'TikTok içeriklerinizi öne çıkarın', 'TikTok takipçi, beğeni, izlenme ve paylaşım hizmetleri ile viral içerikler oluşturun. Hızlı teslimat ve kaliteli etkileşim garantisi.', 'TikTok Paketlerini İncele', '/kategori/tiktok-hizmetleri', NULL, NULL, 'zap', 12, 'active', 'tiktok hizmeti', NULL, '2026-06-30 20:56:24', '2026-06-30 20:56:24'),
(5, 'youtube_services', 'YouTube Hizmetleri', 'YouTube kanalınızı büyütün', 'YouTube abone, izlenme, beğeni ve yorum hizmetleri ile kanalınızın performansını artırın. Gerçek kullanıcılar ile organik büyüme desteği.', 'YouTube Paketlerini İncele', '/kategori/youtube-hizmetleri', NULL, NULL, 'youtube', 13, 'active', 'youtube abone hizmeti', NULL, '2026-06-30 20:56:24', '2026-06-30 20:56:24'),
(6, 'seo_services', 'SEO ve Dijital Reklam Hizmetleri', 'Arama motorlarında üst sıralara çıkın', 'Profesyonel SEO hizmetleri, Google Ads yönetimi ve dijital reklam çözümleri ile web sitenizin görünürlüğünü artırın. Hedef kitlenize doğrudan ulaşın.', 'SEO Paketlerini İncele', '/kategori/seo-hizmetleri', NULL, NULL, 'bar-chart', 14, 'active', 'SEO hizmeti', NULL, '2026-06-30 20:56:24', '2026-06-30 20:56:24'),
(7, 'reputation', 'İtibar Yönetimi', 'Dijital itibarınızı profesyonelce yönetin', 'Olumsuz yorumların etkisini azaltın, olumlu müşteri deneyimlerini öne çıkarın. İşletmenizin online itibarını koruyun ve güçlendirin.', 'İtibar Yönetimi Paketleri', '/kategoriler', NULL, NULL, 'shield', 15, 'active', 'itibar yönetimi', NULL, '2026-06-30 20:56:24', '2026-06-30 20:56:24'),
(8, 'how_it_works', 'Nasıl Çalışır?', '4 basit adımda dijital hizmetinizi alın', NULL, NULL, NULL, NULL, NULL, 'zap', 20, 'active', NULL, NULL, '2026-06-30 20:56:24', '2026-06-30 20:56:24'),
(9, 'trust', 'Neden Bizi Tercih Etmelisiniz?', 'Binlerce müşterinin güvendiği profesyonel hizmet', NULL, NULL, NULL, NULL, NULL, 'shield', 30, 'active', NULL, NULL, '2026-06-30 20:56:24', '2026-06-30 20:56:24'),
(10, 'testimonials', 'Müşterilerimiz Ne Diyor?', 'Gerçek müşteri deneyimleri ve değerlendirmeler', NULL, NULL, NULL, NULL, NULL, 'users', 40, 'active', NULL, '[\n    {\"name\":\"Ahmet K.\",\"role\":\"Restoran Sahibi\",\"text\":\"Google yorum hizmeti sayesinde işletmemizin puanı 4.9\'a yükseldi. Müşteri sayımız arttı!\",\"stars\":5},\n    {\"name\":\"Elif Y.\",\"role\":\"E-Ticaret Uzmanı\",\"text\":\"Instagram etkileşim paketleri gerçekten sonuç veriyor. Takipçi ve satışlarımız arttı.\",\"stars\":5},\n    {\"name\":\"Murat D.\",\"role\":\"Dijital Pazarlama Uzmanı\",\"text\":\"Hızlı teslimat ve kaliteli destek için teşekkürler. Gönül rahatlığıyla tavsiye ederim.\",\"stars\":5}\n]', '2026-06-30 20:56:24', '2026-06-30 20:56:24'),
(11, 'info_center', 'Bilgi Merkezi', 'Dijital hizmetler hakkında merak ettikleriniz', NULL, 'Tüm Rehberleri Gör', '/blog', NULL, NULL, 'book-open', 50, 'active', 'dijital hizmet rehberi', '[\n    {\"title\":\"Google yorum hizmeti nedir?\",\"desc\":\"Google haritalar ve işletme profilinize gerçek kullanıcı yorumları ekleyerek puanınızı yükseltin.\",\"link\":\"/blog\"},\n    {\"title\":\"Instagram etkileşimi nasıl artırılır?\",\"desc\":\"Takipçi, beğeni ve yorum hizmetleri ile Instagram hesabınızı büyütün.\",\"link\":\"/blog\"},\n    {\"title\":\"Sosyal medya etkileşimi neden önemlidir?\",\"desc\":\"Güçlü sosyal medya varlığı, marka bilinirliği ve müşteri güvenini artırır.\",\"link\":\"/blog\"},\n    {\"title\":\"İtibar yönetimi neden önemlidir?\",\"desc\":\"Online itibarınızı yöneterek müşteri güvenini koruyun ve satışlarınızı artırın.\",\"link\":\"/blog\"},\n    {\"title\":\"SEO ile dijital görünürlük nasıl artırılır?\",\"desc\":\"Arama motoru optimizasyonu ile web sitenizin organik trafiğini artırın.\",\"link\":\"/blog\"}\n]', '2026-06-30 20:56:24', '2026-06-30 20:56:24'),
(12, 'seo_text', 'Yorum Hizmeti - Türkiye\'nin Güvenilir Dijital Hizmet Platformu', NULL, 'Yorum Hizmeti olarak Google, Instagram, TikTok, YouTube ve diğer dijital platformlarda markanızın görünürlüğünü artırıyoruz. Profesyonel ekibimiz, gerçek kullanıcı etkileşimleri ve hızlı teslimat ile işletmenizin dijital dünyada öne çıkmasını sağlıyor. Güvenli ödeme seçenekleri, 7/24 destek ve para iade garantisi ile hizmet veriyoruz. Türkiye genelinde binlerce işletme ve girişimciye dijital büyüme çözümleri sunuyoruz.', NULL, NULL, NULL, NULL, 'info', 90, 'active', 'yorum hizmeti', NULL, '2026-06-30 20:56:24', '2026-06-30 20:56:24'),
(13, 'cta', 'Hemen Sipariş Verin, Farkı Hissedin!', 'Markanız için doğru adım atın, dijitalde bir adım öne geçin.', NULL, 'Hizmetleri Keşfet', '/kategoriler', NULL, NULL, 'zap', 95, 'active', NULL, NULL, '2026-06-30 20:56:24', '2026-06-30 20:56:24');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `orders`
--

CREATE TABLE `orders` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `order_number` varchar(20) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_gateway` varchar(50) DEFAULT NULL,
  `payment_status` enum('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending',
  `order_status` enum('payment_pending','paid','preparing','processing','completed','cancelled','refunded','waiting_customer_info') NOT NULL DEFAULT 'payment_pending',
  `customer_note` text DEFAULT NULL,
  `admin_note` text DEFAULT NULL,
  `customer_visible_note` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `order_fields`
--

CREATE TABLE `order_fields` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `order_item_id` int(10) UNSIGNED DEFAULT NULL,
  `field_key` varchar(50) NOT NULL,
  `field_label` varchar(100) NOT NULL,
  `field_value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `order_items`
--

CREATE TABLE `order_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `package_id` int(10) UNSIGNED DEFAULT NULL,
  `package_name` varchar(300) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `order_status_logs`
--

CREATE TABLE `order_status_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `old_status` varchar(50) DEFAULT NULL,
  `new_status` varchar(50) NOT NULL,
  `note` text DEFAULT NULL,
  `created_by` varchar(50) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `packages`
--

CREATE TABLE `packages` (
  `id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(300) NOT NULL,
  `slug` varchar(300) NOT NULL,
  `short_description` varchar(500) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(500) DEFAULT NULL,
  `image_alt` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount_price` decimal(10,2) DEFAULT NULL,
  `delivery_time` varchar(100) DEFAULT NULL,
  `min_quantity` int(11) NOT NULL DEFAULT 1,
  `max_quantity` int(11) NOT NULL DEFAULT 1,
  `badge` varchar(50) DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `seo_title` varchar(200) DEFAULT NULL,
  `seo_description` varchar(500) DEFAULT NULL,
  `seo_focus_keyword` varchar(100) DEFAULT NULL,
  `seo_score` int(11) DEFAULT 0,
  `canonical_url` varchar(500) DEFAULT NULL,
  `og_title` varchar(200) DEFAULT NULL,
  `og_description` varchar(500) DEFAULT NULL,
  `og_image` varchar(500) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `packages`
--

INSERT INTO `packages` (`id`, `category_id`, `name`, `slug`, `short_description`, `description`, `image`, `image_alt`, `price`, `discount_price`, `delivery_time`, `min_quantity`, `max_quantity`, `badge`, `is_featured`, `status`, `sort_order`, `seo_title`, `seo_description`, `seo_focus_keyword`, `seo_score`, `canonical_url`, `og_title`, `og_description`, `og_image`, `created_at`, `updated_at`) VALUES
(453, 9, 'Google Harita Yorum 10 Adet', 'google-harita-yorum-toplama-baslangic-paketi-10-davet', '10 müşteri daveti için Etik yorum toplama / gerçek müşteri daveti hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 10 müşteri daveti. Servis tipi: Etik yorum toplama / gerçek müşteri daveti. Sahte yorum üretimi değil, işletmenizin gerçek müşterilerine yorum daveti süreci planlanır. Teslimat: 2-5 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 349.00, NULL, '2-5 iş günü', 1, 10, NULL, 1, 'active', 1, 'Google Harita Yorum Toplama Başlangıç Paketi | 10 Davet | Yorum Hizmet', 'Google Harita Yorum Toplama Başlangıç Paketi | 10 Davet için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli', '', 45, '', '', '', NULL, '2026-07-02 00:20:49', '2026-07-02 01:16:05'),
(454, 9, '', 'google-harita-yorum-toplama-profesyonel-paketi-25-davet', '25 müşteri daveti için Etik yorum toplama / gerçek müşteri daveti hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 25 müşteri daveti. Servis tipi: Etik yorum toplama / gerçek müşteri daveti. WhatsApp, QR ve kısa link ile müşteri yorum akışı hazırlanır. Teslimat: 3-7 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 749.00, NULL, '3-7 iş günü', 1, 10, NULL, 0, 'active', 2, 'Google Harita Yorum Toplama Profesyonel Paketi | 25 Davet | Yorum Hizm', 'Google Harita Yorum Toplama Profesyonel Paketi | 25 Davet için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenl', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(455, 9, '', 'google-harita-yorum-toplama-kurumsal-paketi-50-davet', '50 müşteri daveti için Etik yorum toplama / gerçek müşteri daveti hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 50 müşteri daveti. Servis tipi: Etik yorum toplama / gerçek müşteri daveti. Çok şubeli veya yoğun işletmeler için yorum toplama ve takip süreci planlanır. Teslimat: 5-10 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 1532.82, 1299.00, '5-10 iş günü', 1, 10, NULL, 0, 'active', 3, 'Google Harita Yorum Toplama Kurumsal Paketi | 50 Davet | Yorum Hizmeti', 'Google Harita Yorum Toplama Kurumsal Paketi | 50 Davet için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli s', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(456, 10, '', 'google-5-yildiz-degerlendirme-yonetimi-20-davet', '20 değerlendirme daveti için Etik müşteri değerlendirme akışı hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 20 değerlendirme daveti. Servis tipi: Etik müşteri değerlendirme akışı. Müşterilerinizin Google İşletme Profilinizde değerlendirme bırakmasını kolaylaştıran yönlendirme paketi. Teslimat: 3-7 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 599.00, NULL, '3-7 iş günü', 1, 10, NULL, 1, 'active', 4, 'Google 5 Yıldız Değerlendirme Yönetimi | 20 Davet | Yorum Hizmeti', 'Google 5 Yıldız Değerlendirme Yönetimi | 20 Davet için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipari', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(457, 10, '', 'google-5-yildiz-degerlendirme-kurumsal-takip-paketi-60-davet', '60 değerlendirme daveti için Etik müşteri değerlendirme akışı hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 60 değerlendirme daveti. Servis tipi: Etik müşteri değerlendirme akışı. Değerlendirme linki, mesaj şablonu ve takip planı ile ilerleyen kurumsal hizmet. Teslimat: 5-10 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 1768.82, 1499.00, '5-10 iş günü', 1, 10, NULL, 0, 'active', 5, 'Google 5 Yıldız Değerlendirme Kurumsal Takip Paketi | 60 Davet | Yorum', 'Google 5 Yıldız Değerlendirme Kurumsal Takip Paketi | 60 Davet için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla g', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(458, 11, '', 'google-harita-gorunurluk-baslangic-paketi-1-isletme', '1 işletme için Yerel görünürlük optimizasyonu hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1 işletme. Servis tipi: Yerel görünürlük optimizasyonu. Google Harita kategori, açıklama, hizmet alanı ve temel profil düzeni gözden geçirilir. Teslimat: 3-5 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 799.00, NULL, '3-5 iş günü', 1, 10, NULL, 1, 'active', 6, 'Google Harita Görünürlük Başlangıç Paketi | 1 İşletme | Yorum Hizmeti', 'Google Harita Görünürlük Başlangıç Paketi | 1 İşletme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli si', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(459, 11, '', 'google-harita-yerel-siralama-destek-paketi-1-isletme', '1 işletme için Yerel SEO / profil optimizasyonu hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1 işletme. Servis tipi: Yerel SEO / profil optimizasyonu. Harita görünürlüğü için kategori, anahtar kelime, fotoğraf, açıklama ve yorum yanıt sistemi düzenlenir. Teslimat: 5-10 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 1699.00, NULL, '5-10 iş günü', 1, 10, NULL, 0, 'active', 7, 'Google Harita Yerel Sıralama Destek Paketi | 1 İşletme | Yorum Hizmeti', 'Google Harita Yerel Sıralama Destek Paketi | 1 İşletme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli s', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(460, 12, '', 'google-isletme-profili-optimizasyon-paketi-baslangic', '1 işletme için Profil optimizasyonu hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1 işletme. Servis tipi: Profil optimizasyonu. Google İşletme Profili temel bilgiler, hizmetler, açıklama ve görsel düzeni SEO odaklı optimize edilir. Teslimat: 3-5 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 999.00, NULL, '3-5 iş günü', 1, 10, NULL, 1, 'active', 8, 'Google İşletme Profili Optimizasyon Paketi | Başlangıç | Yorum Hizmeti', 'Google İşletme Profili Optimizasyon Paketi | Başlangıç için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli s', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(461, 12, '', 'google-isletme-profili-kurumsal-bakim-paketi-aylik', '1 aylık bakım için Aylık profil yönetimi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1 aylık bakım. Servis tipi: Aylık profil yönetimi. Aylık gönderi planı, profil kontrolü, yorum yanıt önerileri ve yerel görünürlük takibi içerir. Teslimat: 7-30 gün içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 4128.82, 3499.00, '7-30 gün', 1, 10, NULL, 0, 'active', 9, 'Google İşletme Profili Kurumsal Bakım Paketi | Aylık | Yorum Hizmeti', 'Google İşletme Profili Kurumsal Bakım Paketi | Aylık için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sip', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(462, 13, '', 'google-yorum-yanit-ve-itibar-takip-paketi-20-yanit', '20 yanıt önerisi için İtibar yönetimi / yorum yanıtı hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 20 yanıt önerisi. Servis tipi: İtibar yönetimi / yorum yanıtı. Google yorumlarına marka diline uygun profesyonel yanıt önerileri hazırlanır. Teslimat: 3-5 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 699.00, NULL, '3-5 iş günü', 1, 10, NULL, 0, 'active', 10, 'Google Yorum Yanıt ve İtibar Takip Paketi | 20 Yanıt | Yorum Hizmeti', 'Google Yorum Yanıt ve İtibar Takip Paketi | 20 Yanıt için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sip', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(463, 14, '', 'instagram-turk-takipci-baslangic-paketi-100-takipci', '100 takipçi için Türk karışık / kademeli takipçi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 100 takipçi. Servis tipi: Türk karışık / kademeli takipçi. Profil linki ile başlar, teslimat kademeli ilerler. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 64.90, NULL, '1-3 iş günü', 1, 10, NULL, 1, 'active', 11, 'Instagram Türk Takipçi Başlangıç Paketi | 100 Takipçi | Yorum Hizmeti', 'Instagram Türk Takipçi Başlangıç Paketi | 100 Takipçi için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli si', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(464, 14, '', 'instagram-turk-takipci-profesyonel-paketi-500-takipci', '500 takipçi için Türk karışık / kademeli takipçi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 500 takipçi. Servis tipi: Türk karışık / kademeli takipçi. Profil güvenliği için kademeli teslimat planı uygulanır. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 289.00, NULL, '1-3 iş günü', 1, 10, NULL, 0, 'active', 12, 'Instagram Türk Takipçi Profesyonel Paketi | 500 Takipçi | Yorum Hizmet', 'Instagram Türk Takipçi Profesyonel Paketi | 500 Takipçi için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(465, 14, '', 'instagram-dusmeyen-takipci-premium-paketi-1-000-takipci', '1000 takipçi için Yüksek tutunma hedefli takipçi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1000 takipçi. Servis tipi: Yüksek tutunma hedefli takipçi. Tutunma odaklı servis tercih edilir, telafi koşulu yönetici tarafından belirlenir. Teslimat: 1-4 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 765.82, 649.00, '1-4 iş günü', 1, 10, NULL, 0, 'active', 13, 'Instagram Düşmeyen Takipçi Premium Paketi | 1.000 Takipçi | Yorum Hizm', 'Instagram Düşmeyen Takipçi Premium Paketi | 1.000 Takipçi için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenl', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(466, 14, '', 'instagram-kurumsal-takipci-buyume-paketi-2-500-takipci', '2500 takipçi için Kurumsal görünürlük destekli takipçi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 2500 takipçi. Servis tipi: Kurumsal görünürlük destekli takipçi. Kurumsal hesaplar için kademeli takipçi görünürlüğü hizmetidir. Teslimat: 2-5 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 1768.82, 1499.00, '2-5 iş günü', 1, 10, NULL, 0, 'active', 14, 'Instagram Kurumsal Takipçi Büyüme Paketi | 2.500 Takipçi | Yorum Hizme', 'Instagram Kurumsal Takipçi Büyüme Paketi | 2.500 Takipçi için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(467, 15, '', 'instagram-gonderi-begeni-baslangic-paketi-100-begeni', '100 beğeni için Standart gönderi beğeni hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 100 beğeni. Servis tipi: Standart gönderi beğeni. Gönderi linki ile başlar, beğeni teslimatı kademeli ilerler. Teslimat: 1-24 saat içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 29.90, NULL, '1-24 saat', 1, 10, NULL, 1, 'active', 15, 'Instagram Gönderi Beğeni Başlangıç Paketi | 100 Beğeni | Yorum Hizmeti', 'Instagram Gönderi Beğeni Başlangıç Paketi | 100 Beğeni için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli s', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(468, 15, '', 'instagram-gonderi-begeni-profesyonel-paketi-500-begeni', '500 beğeni için Standart gönderi beğeni hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 500 beğeni. Servis tipi: Standart gönderi beğeni. Yeni gönderiler için hızlı sosyal kanıt etkisi sağlar. Teslimat: 1-24 saat içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 119.00, NULL, '1-24 saat', 1, 10, NULL, 0, 'active', 16, 'Instagram Gönderi Beğeni Profesyonel Paketi | 500 Beğeni | Yorum Hizme', 'Instagram Gönderi Beğeni Profesyonel Paketi | 500 Beğeni için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(469, 15, '', 'instagram-premium-begeni-paketi-1-000-begeni', '1000 beğeni için Premium / yüksek tutunma hedefli beğeni hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1000 beğeni. Servis tipi: Premium / yüksek tutunma hedefli beğeni. Kademeli teslimatla gönderi etkileşim görünümünü güçlendirir. Teslimat: 1-2 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 270.22, 229.00, '1-2 iş günü', 1, 10, NULL, 0, 'active', 17, 'Instagram Premium Beğeni Paketi | 1.000 Beğeni | Yorum Hizmeti', 'Instagram Premium Beğeni Paketi | 1.000 Beğeni için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş o', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(470, 16, '', 'instagram-ozel-yorum-baslangic-paketi-10-yorum', '10 yorum için Özel metin yorum hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 10 yorum. Servis tipi: Özel metin yorum. Yorum metinleri sipariş notunda belirtilir veya doğal marka diliyle hazırlanır. Teslimat: 1-2 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 169.00, NULL, '1-2 iş günü', 1, 10, NULL, 1, 'active', 18, 'Instagram Özel Yorum Başlangıç Paketi | 10 Yorum | Yorum Hizmeti', 'Instagram Özel Yorum Başlangıç Paketi | 10 Yorum için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(471, 16, '', 'instagram-ozel-yorum-profesyonel-paketi-25-yorum', '25 yorum için Özel metin yorum hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 25 yorum. Servis tipi: Özel metin yorum. Kampanya, ürün veya hizmet gönderileri için yorum çeşitliliği sağlar. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 379.00, NULL, '1-3 iş günü', 1, 10, NULL, 0, 'active', 19, 'Instagram Özel Yorum Profesyonel Paketi | 25 Yorum | Yorum Hizmeti', 'Instagram Özel Yorum Profesyonel Paketi | 25 Yorum için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipar', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(472, 16, '', 'instagram-yorum-kurumsal-paket-50-yorum', '50 yorum için Özel metin yorum hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 50 yorum. Servis tipi: Özel metin yorum. Kurumsal marka diliyle hazırlanmış yorum metinleri desteklenir. Teslimat: 2-4 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 824.82, 699.00, '2-4 iş günü', 1, 10, NULL, 0, 'active', 20, 'Instagram Yorum Kurumsal Paket | 50 Yorum | Yorum Hizmeti', 'Instagram Yorum Kurumsal Paket | 50 Yorum için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluştu', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(473, 17, '', 'instagram-yorum-begeni-paketi-50-begeni', '50 yorum beğeni için Yorum beğeni etkileşimi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 50 yorum beğeni. Servis tipi: Yorum beğeni etkileşimi. Seçili yorumların öne çıkmasına yardımcı olur. Teslimat: 1-2 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 189.00, NULL, '1-2 iş günü', 1, 10, NULL, 0, 'active', 21, 'Instagram Yorum Beğeni Paketi | 50 Beğeni | Yorum Hizmeti', 'Instagram Yorum Beğeni Paketi | 50 Beğeni için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluştu', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(474, 17, '', 'instagram-yorum-begeni-profesyonel-paketi-250-begeni', '250 yorum beğeni için Yorum beğeni etkileşimi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 250 yorum beğeni. Servis tipi: Yorum beğeni etkileşimi. Yorum etkileşimi yüksek gönderiler için uygundur. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 799.00, NULL, '1-3 iş günü', 1, 10, NULL, 0, 'active', 22, 'Instagram Yorum Beğeni Profesyonel Paketi | 250 Beğeni | Yorum Hizmeti', 'Instagram Yorum Beğeni Profesyonel Paketi | 250 Beğeni için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli s', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(475, 18, '', 'instagram-reels-izlenme-baslangic-paketi-1-000-izlenme', '1000 izlenme için Reels izlenme hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1000 izlenme. Servis tipi: Reels izlenme. Reels videosu için hızlı başlangıç görünürlüğü sağlar. Teslimat: 1-24 saat içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 39.90, NULL, '1-24 saat', 1, 10, NULL, 1, 'active', 23, 'Instagram Reels İzlenme Başlangıç Paketi | 1.000 İzlenme | Yorum Hizme', 'Instagram Reels İzlenme Başlangıç Paketi | 1.000 İzlenme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(476, 18, '', 'instagram-reels-izlenme-profesyonel-paketi-10-000-izlenme', '10000 izlenme için Reels izlenme hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 10000 izlenme. Servis tipi: Reels izlenme. Keşfet ve Reels performansı için kademeli izlenme desteği. Teslimat: 1-2 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 249.00, NULL, '1-2 iş günü', 1, 10, NULL, 0, 'active', 24, 'Instagram Reels İzlenme Profesyonel Paketi | 10.000 İzlenme | Yorum Hi', 'Instagram Reels İzlenme Profesyonel Paketi | 10.000 İzlenme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güve', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(477, 18, '', 'instagram-reels-izlenme-viral-paket-50-000-izlenme', '50000 izlenme için Yüksek hacimli Reels izlenme hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 50000 izlenme. Servis tipi: Yüksek hacimli Reels izlenme. Kampanya ve lansman videolarında yüksek izlenme görünürlüğü için uygundur. Teslimat: 2-5 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 1178.82, 999.00, '2-5 iş günü', 1, 10, NULL, 0, 'active', 25, 'Instagram Reels İzlenme Viral Paket | 50.000 İzlenme | Yorum Hizmeti', 'Instagram Reels İzlenme Viral Paket | 50.000 İzlenme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sip', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(478, 19, '', 'instagram-hikaye-izlenme-baslangic-paketi-500-izlenme', '500 izlenme için Hikaye izlenme hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 500 izlenme. Servis tipi: Hikaye izlenme. Hikaye linki ile başlar, teslimat aktif hikaye süresi içinde yapılır. Teslimat: 1-24 saat içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 39.00, NULL, '1-24 saat', 1, 10, NULL, 0, 'active', 26, 'Instagram Hikaye İzlenme Başlangıç Paketi | 500 İzlenme | Yorum Hizmet', 'Instagram Hikaye İzlenme Başlangıç Paketi | 500 İzlenme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(479, 19, '', 'instagram-hikaye-izlenme-profesyonel-paketi-2-500-izlenme', '2500 izlenme için Hikaye izlenme hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 2500 izlenme. Servis tipi: Hikaye izlenme. Kampanya hikayeleri için görünürlük desteği sağlar. Teslimat: 1-24 saat içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 169.00, NULL, '1-24 saat', 1, 10, NULL, 0, 'active', 27, 'Instagram Hikaye İzlenme Profesyonel Paketi | 2.500 İzlenme | Yorum Hi', 'Instagram Hikaye İzlenme Profesyonel Paketi | 2.500 İzlenme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güve', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(480, 20, '', 'instagram-kaydetme-paketi-100-kaydetme', '100 kaydetme için Gönderi kaydetme hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 100 kaydetme. Servis tipi: Gönderi kaydetme. Gönderinin algoritmik etkileşim sinyalini güçlendirmeye yardımcı olur. Teslimat: 1-2 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 49.00, NULL, '1-2 iş günü', 1, 10, NULL, 0, 'active', 28, 'Instagram Kaydetme Paketi | 100 Kaydetme | Yorum Hizmeti', 'Instagram Kaydetme Paketi | 100 Kaydetme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluştur', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(481, 20, '', 'instagram-kaydetme-profesyonel-paketi-500-kaydetme', '500 kaydetme için Gönderi kaydetme hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 500 kaydetme. Servis tipi: Gönderi kaydetme. Rehber, kampanya ve ürün gönderileri için kaydetme odaklı paket. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 199.00, NULL, '1-3 iş günü', 1, 10, NULL, 0, 'active', 29, 'Instagram Kaydetme Profesyonel Paketi | 500 Kaydetme | Yorum Hizmeti', 'Instagram Kaydetme Profesyonel Paketi | 500 Kaydetme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sip', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(482, 21, '', 'instagram-profil-ziyareti-paketi-1-000-ziyaret', '1000 profil ziyareti için Profil ziyaret trafiği hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1000 profil ziyareti. Servis tipi: Profil ziyaret trafiği. Profil linki üzerinden görünürlük ve ziyaret sinyali oluşturur. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 129.00, NULL, '1-3 iş günü', 1, 10, NULL, 0, 'active', 30, 'Instagram Profil Ziyareti Paketi | 1.000 Ziyaret | Yorum Hizmeti', 'Instagram Profil Ziyareti Paketi | 1.000 Ziyaret için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(483, 21, '', 'instagram-profil-ziyareti-kurumsal-paketi-10-000-ziyaret', '10000 profil ziyareti için Profil ziyaret trafiği hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 10000 profil ziyareti. Servis tipi: Profil ziyaret trafiği. Kurumsal hesaplarda profil görünürlüğü artırma çalışmalarına destek olur. Teslimat: 2-5 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 942.82, 799.00, '2-5 iş günü', 1, 10, NULL, 0, 'active', 31, 'Instagram Profil Ziyareti Kurumsal Paketi | 10.000 Ziyaret | Yorum Hiz', 'Instagram Profil Ziyareti Kurumsal Paketi | 10.000 Ziyaret için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güven', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(484, 22, '', 'tiktok-takipci-baslangic-paketi-100-takipci', '100 takipçi için Kademeli takipçi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 100 takipçi. Servis tipi: Kademeli takipçi. Profil linki ile başlar, takipçi teslimatı kademeli ilerler. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 49.00, NULL, '1-3 iş günü', 1, 10, NULL, 1, 'active', 32, 'TikTok Takipçi Başlangıç Paketi | 100 Takipçi | Yorum Hizmeti', 'TikTok Takipçi Başlangıç Paketi | 100 Takipçi için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş ol', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(485, 22, '', 'tiktok-takipci-profesyonel-paketi-500-takipci', '500 takipçi için Kademeli takipçi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 500 takipçi. Servis tipi: Kademeli takipçi. Profil linki ile başlar, takipçi teslimatı kademeli ilerler. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 199.00, NULL, '1-3 iş günü', 1, 10, NULL, 0, 'active', 33, 'TikTok Takipçi Profesyonel Paketi | 500 Takipçi | Yorum Hizmeti', 'TikTok Takipçi Profesyonel Paketi | 500 Takipçi için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(486, 22, '', 'tiktok-takipci-premium-paketi-1-000-takipci', '1000 takipçi için Kademeli takipçi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1000 takipçi. Servis tipi: Kademeli takipçi. Profil linki ile başlar, takipçi teslimatı kademeli ilerler. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 447.22, 379.00, '1-3 iş günü', 1, 10, NULL, 0, 'active', 34, 'TikTok Takipçi Premium Paketi | 1.000 Takipçi | Yorum Hizmeti', 'TikTok Takipçi Premium Paketi | 1.000 Takipçi için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş ol', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(487, 22, '', 'tiktok-takipci-kurumsal-paketi-2-500-takipci', '2500 takipçi için Kademeli takipçi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 2500 takipçi. Servis tipi: Kademeli takipçi. Profil linki ile başlar, takipçi teslimatı kademeli ilerler. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 1060.82, 899.00, '1-3 iş günü', 1, 10, NULL, 0, 'active', 35, 'TikTok Takipçi Kurumsal Paketi | 2.500 Takipçi | Yorum Hizmeti', 'TikTok Takipçi Kurumsal Paketi | 2.500 Takipçi için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş o', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(488, 23, '', 'tiktok-begeni-baslangic-paketi-100-begeni', '100 beğeni için Video beğeni hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 100 beğeni. Servis tipi: Video beğeni. Video linki ile başlar, beğeni etkileşimi kademeli teslim edilir. Teslimat: 1-24 saat içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 24.90, NULL, '1-24 saat', 1, 10, NULL, 0, 'active', 36, 'TikTok Beğeni Başlangıç Paketi | 100 Beğeni | Yorum Hizmeti', 'TikTok Beğeni Başlangıç Paketi | 100 Beğeni için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluş', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(489, 23, '', 'tiktok-begeni-profesyonel-paketi-500-begeni', '500 beğeni için Video beğeni hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 500 beğeni. Servis tipi: Video beğeni. Video linki ile başlar, beğeni etkileşimi kademeli teslim edilir. Teslimat: 1-24 saat içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 99.00, NULL, '1-24 saat', 1, 10, NULL, 0, 'active', 37, 'TikTok Beğeni Profesyonel Paketi | 500 Beğeni | Yorum Hizmeti', 'TikTok Beğeni Profesyonel Paketi | 500 Beğeni için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş ol', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(490, 23, '', 'tiktok-begeni-premium-paketi-1-000-begeni', '1000 beğeni için Video beğeni hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1000 beğeni. Servis tipi: Video beğeni. Video linki ile başlar, beğeni etkileşimi kademeli teslim edilir. Teslimat: 1-24 saat içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 211.22, 179.00, '1-24 saat', 1, 10, NULL, 0, 'active', 38, 'TikTok Beğeni Premium Paketi | 1.000 Beğeni | Yorum Hizmeti', 'TikTok Beğeni Premium Paketi | 1.000 Beğeni için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluş', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(491, 23, '', 'tiktok-begeni-kurumsal-paketi-5-000-begeni', '5000 beğeni için Video beğeni hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 5000 beğeni. Servis tipi: Video beğeni. Video linki ile başlar, beğeni etkileşimi kademeli teslim edilir. Teslimat: 1-24 saat içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 942.82, 799.00, '1-24 saat', 1, 10, NULL, 0, 'active', 39, 'TikTok Beğeni Kurumsal Paketi | 5.000 Beğeni | Yorum Hizmeti', 'TikTok Beğeni Kurumsal Paketi | 5.000 Beğeni için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş olu', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(492, 24, '', 'tiktok-izlenme-baslangic-paketi-1-000-i-zlenme', '1000 i̇zlenme için Video izlenme hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1000 i̇zlenme. Servis tipi: Video izlenme. Kısa video görünürlüğünü artırmak için kademeli izlenme desteği sağlar. Teslimat: 1-2 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 29.00, NULL, '1-2 iş günü', 1, 10, NULL, 1, 'active', 40, 'TikTok İzlenme Başlangıç Paketi | 1.000 İzlenme | Yorum Hizmeti', 'TikTok İzlenme Başlangıç Paketi | 1.000 İzlenme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(493, 24, '', 'tiktok-izlenme-profesyonel-paketi-10-000-i-zlenme', '10000 i̇zlenme için Video izlenme hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 10000 i̇zlenme. Servis tipi: Video izlenme. Kısa video görünürlüğünü artırmak için kademeli izlenme desteği sağlar. Teslimat: 1-2 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 199.00, NULL, '1-2 iş günü', 1, 10, NULL, 0, 'active', 41, 'TikTok İzlenme Profesyonel Paketi | 10.000 İzlenme | Yorum Hizmeti', 'TikTok İzlenme Profesyonel Paketi | 10.000 İzlenme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipa', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(494, 24, '', 'tiktok-izlenme-premium-paketi-50-000-i-zlenme', '50000 i̇zlenme için Video izlenme hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 50000 i̇zlenme. Servis tipi: Video izlenme. Kısa video görünürlüğünü artırmak için kademeli izlenme desteği sağlar. Teslimat: 1-2 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 942.82, 799.00, '1-2 iş günü', 1, 10, NULL, 0, 'active', 42, 'TikTok İzlenme Premium Paketi | 50.000 İzlenme | Yorum Hizmeti', 'TikTok İzlenme Premium Paketi | 50.000 İzlenme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(495, 24, '', 'tiktok-izlenme-kurumsal-paketi-100-000-i-zlenme', '100000 i̇zlenme için Video izlenme hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 100000 i̇zlenme. Servis tipi: Video izlenme. Kısa video görünürlüğünü artırmak için kademeli izlenme desteği sağlar. Teslimat: 1-2 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 1650.82, 1399.00, '1-2 iş günü', 1, 10, NULL, 0, 'active', 43, 'TikTok İzlenme Kurumsal Paketi | 100.000 İzlenme | Yorum Hizmeti', 'TikTok İzlenme Kurumsal Paketi | 100.000 İzlenme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipari', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(496, 25, '', 'tiktok-yorum-baslangic-paketi-10-yorum', '10 yorum için Özel metin yorum hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 10 yorum. Servis tipi: Özel metin yorum. Yorum metinleri marka diline göre sipariş notunda belirtilebilir. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 129.00, NULL, '1-3 iş günü', 1, 10, NULL, 0, 'active', 44, 'TikTok Yorum Başlangıç Paketi | 10 Yorum | Yorum Hizmeti', 'TikTok Yorum Başlangıç Paketi | 10 Yorum için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluştur', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(497, 25, '', 'tiktok-yorum-profesyonel-paketi-25-yorum', '25 yorum için Özel metin yorum hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 25 yorum. Servis tipi: Özel metin yorum. Yorum metinleri marka diline göre sipariş notunda belirtilebilir. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 299.00, NULL, '1-3 iş günü', 1, 10, NULL, 0, 'active', 45, 'TikTok Yorum Profesyonel Paketi | 25 Yorum | Yorum Hizmeti', 'TikTok Yorum Profesyonel Paketi | 25 Yorum için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluşt', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(498, 25, '', 'tiktok-yorum-premium-paketi-50-yorum', '50 yorum için Özel metin yorum hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 50 yorum. Servis tipi: Özel metin yorum. Yorum metinleri marka diline göre sipariş notunda belirtilebilir. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 647.82, 549.00, '1-3 iş günü', 1, 10, NULL, 0, 'active', 46, 'TikTok Yorum Premium Paketi | 50 Yorum | Yorum Hizmeti', 'TikTok Yorum Premium Paketi | 50 Yorum için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluşturun', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(499, 25, '', 'tiktok-yorum-kurumsal-paketi-100-yorum', '100 yorum için Özel metin yorum hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 100 yorum. Servis tipi: Özel metin yorum. Yorum metinleri marka diline göre sipariş notunda belirtilebilir. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 1178.82, 999.00, '1-3 iş günü', 1, 10, NULL, 0, 'active', 47, 'TikTok Yorum Kurumsal Paketi | 100 Yorum | Yorum Hizmeti', 'TikTok Yorum Kurumsal Paketi | 100 Yorum için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluştur', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(500, 26, '', 'tiktok-canli-yayin-izleyici-baslangic-paketi-50-i-zleyici', '50 i̇zleyici için Canlı yayın izleyici hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 50 i̇zleyici. Servis tipi: Canlı yayın izleyici. Canlı yayın saatinde aktif edilir, yayın bağlantısı ve zaman bilgisi gerekir. Teslimat: Yayın saatine göre içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 199.00, NULL, 'Yayın saatine göre', 1, 10, NULL, 0, 'active', 48, 'TikTok Canlı Yayın İzleyici Başlangıç Paketi | 50 İzleyici | Yorum Hi', 'TikTok Canlı Yayın İzleyici Başlangıç Paketi | 50 İzleyici için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güve', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(501, 26, '', 'tiktok-canli-yayin-izleyici-profesyonel-paketi-100-i-zleyici', '100 i̇zleyici için Canlı yayın izleyici hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 100 i̇zleyici. Servis tipi: Canlı yayın izleyici. Canlı yayın saatinde aktif edilir, yayın bağlantısı ve zaman bilgisi gerekir. Teslimat: Yayın saatine göre içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 349.00, NULL, 'Yayın saatine göre', 1, 10, NULL, 0, 'active', 49, 'TikTok Canlı Yayın İzleyici Profesyonel Paketi | 100 İzleyici | Yorum', 'TikTok Canlı Yayın İzleyici Profesyonel Paketi | 100 İzleyici için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla g', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(502, 26, '', 'tiktok-canli-yayin-izleyici-premium-paketi-250-i-zleyici', '250 i̇zleyici için Canlı yayın izleyici hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 250 i̇zleyici. Servis tipi: Canlı yayın izleyici. Canlı yayın saatinde aktif edilir, yayın bağlantısı ve zaman bilgisi gerekir. Teslimat: Yayın saatine göre içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 883.82, 749.00, 'Yayın saatine göre', 1, 10, NULL, 0, 'active', 50, 'TikTok Canlı Yayın İzleyici Premium Paketi | 250 İzleyici | Yorum Hiz', 'TikTok Canlı Yayın İzleyici Premium Paketi | 250 İzleyici için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güven', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(503, 26, '', 'tiktok-canli-yayin-izleyici-kurumsal-paketi-500-i-zleyici', '500 i̇zleyici için Canlı yayın izleyici hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 500 i̇zleyici. Servis tipi: Canlı yayın izleyici. Canlı yayın saatinde aktif edilir, yayın bağlantısı ve zaman bilgisi gerekir. Teslimat: Yayın saatine göre içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 1650.82, 1399.00, 'Yayın saatine göre', 1, 10, NULL, 0, 'active', 51, 'TikTok Canlı Yayın İzleyici Kurumsal Paketi | 500 İzleyici | Yorum Hi', 'TikTok Canlı Yayın İzleyici Kurumsal Paketi | 500 İzleyici için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güve', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(504, 27, '', 'youtube-abone-baslangic-paketi-100-abone', '100 abone için Kademeli kanal abonesi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 100 abone. Servis tipi: Kademeli kanal abonesi. Kanal linki ile başlar, kademeli abone teslimatı uygulanır. Teslimat: 2-5 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 149.00, NULL, '2-5 iş günü', 1, 10, NULL, 1, 'active', 52, 'YouTube Abone Başlangıç Paketi | 100 Abone | Yorum Hizmeti', 'YouTube Abone Başlangıç Paketi | 100 Abone için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluşt', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(505, 27, '', 'youtube-abone-profesyonel-paketi-500-abone', '500 abone için Kademeli kanal abonesi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 500 abone. Servis tipi: Kademeli kanal abonesi. Kanal linki ile başlar, kademeli abone teslimatı uygulanır. Teslimat: 2-5 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 649.00, NULL, '2-5 iş günü', 1, 10, NULL, 0, 'active', 53, 'YouTube Abone Profesyonel Paketi | 500 Abone | Yorum Hizmeti', 'YouTube Abone Profesyonel Paketi | 500 Abone için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş olu', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49');
INSERT INTO `packages` (`id`, `category_id`, `name`, `slug`, `short_description`, `description`, `image`, `image_alt`, `price`, `discount_price`, `delivery_time`, `min_quantity`, `max_quantity`, `badge`, `is_featured`, `status`, `sort_order`, `seo_title`, `seo_description`, `seo_focus_keyword`, `seo_score`, `canonical_url`, `og_title`, `og_description`, `og_image`, `created_at`, `updated_at`) VALUES
(506, 27, '', 'youtube-abone-premium-paketi-1-000-abone', '1000 abone için Kademeli kanal abonesi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1000 abone. Servis tipi: Kademeli kanal abonesi. Kanal linki ile başlar, kademeli abone teslimatı uygulanır. Teslimat: 2-5 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 1414.82, 1199.00, '2-5 iş günü', 1, 10, NULL, 0, 'active', 54, 'YouTube Abone Premium Paketi | 1.000 Abone | Yorum Hizmeti', 'YouTube Abone Premium Paketi | 1.000 Abone için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluşt', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(507, 27, '', 'youtube-abone-kurumsal-paketi-2-500-abone', '2500 abone için Kademeli kanal abonesi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 2500 abone. Servis tipi: Kademeli kanal abonesi. Kanal linki ile başlar, kademeli abone teslimatı uygulanır. Teslimat: 2-5 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 3184.82, 2699.00, '2-5 iş günü', 1, 10, NULL, 0, 'active', 55, 'YouTube Abone Kurumsal Paketi | 2.500 Abone | Yorum Hizmeti', 'YouTube Abone Kurumsal Paketi | 2.500 Abone için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluş', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(508, 28, '', 'youtube-izlenme-baslangic-paketi-1-000-i-zlenme', '1000 i̇zlenme için Video izlenme hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1000 i̇zlenme. Servis tipi: Video izlenme. Video linki ile izlenme teslimatı yapılır, süre ve kaynak türü paket detayında takip edilir. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 99.00, NULL, '1-3 iş günü', 1, 10, NULL, 0, 'active', 56, 'YouTube İzlenme Başlangıç Paketi | 1.000 İzlenme | Yorum Hizmeti', 'YouTube İzlenme Başlangıç Paketi | 1.000 İzlenme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipari', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(509, 28, '', 'youtube-izlenme-profesyonel-paketi-5-000-i-zlenme', '5000 i̇zlenme için Video izlenme hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 5000 i̇zlenme. Servis tipi: Video izlenme. Video linki ile izlenme teslimatı yapılır, süre ve kaynak türü paket detayında takip edilir. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 399.00, NULL, '1-3 iş günü', 1, 10, NULL, 0, 'active', 57, 'YouTube İzlenme Profesyonel Paketi | 5.000 İzlenme | Yorum Hizmeti', 'YouTube İzlenme Profesyonel Paketi | 5.000 İzlenme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipa', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(510, 28, '', 'youtube-izlenme-premium-paketi-10-000-i-zlenme', '10000 i̇zlenme için Video izlenme hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 10000 i̇zlenme. Servis tipi: Video izlenme. Video linki ile izlenme teslimatı yapılır, süre ve kaynak türü paket detayında takip edilir. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 824.82, 699.00, '1-3 iş günü', 1, 10, NULL, 0, 'active', 58, 'YouTube İzlenme Premium Paketi | 10.000 İzlenme | Yorum Hizmeti', 'YouTube İzlenme Premium Paketi | 10.000 İzlenme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(511, 28, '', 'youtube-izlenme-kurumsal-paketi-50-000-i-zlenme', '50000 i̇zlenme için Video izlenme hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 50000 i̇zlenme. Servis tipi: Video izlenme. Video linki ile izlenme teslimatı yapılır, süre ve kaynak türü paket detayında takip edilir. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 3538.82, 2999.00, '1-3 iş günü', 1, 10, NULL, 0, 'active', 59, 'YouTube İzlenme Kurumsal Paketi | 50.000 İzlenme | Yorum Hizmeti', 'YouTube İzlenme Kurumsal Paketi | 50.000 İzlenme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipari', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(512, 31, '', 'youtube-shorts-izlenme-baslangic-paketi-1-000-i-zlenme', '1000 i̇zlenme için Shorts izlenme hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1000 i̇zlenme. Servis tipi: Shorts izlenme. Shorts videoları için hızlı görünürlük desteği sağlar. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 79.00, NULL, '1-3 iş günü', 1, 10, NULL, 1, 'active', 60, 'YouTube Shorts İzlenme Başlangıç Paketi | 1.000 İzlenme | Yorum Hizme', 'YouTube Shorts İzlenme Başlangıç Paketi | 1.000 İzlenme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(513, 31, '', 'youtube-shorts-izlenme-profesyonel-paketi-10-000-i-zlenme', '10000 i̇zlenme için Shorts izlenme hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 10000 i̇zlenme. Servis tipi: Shorts izlenme. Shorts videoları için hızlı görünürlük desteği sağlar. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 499.00, NULL, '1-3 iş günü', 1, 10, NULL, 0, 'active', 61, 'YouTube Shorts İzlenme Profesyonel Paketi | 10.000 İzlenme | Yorum Hi', 'YouTube Shorts İzlenme Profesyonel Paketi | 10.000 İzlenme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güve', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(514, 31, '', 'youtube-shorts-izlenme-premium-paketi-50-000-i-zlenme', '50000 i̇zlenme için Shorts izlenme hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 50000 i̇zlenme. Servis tipi: Shorts izlenme. Shorts videoları için hızlı görünürlük desteği sağlar. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 2240.82, 1899.00, '1-3 iş günü', 1, 10, NULL, 0, 'active', 62, 'YouTube Shorts İzlenme Premium Paketi | 50.000 İzlenme | Yorum Hizmet', 'YouTube Shorts İzlenme Premium Paketi | 50.000 İzlenme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(515, 31, '', 'youtube-shorts-izlenme-kurumsal-paketi-100-000-i-zlenme', '100000 i̇zlenme için Shorts izlenme hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 100000 i̇zlenme. Servis tipi: Shorts izlenme. Shorts videoları için hızlı görünürlük desteği sağlar. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 3892.82, 3299.00, '1-3 iş günü', 1, 10, NULL, 0, 'active', 63, 'YouTube Shorts İzlenme Kurumsal Paketi | 100.000 İzlenme | Yorum Hizm', 'YouTube Shorts İzlenme Kurumsal Paketi | 100.000 İzlenme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenl', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(516, 29, '', 'youtube-begeni-baslangic-paketi-100-begeni', '100 beğeni için Video beğeni hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 100 beğeni. Servis tipi: Video beğeni. Video linki ile beğeni teslimatı kademeli yapılır. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 79.00, NULL, '1-3 iş günü', 1, 10, NULL, 0, 'active', 64, 'YouTube Beğeni Başlangıç Paketi | 100 Beğeni | Yorum Hizmeti', 'YouTube Beğeni Başlangıç Paketi | 100 Beğeni için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş olu', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(517, 29, '', 'youtube-begeni-profesyonel-paketi-500-begeni', '500 beğeni için Video beğeni hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 500 beğeni. Servis tipi: Video beğeni. Video linki ile beğeni teslimatı kademeli yapılır. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 299.00, NULL, '1-3 iş günü', 1, 10, NULL, 0, 'active', 65, 'YouTube Beğeni Profesyonel Paketi | 500 Beğeni | Yorum Hizmeti', 'YouTube Beğeni Profesyonel Paketi | 500 Beğeni için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş o', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(518, 29, '', 'youtube-begeni-premium-paketi-1-000-begeni', '1000 beğeni için Video beğeni hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1000 beğeni. Servis tipi: Video beğeni. Video linki ile beğeni teslimatı kademeli yapılır. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 647.82, 549.00, '1-3 iş günü', 1, 10, NULL, 0, 'active', 66, 'YouTube Beğeni Premium Paketi | 1.000 Beğeni | Yorum Hizmeti', 'YouTube Beğeni Premium Paketi | 1.000 Beğeni için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş olu', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(519, 29, '', 'youtube-begeni-kurumsal-paketi-5-000-begeni', '5000 beğeni için Video beğeni hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 5000 beğeni. Servis tipi: Video beğeni. Video linki ile beğeni teslimatı kademeli yapılır. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 2712.82, 2299.00, '1-3 iş günü', 1, 10, NULL, 0, 'active', 67, 'YouTube Beğeni Kurumsal Paketi | 5.000 Beğeni | Yorum Hizmeti', 'YouTube Beğeni Kurumsal Paketi | 5.000 Beğeni için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş ol', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(520, 30, '', 'youtube-yorum-baslangic-paketi-10-yorum', '10 yorum için Özel metin yorum hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 10 yorum. Servis tipi: Özel metin yorum. Yorum metinleri sipariş notunda iletilebilir veya marka diline göre önerilir. Teslimat: 1-4 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 159.00, NULL, '1-4 iş günü', 1, 10, NULL, 0, 'active', 68, 'YouTube Yorum Başlangıç Paketi | 10 Yorum | Yorum Hizmeti', 'YouTube Yorum Başlangıç Paketi | 10 Yorum için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluştu', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(521, 30, '', 'youtube-yorum-profesyonel-paketi-25-yorum', '25 yorum için Özel metin yorum hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 25 yorum. Servis tipi: Özel metin yorum. Yorum metinleri sipariş notunda iletilebilir veya marka diline göre önerilir. Teslimat: 1-4 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 349.00, NULL, '1-4 iş günü', 1, 10, NULL, 0, 'active', 69, 'YouTube Yorum Profesyonel Paketi | 25 Yorum | Yorum Hizmeti', 'YouTube Yorum Profesyonel Paketi | 25 Yorum için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluş', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(522, 30, '', 'youtube-yorum-premium-paketi-50-yorum', '50 yorum için Özel metin yorum hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 50 yorum. Servis tipi: Özel metin yorum. Yorum metinleri sipariş notunda iletilebilir veya marka diline göre önerilir. Teslimat: 1-4 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 765.82, 649.00, '1-4 iş günü', 1, 10, NULL, 0, 'active', 70, 'YouTube Yorum Premium Paketi | 50 Yorum | Yorum Hizmeti', 'YouTube Yorum Premium Paketi | 50 Yorum için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluşturu', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(523, 30, '', 'youtube-yorum-kurumsal-paketi-100-yorum', '100 yorum için Özel metin yorum hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 100 yorum. Servis tipi: Özel metin yorum. Yorum metinleri sipariş notunda iletilebilir veya marka diline göre önerilir. Teslimat: 1-4 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 1414.82, 1199.00, '1-4 iş günü', 1, 10, NULL, 0, 'active', 71, 'YouTube Yorum Kurumsal Paketi | 100 Yorum | Yorum Hizmeti', 'YouTube Yorum Kurumsal Paketi | 100 Yorum için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluştu', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(524, 32, '', 'facebook-sayfa-begeni-baslangic-paketi-100-begeni', '100 beğeni için Sayfa beğeni hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 100 beğeni. Servis tipi: Sayfa beğeni. Facebook sayfanız için kademeli beğeni hizmeti. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 99.00, NULL, '1-3 iş günü', 1, 10, NULL, 0, 'active', 72, 'Facebook Sayfa Beğeni Başlangıç Paketi | 100 Beğeni | Yorum Hizmeti', 'Facebook Sayfa Beğeni Başlangıç Paketi | 100 Beğeni için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipa', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(525, 32, '', 'facebook-sayfa-begeni-profesyonel-paketi-500-begeni', '500 beğeni için Sayfa beğeni hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 500 beğeni. Servis tipi: Sayfa beğeni. Facebook sayfanız için kademeli beğeni hizmeti. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 399.00, NULL, '1-3 iş günü', 1, 10, NULL, 0, 'active', 73, 'Facebook Sayfa Beğeni Profesyonel Paketi | 500 Beğeni | Yorum Hizmeti', 'Facebook Sayfa Beğeni Profesyonel Paketi | 500 Beğeni için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli si', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(526, 32, '', 'facebook-sayfa-begeni-premium-paketi-1-000-begeni', '1000 beğeni için Sayfa beğeni hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1000 beğeni. Servis tipi: Sayfa beğeni. Facebook sayfanız için kademeli beğeni hizmeti. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 824.82, 699.00, '1-3 iş günü', 1, 10, NULL, 0, 'active', 74, 'Facebook Sayfa Beğeni Premium Paketi | 1.000 Beğeni | Yorum Hizmeti', 'Facebook Sayfa Beğeni Premium Paketi | 1.000 Beğeni için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipa', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(527, 32, '', 'facebook-sayfa-begeni-kurumsal-paketi-5-000-begeni', '5000 beğeni için Sayfa beğeni hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 5000 beğeni. Servis tipi: Sayfa beğeni. Facebook sayfanız için kademeli beğeni hizmeti. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 3538.82, 2999.00, '1-3 iş günü', 1, 10, NULL, 0, 'active', 75, 'Facebook Sayfa Beğeni Kurumsal Paketi | 5.000 Beğeni | Yorum Hizmeti', 'Facebook Sayfa Beğeni Kurumsal Paketi | 5.000 Beğeni için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sip', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(528, 33, '', 'facebook-gonderi-begeni-baslangic-paketi-100-begeni', '100 beğeni için Gönderi beğeni hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 100 beğeni. Servis tipi: Gönderi beğeni. Gönderi linkiyle çalışır, kampanya içerikleri için uygundur. Teslimat: 1-2 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 59.00, NULL, '1-2 iş günü', 1, 10, NULL, 0, 'active', 76, 'Facebook Gönderi Beğeni Başlangıç Paketi | 100 Beğeni | Yorum Hizmeti', 'Facebook Gönderi Beğeni Başlangıç Paketi | 100 Beğeni için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli si', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(529, 33, '', 'facebook-gonderi-begeni-profesyonel-paketi-500-begeni', '500 beğeni için Gönderi beğeni hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 500 beğeni. Servis tipi: Gönderi beğeni. Gönderi linkiyle çalışır, kampanya içerikleri için uygundur. Teslimat: 1-2 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 229.00, NULL, '1-2 iş günü', 1, 10, NULL, 0, 'active', 77, 'Facebook Gönderi Beğeni Profesyonel Paketi | 500 Beğeni | Yorum Hizmet', 'Facebook Gönderi Beğeni Profesyonel Paketi | 500 Beğeni için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(530, 33, '', 'facebook-gonderi-begeni-premium-paketi-1-000-begeni', '1000 beğeni için Gönderi beğeni hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1000 beğeni. Servis tipi: Gönderi beğeni. Gönderi linkiyle çalışır, kampanya içerikleri için uygundur. Teslimat: 1-2 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 470.82, 399.00, '1-2 iş günü', 1, 10, NULL, 0, 'active', 78, 'Facebook Gönderi Beğeni Premium Paketi | 1.000 Beğeni | Yorum Hizmeti', 'Facebook Gönderi Beğeni Premium Paketi | 1.000 Beğeni için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli si', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(531, 33, '', 'facebook-gonderi-begeni-kurumsal-paketi-5-000-begeni', '5000 beğeni için Gönderi beğeni hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 5000 beğeni. Servis tipi: Gönderi beğeni. Gönderi linkiyle çalışır, kampanya içerikleri için uygundur. Teslimat: 1-2 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 2122.82, 1799.00, '1-2 iş günü', 1, 10, NULL, 0, 'active', 79, 'Facebook Gönderi Beğeni Kurumsal Paketi | 5.000 Beğeni | Yorum Hizmeti', 'Facebook Gönderi Beğeni Kurumsal Paketi | 5.000 Beğeni için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli s', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(532, 34, '', 'facebook-yorum-baslangic-paketi-10-yorum', '10 yorum için Özel metin yorum hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 10 yorum. Servis tipi: Özel metin yorum. Gönderi yorumları için özel metin desteği. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 139.00, NULL, '1-3 iş günü', 1, 10, NULL, 0, 'active', 80, 'Facebook Yorum Başlangıç Paketi | 10 Yorum | Yorum Hizmeti', 'Facebook Yorum Başlangıç Paketi | 10 Yorum için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluşt', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(533, 34, '', 'facebook-yorum-profesyonel-paketi-25-yorum', '25 yorum için Özel metin yorum hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 25 yorum. Servis tipi: Özel metin yorum. Gönderi yorumları için özel metin desteği. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 319.00, NULL, '1-3 iş günü', 1, 10, NULL, 0, 'active', 81, 'Facebook Yorum Profesyonel Paketi | 25 Yorum | Yorum Hizmeti', 'Facebook Yorum Profesyonel Paketi | 25 Yorum için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş olu', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(534, 34, '', 'facebook-yorum-premium-paketi-50-yorum', '50 yorum için Özel metin yorum hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 50 yorum. Servis tipi: Özel metin yorum. Gönderi yorumları için özel metin desteği. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 683.22, 579.00, '1-3 iş günü', 1, 10, NULL, 0, 'active', 82, 'Facebook Yorum Premium Paketi | 50 Yorum | Yorum Hizmeti', 'Facebook Yorum Premium Paketi | 50 Yorum için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluştur', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(535, 34, '', 'facebook-yorum-kurumsal-paketi-100-yorum', '100 yorum için Özel metin yorum hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 100 yorum. Servis tipi: Özel metin yorum. Gönderi yorumları için özel metin desteği. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 1296.82, 1099.00, '1-3 iş günü', 1, 10, NULL, 0, 'active', 83, 'Facebook Yorum Kurumsal Paketi | 100 Yorum | Yorum Hizmeti', 'Facebook Yorum Kurumsal Paketi | 100 Yorum için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluşt', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(536, 35, '', 'facebook-video-izlenme-baslangic-paketi-1-000-i-zlenme', '1000 i̇zlenme için Video izlenme hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1000 i̇zlenme. Servis tipi: Video izlenme. Video içerikleri için izlenme görünürlüğü paketi. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 79.00, NULL, '1-3 iş günü', 1, 10, NULL, 0, 'active', 84, 'Facebook Video İzlenme Başlangıç Paketi | 1.000 İzlenme | Yorum Hizme', 'Facebook Video İzlenme Başlangıç Paketi | 1.000 İzlenme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(537, 35, '', 'facebook-video-izlenme-profesyonel-paketi-10-000-i-zlenme', '10000 i̇zlenme için Video izlenme hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 10000 i̇zlenme. Servis tipi: Video izlenme. Video içerikleri için izlenme görünürlüğü paketi. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 449.00, NULL, '1-3 iş günü', 1, 10, NULL, 0, 'active', 85, 'Facebook Video İzlenme Profesyonel Paketi | 10.000 İzlenme | Yorum Hi', 'Facebook Video İzlenme Profesyonel Paketi | 10.000 İzlenme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güve', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(538, 35, '', 'facebook-video-izlenme-premium-paketi-50-000-i-zlenme', '50000 i̇zlenme için Video izlenme hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 50000 i̇zlenme. Servis tipi: Video izlenme. Video içerikleri için izlenme görünürlüğü paketi. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 2004.82, 1699.00, '1-3 iş günü', 1, 10, NULL, 0, 'active', 86, 'Facebook Video İzlenme Premium Paketi | 50.000 İzlenme | Yorum Hizmet', 'Facebook Video İzlenme Premium Paketi | 50.000 İzlenme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(539, 35, '', 'facebook-video-izlenme-kurumsal-paketi-100-000-i-zlenme', '100000 i̇zlenme için Video izlenme hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 100000 i̇zlenme. Servis tipi: Video izlenme. Video içerikleri için izlenme görünürlüğü paketi. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 3538.82, 2999.00, '1-3 iş günü', 1, 10, NULL, 0, 'active', 87, 'Facebook Video İzlenme Kurumsal Paketi | 100.000 İzlenme | Yorum Hizm', 'Facebook Video İzlenme Kurumsal Paketi | 100.000 İzlenme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenl', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(540, 37, '', 'x-takipci-baslangic-paketi-100-takipci', '100 takipçi için Kademeli takipçi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 100 takipçi. Servis tipi: Kademeli takipçi. X profiliniz için kademeli takipçi hizmeti. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 119.00, NULL, '1-3 iş günü', 1, 10, NULL, 0, 'active', 88, 'X Takipçi Başlangıç Paketi | 100 Takipçi | Yorum Hizmeti', 'X Takipçi Başlangıç Paketi | 100 Takipçi için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluştur', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(541, 37, '', 'x-takipci-profesyonel-paketi-500-takipci', '500 takipçi için Kademeli takipçi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 500 takipçi. Servis tipi: Kademeli takipçi. X profiliniz için kademeli takipçi hizmeti. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 499.00, NULL, '1-3 iş günü', 1, 10, NULL, 0, 'active', 89, 'X Takipçi Profesyonel Paketi | 500 Takipçi | Yorum Hizmeti', 'X Takipçi Profesyonel Paketi | 500 Takipçi için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluşt', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(542, 37, '', 'x-takipci-premium-paketi-1-000-takipci', '1000 takipçi için Kademeli takipçi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1000 takipçi. Servis tipi: Kademeli takipçi. X profiliniz için kademeli takipçi hizmeti. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 1060.82, 899.00, '1-3 iş günü', 1, 10, NULL, 0, 'active', 90, 'X Takipçi Premium Paketi | 1.000 Takipçi | Yorum Hizmeti', 'X Takipçi Premium Paketi | 1.000 Takipçi için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluştur', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(543, 37, '', 'x-takipci-kurumsal-paketi-2-500-takipci', '2500 takipçi için Kademeli takipçi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 2500 takipçi. Servis tipi: Kademeli takipçi. X profiliniz için kademeli takipçi hizmeti. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 2358.82, 1999.00, '1-3 iş günü', 1, 10, NULL, 0, 'active', 91, 'X Takipçi Kurumsal Paketi | 2.500 Takipçi | Yorum Hizmeti', 'X Takipçi Kurumsal Paketi | 2.500 Takipçi için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluştu', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(544, 38, '', 'x-begeni-baslangic-paketi-100-begeni', '100 beğeni için Tweet beğeni hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 100 beğeni. Servis tipi: Tweet beğeni. Tweet linki ile beğeni etkileşimi sağlanır. Teslimat: 1-2 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 69.00, NULL, '1-2 iş günü', 1, 10, NULL, 0, 'active', 92, 'X Beğeni Başlangıç Paketi | 100 Beğeni | Yorum Hizmeti', 'X Beğeni Başlangıç Paketi | 100 Beğeni için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluşturun', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(545, 38, '', 'x-begeni-profesyonel-paketi-500-begeni', '500 beğeni için Tweet beğeni hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 500 beğeni. Servis tipi: Tweet beğeni. Tweet linki ile beğeni etkileşimi sağlanır. Teslimat: 1-2 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 279.00, NULL, '1-2 iş günü', 1, 10, NULL, 0, 'active', 93, 'X Beğeni Profesyonel Paketi | 500 Beğeni | Yorum Hizmeti', 'X Beğeni Profesyonel Paketi | 500 Beğeni için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluştur', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(546, 38, '', 'x-begeni-premium-paketi-1-000-begeni', '1000 beğeni için Tweet beğeni hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1000 beğeni. Servis tipi: Tweet beğeni. Tweet linki ile beğeni etkileşimi sağlanır. Teslimat: 1-2 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 588.82, 499.00, '1-2 iş günü', 1, 10, NULL, 0, 'active', 94, 'X Beğeni Premium Paketi | 1.000 Beğeni | Yorum Hizmeti', 'X Beğeni Premium Paketi | 1.000 Beğeni için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluşturun', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(547, 38, '', 'x-begeni-kurumsal-paketi-5-000-begeni', '5000 beğeni için Tweet beğeni hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 5000 beğeni. Servis tipi: Tweet beğeni. Tweet linki ile beğeni etkileşimi sağlanır. Teslimat: 1-2 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 2594.82, 2199.00, '1-2 iş günü', 1, 10, NULL, 0, 'active', 95, 'X Beğeni Kurumsal Paketi | 5.000 Beğeni | Yorum Hizmeti', 'X Beğeni Kurumsal Paketi | 5.000 Beğeni için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluşturu', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(548, 39, '', 'x-retweet-baslangic-paketi-50-retweet', '50 retweet için Retweet hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 50 retweet. Servis tipi: Retweet. Tweet yayılımı için retweet hizmeti. Teslimat: 1-2 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 89.00, NULL, '1-2 iş günü', 1, 10, NULL, 0, 'active', 96, 'X Retweet Başlangıç Paketi | 50 Retweet | Yorum Hizmeti', 'X Retweet Başlangıç Paketi | 50 Retweet için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluşturu', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(549, 39, '', 'x-retweet-profesyonel-paketi-250-retweet', '250 retweet için Retweet hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 250 retweet. Servis tipi: Retweet. Tweet yayılımı için retweet hizmeti. Teslimat: 1-2 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 349.00, NULL, '1-2 iş günü', 1, 10, NULL, 0, 'active', 97, 'X Retweet Profesyonel Paketi | 250 Retweet | Yorum Hizmeti', 'X Retweet Profesyonel Paketi | 250 Retweet için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluşt', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(550, 39, '', 'x-retweet-premium-paketi-500-retweet', '500 retweet için Retweet hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 500 retweet. Servis tipi: Retweet. Tweet yayılımı için retweet hizmeti. Teslimat: 1-2 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 765.82, 649.00, '1-2 iş günü', 1, 10, NULL, 0, 'active', 98, 'X Retweet Premium Paketi | 500 Retweet | Yorum Hizmeti', 'X Retweet Premium Paketi | 500 Retweet için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluşturun', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(551, 39, '', 'x-retweet-kurumsal-paketi-1-000-retweet', '1000 retweet için Retweet hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1000 retweet. Servis tipi: Retweet. Tweet yayılımı için retweet hizmeti. Teslimat: 1-2 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 1414.82, 1199.00, '1-2 iş günü', 1, 10, NULL, 0, 'active', 99, 'X Retweet Kurumsal Paketi | 1.000 Retweet | Yorum Hizmeti', 'X Retweet Kurumsal Paketi | 1.000 Retweet için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluştu', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(552, 40, '', 'x-goruntulenme-baslangic-paketi-1-000-goruntulenme', '1000 görüntülenme için Tweet görüntülenme hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1000 görüntülenme. Servis tipi: Tweet görüntülenme. Tweet görünürlük metriğini destekleyen kademeli hizmet. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 69.00, NULL, '1-3 iş günü', 1, 10, NULL, 0, 'active', 100, 'X Görüntülenme Başlangıç Paketi | 1.000 Görüntülenme | Yorum Hizmeti', 'X Görüntülenme Başlangıç Paketi | 1.000 Görüntülenme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sip', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(553, 40, '', 'x-goruntulenme-profesyonel-paketi-10-000-goruntulenme', '10000 görüntülenme için Tweet görüntülenme hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 10000 görüntülenme. Servis tipi: Tweet görüntülenme. Tweet görünürlük metriğini destekleyen kademeli hizmet. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 399.00, NULL, '1-3 iş günü', 1, 10, NULL, 0, 'active', 101, 'X Görüntülenme Profesyonel Paketi | 10.000 Görüntülenme | Yorum Hizmet', 'X Görüntülenme Profesyonel Paketi | 10.000 Görüntülenme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(554, 40, '', 'x-goruntulenme-premium-paketi-50-000-goruntulenme', '50000 görüntülenme için Tweet görüntülenme hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 50000 görüntülenme. Servis tipi: Tweet görüntülenme. Tweet görünürlük metriğini destekleyen kademeli hizmet. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 1768.82, 1499.00, '1-3 iş günü', 1, 10, NULL, 0, 'active', 102, 'X Görüntülenme Premium Paketi | 50.000 Görüntülenme | Yorum Hizmeti', 'X Görüntülenme Premium Paketi | 50.000 Görüntülenme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipa', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(555, 40, '', 'x-goruntulenme-kurumsal-paketi-100-000-goruntulenme', '100000 görüntülenme için Tweet görüntülenme hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 100000 görüntülenme. Servis tipi: Tweet görüntülenme. Tweet görünürlük metriğini destekleyen kademeli hizmet. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 3066.82, 2599.00, '1-3 iş günü', 1, 10, NULL, 0, 'active', 103, 'X Görüntülenme Kurumsal Paketi | 100.000 Görüntülenme | Yorum Hizmeti', 'X Görüntülenme Kurumsal Paketi | 100.000 Görüntülenme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli si', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(556, 41, '', 'x-yorum-baslangic-paketi-10-yorum', '10 yorum için Özel metin yorum hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 10 yorum. Servis tipi: Özel metin yorum. Tweet altı yorum metinleri marka diline göre hazırlanabilir. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 139.00, NULL, '1-3 iş günü', 1, 10, NULL, 0, 'active', 104, 'X Yorum Başlangıç Paketi | 10 Yorum | Yorum Hizmeti', 'X Yorum Başlangıç Paketi | 10 Yorum için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluşturun.', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(557, 41, '', 'x-yorum-profesyonel-paketi-25-yorum', '25 yorum için Özel metin yorum hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 25 yorum. Servis tipi: Özel metin yorum. Tweet altı yorum metinleri marka diline göre hazırlanabilir. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 319.00, NULL, '1-3 iş günü', 1, 10, NULL, 0, 'active', 105, 'X Yorum Profesyonel Paketi | 25 Yorum | Yorum Hizmeti', 'X Yorum Profesyonel Paketi | 25 Yorum için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluşturun.', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(558, 41, '', 'x-yorum-premium-paketi-50-yorum', '50 yorum için Özel metin yorum hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 50 yorum. Servis tipi: Özel metin yorum. Tweet altı yorum metinleri marka diline göre hazırlanabilir. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 706.82, 599.00, '1-3 iş günü', 1, 10, NULL, 0, 'active', 106, 'X Yorum Premium Paketi | 50 Yorum | Yorum Hizmeti', 'X Yorum Premium Paketi | 50 Yorum için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluşturun.', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(559, 41, '', 'x-yorum-kurumsal-paketi-100-yorum', '100 yorum için Özel metin yorum hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 100 yorum. Servis tipi: Özel metin yorum. Tweet altı yorum metinleri marka diline göre hazırlanabilir. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 1296.82, 1099.00, '1-3 iş günü', 1, 10, NULL, 0, 'active', 107, 'X Yorum Kurumsal Paketi | 100 Yorum | Yorum Hizmeti', 'X Yorum Kurumsal Paketi | 100 Yorum için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluşturun.', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(560, 43, '', 'threads-takipci-baslangic-paketi-100-takipci', '100 takipçi için Kademeli takipçi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 100 takipçi. Servis tipi: Kademeli takipçi. Threads profili için kademeli takipçi hizmeti. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 99.00, NULL, '1-3 iş günü', 1, 10, NULL, 0, 'active', 108, 'Threads Takipçi Başlangıç Paketi | 100 Takipçi | Yorum Hizmeti', 'Threads Takipçi Başlangıç Paketi | 100 Takipçi için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş o', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49');
INSERT INTO `packages` (`id`, `category_id`, `name`, `slug`, `short_description`, `description`, `image`, `image_alt`, `price`, `discount_price`, `delivery_time`, `min_quantity`, `max_quantity`, `badge`, `is_featured`, `status`, `sort_order`, `seo_title`, `seo_description`, `seo_focus_keyword`, `seo_score`, `canonical_url`, `og_title`, `og_description`, `og_image`, `created_at`, `updated_at`) VALUES
(561, 43, '', 'threads-takipci-profesyonel-paketi-500-takipci', '500 takipçi için Kademeli takipçi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 500 takipçi. Servis tipi: Kademeli takipçi. Threads profili için kademeli takipçi hizmeti. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 399.00, NULL, '1-3 iş günü', 1, 10, NULL, 0, 'active', 109, 'Threads Takipçi Profesyonel Paketi | 500 Takipçi | Yorum Hizmeti', 'Threads Takipçi Profesyonel Paketi | 500 Takipçi için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(562, 43, '', 'threads-takipci-premium-paketi-1-000-takipci', '1000 takipçi için Kademeli takipçi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1000 takipçi. Servis tipi: Kademeli takipçi. Threads profili için kademeli takipçi hizmeti. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 883.82, 749.00, '1-3 iş günü', 1, 10, NULL, 0, 'active', 110, 'Threads Takipçi Premium Paketi | 1.000 Takipçi | Yorum Hizmeti', 'Threads Takipçi Premium Paketi | 1.000 Takipçi için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş o', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(563, 43, '', 'threads-takipci-kurumsal-paketi-2-500-takipci', '2500 takipçi için Kademeli takipçi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 2500 takipçi. Servis tipi: Kademeli takipçi. Threads profili için kademeli takipçi hizmeti. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 2004.82, 1699.00, '1-3 iş günü', 1, 10, NULL, 0, 'active', 111, 'Threads Takipçi Kurumsal Paketi | 2.500 Takipçi | Yorum Hizmeti', 'Threads Takipçi Kurumsal Paketi | 2.500 Takipçi için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(564, 44, '', 'threads-begeni-baslangic-paketi-100-begeni', '100 beğeni için Gönderi beğeni hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 100 beğeni. Servis tipi: Gönderi beğeni. Threads gönderilerinde beğeni etkileşimi oluşturur. Teslimat: 1-2 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 49.00, NULL, '1-2 iş günü', 1, 10, NULL, 0, 'active', 112, 'Threads Beğeni Başlangıç Paketi | 100 Beğeni | Yorum Hizmeti', 'Threads Beğeni Başlangıç Paketi | 100 Beğeni için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş olu', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(565, 44, '', 'threads-begeni-profesyonel-paketi-500-begeni', '500 beğeni için Gönderi beğeni hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 500 beğeni. Servis tipi: Gönderi beğeni. Threads gönderilerinde beğeni etkileşimi oluşturur. Teslimat: 1-2 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 199.00, NULL, '1-2 iş günü', 1, 10, NULL, 0, 'active', 113, 'Threads Beğeni Profesyonel Paketi | 500 Beğeni | Yorum Hizmeti', 'Threads Beğeni Profesyonel Paketi | 500 Beğeni için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş o', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(566, 44, '', 'threads-begeni-premium-paketi-1-000-begeni', '1000 beğeni için Gönderi beğeni hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1000 beğeni. Servis tipi: Gönderi beğeni. Threads gönderilerinde beğeni etkileşimi oluşturur. Teslimat: 1-2 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 411.82, 349.00, '1-2 iş günü', 1, 10, NULL, 0, 'active', 114, 'Threads Beğeni Premium Paketi | 1.000 Beğeni | Yorum Hizmeti', 'Threads Beğeni Premium Paketi | 1.000 Beğeni için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş olu', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(567, 44, '', 'threads-begeni-kurumsal-paketi-5-000-begeni', '5000 beğeni için Gönderi beğeni hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 5000 beğeni. Servis tipi: Gönderi beğeni. Threads gönderilerinde beğeni etkileşimi oluşturur. Teslimat: 1-2 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 1768.82, 1499.00, '1-2 iş günü', 1, 10, NULL, 0, 'active', 115, 'Threads Beğeni Kurumsal Paketi | 5.000 Beğeni | Yorum Hizmeti', 'Threads Beğeni Kurumsal Paketi | 5.000 Beğeni için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş ol', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(568, 45, '', 'threads-yorum-baslangic-paketi-10-yorum', '10 yorum için Özel metin yorum hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 10 yorum. Servis tipi: Özel metin yorum. Threads gönderileri için yorum paketi. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 129.00, NULL, '1-3 iş günü', 1, 10, NULL, 0, 'active', 116, 'Threads Yorum Başlangıç Paketi | 10 Yorum | Yorum Hizmeti', 'Threads Yorum Başlangıç Paketi | 10 Yorum için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluştu', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(569, 45, '', 'threads-yorum-profesyonel-paketi-25-yorum', '25 yorum için Özel metin yorum hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 25 yorum. Servis tipi: Özel metin yorum. Threads gönderileri için yorum paketi. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 299.00, NULL, '1-3 iş günü', 1, 10, NULL, 0, 'active', 117, 'Threads Yorum Profesyonel Paketi | 25 Yorum | Yorum Hizmeti', 'Threads Yorum Profesyonel Paketi | 25 Yorum için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluş', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(570, 45, '', 'threads-yorum-premium-paketi-50-yorum', '50 yorum için Özel metin yorum hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 50 yorum. Servis tipi: Özel metin yorum. Threads gönderileri için yorum paketi. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 647.82, 549.00, '1-3 iş günü', 1, 10, NULL, 0, 'active', 118, 'Threads Yorum Premium Paketi | 50 Yorum | Yorum Hizmeti', 'Threads Yorum Premium Paketi | 50 Yorum için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluşturu', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(571, 45, '', 'threads-yorum-kurumsal-paketi-100-yorum', '100 yorum için Özel metin yorum hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 100 yorum. Servis tipi: Özel metin yorum. Threads gönderileri için yorum paketi. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 1178.82, 999.00, '1-3 iş günü', 1, 10, NULL, 0, 'active', 119, 'Threads Yorum Kurumsal Paketi | 100 Yorum | Yorum Hizmeti', 'Threads Yorum Kurumsal Paketi | 100 Yorum için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluştu', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(572, 47, '', 'telegram-uye-baslangic-paketi-100-uye', '100 üye için Kanal/grup üyesi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 100 üye. Servis tipi: Kanal/grup üyesi. Telegram kanal veya grup linkiyle çalışır. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 89.00, NULL, '1-3 iş günü', 1, 10, NULL, 0, 'active', 120, 'Telegram Üye Başlangıç Paketi | 100 Üye | Yorum Hizmeti', 'Telegram Üye Başlangıç Paketi | 100 Üye için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluşturu', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(573, 47, '', 'telegram-uye-profesyonel-paketi-500-uye', '500 üye için Kanal/grup üyesi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 500 üye. Servis tipi: Kanal/grup üyesi. Telegram kanal veya grup linkiyle çalışır. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 349.00, NULL, '1-3 iş günü', 1, 10, NULL, 0, 'active', 121, 'Telegram Üye Profesyonel Paketi | 500 Üye | Yorum Hizmeti', 'Telegram Üye Profesyonel Paketi | 500 Üye için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluştu', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(574, 47, '', 'telegram-uye-premium-paketi-1-000-uye', '1000 üye için Kanal/grup üyesi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1000 üye. Servis tipi: Kanal/grup üyesi. Telegram kanal veya grup linkiyle çalışır. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 765.82, 649.00, '1-3 iş günü', 1, 10, NULL, 0, 'active', 122, 'Telegram Üye Premium Paketi | 1.000 Üye | Yorum Hizmeti', 'Telegram Üye Premium Paketi | 1.000 Üye için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluşturu', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(575, 47, '', 'telegram-uye-kurumsal-paketi-5-000-uye', '5000 üye için Kanal/grup üyesi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 5000 üye. Servis tipi: Kanal/grup üyesi. Telegram kanal veya grup linkiyle çalışır. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 3420.82, 2899.00, '1-3 iş günü', 1, 10, NULL, 0, 'active', 123, 'Telegram Üye Kurumsal Paketi | 5.000 Üye | Yorum Hizmeti', 'Telegram Üye Kurumsal Paketi | 5.000 Üye için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluştur', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(576, 48, '', 'telegram-kanal-uyesi-baslangic-paketi-100-uyesi', '100 üyesi için Kanal üyesi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 100 üyesi. Servis tipi: Kanal üyesi. Kanal büyütme çalışmaları için kademeli üye hizmeti. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 99.00, NULL, '1-3 iş günü', 1, 10, NULL, 0, 'active', 124, 'Telegram Kanal Üyesi Başlangıç Paketi | 100 Üyesi | Yorum Hizmeti', 'Telegram Kanal Üyesi Başlangıç Paketi | 100 Üyesi için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipari', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(577, 48, '', 'telegram-kanal-uyesi-profesyonel-paketi-500-uyesi', '500 üyesi için Kanal üyesi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 500 üyesi. Servis tipi: Kanal üyesi. Kanal büyütme çalışmaları için kademeli üye hizmeti. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 379.00, NULL, '1-3 iş günü', 1, 10, NULL, 0, 'active', 125, 'Telegram Kanal Üyesi Profesyonel Paketi | 500 Üyesi | Yorum Hizmeti', 'Telegram Kanal Üyesi Profesyonel Paketi | 500 Üyesi için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipa', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(578, 48, '', 'telegram-kanal-uyesi-premium-paketi-1-000-uyesi', '1000 üyesi için Kanal üyesi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1000 üyesi. Servis tipi: Kanal üyesi. Kanal büyütme çalışmaları için kademeli üye hizmeti. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 824.82, 699.00, '1-3 iş günü', 1, 10, NULL, 0, 'active', 126, 'Telegram Kanal Üyesi Premium Paketi | 1.000 Üyesi | Yorum Hizmeti', 'Telegram Kanal Üyesi Premium Paketi | 1.000 Üyesi için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipari', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(579, 48, '', 'telegram-kanal-uyesi-kurumsal-paketi-5-000-uyesi', '5000 üyesi için Kanal üyesi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 5000 üyesi. Servis tipi: Kanal üyesi. Kanal büyütme çalışmaları için kademeli üye hizmeti. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 3538.82, 2999.00, '1-3 iş günü', 1, 10, NULL, 0, 'active', 127, 'Telegram Kanal Üyesi Kurumsal Paketi | 5.000 Üyesi | Yorum Hizmeti', 'Telegram Kanal Üyesi Kurumsal Paketi | 5.000 Üyesi için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipar', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(580, 49, '', 'telegram-grup-uyesi-baslangic-paketi-100-uyesi', '100 üyesi için Grup üyesi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 100 üyesi. Servis tipi: Grup üyesi. Telegram grup linkiyle kademeli üye teslimatı. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 99.00, NULL, '1-3 iş günü', 1, 10, NULL, 0, 'active', 128, 'Telegram Grup Üyesi Başlangıç Paketi | 100 Üyesi | Yorum Hizmeti', 'Telegram Grup Üyesi Başlangıç Paketi | 100 Üyesi için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(581, 49, '', 'telegram-grup-uyesi-profesyonel-paketi-500-uyesi', '500 üyesi için Grup üyesi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 500 üyesi. Servis tipi: Grup üyesi. Telegram grup linkiyle kademeli üye teslimatı. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 379.00, NULL, '1-3 iş günü', 1, 10, NULL, 0, 'active', 129, 'Telegram Grup Üyesi Profesyonel Paketi | 500 Üyesi | Yorum Hizmeti', 'Telegram Grup Üyesi Profesyonel Paketi | 500 Üyesi için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipar', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(582, 49, '', 'telegram-grup-uyesi-premium-paketi-1-000-uyesi', '1000 üyesi için Grup üyesi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1000 üyesi. Servis tipi: Grup üyesi. Telegram grup linkiyle kademeli üye teslimatı. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 824.82, 699.00, '1-3 iş günü', 1, 10, NULL, 0, 'active', 130, 'Telegram Grup Üyesi Premium Paketi | 1.000 Üyesi | Yorum Hizmeti', 'Telegram Grup Üyesi Premium Paketi | 1.000 Üyesi için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(583, 49, '', 'telegram-grup-uyesi-kurumsal-paketi-5-000-uyesi', '5000 üyesi için Grup üyesi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 5000 üyesi. Servis tipi: Grup üyesi. Telegram grup linkiyle kademeli üye teslimatı. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 3538.82, 2999.00, '1-3 iş günü', 1, 10, NULL, 0, 'active', 131, 'Telegram Grup Üyesi Kurumsal Paketi | 5.000 Üyesi | Yorum Hizmeti', 'Telegram Grup Üyesi Kurumsal Paketi | 5.000 Üyesi için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipari', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(584, 50, '', 'telegram-goruntulenme-baslangic-paketi-1-000-goruntulenme', '1000 görüntülenme için Post görüntülenme hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1000 görüntülenme. Servis tipi: Post görüntülenme. Kanal gönderileri için görüntülenme desteği. Teslimat: 1-2 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 59.00, NULL, '1-2 iş günü', 1, 10, NULL, 0, 'active', 132, 'Telegram Görüntülenme Başlangıç Paketi | 1.000 Görüntülenme | Yorum Hi', 'Telegram Görüntülenme Başlangıç Paketi | 1.000 Görüntülenme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güve', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(585, 50, '', 'telegram-goruntulenme-profesyonel-paketi-10-000-goruntulenme', '10000 görüntülenme için Post görüntülenme hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 10000 görüntülenme. Servis tipi: Post görüntülenme. Kanal gönderileri için görüntülenme desteği. Teslimat: 1-2 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 299.00, NULL, '1-2 iş günü', 1, 10, NULL, 0, 'active', 133, 'Telegram Görüntülenme Profesyonel Paketi | 10.000 Görüntülenme | Yorum', 'Telegram Görüntülenme Profesyonel Paketi | 10.000 Görüntülenme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla g', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(586, 50, '', 'telegram-goruntulenme-premium-paketi-50-000-goruntulenme', '50000 görüntülenme için Post görüntülenme hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 50000 görüntülenme. Servis tipi: Post görüntülenme. Kanal gönderileri için görüntülenme desteği. Teslimat: 1-2 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 1178.82, 999.00, '1-2 iş günü', 1, 10, NULL, 0, 'active', 134, 'Telegram Görüntülenme Premium Paketi | 50.000 Görüntülenme | Yorum Hiz', 'Telegram Görüntülenme Premium Paketi | 50.000 Görüntülenme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güven', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(587, 50, '', 'telegram-goruntulenme-kurumsal-paketi-100-000-goruntulenme', '100000 görüntülenme için Post görüntülenme hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 100000 görüntülenme. Servis tipi: Post görüntülenme. Kanal gönderileri için görüntülenme desteği. Teslimat: 1-2 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 2122.82, 1799.00, '1-2 iş günü', 1, 10, NULL, 0, 'active', 135, 'Telegram Görüntülenme Kurumsal Paketi | 100.000 Görüntülenme | Yorum H', 'Telegram Görüntülenme Kurumsal Paketi | 100.000 Görüntülenme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güv', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(588, 51, '', 'telegram-reaksiyon-baslangic-paketi-100-reaksiyon', '100 reaksiyon için Emoji reaksiyon hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 100 reaksiyon. Servis tipi: Emoji reaksiyon. Telegram postlarına reaksiyon etkileşimi sağlar. Teslimat: 1-2 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 69.00, NULL, '1-2 iş günü', 1, 10, NULL, 0, 'active', 136, 'Telegram Reaksiyon Başlangıç Paketi | 100 Reaksiyon | Yorum Hizmeti', 'Telegram Reaksiyon Başlangıç Paketi | 100 Reaksiyon için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipa', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(589, 51, '', 'telegram-reaksiyon-profesyonel-paketi-500-reaksiyon', '500 reaksiyon için Emoji reaksiyon hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 500 reaksiyon. Servis tipi: Emoji reaksiyon. Telegram postlarına reaksiyon etkileşimi sağlar. Teslimat: 1-2 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 279.00, NULL, '1-2 iş günü', 1, 10, NULL, 0, 'active', 137, 'Telegram Reaksiyon Profesyonel Paketi | 500 Reaksiyon | Yorum Hizmeti', 'Telegram Reaksiyon Profesyonel Paketi | 500 Reaksiyon için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli si', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(590, 51, '', 'telegram-reaksiyon-premium-paketi-1-000-reaksiyon', '1000 reaksiyon için Emoji reaksiyon hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1000 reaksiyon. Servis tipi: Emoji reaksiyon. Telegram postlarına reaksiyon etkileşimi sağlar. Teslimat: 1-2 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 588.82, 499.00, '1-2 iş günü', 1, 10, NULL, 0, 'active', 138, 'Telegram Reaksiyon Premium Paketi | 1.000 Reaksiyon | Yorum Hizmeti', 'Telegram Reaksiyon Premium Paketi | 1.000 Reaksiyon için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipa', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(591, 51, '', 'telegram-reaksiyon-kurumsal-paketi-5-000-reaksiyon', '5000 reaksiyon için Emoji reaksiyon hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 5000 reaksiyon. Servis tipi: Emoji reaksiyon. Telegram postlarına reaksiyon etkileşimi sağlar. Teslimat: 1-2 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 2358.82, 1999.00, '1-2 iş günü', 1, 10, NULL, 0, 'active', 139, 'Telegram Reaksiyon Kurumsal Paketi | 5.000 Reaksiyon | Yorum Hizmeti', 'Telegram Reaksiyon Kurumsal Paketi | 5.000 Reaksiyon için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sip', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(592, 53, '', 'spotify-dinlenme-baslangic-paketi-1-000-dinlenme', '1000 dinlenme için Şarkı dinlenme hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1000 dinlenme. Servis tipi: Şarkı dinlenme. Şarkı linki ile dinlenme teslimatı yapılır. Teslimat: 2-5 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 99.00, NULL, '2-5 iş günü', 1, 10, NULL, 0, 'active', 140, 'Spotify Dinlenme Başlangıç Paketi | 1.000 Dinlenme | Yorum Hizmeti', 'Spotify Dinlenme Başlangıç Paketi | 1.000 Dinlenme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipar', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:49', '2026-07-02 00:20:49'),
(593, 53, '', 'spotify-dinlenme-profesyonel-paketi-10-000-dinlenme', '10000 dinlenme için Şarkı dinlenme hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 10000 dinlenme. Servis tipi: Şarkı dinlenme. Şarkı linki ile dinlenme teslimatı yapılır. Teslimat: 2-5 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 599.00, NULL, '2-5 iş günü', 1, 10, NULL, 0, 'active', 141, 'Spotify Dinlenme Profesyonel Paketi | 10.000 Dinlenme | Yorum Hizmeti', 'Spotify Dinlenme Profesyonel Paketi | 10.000 Dinlenme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli si', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(594, 53, '', 'spotify-dinlenme-premium-paketi-50-000-dinlenme', '50000 dinlenme için Şarkı dinlenme hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 50000 dinlenme. Servis tipi: Şarkı dinlenme. Şarkı linki ile dinlenme teslimatı yapılır. Teslimat: 2-5 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 2948.82, 2499.00, '2-5 iş günü', 1, 10, NULL, 0, 'active', 142, 'Spotify Dinlenme Premium Paketi | 50.000 Dinlenme | Yorum Hizmeti', 'Spotify Dinlenme Premium Paketi | 50.000 Dinlenme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipari', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(595, 53, '', 'spotify-dinlenme-kurumsal-paketi-100-000-dinlenme', '100000 dinlenme için Şarkı dinlenme hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 100000 dinlenme. Servis tipi: Şarkı dinlenme. Şarkı linki ile dinlenme teslimatı yapılır. Teslimat: 2-5 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 5308.82, 4499.00, '2-5 iş günü', 1, 10, NULL, 0, 'active', 143, 'Spotify Dinlenme Kurumsal Paketi | 100.000 Dinlenme | Yorum Hizmeti', 'Spotify Dinlenme Kurumsal Paketi | 100.000 Dinlenme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipa', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(596, 54, '', 'spotify-takipci-baslangic-paketi-100-takipci', '100 takipçi için Profil takipçi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 100 takipçi. Servis tipi: Profil takipçi. Sanatçı profili için takipçi hizmeti. Teslimat: 2-5 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 119.00, NULL, '2-5 iş günü', 1, 10, NULL, 0, 'active', 144, 'Spotify Takipçi Başlangıç Paketi | 100 Takipçi | Yorum Hizmeti', 'Spotify Takipçi Başlangıç Paketi | 100 Takipçi için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş o', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(597, 54, '', 'spotify-takipci-profesyonel-paketi-500-takipci', '500 takipçi için Profil takipçi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 500 takipçi. Servis tipi: Profil takipçi. Sanatçı profili için takipçi hizmeti. Teslimat: 2-5 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 499.00, NULL, '2-5 iş günü', 1, 10, NULL, 0, 'active', 145, 'Spotify Takipçi Profesyonel Paketi | 500 Takipçi | Yorum Hizmeti', 'Spotify Takipçi Profesyonel Paketi | 500 Takipçi için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(598, 54, '', 'spotify-takipci-premium-paketi-1-000-takipci', '1000 takipçi için Profil takipçi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1000 takipçi. Servis tipi: Profil takipçi. Sanatçı profili için takipçi hizmeti. Teslimat: 2-5 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 1060.82, 899.00, '2-5 iş günü', 1, 10, NULL, 0, 'active', 146, 'Spotify Takipçi Premium Paketi | 1.000 Takipçi | Yorum Hizmeti', 'Spotify Takipçi Premium Paketi | 1.000 Takipçi için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş o', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(599, 54, '', 'spotify-takipci-kurumsal-paketi-5-000-takipci', '5000 takipçi için Profil takipçi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 5000 takipçi. Servis tipi: Profil takipçi. Sanatçı profili için takipçi hizmeti. Teslimat: 2-5 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 4718.82, 3999.00, '2-5 iş günü', 1, 10, NULL, 0, 'active', 147, 'Spotify Takipçi Kurumsal Paketi | 5.000 Takipçi | Yorum Hizmeti', 'Spotify Takipçi Kurumsal Paketi | 5.000 Takipçi için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(600, 55, '', 'spotify-playlist-ekleme-baslangic-paketi-1-ekleme', '1 ekleme için Playlist yerleştirme hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1 ekleme. Servis tipi: Playlist yerleştirme. Parça linki değerlendirilir, uygun playlist çalışması planlanır. Teslimat: 3-10 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 499.00, NULL, '3-10 iş günü', 1, 10, NULL, 0, 'active', 148, 'Spotify Playlist Ekleme Başlangıç Paketi | 1 Ekleme | Yorum Hizmeti', 'Spotify Playlist Ekleme Başlangıç Paketi | 1 Ekleme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipa', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(601, 55, '', 'spotify-playlist-ekleme-profesyonel-paketi-3-ekleme', '3 ekleme için Playlist yerleştirme hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 3 ekleme. Servis tipi: Playlist yerleştirme. Parça linki değerlendirilir, uygun playlist çalışması planlanır. Teslimat: 3-10 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 1199.00, NULL, '3-10 iş günü', 1, 10, NULL, 0, 'active', 149, 'Spotify Playlist Ekleme Profesyonel Paketi | 3 Ekleme | Yorum Hizmeti', 'Spotify Playlist Ekleme Profesyonel Paketi | 3 Ekleme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli si', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(602, 55, '', 'spotify-playlist-ekleme-premium-paketi-5-ekleme', '5 ekleme için Playlist yerleştirme hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 5 ekleme. Servis tipi: Playlist yerleştirme. Parça linki değerlendirilir, uygun playlist çalışması planlanır. Teslimat: 3-10 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 2358.82, 1999.00, '3-10 iş günü', 1, 10, NULL, 0, 'active', 150, 'Spotify Playlist Ekleme Premium Paketi | 5 Ekleme | Yorum Hizmeti', 'Spotify Playlist Ekleme Premium Paketi | 5 Ekleme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipari', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(603, 55, '', 'spotify-playlist-ekleme-kurumsal-paketi-10-ekleme', '10 ekleme için Playlist yerleştirme hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 10 ekleme. Servis tipi: Playlist yerleştirme. Parça linki değerlendirilir, uygun playlist çalışması planlanır. Teslimat: 3-10 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 4128.82, 3499.00, '3-10 iş günü', 1, 10, NULL, 0, 'active', 151, 'Spotify Playlist Ekleme Kurumsal Paketi | 10 Ekleme | Yorum Hizmeti', 'Spotify Playlist Ekleme Kurumsal Paketi | 10 Ekleme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipa', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(604, 56, '', 'spotify-kaydetme-baslangic-paketi-100-kaydetme', '100 kaydetme için Şarkı kaydetme hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 100 kaydetme. Servis tipi: Şarkı kaydetme. Şarkı kaydetme sinyalini destekler. Teslimat: 2-5 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 99.00, NULL, '2-5 iş günü', 1, 10, NULL, 0, 'active', 152, 'Spotify Kaydetme Başlangıç Paketi | 100 Kaydetme | Yorum Hizmeti', 'Spotify Kaydetme Başlangıç Paketi | 100 Kaydetme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(605, 56, '', 'spotify-kaydetme-profesyonel-paketi-500-kaydetme', '500 kaydetme için Şarkı kaydetme hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 500 kaydetme. Servis tipi: Şarkı kaydetme. Şarkı kaydetme sinyalini destekler. Teslimat: 2-5 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 399.00, NULL, '2-5 iş günü', 1, 10, NULL, 0, 'active', 153, 'Spotify Kaydetme Profesyonel Paketi | 500 Kaydetme | Yorum Hizmeti', 'Spotify Kaydetme Profesyonel Paketi | 500 Kaydetme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipar', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(606, 56, '', 'spotify-kaydetme-premium-paketi-1-000-kaydetme', '1000 kaydetme için Şarkı kaydetme hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1000 kaydetme. Servis tipi: Şarkı kaydetme. Şarkı kaydetme sinyalini destekler. Teslimat: 2-5 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 883.82, 749.00, '2-5 iş günü', 1, 10, NULL, 0, 'active', 154, 'Spotify Kaydetme Premium Paketi | 1.000 Kaydetme | Yorum Hizmeti', 'Spotify Kaydetme Premium Paketi | 1.000 Kaydetme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(607, 56, '', 'spotify-kaydetme-kurumsal-paketi-5-000-kaydetme', '5000 kaydetme için Şarkı kaydetme hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 5000 kaydetme. Servis tipi: Şarkı kaydetme. Şarkı kaydetme sinyalini destekler. Teslimat: 2-5 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 3774.82, 3199.00, '2-5 iş günü', 1, 10, NULL, 0, 'active', 155, 'Spotify Kaydetme Kurumsal Paketi | 5.000 Kaydetme | Yorum Hizmeti', 'Spotify Kaydetme Kurumsal Paketi | 5.000 Kaydetme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipari', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(608, 58, '', 'discord-sunucu-uyesi-baslangic-paketi-100-uyesi', '100 üyesi için Sunucu üyesi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 100 üyesi. Servis tipi: Sunucu üyesi. Discord sunucu davet linki gerekir. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 129.00, NULL, '1-3 iş günü', 1, 10, NULL, 0, 'active', 156, 'Discord Sunucu Üyesi Başlangıç Paketi | 100 Üyesi | Yorum Hizmeti', 'Discord Sunucu Üyesi Başlangıç Paketi | 100 Üyesi için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipari', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(609, 58, '', 'discord-sunucu-uyesi-profesyonel-paketi-500-uyesi', '500 üyesi için Sunucu üyesi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 500 üyesi. Servis tipi: Sunucu üyesi. Discord sunucu davet linki gerekir. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 549.00, NULL, '1-3 iş günü', 1, 10, NULL, 0, 'active', 157, 'Discord Sunucu Üyesi Profesyonel Paketi | 500 Üyesi | Yorum Hizmeti', 'Discord Sunucu Üyesi Profesyonel Paketi | 500 Üyesi için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipa', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(610, 58, '', 'discord-sunucu-uyesi-premium-paketi-1-000-uyesi', '1000 üyesi için Sunucu üyesi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1000 üyesi. Servis tipi: Sunucu üyesi. Discord sunucu davet linki gerekir. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 1178.82, 999.00, '1-3 iş günü', 1, 10, NULL, 0, 'active', 158, 'Discord Sunucu Üyesi Premium Paketi | 1.000 Üyesi | Yorum Hizmeti', 'Discord Sunucu Üyesi Premium Paketi | 1.000 Üyesi için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipari', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(611, 58, '', 'discord-sunucu-uyesi-kurumsal-paketi-2-500-uyesi', '2500 üyesi için Sunucu üyesi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 2500 üyesi. Servis tipi: Sunucu üyesi. Discord sunucu davet linki gerekir. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 2712.82, 2299.00, '1-3 iş günü', 1, 10, NULL, 0, 'active', 159, 'Discord Sunucu Üyesi Kurumsal Paketi | 2.500 Üyesi | Yorum Hizmeti', 'Discord Sunucu Üyesi Kurumsal Paketi | 2.500 Üyesi için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipar', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(612, 59, '', 'discord-online-uye-baslangic-paketi-25-uye', '25 üye için Online üye hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 25 üye. Servis tipi: Online üye. Sunucuda online görünürlük desteği sağlar. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 149.00, NULL, '1-3 iş günü', 1, 10, NULL, 0, 'active', 160, 'Discord Online Üye Başlangıç Paketi | 25 Üye | Yorum Hizmeti', 'Discord Online Üye Başlangıç Paketi | 25 Üye için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş olu', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(613, 59, '', 'discord-online-uye-profesyonel-paketi-100-uye', '100 üye için Online üye hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 100 üye. Servis tipi: Online üye. Sunucuda online görünürlük desteği sağlar. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 499.00, NULL, '1-3 iş günü', 1, 10, NULL, 0, 'active', 161, 'Discord Online Üye Profesyonel Paketi | 100 Üye | Yorum Hizmeti', 'Discord Online Üye Profesyonel Paketi | 100 Üye için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(614, 59, '', 'discord-online-uye-premium-paketi-250-uye', '250 üye için Online üye hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 250 üye. Servis tipi: Online üye. Sunucuda online görünürlük desteği sağlar. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 1178.82, 999.00, '1-3 iş günü', 1, 10, NULL, 0, 'active', 162, 'Discord Online Üye Premium Paketi | 250 Üye | Yorum Hizmeti', 'Discord Online Üye Premium Paketi | 250 Üye için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluş', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(615, 59, '', 'discord-online-uye-kurumsal-paketi-500-uye', '500 üye için Online üye hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 500 üye. Servis tipi: Online üye. Sunucuda online görünürlük desteği sağlar. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 2122.82, 1799.00, '1-3 iş günü', 1, 10, NULL, 0, 'active', 163, 'Discord Online Üye Kurumsal Paketi | 500 Üye | Yorum Hizmeti', 'Discord Online Üye Kurumsal Paketi | 500 Üye için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş olu', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50');
INSERT INTO `packages` (`id`, `category_id`, `name`, `slug`, `short_description`, `description`, `image`, `image_alt`, `price`, `discount_price`, `delivery_time`, `min_quantity`, `max_quantity`, `badge`, `is_featured`, `status`, `sort_order`, `seo_title`, `seo_description`, `seo_focus_keyword`, `seo_score`, `canonical_url`, `og_title`, `og_description`, `og_image`, `created_at`, `updated_at`) VALUES
(616, 60, '', 'discord-sunucu-boost-baslangic-paketi-2-boost', '2 boost için Boost hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 2 boost. Servis tipi: Boost. Sunucu boost ihtiyacı için paketli hizmet. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 299.00, NULL, '1-3 iş günü', 1, 10, NULL, 0, 'active', 164, 'Discord Sunucu Boost Başlangıç Paketi | 2 Boost | Yorum Hizmeti', 'Discord Sunucu Boost Başlangıç Paketi | 2 Boost için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(617, 60, '', 'discord-sunucu-boost-profesyonel-paketi-5-boost', '5 boost için Boost hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 5 boost. Servis tipi: Boost. Sunucu boost ihtiyacı için paketli hizmet. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 699.00, NULL, '1-3 iş günü', 1, 10, NULL, 0, 'active', 165, 'Discord Sunucu Boost Profesyonel Paketi | 5 Boost | Yorum Hizmeti', 'Discord Sunucu Boost Profesyonel Paketi | 5 Boost için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipari', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(618, 60, '', 'discord-sunucu-boost-premium-paketi-10-boost', '10 boost için Boost hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 10 boost. Servis tipi: Boost. Sunucu boost ihtiyacı için paketli hizmet. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 1532.82, 1299.00, '1-3 iş günü', 1, 10, NULL, 0, 'active', 166, 'Discord Sunucu Boost Premium Paketi | 10 Boost | Yorum Hizmeti', 'Discord Sunucu Boost Premium Paketi | 10 Boost için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş o', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(619, 60, '', 'discord-sunucu-boost-kurumsal-paketi-20-boost', '20 boost için Boost hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 20 boost. Servis tipi: Boost. Sunucu boost ihtiyacı için paketli hizmet. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 2830.82, 2399.00, '1-3 iş günü', 1, 10, NULL, 0, 'active', 167, 'Discord Sunucu Boost Kurumsal Paketi | 20 Boost | Yorum Hizmeti', 'Discord Sunucu Boost Kurumsal Paketi | 20 Boost için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(620, 62, '', 'linkedin-takipci-baslangic-paketi-100-takipci', '100 takipçi için Profesyonel takipçi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 100 takipçi. Servis tipi: Profesyonel takipçi. LinkedIn şirket sayfası veya profil linki ile çalışır. Teslimat: 2-5 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 199.00, NULL, '2-5 iş günü', 1, 10, NULL, 0, 'active', 168, 'LinkedIn Takipçi Başlangıç Paketi | 100 Takipçi | Yorum Hizmeti', 'LinkedIn Takipçi Başlangıç Paketi | 100 Takipçi için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(621, 62, '', 'linkedin-takipci-profesyonel-paketi-500-takipci', '500 takipçi için Profesyonel takipçi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 500 takipçi. Servis tipi: Profesyonel takipçi. LinkedIn şirket sayfası veya profil linki ile çalışır. Teslimat: 2-5 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 799.00, NULL, '2-5 iş günü', 1, 10, NULL, 0, 'active', 169, 'LinkedIn Takipçi Profesyonel Paketi | 500 Takipçi | Yorum Hizmeti', 'LinkedIn Takipçi Profesyonel Paketi | 500 Takipçi için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipari', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(622, 62, '', 'linkedin-takipci-premium-paketi-1-000-takipci', '1000 takipçi için Profesyonel takipçi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1000 takipçi. Servis tipi: Profesyonel takipçi. LinkedIn şirket sayfası veya profil linki ile çalışır. Teslimat: 2-5 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 1768.82, 1499.00, '2-5 iş günü', 1, 10, NULL, 0, 'active', 170, 'LinkedIn Takipçi Premium Paketi | 1.000 Takipçi | Yorum Hizmeti', 'LinkedIn Takipçi Premium Paketi | 1.000 Takipçi için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(623, 62, '', 'linkedin-takipci-kurumsal-paketi-2-500-takipci', '2500 takipçi için Profesyonel takipçi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 2500 takipçi. Servis tipi: Profesyonel takipçi. LinkedIn şirket sayfası veya profil linki ile çalışır. Teslimat: 2-5 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 4128.82, 3499.00, '2-5 iş günü', 1, 10, NULL, 0, 'active', 171, 'LinkedIn Takipçi Kurumsal Paketi | 2.500 Takipçi | Yorum Hizmeti', 'LinkedIn Takipçi Kurumsal Paketi | 2.500 Takipçi için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(624, 63, '', 'linkedin-begeni-baslangic-paketi-50-begeni', '50 beğeni için Gönderi beğeni hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 50 beğeni. Servis tipi: Gönderi beğeni. LinkedIn gönderileri için profesyonel etkileşim. Teslimat: 2-5 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 149.00, NULL, '2-5 iş günü', 1, 10, NULL, 0, 'active', 172, 'LinkedIn Beğeni Başlangıç Paketi | 50 Beğeni | Yorum Hizmeti', 'LinkedIn Beğeni Başlangıç Paketi | 50 Beğeni için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş olu', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(625, 63, '', 'linkedin-begeni-profesyonel-paketi-250-begeni', '250 beğeni için Gönderi beğeni hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 250 beğeni. Servis tipi: Gönderi beğeni. LinkedIn gönderileri için profesyonel etkileşim. Teslimat: 2-5 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 599.00, NULL, '2-5 iş günü', 1, 10, NULL, 0, 'active', 173, 'LinkedIn Beğeni Profesyonel Paketi | 250 Beğeni | Yorum Hizmeti', 'LinkedIn Beğeni Profesyonel Paketi | 250 Beğeni için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(626, 63, '', 'linkedin-begeni-premium-paketi-500-begeni', '500 beğeni için Gönderi beğeni hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 500 beğeni. Servis tipi: Gönderi beğeni. LinkedIn gönderileri için profesyonel etkileşim. Teslimat: 2-5 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 1296.82, 1099.00, '2-5 iş günü', 1, 10, NULL, 0, 'active', 174, 'LinkedIn Beğeni Premium Paketi | 500 Beğeni | Yorum Hizmeti', 'LinkedIn Beğeni Premium Paketi | 500 Beğeni için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluş', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(627, 63, '', 'linkedin-begeni-kurumsal-paketi-1-000-begeni', '1000 beğeni için Gönderi beğeni hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1000 beğeni. Servis tipi: Gönderi beğeni. LinkedIn gönderileri için profesyonel etkileşim. Teslimat: 2-5 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 2358.82, 1999.00, '2-5 iş günü', 1, 10, NULL, 0, 'active', 175, 'LinkedIn Beğeni Kurumsal Paketi | 1.000 Beğeni | Yorum Hizmeti', 'LinkedIn Beğeni Kurumsal Paketi | 1.000 Beğeni için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş o', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(628, 64, '', 'linkedin-baglanti-baslangic-paketi-50-baglanti', '50 bağlantı için Bağlantı hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 50 bağlantı. Servis tipi: Bağlantı. Profesyonel network görünürlüğü için bağlantı desteği. Teslimat: 2-7 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 199.00, NULL, '2-7 iş günü', 1, 10, NULL, 0, 'active', 176, 'LinkedIn Bağlantı Başlangıç Paketi | 50 Bağlantı | Yorum Hizmeti', 'LinkedIn Bağlantı Başlangıç Paketi | 50 Bağlantı için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(629, 64, '', 'linkedin-baglanti-profesyonel-paketi-100-baglanti', '100 bağlantı için Bağlantı hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 100 bağlantı. Servis tipi: Bağlantı. Profesyonel network görünürlüğü için bağlantı desteği. Teslimat: 2-7 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 349.00, NULL, '2-7 iş günü', 1, 10, NULL, 0, 'active', 177, 'LinkedIn Bağlantı Profesyonel Paketi | 100 Bağlantı | Yorum Hizmeti', 'LinkedIn Bağlantı Profesyonel Paketi | 100 Bağlantı için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipa', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(630, 64, '', 'linkedin-baglanti-premium-paketi-250-baglanti', '250 bağlantı için Bağlantı hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 250 bağlantı. Servis tipi: Bağlantı. Profesyonel network görünürlüğü için bağlantı desteği. Teslimat: 2-7 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 942.82, 799.00, '2-7 iş günü', 1, 10, NULL, 0, 'active', 178, 'LinkedIn Bağlantı Premium Paketi | 250 Bağlantı | Yorum Hizmeti', 'LinkedIn Bağlantı Premium Paketi | 250 Bağlantı için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(631, 64, '', 'linkedin-baglanti-kurumsal-paketi-500-baglanti', '500 bağlantı için Bağlantı hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 500 bağlantı. Servis tipi: Bağlantı. Profesyonel network görünürlüğü için bağlantı desteği. Teslimat: 2-7 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 1768.82, 1499.00, '2-7 iş günü', 1, 10, NULL, 0, 'active', 179, 'LinkedIn Bağlantı Kurumsal Paketi | 500 Bağlantı | Yorum Hizmeti', 'LinkedIn Bağlantı Kurumsal Paketi | 500 Bağlantı için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(632, 65, '', 'linkedin-goruntulenme-baslangic-paketi-1-000-goruntulenme', '1000 görüntülenme için Gönderi görüntülenme hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1000 görüntülenme. Servis tipi: Gönderi görüntülenme. LinkedIn içerikleri için görüntülenme desteği. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 149.00, NULL, '1-3 iş günü', 1, 10, NULL, 0, 'active', 180, 'LinkedIn Görüntülenme Başlangıç Paketi | 1.000 Görüntülenme | Yorum Hi', 'LinkedIn Görüntülenme Başlangıç Paketi | 1.000 Görüntülenme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güve', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(633, 65, '', 'linkedin-goruntulenme-profesyonel-paketi-5-000-goruntulenme', '5000 görüntülenme için Gönderi görüntülenme hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 5000 görüntülenme. Servis tipi: Gönderi görüntülenme. LinkedIn içerikleri için görüntülenme desteği. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 599.00, NULL, '1-3 iş günü', 1, 10, NULL, 0, 'active', 181, 'LinkedIn Görüntülenme Profesyonel Paketi | 5.000 Görüntülenme | Yorum', 'LinkedIn Görüntülenme Profesyonel Paketi | 5.000 Görüntülenme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla gü', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(634, 65, '', 'linkedin-goruntulenme-premium-paketi-10-000-goruntulenme', '10000 görüntülenme için Gönderi görüntülenme hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 10000 görüntülenme. Servis tipi: Gönderi görüntülenme. LinkedIn içerikleri için görüntülenme desteği. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 1178.82, 999.00, '1-3 iş günü', 1, 10, NULL, 0, 'active', 182, 'LinkedIn Görüntülenme Premium Paketi | 10.000 Görüntülenme | Yorum Hiz', 'LinkedIn Görüntülenme Premium Paketi | 10.000 Görüntülenme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güven', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(635, 65, '', 'linkedin-goruntulenme-kurumsal-paketi-50-000-goruntulenme', '50000 görüntülenme için Gönderi görüntülenme hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 50000 görüntülenme. Servis tipi: Gönderi görüntülenme. LinkedIn içerikleri için görüntülenme desteği. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 4718.82, 3999.00, '1-3 iş günü', 1, 10, NULL, 0, 'active', 183, 'LinkedIn Görüntülenme Kurumsal Paketi | 50.000 Görüntülenme | Yorum Hi', 'LinkedIn Görüntülenme Kurumsal Paketi | 50.000 Görüntülenme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güve', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(636, 67, '', 'twitch-takipci-baslangic-paketi-100-takipci', '100 takipçi için Kanal takipçi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 100 takipçi. Servis tipi: Kanal takipçi. Twitch kanal linki ile çalışır. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 149.00, NULL, '1-3 iş günü', 1, 10, NULL, 0, 'active', 184, 'Twitch Takipçi Başlangıç Paketi | 100 Takipçi | Yorum Hizmeti', 'Twitch Takipçi Başlangıç Paketi | 100 Takipçi için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş ol', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(637, 67, '', 'twitch-takipci-profesyonel-paketi-500-takipci', '500 takipçi için Kanal takipçi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 500 takipçi. Servis tipi: Kanal takipçi. Twitch kanal linki ile çalışır. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 599.00, NULL, '1-3 iş günü', 1, 10, NULL, 0, 'active', 185, 'Twitch Takipçi Profesyonel Paketi | 500 Takipçi | Yorum Hizmeti', 'Twitch Takipçi Profesyonel Paketi | 500 Takipçi için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(638, 67, '', 'twitch-takipci-premium-paketi-1-000-takipci', '1000 takipçi için Kanal takipçi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1000 takipçi. Servis tipi: Kanal takipçi. Twitch kanal linki ile çalışır. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 1296.82, 1099.00, '1-3 iş günü', 1, 10, NULL, 0, 'active', 186, 'Twitch Takipçi Premium Paketi | 1.000 Takipçi | Yorum Hizmeti', 'Twitch Takipçi Premium Paketi | 1.000 Takipçi için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş ol', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(639, 67, '', 'twitch-takipci-kurumsal-paketi-2-500-takipci', '2500 takipçi için Kanal takipçi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 2500 takipçi. Servis tipi: Kanal takipçi. Twitch kanal linki ile çalışır. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 2948.82, 2499.00, '1-3 iş günü', 1, 10, NULL, 0, 'active', 187, 'Twitch Takipçi Kurumsal Paketi | 2.500 Takipçi | Yorum Hizmeti', 'Twitch Takipçi Kurumsal Paketi | 2.500 Takipçi için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş o', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(640, 68, '', 'twitch-video-izlenme-baslangic-paketi-1-000-i-zlenme', '1000 i̇zlenme için Video izlenme hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1000 i̇zlenme. Servis tipi: Video izlenme. Klip veya video linki ile izlenme desteği. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 99.00, NULL, '1-3 iş günü', 1, 10, NULL, 0, 'active', 188, 'Twitch Video İzlenme Başlangıç Paketi | 1.000 İzlenme | Yorum Hizmeti', 'Twitch Video İzlenme Başlangıç Paketi | 1.000 İzlenme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli s', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(641, 68, '', 'twitch-video-izlenme-profesyonel-paketi-5-000-i-zlenme', '5000 i̇zlenme için Video izlenme hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 5000 i̇zlenme. Servis tipi: Video izlenme. Klip veya video linki ile izlenme desteği. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 399.00, NULL, '1-3 iş günü', 1, 10, NULL, 0, 'active', 189, 'Twitch Video İzlenme Profesyonel Paketi | 5.000 İzlenme | Yorum Hizme', 'Twitch Video İzlenme Profesyonel Paketi | 5.000 İzlenme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(642, 68, '', 'twitch-video-izlenme-premium-paketi-10-000-i-zlenme', '10000 i̇zlenme için Video izlenme hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 10000 i̇zlenme. Servis tipi: Video izlenme. Klip veya video linki ile izlenme desteği. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 883.82, 749.00, '1-3 iş günü', 1, 10, NULL, 0, 'active', 190, 'Twitch Video İzlenme Premium Paketi | 10.000 İzlenme | Yorum Hizmeti', 'Twitch Video İzlenme Premium Paketi | 10.000 İzlenme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli si', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(643, 68, '', 'twitch-video-izlenme-kurumsal-paketi-50-000-i-zlenme', '50000 i̇zlenme için Video izlenme hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 50000 i̇zlenme. Servis tipi: Video izlenme. Klip veya video linki ile izlenme desteği. Teslimat: 1-3 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 3538.82, 2999.00, '1-3 iş günü', 1, 10, NULL, 0, 'active', 191, 'Twitch Video İzlenme Kurumsal Paketi | 50.000 İzlenme | Yorum Hizmeti', 'Twitch Video İzlenme Kurumsal Paketi | 50.000 İzlenme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli s', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(644, 69, '', 'twitch-canli-izleyici-baslangic-paketi-25-i-zleyici', '25 i̇zleyici için Canlı izleyici hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 25 i̇zleyici. Servis tipi: Canlı izleyici. Yayın saati ve kanal linki gerekir, canlı yayın görünürlüğü sağlar. Teslimat: Yayın saatine göre içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 199.00, NULL, 'Yayın saatine göre', 1, 10, NULL, 0, 'active', 192, 'Twitch Canlı İzleyici Başlangıç Paketi | 25 İzleyici | Yorum Hizmeti', 'Twitch Canlı İzleyici Başlangıç Paketi | 25 İzleyici için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli si', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(645, 69, '', 'twitch-canli-izleyici-profesyonel-paketi-100-i-zleyici', '100 i̇zleyici için Canlı izleyici hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 100 i̇zleyici. Servis tipi: Canlı izleyici. Yayın saati ve kanal linki gerekir, canlı yayın görünürlüğü sağlar. Teslimat: Yayın saatine göre içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 649.00, NULL, 'Yayın saatine göre', 1, 10, NULL, 0, 'active', 193, 'Twitch Canlı İzleyici Profesyonel Paketi | 100 İzleyici | Yorum Hizme', 'Twitch Canlı İzleyici Profesyonel Paketi | 100 İzleyici için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(646, 69, '', 'twitch-canli-izleyici-premium-paketi-250-i-zleyici', '250 i̇zleyici için Canlı izleyici hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 250 i̇zleyici. Servis tipi: Canlı izleyici. Yayın saati ve kanal linki gerekir, canlı yayın görünürlüğü sağlar. Teslimat: Yayın saatine göre içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 1532.82, 1299.00, 'Yayın saatine göre', 1, 10, NULL, 0, 'active', 194, 'Twitch Canlı İzleyici Premium Paketi | 250 İzleyici | Yorum Hizmeti', 'Twitch Canlı İzleyici Premium Paketi | 250 İzleyici için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sip', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(647, 69, '', 'twitch-canli-izleyici-kurumsal-paketi-500-i-zleyici', '500 i̇zleyici için Canlı izleyici hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 500 i̇zleyici. Servis tipi: Canlı izleyici. Yayın saati ve kanal linki gerekir, canlı yayın görünürlüğü sağlar. Teslimat: Yayın saatine göre içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 2830.82, 2399.00, 'Yayın saatine göre', 1, 10, NULL, 0, 'active', 195, 'Twitch Canlı İzleyici Kurumsal Paketi | 500 İzleyici | Yorum Hizmeti', 'Twitch Canlı İzleyici Kurumsal Paketi | 500 İzleyici için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli si', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(648, 70, '', 'seo-analizi-baslangic-raporu-1-web-site', '1 rapor için SEO analiz hizmeti hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1 rapor. Servis tipi: SEO analiz hizmeti. Teknik durum, meta başlıklar, hız, indekslenme ve temel rakip taraması yapılır. Teslimat: 3-5 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 799.00, NULL, '3-5 iş günü', 1, 10, NULL, 1, 'active', 196, 'SEO Analizi Başlangıç Raporu | 1 Web Site | Yorum Hizmeti', 'SEO Analizi Başlangıç Raporu | 1 Web Site için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluştu', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(649, 70, '', 'seo-analizi-profesyonel-raporu-rakip-dahil', '1 rapor için SEO analiz hizmeti hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1 rapor. Servis tipi: SEO analiz hizmeti. Rakip analizi, anahtar kelime fırsatları ve teknik SEO kontrol listesi hazırlanır. Teslimat: 5-7 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 1499.00, NULL, '5-7 iş günü', 1, 10, NULL, 0, 'active', 197, 'SEO Analizi Profesyonel Raporu | Rakip Dahil | Yorum Hizmeti', 'SEO Analizi Profesyonel Raporu | Rakip Dahil için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş olu', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(650, 71, '', 'backlink-baslangic-paketi-10-link', '10 backlink için Backlink çalışması hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 10 backlink. Servis tipi: Backlink çalışması. Doğal dağılımlı tanıtım ve link planı hazırlanır, kalite kontrol yapılır. Teslimat: 7-15 gün içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 999.00, NULL, '7-15 gün', 1, 10, NULL, 0, 'active', 198, 'Backlink Başlangıç Paketi | 10 Link | Yorum Hizmeti', 'Backlink Başlangıç Paketi | 10 Link için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluşturun.', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(651, 71, '', 'otoriter-backlink-profesyonel-paketi-25-link', '25 backlink için Backlink çalışması hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 25 backlink. Servis tipi: Backlink çalışması. Otoriter kaynak dağılımı ve anchor text dengesiyle backlink planı. Teslimat: 10-20 gün içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 2499.00, NULL, '10-20 gün', 1, 10, NULL, 0, 'active', 199, 'Otoriter Backlink Profesyonel Paketi | 25 Link | Yorum Hizmeti', 'Otoriter Backlink Profesyonel Paketi | 25 Link için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş o', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(652, 72, '', 'yerel-seo-baslangic-paketi-1-isletme', '1 işletme için Yerel SEO hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1 işletme. Servis tipi: Yerel SEO. Google Harita, yerel anahtar kelime, NAP tutarlılığı ve profil optimizasyonu içerir. Teslimat: 7-15 gün içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 1499.00, NULL, '7-15 gün', 1, 10, NULL, 1, 'active', 200, 'Yerel SEO Başlangıç Paketi | 1 İşletme | Yorum Hizmeti', 'Yerel SEO Başlangıç Paketi | 1 İşletme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluşturun', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(653, 72, '', 'yerel-seo-kurumsal-paket-aylik', '1 aylık için Yerel SEO yönetimi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1 aylık. Servis tipi: Yerel SEO yönetimi. Aylık yerel SEO izleme, içerik önerileri ve Google İşletme Profili bakımını kapsar. Teslimat: 30 gün içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 5898.82, 4999.00, '30 gün', 1, 10, NULL, 0, 'active', 201, 'Yerel SEO Kurumsal Paket | Aylık | Yorum Hizmeti', 'Yerel SEO Kurumsal Paket | Aylık için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluşturun.', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(654, 73, '', 'teknik-seo-duzeltme-paketi-baslangic', '1 site için Teknik SEO hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1 site. Servis tipi: Teknik SEO. Tarama hataları, canonical, sitemap, robots, hız ve temel teknik düzen kontrolü yapılır. Teslimat: 7-15 gün içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 1999.00, NULL, '7-15 gün', 1, 10, NULL, 0, 'active', 202, 'Teknik SEO Düzeltme Paketi | Başlangıç | Yorum Hizmeti', 'Teknik SEO Düzeltme Paketi | Başlangıç için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluşturun', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(655, 74, '', 'seo-uyumlu-blog-makalesi-paketi-4-makale', '4 makale için SEO içerik üretimi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 4 makale. Servis tipi: SEO içerik üretimi. Odak anahtar kelime, meta açıklama ve H2/H3 düzeniyle makale hazırlanır. Teslimat: 7-10 gün içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 1599.00, NULL, '7-10 gün', 1, 10, NULL, 1, 'active', 203, 'SEO Uyumlu Blog Makalesi Paketi | 4 Makale | Yorum Hizmeti', 'SEO Uyumlu Blog Makalesi Paketi | 4 Makale için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluşt', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(656, 75, '', 'google-ads-kurulum-paketi-tek-kampanya', '1 kampanya için Google Ads yönetimi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1 kampanya. Servis tipi: Google Ads yönetimi. Dönüşüm odaklı kampanya kurulumu, anahtar kelime gruplama ve reklam metni hazırlanır. Teslimat: 3-5 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 1999.00, NULL, '3-5 iş günü', 1, 10, NULL, 1, 'active', 204, 'Google Ads Kurulum Paketi | Tek Kampanya | Yorum Hizmeti', 'Google Ads Kurulum Paketi | Tek Kampanya için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluştur', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(657, 75, '', 'google-ads-aylik-yonetim-paketi-standart', '1 aylık için Google Ads yönetimi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1 aylık. Servis tipi: Google Ads yönetimi. Aylık optimizasyon, negatif kelime kontrolü ve performans raporu içerir. Teslimat: 30 gün içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 5898.82, 4999.00, '30 gün', 1, 10, NULL, 0, 'active', 205, 'Google Ads Aylık Yönetim Paketi | Standart | Yorum Hizmeti', 'Google Ads Aylık Yönetim Paketi | Standart için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluşt', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(658, 76, '', 'meta-reklam-kurulum-paketi-facebook-instagram', '1 kampanya için Meta reklam yönetimi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1 kampanya. Servis tipi: Meta reklam yönetimi. Hedef kitle, reklam seti, kreatif yönlendirme ve kampanya kurulumu yapılır. Teslimat: 3-5 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 1999.00, NULL, '3-5 iş günü', 1, 10, NULL, 1, 'active', 206, 'Meta Reklam Kurulum Paketi | Facebook + Instagram | Yorum Hizmeti', 'Meta Reklam Kurulum Paketi | Facebook + Instagram için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipari', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(659, 76, '', 'meta-reklam-aylik-yonetim-paketi-standart', '1 aylık için Meta reklam yönetimi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1 aylık. Servis tipi: Meta reklam yönetimi. Aylık reklam optimizasyonu, hedef kitle testi ve raporlama içerir. Teslimat: 30 gün içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 5898.82, 4999.00, '30 gün', 1, 10, NULL, 0, 'active', 207, 'Meta Reklam Aylık Yönetim Paketi | Standart | Yorum Hizmeti', 'Meta Reklam Aylık Yönetim Paketi | Standart için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluş', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(660, 77, '', 'instagram-reklam-kampanya-paketi-1-kampanya', '1 kampanya için Instagram reklam yönetimi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1 kampanya. Servis tipi: Instagram reklam yönetimi. Instagram hedef kitle, reklam metni ve kampanya kurulumu yapılır. Teslimat: 3-5 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 1799.00, NULL, '3-5 iş günü', 1, 10, NULL, 0, 'active', 208, 'Instagram Reklam Kampanya Paketi | 1 Kampanya | Yorum Hizmeti', 'Instagram Reklam Kampanya Paketi | 1 Kampanya için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş ol', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(661, 78, '', 'tiktok-reklam-kurulum-paketi-1-kampanya', '1 kampanya için TikTok reklam yönetimi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1 kampanya. Servis tipi: TikTok reklam yönetimi. TikTok reklam hesabı kampanya kurulumu ve hedef kitle düzeni hazırlanır. Teslimat: 3-5 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 1999.00, NULL, '3-5 iş günü', 1, 10, NULL, 0, 'active', 209, 'TikTok Reklam Kurulum Paketi | 1 Kampanya | Yorum Hizmeti', 'TikTok Reklam Kurulum Paketi | 1 Kampanya için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluştu', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(662, 79, '', 'youtube-reklam-kurulum-paketi-video-kampanya', '1 kampanya için YouTube reklam yönetimi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1 kampanya. Servis tipi: YouTube reklam yönetimi. Video reklam kampanyası kurulumu, hedefleme ve temel optimizasyon yapılır. Teslimat: 3-5 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 2499.00, NULL, '3-5 iş günü', 1, 10, NULL, 0, 'active', 210, 'YouTube Reklam Kurulum Paketi | Video Kampanya | Yorum Hizmeti', 'YouTube Reklam Kurulum Paketi | Video Kampanya için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş o', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(663, 80, '', 'marka-itibar-yonetimi-baslangic-paketi-1-marka', '1 marka için İtibar yönetimi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1 marka. Servis tipi: İtibar yönetimi. Marka arama sonuçları, yorumlar ve şikayet alanları için izleme planı hazırlanır. Teslimat: 7-15 gün içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 2499.00, NULL, '7-15 gün', 1, 10, NULL, 1, 'active', 211, 'Marka İtibar Yönetimi Başlangıç Paketi | 1 Marka | Yorum Hizmeti', 'Marka İtibar Yönetimi Başlangıç Paketi | 1 Marka için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(664, 81, '', 'sikayet-yonetimi-profesyonel-paketi-aylik', '1 aylık için Şikayet yönetimi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1 aylık. Servis tipi: Şikayet yönetimi. Şikayet takibi, yanıt önerileri ve marka iletişim planı hazırlanır. Teslimat: 30 gün içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 5898.82, 4999.00, '30 gün', 1, 10, NULL, 0, 'active', 212, 'Şikayet Yönetimi Profesyonel Paketi | Aylık | Yorum Hizmeti', 'Şikayet Yönetimi Profesyonel Paketi | Aylık için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluş', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(665, 82, '', 'olumsuz-yorum-yonetimi-paketi-20-yanit', '20 yanıt için Yorum yanıt yönetimi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 20 yanıt. Servis tipi: Yorum yanıt yönetimi. Olumsuz yorumlar için profesyonel yanıt şablonları ve aksiyon planı hazırlanır. Teslimat: 5-10 gün içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 1499.00, NULL, '5-10 gün', 1, 10, NULL, 0, 'active', 213, 'Olumsuz Yorum Yönetimi Paketi | 20 Yanıt | Yorum Hizmeti', 'Olumsuz Yorum Yönetimi Paketi | 20 Yanıt için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluştur', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(666, 83, '', 'online-itibar-kurumsal-paket-aylik', '1 aylık için Online itibar yönetimi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1 aylık. Servis tipi: Online itibar yönetimi. Yorum, şikayet, arama görünürlüğü ve sosyal medya algısı aylık takip edilir. Teslimat: 30 gün içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 8258.82, 6999.00, '30 gün', 1, 10, NULL, 0, 'active', 214, 'Online İtibar Kurumsal Paket | Aylık | Yorum Hizmeti', 'Online İtibar Kurumsal Paket | Aylık için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluşturun.', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(667, 90, '', 'kurumsal-web-site-tasarim-paketi-5-sayfa', '5 sayfa için Web tasarım hizmeti hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 5 sayfa. Servis tipi: Web tasarım hizmeti. SEO uyumlu, mobil uyumlu kurumsal web site tasarımı hazırlanır. Teslimat: 10-20 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 9438.82, 7999.00, '10-20 iş günü', 1, 10, NULL, 1, 'active', 215, 'Kurumsal Web Site Tasarım Paketi | 5 Sayfa | Yorum Hizmeti', 'Kurumsal Web Site Tasarım Paketi | 5 Sayfa için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluşt', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(668, 91, '', 'landing-page-tasarim-paketi-tek-sayfa', '1 sayfa için Landing page tasarım hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1 sayfa. Servis tipi: Landing page tasarım. Reklam kampanyaları için dönüşüm odaklı tek sayfa tasarım hazırlanır. Teslimat: 5-10 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 3999.00, NULL, '5-10 iş günü', 1, 10, NULL, 0, 'active', 216, 'Landing Page Tasarım Paketi | Tek Sayfa | Yorum Hizmeti', 'Landing Page Tasarım Paketi | Tek Sayfa için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluşturu', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(669, 92, '', 'wordpress-kurumsal-site-paketi-baslangic', '1 site için WordPress web tasarım hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1 site. Servis tipi: WordPress web tasarım. WordPress tabanlı yönetilebilir, SEO uyumlu kurumsal site kurulumu. Teslimat: 10-15 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 8258.82, 6999.00, '10-15 iş günü', 1, 10, NULL, 0, 'active', 217, 'WordPress Kurumsal Site Paketi | Başlangıç | Yorum Hizmeti', 'WordPress Kurumsal Site Paketi | Başlangıç için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluşt', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(670, 93, '', 'e-ticaret-site-kurulum-paketi-baslangic', '1 site için E-ticaret kurulum hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1 site. Servis tipi: E-ticaret kurulum. Ürün, kategori, ödeme ve kargo altyapısına uygun e-ticaret site kurulumu. Teslimat: 15-25 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 12999.00, NULL, '15-25 iş günü', 1, 10, NULL, 1, 'active', 218, 'E-Ticaret Site Kurulum Paketi | Başlangıç | Yorum Hizmeti', 'E-Ticaret Site Kurulum Paketi | Başlangıç için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluştu', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50');
INSERT INTO `packages` (`id`, `category_id`, `name`, `slug`, `short_description`, `description`, `image`, `image_alt`, `price`, `discount_price`, `delivery_time`, `min_quantity`, `max_quantity`, `badge`, `is_featured`, `status`, `sort_order`, `seo_title`, `seo_description`, `seo_focus_keyword`, `seo_score`, `canonical_url`, `og_title`, `og_description`, `og_image`, `created_at`, `updated_at`) VALUES
(671, 94, '', 'pazaryeri-entegrasyon-danismanligi-baslangic', '1 danışmanlık için Pazaryeri entegrasyonu hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1 danışmanlık. Servis tipi: Pazaryeri entegrasyonu. Trendyol, Hepsiburada veya N11 süreçleri için entegrasyon planı hazırlanır. Teslimat: 7-15 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 4999.00, NULL, '7-15 iş günü', 1, 10, NULL, 0, 'active', 219, 'Pazaryeri Entegrasyon Danışmanlığı | Başlangıç | Yorum Hizmeti', 'Pazaryeri Entegrasyon Danışmanlığı | Başlangıç için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş o', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(672, 95, '', 'woocommerce-opencart-bakim-paketi-aylik', '1 aylık için E-ticaret bakım hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1 aylık. Servis tipi: E-ticaret bakım. Ürün, kategori, kampanya ve temel teknik bakım desteği. Teslimat: 30 gün içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 4718.82, 3999.00, '30 gün', 1, 10, NULL, 0, 'active', 220, 'WooCommerce / OpenCart Bakım Paketi | Aylık | Yorum Hizmeti', 'WooCommerce / OpenCart Bakım Paketi | Aylık için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluş', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(673, 96, '', 'mobil-uygulama-arayuz-tasarim-paketi-baslangic', '1 proje için Mobil uygulama tasarım hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1 proje. Servis tipi: Mobil uygulama tasarım. iOS ve Android için kullanıcı akışı ve arayüz tasarımı hazırlanır. Teslimat: 15-25 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 9999.00, NULL, '15-25 iş günü', 1, 10, NULL, 0, 'active', 221, 'Mobil Uygulama Arayüz Tasarım Paketi | Başlangıç | Yorum Hizmeti', 'Mobil Uygulama Arayüz Tasarım Paketi | Başlangıç için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(674, 97, '', 'ios-ve-android-uygulama-gelistirme-paketi-mvp', '1 proje için Mobil uygulama geliştirme hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1 proje. Servis tipi: Mobil uygulama geliştirme. MVP seviyesinde mobil uygulama geliştirme ve yayın hazırlığı yapılır. Teslimat: 30-60 gün içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 34999.00, NULL, '30-60 gün', 1, 10, NULL, 0, 'active', 222, 'iOS & Android Uygulama Geliştirme Paketi | MVP | Yorum Hizmeti', 'iOS & Android Uygulama Geliştirme Paketi | MVP için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş o', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(675, 98, '', 'app-store-play-store-yayinlama-paketi', '1 yayın için Mağaza yayınlama hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1 yayın. Servis tipi: Mağaza yayınlama. Uygulama mağaza metinleri, görselleri ve yayın kontrol listesi hazırlanır. Teslimat: 5-10 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 4999.00, NULL, '5-10 iş günü', 1, 10, NULL, 0, 'active', 223, 'App Store / Play Store Yayınlama Paketi | Yorum Hizmeti', 'App Store / Play Store Yayınlama Paketi için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluşturu', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(676, 99, '', 'seo-blog-makalesi-paketi-4-makale', '4 makale için SEO içerik hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 4 makale. Servis tipi: SEO içerik. Odak anahtar kelime ve başlık hiyerarşisiyle blog içerikleri hazırlanır. Teslimat: 7-10 gün içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 1799.00, NULL, '7-10 gün', 1, 10, NULL, 1, 'active', 224, 'SEO Blog Makalesi Paketi | 4 Makale | Yorum Hizmeti', 'SEO Blog Makalesi Paketi | 4 Makale için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluşturun.', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(677, 100, '', 'sosyal-medya-icerik-takvimi-paketi-15-icerik', '15 içerik için İçerik planlama hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 15 içerik. Servis tipi: İçerik planlama. Aylık sosyal medya içerik fikirleri, başlık ve açıklama metinleri hazırlanır. Teslimat: 7-10 gün içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 2499.00, NULL, '7-10 gün', 1, 10, NULL, 0, 'active', 225, 'Sosyal Medya İçerik Takvimi Paketi | 15 İçerik | Yorum Hizmeti', 'Sosyal Medya İçerik Takvimi Paketi | 15 İçerik için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş o', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(678, 101, '', 'e-ticaret-urun-aciklamasi-paketi-50-urun', '50 ürün için Ürün açıklaması hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 50 ürün. Servis tipi: Ürün açıklaması. SEO uyumlu ürün başlığı, açıklama ve kısa satış metinleri hazırlanır. Teslimat: 7-12 gün içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 2999.00, NULL, '7-12 gün', 1, 10, NULL, 0, 'active', 226, 'E-Ticaret Ürün Açıklaması Paketi | 50 Ürün | Yorum Hizmeti', 'E-Ticaret Ürün Açıklaması Paketi | 50 Ürün için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluşt', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(679, 102, '', 'logo-tasarim-paketi-3-konsept', '3 konsept için Logo tasarım hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 3 konsept. Servis tipi: Logo tasarım. Markaya uygun 3 logo konsepti ve revizyon süreci içerir. Teslimat: 5-10 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 3499.00, NULL, '5-10 iş günü', 1, 10, NULL, 0, 'active', 227, 'Logo Tasarım Paketi | 3 Konsept | Yorum Hizmeti', 'Logo Tasarım Paketi | 3 Konsept için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluşturun.', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(680, 103, '', 'sosyal-medya-gorsel-tasarim-paketi-10-post', '10 post için Grafik tasarım hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 10 post. Servis tipi: Grafik tasarım. Instagram ve Facebook için marka uyumlu post tasarımları hazırlanır. Teslimat: 5-7 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 2499.00, NULL, '5-7 iş günü', 1, 10, NULL, 1, 'active', 228, 'Sosyal Medya Görsel Tasarım Paketi | 10 Post | Yorum Hizmeti', 'Sosyal Medya Görsel Tasarım Paketi | 10 Post için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş olu', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(681, 104, '', 'reklam-banner-tasarim-paketi-5-kreatif', '5 kreatif için Banner tasarım hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 5 kreatif. Servis tipi: Banner tasarım. Meta ve Google reklamları için dönüşüm odaklı banner tasarımları hazırlanır. Teslimat: 3-5 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 1999.00, NULL, '3-5 iş günü', 1, 10, NULL, 0, 'active', 229, 'Reklam Banner Tasarım Paketi | 5 Kreatif | Yorum Hizmeti', 'Reklam Banner Tasarım Paketi | 5 Kreatif için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluştur', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(682, 105, '', 'google-isletme-profili-kurulum-paketi-1-isletme', '1 işletme için Yerel işletme kurulumu hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1 işletme. Servis tipi: Yerel işletme kurulumu. Yeni işletmeler için Google İşletme Profili kurulum ve doğrulama yönlendirmesi yapılır. Teslimat: 3-5 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 1299.00, NULL, '3-5 iş günü', 1, 10, NULL, 1, 'active', 230, 'Google İşletme Profili Kurulum Paketi | 1 İşletme | Yorum Hizmeti', 'Google İşletme Profili Kurulum Paketi | 1 İşletme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipari', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(683, 106, '', 'qr-yorum-karti-tasarim-paketi-dijital', '1 tasarım için Yorum kartı tasarımı hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1 tasarım. Servis tipi: Yorum kartı tasarımı. Google yorum linkine yönlenen QR kart tasarımı hazırlanır. Teslimat: 2-4 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 799.00, NULL, '2-4 iş günü', 1, 10, NULL, 0, 'active', 231, 'QR Yorum Kartı Tasarım Paketi | Dijital | Yorum Hizmeti', 'QR Yorum Kartı Tasarım Paketi | Dijital için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluşturu', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(684, 107, '', 'harita-optimizasyon-yerel-paket-1-isletme', '1 işletme için Harita optimizasyon hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1 işletme. Servis tipi: Harita optimizasyon. Kategori, açıklama, hizmet alanı ve fotoğraf optimizasyonu yapılır. Teslimat: 5-10 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 1999.00, NULL, '5-10 iş günü', 1, 10, NULL, 0, 'active', 232, 'Harita Optimizasyon Yerel Paket | 1 İşletme | Yorum Hizmeti', 'Harita Optimizasyon Yerel Paket | 1 İşletme için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipariş oluş', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50'),
(685, 108, '', 'yerel-reklam-kampanya-kurulum-paketi-1-kampanya', '1 kampanya için Yerel reklam yönetimi hizmet paketi. Kademeli teslimat, sipariş takip ekranı ve destek dahildir.', 'Paket kapsamı: 1 kampanya. Servis tipi: Yerel reklam yönetimi. Yakın çevre hedeflemeli Google veya Meta reklam kampanyası kurulur. Teslimat: 3-5 iş günü içinde kademeli ilerler. Gerekli bilgi: ilgili profil, gönderi, video, kanal, işletme veya web sayfası bağlantısı. Not: Teslimat hızı ve görünürlük sonucu platform yoğunluğu ve hesap durumuna göre değişebilir.', NULL, '', 1999.00, NULL, '3-5 iş günü', 1, 10, NULL, 0, 'active', 233, 'Yerel Reklam Kampanya Kurulum Paketi | 1 Kampanya | Yorum Hizmeti', 'Yerel Reklam Kampanya Kurulum Paketi | 1 Kampanya için fiyat, teslimat süresi ve hizmet detaylarını inceleyin. SEO uyumlu paket açıklamasıyla güvenli sipari', '', 0, NULL, NULL, NULL, NULL, '2026-07-02 00:20:50', '2026-07-02 00:20:50');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `package_fields`
--

CREATE TABLE `package_fields` (
  `id` int(10) UNSIGNED NOT NULL,
  `package_id` int(10) UNSIGNED NOT NULL,
  `field_key` varchar(50) NOT NULL,
  `field_label` varchar(100) NOT NULL,
  `field_type` enum('text','textarea','url','email','number','select') NOT NULL DEFAULT 'text',
  `is_required` tinyint(1) NOT NULL DEFAULT 1,
  `placeholder` varchar(200) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `pages`
--

CREATE TABLE `pages` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(300) NOT NULL,
  `slug` varchar(300) NOT NULL,
  `content` longtext DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `seo_title` varchar(200) DEFAULT NULL,
  `seo_description` varchar(500) DEFAULT NULL,
  `seo_focus_keyword` varchar(100) DEFAULT NULL,
  `seo_score` int(11) DEFAULT 0,
  `canonical_url` varchar(500) DEFAULT NULL,
  `og_title` varchar(200) DEFAULT NULL,
  `og_description` varchar(500) DEFAULT NULL,
  `og_image` varchar(500) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `pages`
--

INSERT INTO `pages` (`id`, `title`, `slug`, `content`, `status`, `seo_title`, `seo_description`, `seo_focus_keyword`, `seo_score`, `canonical_url`, `og_title`, `og_description`, `og_image`, `created_at`, `updated_at`) VALUES
(1, 'Hakkımızda', 'hakkimizda', '<h2>Hakkımızda</h2><p>Yorum Hizmeti olarak, dijital dünyada işletmelerin büyümesine yardımcı olan profesyonel hizmetler sunuyoruz. Google, sosyal medya, SEO ve dijital reklam alanlarında uzman ekibimizle müşterilerimize güvenilir ve etkili çözümler üretiyoruz.</p><h3>Misyonumuz</h3><p>İşletmelerin dijital varlığını güçlendirmek ve sürdürülebilir büyüme sağlamak için kaliteli, şeffaf ve güvenilir hizmet sunmaktır.</p><h3>Vizyonumuz</h3><p>Türkiye\'nin en güvenilir dijital hizmet platformu olmak.</p>', 'active', 'Hakkımızda - Yorum Hizmeti', 'Yorum Hizmeti hakkında bilgi edinin. Dijital hizmetlerde güvenilir çözüm ortağınız.', NULL, 0, NULL, NULL, NULL, NULL, '2026-06-30 23:57:17', '2026-06-30 23:57:17'),
(2, 'Gizlilik Politikası', 'gizlilik-politikasi', '<h2>Gizlilik Politikası</h2><p>Bu gizlilik politikası, yorumhizmeti.tr web sitesinin kullanıcı verilerini nasıl topladığını, kullandığını ve koruduğunu açıklamaktadır.</p><p>Kişisel verileriniz 6698 sayılı KVKK kapsamında korunmaktadır.</p>', 'active', 'Gizlilik Politikası - Yorum Hizmeti', 'Yorum Hizmeti gizlilik politikası ve veri koruma bilgileri.', NULL, 0, NULL, NULL, NULL, NULL, '2026-06-30 23:57:17', '2026-06-30 23:57:17'),
(3, 'Mesafeli Satış Sözleşmesi', 'mesafeli-satis-sozlesmesi', '<h2>Mesafeli Satış Sözleşmesi</h2><p>Bu sözleşme, 6502 sayılı Tüketicinin Korunması Hakkında Kanun ve Mesafeli Sözleşmeler Yönetmeliği kapsamında düzenlenmiştir.</p>', 'active', 'Mesafeli Satış Sözleşmesi - Yorum Hizmeti', 'Yorum Hizmeti mesafeli satış sözleşmesi.', NULL, 0, NULL, NULL, NULL, NULL, '2026-06-30 23:57:17', '2026-06-30 23:57:17'),
(4, 'KVKK Aydınlatma Metni', 'kvkk', '<h2>KVKK Aydınlatma Metni</h2><p>6698 sayılı Kişisel Verilerin Korunması Kanunu kapsamında, kişisel verilerinizin işlenmesine ilişkin aydınlatma metnidir.</p>', 'active', 'KVKK Aydınlatma Metni - Yorum Hizmeti', 'KVKK kapsamında kişisel verilerin korunması hakkında bilgilendirme.', NULL, 0, NULL, NULL, NULL, NULL, '2026-06-30 23:57:17', '2026-06-30 23:57:17'),
(5, 'İade ve Teslimat Politikası', 'iade-teslimat-politikasi', '<h2>İade ve Teslimat Politikası</h2><p>Hizmet başlamadan önce tam iade yapılmaktadır. Hizmet başladıktan sonra iade koşulları değerlendirilir.</p><h3>Teslimat</h3><p>Dijital hizmetlerimiz, sipariş onayından sonra belirtilen süre içinde teslim edilmektedir.</p>', 'active', 'İade ve Teslimat Politikası - Yorum Hizmeti', 'Yorum Hizmeti iade ve teslimat politikası.', NULL, 0, NULL, NULL, NULL, NULL, '2026-06-30 23:57:17', '2026-06-30 23:57:17');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(10) UNSIGNED NOT NULL,
  `email` varchar(191) NOT NULL,
  `token` varchar(100) NOT NULL,
  `type` enum('user','admin') NOT NULL DEFAULT 'user',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `payments`
--

CREATE TABLE `payments` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `gateway_key` varchar(50) NOT NULL,
  `transaction_id` varchar(200) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `currency` varchar(3) NOT NULL DEFAULT 'TRY',
  `status` enum('pending','completed','failed','refunded') NOT NULL DEFAULT 'pending',
  `raw_response` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `payment_gateways`
--

CREATE TABLE `payment_gateways` (
  `id` int(10) UNSIGNED NOT NULL,
  `gateway_key` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  `type` enum('online','manual') NOT NULL DEFAULT 'online',
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`settings`)),
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `payment_gateways`
--

INSERT INTO `payment_gateways` (`id`, `gateway_key`, `name`, `description`, `type`, `is_active`, `is_default`, `settings`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'bank_transfer', 'Havale/EFT', 'Banka havalesi veya EFT ile ödeme', 'manual', 1, 0, '{}', 1, '2026-06-30 23:57:17', '2026-06-30 23:57:17'),
(2, 'paytr', 'PayTR', 'PayTR sanal POS ile online ödeme', 'online', 0, 0, '{\"merchant_id\":\"\",\"merchant_key\":\"\",\"merchant_salt\":\"\",\"test_mode\":\"1\"}', 2, '2026-06-30 23:57:17', '2026-06-30 23:57:17'),
(3, 'iyzico', 'iyzico', 'iyzico sanal POS ile online ödeme', 'online', 0, 0, '{\"api_key\":\"\",\"secret_key\":\"\",\"base_url\":\"https://sandbox-api.iyzipay.com\",\"test_mode\":\"1\"}', 3, '2026-06-30 23:57:17', '2026-06-30 23:57:17');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `seo_redirects`
--

CREATE TABLE `seo_redirects` (
  `id` int(10) UNSIGNED NOT NULL,
  `old_url` varchar(500) NOT NULL,
  `new_url` varchar(500) NOT NULL,
  `status_code` int(11) NOT NULL DEFAULT 301,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `settings`
--

CREATE TABLE `settings` (
  `id` int(10) UNSIGNED NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_group` varchar(50) NOT NULL DEFAULT 'general'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`) VALUES
(1, 'site_name', 'Yorum Hizmeti', 'general'),
(2, 'site_slogan', 'Google Yorum, Sosyal Medya ve Dijital Güven Hizmetleri', 'general'),
(3, 'site_logo', '', 'general'),
(4, 'site_favicon', '', 'general'),
(5, 'site_email', 'info@yorumhizmeti.tr', 'general'),
(6, 'site_phone', '+905517238707', 'general'),
(7, 'site_whatsapp', '905000000000', 'general'),
(8, 'site_address', 'İstanbul, Türkiye', 'general'),
(9, 'site_url', 'https://yorumhizmeti.tr', 'general'),
(10, 'footer_text', '© 2024 Yorum Hizmeti. Tüm hakları saklıdır.', 'general'),
(11, 'instagram_url', '', 'social'),
(12, 'facebook_url', '', 'social'),
(13, 'youtube_url', '', 'social'),
(14, 'tiktok_url', '', 'social'),
(15, 'x_url', '', 'social'),
(16, 'default_seo_title', 'Yorum Hizmeti | Google Yorum ve Sosyal Medya Paketleri', 'seo'),
(17, 'default_seo_description', 'Google yorum, Instagram, TikTok, YouTube ve sosyal medya paketleriyle işletmenizin dijital güvenini artırın. Hızlı, güvenli ve profesyonel hizmet.', 'seo'),
(18, 'default_og_image', '', 'seo'),
(19, 'maintenance_mode', '0', 'general'),
(20, 'header_script', '', 'scripts'),
(21, 'footer_script', '', 'scripts'),
(22, 'smtp_host', 'smtp.gmail.com', 'mail'),
(23, 'smtp_port', '587', 'mail'),
(24, 'smtp_username', '', 'mail'),
(25, 'smtp_password', '', 'mail'),
(26, 'smtp_encryption', 'tls', 'mail'),
(27, 'smtp_from_email', 'noreply@yorumhizmeti.tr', 'mail'),
(28, 'smtp_from_name', 'Yorum Hizmeti', 'mail'),
(29, 'license_install_id', 'inst_b90c6b45142e0c535d9bd54ab9c404fc', 'license'),
(30, 'license_server_url', 'https://lisans.dijialanya.com/', 'license'),
(31, 'license_key', 'DIGI-Q8G6-U3EU-BZZA-NYAY', 'license'),
(32, 'license_enabled', 'true', 'license'),
(33, 'license_status', 'active', 'license'),
(34, 'license_message', 'Lisans doğrulandı (Sunucu bağlantısı atlandı).', 'license'),
(35, 'license_last_check_at', '2026-07-01 18:14:48', 'license'),
(36, 'license_next_check_at', '2026-07-02 18:14:48', 'license'),
(37, 'license_cached_response', '{\"success\":true,\"status\":\"active\",\"message\":\"Lisans doğrulandı (Sunucu bağlantısı atlandı).\",\"expires_at\":null,\"domain\":null,\"features\":[]}', 'license'),
(38, 'license_grace_until', '', 'license'),
(39, 'whatsapp_number', '+905517238707', 'contact'),
(40, 'social_instagram', '', 'social'),
(41, 'social_twitter', '', 'social'),
(42, 'social_youtube', '', 'social'),
(43, 'theme_primary', '#EA580C', 'theme'),
(44, 'theme_secondary', '#9A3412', 'theme'),
(45, 'theme_accent', '#F97316', 'theme'),
(46, 'theme_button', '#EA580C', 'theme'),
(47, 'theme_button_hover', '#C2410C', 'theme'),
(48, 'theme_bg', '#FFF7ED', 'theme'),
(49, 'theme_card', '#FFFFFF', 'theme'),
(50, 'theme_text', '#1C1917', 'theme'),
(51, 'theme_muted', '#78716C', 'theme'),
(52, 'theme_border', '#FED7AA', 'theme'),
(53, 'google_analytics_id', '', 'seo');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `support_messages`
--

CREATE TABLE `support_messages` (
  `id` int(10) UNSIGNED NOT NULL,
  `ticket_id` int(10) UNSIGNED NOT NULL,
  `sender_type` enum('user','admin') NOT NULL,
  `sender_id` int(10) UNSIGNED NOT NULL,
  `message` text NOT NULL,
  `attachment` varchar(500) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `support_tickets`
--

CREATE TABLE `support_tickets` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED DEFAULT NULL,
  `ticket_number` varchar(20) NOT NULL,
  `subject` varchar(300) NOT NULL,
  `priority` enum('low','medium','high','urgent') NOT NULL DEFAULT 'medium',
  `status` enum('open','answered','customer_reply','closed') NOT NULL DEFAULT 'open',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(191) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('active','inactive','banned') NOT NULL DEFAULT 'active',
  `email_verified_at` datetime DEFAULT NULL,
  `last_login_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_admin` (`admin_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_date` (`created_at`);

--
-- Tablo için indeksler `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Tablo için indeksler `bank_accounts`
--
ALTER TABLE `bank_accounts`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `bank_transfer_notifications`
--
ALTER TABLE `bank_transfer_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order` (`order_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_status` (`status`);

--
-- Tablo için indeksler `blog_categories`
--
ALTER TABLE `blog_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_status` (`status`);

--
-- Tablo için indeksler `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_category` (`blog_category_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_published` (`published_at`);

--
-- Tablo için indeksler `blog_post_tags`
--
ALTER TABLE `blog_post_tags`
  ADD PRIMARY KEY (`post_id`,`tag_id`),
  ADD KEY `idx_post_id` (`post_id`),
  ADD KEY `idx_tag_id` (`tag_id`);

--
-- Tablo için indeksler `blog_tags`
--
ALTER TABLE `blog_tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Tablo için indeksler `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_parent` (`parent_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_sort` (`sort_order`);

--
-- Tablo için indeksler `email_templates`
--
ALTER TABLE `email_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `template_key` (`template_key`);

--
-- Tablo için indeksler `faqs`
--
ALTER TABLE `faqs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_sort` (`sort_order`);

--
-- Tablo için indeksler `home_sections`
--
ALTER TABLE `home_sections`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `section_key` (`section_key`);

--
-- Tablo için indeksler `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_order_number` (`order_number`),
  ADD KEY `idx_status` (`order_status`),
  ADD KEY `idx_payment` (`payment_status`);

--
-- Tablo için indeksler `order_fields`
--
ALTER TABLE `order_fields`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order` (`order_id`),
  ADD KEY `idx_item` (`order_item_id`);

--
-- Tablo için indeksler `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order` (`order_id`);

--
-- Tablo için indeksler `order_status_logs`
--
ALTER TABLE `order_status_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order` (`order_id`);

--
-- Tablo için indeksler `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_category` (`category_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_sort` (`sort_order`),
  ADD KEY `idx_price` (`price`);

--
-- Tablo için indeksler `package_fields`
--
ALTER TABLE `package_fields`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_package` (`package_id`);

--
-- Tablo için indeksler `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_status` (`status`);

--
-- Tablo için indeksler `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_token` (`token`);

--
-- Tablo için indeksler `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order` (`order_id`),
  ADD KEY `idx_gateway` (`gateway_key`);

--
-- Tablo için indeksler `payment_gateways`
--
ALTER TABLE `payment_gateways`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `gateway_key` (`gateway_key`);

--
-- Tablo için indeksler `seo_redirects`
--
ALTER TABLE `seo_redirects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_old_url` (`old_url`(191));

--
-- Tablo için indeksler `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`),
  ADD KEY `idx_key` (`setting_key`),
  ADD KEY `idx_group` (`setting_group`);

--
-- Tablo için indeksler `support_messages`
--
ALTER TABLE `support_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ticket` (`ticket_id`);

--
-- Tablo için indeksler `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ticket_number` (`ticket_number`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_ticket` (`ticket_number`);

--
-- Tablo için indeksler `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_status` (`status`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `bank_accounts`
--
ALTER TABLE `bank_accounts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Tablo için AUTO_INCREMENT değeri `bank_transfer_notifications`
--
ALTER TABLE `bank_transfer_notifications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `blog_categories`
--
ALTER TABLE `blog_categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Tablo için AUTO_INCREMENT değeri `blog_posts`
--
ALTER TABLE `blog_posts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Tablo için AUTO_INCREMENT değeri `blog_tags`
--
ALTER TABLE `blog_tags`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;

--
-- Tablo için AUTO_INCREMENT değeri `email_templates`
--
ALTER TABLE `email_templates`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Tablo için AUTO_INCREMENT değeri `faqs`
--
ALTER TABLE `faqs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Tablo için AUTO_INCREMENT değeri `home_sections`
--
ALTER TABLE `home_sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Tablo için AUTO_INCREMENT değeri `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `order_fields`
--
ALTER TABLE `order_fields`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `order_status_logs`
--
ALTER TABLE `order_status_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `packages`
--
ALTER TABLE `packages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=686;

--
-- Tablo için AUTO_INCREMENT değeri `package_fields`
--
ALTER TABLE `package_fields`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Tablo için AUTO_INCREMENT değeri `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Tablo için AUTO_INCREMENT değeri `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `payment_gateways`
--
ALTER TABLE `payment_gateways`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Tablo için AUTO_INCREMENT değeri `seo_redirects`
--
ALTER TABLE `seo_redirects`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- Tablo için AUTO_INCREMENT değeri `support_messages`
--
ALTER TABLE `support_messages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `support_tickets`
--
ALTER TABLE `support_tickets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
