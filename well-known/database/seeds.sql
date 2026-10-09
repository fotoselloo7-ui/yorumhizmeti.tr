-- =====================================================
-- YorumPanel Pro - Seed Data
-- =====================================================

SET NAMES utf8mb4;

-- ─── Varsayılan Admin ───
INSERT INTO `admins` (`name`, `email`, `password`, `role`, `status`, `must_change_password`) VALUES
('Admin', 'admin@yorumhizmeti.tr', '$2y$10$kijA4bMSuQGyZ1/erH9/4uIkl1UDYJNlKEck7pniTdxZJhYxo2IGq', 'super_admin', 'active', 1);
-- Şifre: Admin123!

-- ─── Ödeme Modülleri ───
INSERT INTO `payment_gateways` (`gateway_key`, `name`, `description`, `type`, `is_active`, `is_default`, `settings`, `sort_order`) VALUES
('bank_transfer', 'Havale/EFT', 'Banka havalesi veya EFT ile ödeme', 'manual', 1, 0, '{}', 1),
('paytr', 'PayTR', 'PayTR sanal POS ile online ödeme', 'online', 0, 0, '{"merchant_id":"","merchant_key":"","merchant_salt":"","test_mode":"1"}', 2),
('iyzico', 'iyzico', 'iyzico sanal POS ile online ödeme', 'online', 0, 0, '{"api_key":"","secret_key":"","base_url":"https://sandbox-api.iyzipay.com","test_mode":"1"}', 3);

-- ─── Banka Hesapları ───
INSERT INTO `bank_accounts` (`bank_name`, `account_holder`, `iban`, `branch`, `status`, `sort_order`) VALUES
('Ziraat Bankası', 'NetVera Teknoloji Yazılım', 'TR00 0000 0000 0000 0000 0000 00', 'Online', 'active', 1),
('Garanti BBVA', 'NetVera Teknoloji Yazılım', 'TR00 0000 0000 0000 0000 0000 00', 'Online', 'active', 2);

-- ─── Site Ayarları ───
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_group`) VALUES
('site_name', 'NetVera Teknoloji Yazılım', 'general'),
('site_slogan', 'Yazılım, Dijital Ajans ve Sosyal Medya Hizmetleri', 'general'),
('site_logo', '', 'general'),
('site_favicon', '', 'general'),
('site_email', 'info@yorumhizmeti.tr', 'general'),
('site_phone', '+90 500 000 00 00', 'general'),
('site_whatsapp', '905000000000', 'general'),
('site_address', 'İstanbul, Türkiye', 'general'),
('site_url', 'https://netvera.tr', 'general'),
('footer_text', '© NetVera Teknoloji Yazılım. Tüm hakları saklıdır.', 'general'),
('instagram_url', '', 'social'),
('facebook_url', '', 'social'),
('youtube_url', '', 'social'),
('tiktok_url', '', 'social'),
('x_url', '', 'social'),
('default_seo_title', 'NetVera Teknoloji Yazılım | Yazılım, Dijital Ajans ve Sosyal Medya', 'seo'),
('default_seo_description', 'NetVera Teknoloji Yazılım: hazır yazılım ve web çözümleri, dijital ajans, SEO, reklam yönetimi ve Instagram, TikTok, YouTube hizmetlerini keşfedin.', 'seo'),
('default_og_image', '', 'seo'),
('maintenance_mode', '0', 'general'),
('header_script', '', 'scripts'),
('footer_script', '', 'scripts'),
('smtp_host', 'smtp.gmail.com', 'mail'),
('smtp_port', '587', 'mail'),
('smtp_username', '', 'mail'),
('smtp_password', '', 'mail'),
('smtp_encryption', 'tls', 'mail'),
('smtp_from_email', 'noreply@yorumhizmeti.tr', 'mail'),
('smtp_from_name', 'Yorum Hizmeti', 'mail');

-- ─── Kategoriler ───
INSERT INTO `categories` (`name`, `slug`, `description`, `icon_key`, `sort_order`, `status`, `seo_title`, `seo_description`) VALUES
('Google Hizmetleri', 'google-hizmetleri', 'Google İşletme Profili, Google Harita ve Google SEO hizmetleri', 'globe', 1, 'active', 'Google Hizmetleri - NetVera', 'Profesyonel Google hizmetleri ile işletmenizi dijitalde öne çıkarın.'),
('Instagram Hizmetleri', 'instagram-hizmetleri', 'Instagram büyüme, etkileşim ve yönetim hizmetleri', 'camera', 2, 'active', 'Instagram Hizmetleri | Takipçi, Beğeni ve Reels – NetVera', 'Instagram takipçi, beğeni ve Reels izlenme hizmetlerini keşfedin. Paket miktarlarını, hedef bağlantılarını ve teslimat koşullarını karşılaştırın.'),
('TikTok Hizmetleri', 'tiktok-hizmetleri', 'TikTok büyüme ve etkileşim hizmetleri', 'video', 3, 'active', 'TikTok Hizmetleri | Takipçi, Beğeni ve İzlenme – NetVera', 'TikTok takipçi, beğeni ve video izlenme paketlerini inceleyin. Hedef kullanıcı veya video bağlantısını ve hizmet koşullarını karşılaştırın.'),
('YouTube Hizmetleri', 'youtube-hizmetleri', 'YouTube kanal büyüme ve video tanıtım hizmetleri', 'play-circle', 4, 'active', 'YouTube Hizmetleri | Abone ve İzlenme – NetVera', 'YouTube kanal ve video hizmetleriyle abone, izlenme ve tanıtım seçeneklerini karşılaştırın. Paket koşullarını ve hedef bağlantı bilgilerini inceleyin.'),
('Facebook Hizmetleri', 'facebook-hizmetleri', 'Facebook sayfa yönetimi ve büyüme hizmetleri', 'thumbs-up', 5, 'active', 'Facebook Hizmetleri | Sayfa ve Gönderi – NetVera', 'Facebook sayfa ve gönderi hizmetlerini keşfedin. Hedef bağlantısı, paket kapsamı ve teslimat koşullarını karşılaştırın.'),
('SEO Hizmetleri', 'seo-hizmetleri', 'Arama motoru optimizasyonu ve site içi SEO hizmetleri', 'search', 6, 'active', 'SEO Hizmetleri | Teknik SEO ve Danışmanlık – NetVera', 'Teknik SEO, içerik optimizasyonu, site içi iyileştirme ve arama görünürlüğü çalışmalarını karşılaştırın. Kapsam ve ölçümleme ayrıntılarını inceleyin.'),
('Dijital Reklam', 'dijital-reklam', 'Google Ads, Facebook Ads ve dijital reklam yönetimi', 'target', 7, 'active', 'Dijital Reklam Ajansı | Google Ads ve Meta – NetVera', 'Google Ads, Meta ve sosyal medya reklam yönetimi hizmetlerini keşfedin. Kampanya planlaması, ölçümleme ve optimizasyon kapsamını karşılaştırın.'),
('İtibar Yönetimi', 'itibar-yonetimi', 'Dijital marka itibarı, müşteri iletişimi ve geri bildirim yönetimi', 'shield', 8, 'active', 'Dijital Marka ve İtibar Yönetimi – NetVera', 'Dijital marka ve itibar yönetimi, müşteri iletişimi ve geri bildirim takibi hizmetlerini keşfedin. Şeffaf yönetim süreçlerini inceleyin.');

-- ─── Örnek Paketler ───
INSERT INTO `packages` (`category_id`, `name`, `slug`, `short_description`, `description`, `price`, `discount_price`, `delivery_time`, `min_quantity`, `max_quantity`, `badge`, `status`, `sort_order`, `seo_title`, `seo_description`, `seo_focus_keyword`) VALUES
(1, 'Google İşletme Profili Optimizasyonu', 'google-isletme-profili-optimizasyonu', 'Google İşletme Profilinizi profesyonel olarak optimize ediyoruz.', '<h2>Google İşletme Profili Optimizasyonu</h2><p>İşletmenizin Google Haritalar ve arama sonuçlarında daha görünür olmasını sağlıyoruz.</p><ul><li>Profil bilgi güncellemesi</li><li>Kategori optimizasyonu</li><li>Fotoğraf ve video ekleme</li><li>Açıklama optimizasyonu</li><li>SSS bölümü oluşturma</li></ul>', 1500.00, 1200.00, '3-5 iş günü', 1, 1, 'Popüler', 'active', 1, 'Google İşletme Profili Optimizasyonu', 'Google İşletme Profilinizi optimize ederek yerel aramalarda üst sıralara çıkın.', 'google işletme profili'),
(1, 'Google Harita Yorum Paketi - 10 Yorum', 'google-harita-yorum-10', '10 adet organik Google Harita yorumu.', '<h2>Google Harita Yorum Paketi</h2><p>İşletmeniz için 10 adet gerçek, organik Google Harita yorumu.</p><ul><li>Gerçek hesaplardan yorumlar</li><li>Doğal ve farklı içerikler</li><li>Kademeli paylaşım</li><li>Garanti ve destek</li></ul>', 500.00, NULL, '5-7 iş günü', 1, 10, NULL, 'active', 2, 'Google Harita Yorum Paketi', 'Google Harita yorumlarıyla işletmenizin güvenilirliğini artırın.', 'google harita yorum'),
(2, 'Instagram 1000 Takipçi', 'instagram-1000-takipci', '1000 gerçek Instagram takipçisi.', '<h2>Instagram Takipçi Paketi</h2><p>Hesabınıza 1000 gerçek ve aktif takipçi kazandırıyoruz.</p>', 300.00, 250.00, '2-3 iş günü', 1, 1, 'Çok Satan', 'active', 1, 'Instagram 1000 Takipçi Paketi', 'Instagram takipçi sayınızı hızla artırın.', 'instagram takipçi'),
(3, 'TikTok 5000 İzlenme', 'tiktok-5000-izlenme', 'TikTok videolarınız için 5000 izlenme.', '<h2>TikTok İzlenme Paketi</h2><p>Videolarınızın keşfet''e düşme şansını artırın.</p>', 200.00, NULL, '1-2 iş günü', 1, 1, NULL, 'active', 1, 'TikTok 5000 İzlenme Paketi', 'TikTok izlenmelerinizi artırarak viral olun.', 'tiktok izlenme'),
(4, 'YouTube 1000 Abone', 'youtube-1000-abone', 'YouTube kanalınız için 1000 abone.', '<h2>YouTube Abone Paketi</h2><p>Kanalınızın büyümesini hızlandırın.</p>', 800.00, 650.00, '7-10 iş günü', 1, 1, NULL, 'active', 1, 'YouTube 1000 Abone Paketi', 'YouTube abone sayınızı artırarak kanalınızı büyütün.', 'youtube abone'),
(6, 'SEO Başlangıç Paketi', 'seo-baslangic-paketi', 'Siteniz için temel SEO optimizasyonu.', '<h2>SEO Başlangıç Paketi</h2><p>Web sitenizin arama motorlarında üst sıralara çıkması için temel SEO çalışmaları.</p><ul><li>Site içi SEO analizi</li><li>Anahtar kelime araştırması</li><li>Meta tag optimizasyonu</li><li>Site hız optimizasyonu</li><li>Aylık rapor</li></ul>', 2500.00, 2000.00, '15-20 iş günü', 1, 1, 'Önerilen', 'active', 1, 'SEO Başlangıç Paketi', 'Profesyonel SEO hizmetleri ile Google''da üst sıralara çıkın.', 'seo hizmeti'),
(7, 'Google Ads Yönetimi - Aylık', 'google-ads-yonetimi-aylik', 'Aylık Google Ads kampanya yönetimi.', '<h2>Google Ads Yönetimi</h2><p>Google Ads kampanyalarınızı profesyonel olarak yönetiyoruz.</p>', 3000.00, NULL, 'Aylık', 1, 1, NULL, 'active', 1, 'Google Ads Yönetimi', 'Profesyonel Google Ads yönetimi ile reklam bütçenizi optimize edin.', 'google ads yönetimi'),
(8, 'İtibar Yönetimi Paketi', 'itibar-yonetimi-paketi', 'Online itibarınızı profesyonel olarak yönetiyoruz.', '<h2>İtibar Yönetimi</h2><p>Olumsuz yorumları yönetin, olumlu itibar oluşturun.</p>', 5000.00, 4000.00, '30 iş günü', 1, 1, 'Premium', 'active', 1, 'İtibar Yönetimi Paketi', 'Online itibarınızı profesyonel ekibimizle yönetin.', 'itibar yönetimi');

-- ─── Paket Alanları (Örnek) ───
INSERT INTO `package_fields` (`package_id`, `field_key`, `field_label`, `field_type`, `is_required`, `placeholder`, `sort_order`) VALUES
(1, 'business_name', 'İşletme Adı', 'text', 1, 'Google''daki işletme adınız', 1),
(1, 'link', 'Google İşletme Linki', 'url', 1, 'https://maps.google.com/...', 2),
(1, 'phone', 'Telefon', 'text', 1, 'İletişim numaranız', 3),
(2, 'link', 'Google Harita Linki', 'url', 1, 'https://maps.google.com/...', 1),
(2, 'note', 'Not', 'textarea', 0, 'Eklemek istediğiniz notlar...', 2),
(3, 'username', 'Instagram Kullanıcı Adı', 'text', 1, '@kullaniciadi', 1),
(4, 'link', 'TikTok Video Linki', 'url', 1, 'https://tiktok.com/...', 1),
(5, 'link', 'YouTube Kanal Linki', 'url', 1, 'https://youtube.com/...', 1),
(6, 'website', 'Web Site Adresi', 'url', 1, 'https://siteniz.com', 1),
(6, 'keyword', 'Hedef Anahtar Kelimeler', 'textarea', 1, 'Sıralamak istediğiniz kelimeler...', 2),
(7, 'website', 'Web Site Adresi', 'url', 1, 'https://siteniz.com', 1),
(7, 'note', 'Kampanya Detayları', 'textarea', 0, 'Kampanya hedefleriniz...', 2),
(8, 'business_name', 'İşletme / Marka Adı', 'text', 1, 'İşletme veya marka adınız', 1),
(8, 'website', 'Web Site', 'url', 0, 'https://siteniz.com', 2),
(8, 'note', 'Detaylar', 'textarea', 1, 'İtibar sorunu detayları...', 3);

-- ─── Blog Kategorileri ───
INSERT INTO `blog_categories` (`name`, `slug`, `description`, `icon_key`, `status`, `sort_order`, `seo_title`, `seo_description`) VALUES
('Google İşletme Profili', 'google-isletme-profili', 'Google İşletme Profili hakkında rehber ve ipuçları', 'globe', 'active', 1, 'Google İşletme Profili Rehberi', 'Google İşletme Profili hakkında bilmeniz gereken her şey.'),
('SEO Rehberi', 'seo-rehberi', 'SEO ve arama motoru optimizasyonu hakkında yazılar', 'search', 'active', 2, 'SEO Rehberi', 'SEO hakkında kapsamlı rehber ve ipuçları.'),
('Sosyal Medya', 'sosyal-medya', 'Sosyal medya pazarlaması hakkında yazılar', 'share-2', 'active', 3, 'Sosyal Medya Rehberi', 'Sosyal medya pazarlaması hakkında ipuçları.');

-- ─── Blog Yazıları ───
INSERT INTO `blog_posts` (`blog_category_id`, `title`, `slug`, `excerpt`, `content`, `status`, `published_at`, `seo_title`, `seo_description`, `seo_focus_keyword`) VALUES
(1, 'Google İşletme Profili Nasıl Optimize Edilir?', 'google-isletme-profili-nasil-optimize-edilir', 'Google İşletme Profilinizi optimize ederek yerel aramalarda üst sıralara çıkmanın yollarını öğrenin.', '<h2>Google İşletme Profili Optimizasyonu Rehberi</h2><p>Google İşletme Profili, yerel işletmeler için en önemli dijital pazarlama araçlarından biridir. Doğru optimize edildiğinde, potansiyel müşterilerinizin sizi kolayca bulmasını sağlar.</p><h3>1. Profil Bilgilerinizi Eksiksiz Doldurun</h3><p>İşletme adı, adres, telefon numarası, web sitesi ve çalışma saatlerinizi eksiksiz ve doğru bir şekilde girin.</p><h3>2. Doğru Kategori Seçimi</h3><p>İşletmenizi en iyi tanımlayan kategoriyi seçin. Birincil ve ikincil kategorileri doğru belirleyin.</p><h3>3. Fotoğraf ve Video Ekleyin</h3><p>Kaliteli fotoğraflar ve videolar ekleyerek profilinizi zenginleştirin.</p><h3>4. Düzenli Paylaşım Yapın</h3><p>Google İşletme Profilinizde düzenli olarak güncelleme ve paylaşım yapın.</p><h3>5. Müşteri Yorumlarını Yönetin</h3><p>Gelen yorumlara profesyonel ve zamanında yanıt verin.</p>', 'active', NOW(), 'Google İşletme Profili Nasıl Optimize Edilir?', 'Google İşletme Profilinizi optimize ederek yerel aramalarda üst sıralara çıkın.', 'google işletme profili optimizasyonu'),
(2, 'SEO Nedir? Başlangıç Rehberi', 'seo-nedir-baslangic-rehberi', 'SEO nedir, nasıl çalışır ve neden önemlidir? Kapsamlı başlangıç rehberimizi okuyun.', '<h2>SEO Nedir?</h2><p>SEO (Search Engine Optimization), web sitenizin arama motorlarında daha üst sıralarda yer almasını sağlayan teknik ve stratejik çalışmaların bütünüdür.</p><h3>SEO Neden Önemlidir?</h3><p>İnternet kullanıcılarının büyük çoğunluğu arama motorları üzerinden bilgi arar. İlk sayfada yer almak, organik trafik ve müşteri kazanımı için kritiktir.</p><h3>SEO''nun Temel Bileşenleri</h3><ul><li><strong>Teknik SEO:</strong> Site hızı, mobil uyumluluk, site haritası</li><li><strong>İçerik SEO:</strong> Kaliteli ve optimize edilmiş içerik</li><li><strong>Off-Page SEO:</strong> Backlink ve dış referanslar</li></ul>', 'active', NOW(), 'SEO Nedir? Başlangıç Rehberi', 'SEO nedir, nasıl yapılır? Kapsamlı başlangıç rehberi.', 'seo nedir'),
(3, 'Sosyal Medya Pazarlamasında 2024 Trendleri', 'sosyal-medya-pazarlamasi-2024-trendleri', 'Sosyal medya pazarlamasında öne çıkan trendleri ve stratejileri keşfedin.', '<h2>Sosyal Medya Pazarlaması Trendleri</h2><p>Dijital pazarlama dünyası hızla değişiyor. Sosyal medya platformları, markaların hedef kitleleriyle etkileşim kurmasının en önemli araçları haline geldi.</p><h3>1. Kısa Video İçerikleri</h3><p>TikTok ve Instagram Reels ile kısa video içerikler, en yüksek etkileşim oranlarını yakalıyor. Markaların bu formata yatırım yapması kritik önem taşıyor.</p><h3>2. Topluluk Yönetimi</h3><p>Sadece takipçi sayısı değil, aktif ve bağlı bir topluluk oluşturmak uzun vadeli başarı için şart.</p><h3>3. Etkileyici Pazarlama</h3><p>Mikro ve nano etkileyicilerle yapılan işbirlikleri, daha yüksek dönüşüm oranları sağlıyor.</p><h3>4. Sosyal Ticaret</h3><p>Instagram Shop ve TikTok Shop gibi özellikler, doğrudan sosyal medya üzerinden satışı mümkün kılıyor.</p>', 'active', NOW(), 'Sosyal Medya Pazarlaması 2024 Trendleri', 'Sosyal medya pazarlamasında 2024 yılının öne çıkan trendlerini ve stratejilerini keşfedin.', 'sosyal medya pazarlama');

-- ─── Paketleri Öne Çıkan Olarak İşaretle ───
UPDATE `packages` SET `is_featured` = 1 WHERE `id` IN (1, 2, 3, 4, 5, 6, 7, 8);

-- ─── Ek Paketler (10'a tamamlamak için) ───
INSERT INTO `packages` (`category_id`, `name`, `slug`, `short_description`, `description`, `price`, `discount_price`, `delivery_time`, `min_quantity`, `max_quantity`, `badge`, `is_featured`, `status`, `sort_order`, `seo_title`, `seo_description`, `seo_focus_keyword`) VALUES
(5, 'Facebook Sayfa Beğeni Paketi', 'facebook-sayfa-begeni-paketi', 'Facebook sayfanız için 1000 beğeni.', '<h2>Facebook Sayfa Beğeni Paketi</h2><p>Sayfanızın güvenilirliğini artırın.</p>', 350.00, 280.00, '3-5 iş günü', 1, 1, NULL, 1, 'active', 2, 'Facebook Sayfa Beğeni Paketi', 'Facebook sayfanız için kaliteli beğeniler.', 'facebook beğeni'),
(2, 'Instagram 500 Beğeni', 'instagram-500-begeni', '500 gerçek Instagram gönderi beğenisi.', '<h2>Instagram Beğeni Paketi</h2><p>Gönderilerinizin etkileşimini artırın.</p>', 150.00, NULL, '1-2 iş günü', 1, 5, 'Hızlı', 1, 'active', 3, 'Instagram 500 Beğeni', 'Instagram gönderileriniz için kaliteli beğeniler.', 'instagram beğeni');

-- ─── SSS ───
INSERT INTO `faqs` (`question`, `answer`, `sort_order`, `status`) VALUES
('Siparişim ne kadar sürede teslim edilir?', 'Teslim süresi seçtiğiniz pakete göre değişiklik göstermektedir. Her paketin detay sayfasında tahmini teslim süresi belirtilmiştir. Genellikle 1-10 iş günü arasında siparişler tamamlanmaktadır.', 1, 'active'),
('Ödeme yöntemleri nelerdir?', 'Kredi kartı/banka kartı (PayTR/iyzico) ve Havale/EFT ile ödeme yapabilirsiniz. Tüm ödemeleriniz güvenli altyapılar üzerinden gerçekleştirilmektedir.', 2, 'active'),
('Sipariş verdikten sonra nasıl takip edebilirim?', 'Üye girişi yaptıktan sonra "Siparişlerim" sayfasından tüm siparişlerinizi ve durumlarını takip edebilirsiniz.', 3, 'active'),
('Destek talebi nasıl oluşturabilirim?', 'Üye panelinizden "Destek" bölümüne giderek yeni bir destek talebi oluşturabilirsiniz. Ekibimiz en kısa sürede yanıt verecektir.', 4, 'active'),
('İade politikanız nedir?', 'Hizmet başlamadan önce tam iade yapılmaktadır. Hizmet başladıktan sonra iade koşulları, hizmetin durumuna göre değerlendirilmektedir. Detaylı bilgi için İade Politikası sayfamızı inceleyebilirsiniz.', 5, 'active'),
('Bilgilerim güvende mi?', 'Evet, tüm kişisel bilgileriniz KVKK kapsamında korunmaktadır. SSL sertifikası ile şifrelenmiş güvenli bağlantı kullanılmaktadır.', 6, 'active');

-- ─── Sayfalar ───
INSERT INTO `pages` (`title`, `slug`, `content`, `status`, `seo_title`, `seo_description`) VALUES
('Hakkımızda', 'hakkimizda', '<h2>Hakkımızda</h2><p>Yorum Hizmeti olarak, dijital dünyada işletmelerin büyümesine yardımcı olan profesyonel hizmetler sunuyoruz. Google, sosyal medya, SEO ve dijital reklam alanlarında uzman ekibimizle müşterilerimize güvenilir ve etkili çözümler üretiyoruz.</p><h3>Misyonumuz</h3><p>İşletmelerin dijital varlığını güçlendirmek ve sürdürülebilir büyüme sağlamak için kaliteli, şeffaf ve güvenilir hizmet sunmaktır.</p><h3>Vizyonumuz</h3><p>Türkiye''nin en güvenilir dijital hizmet platformu olmak.</p>', 'active', 'Hakkımızda - NetVera', 'Yorum Hizmeti hakkında bilgi edinin. Dijital hizmetlerde güvenilir çözüm ortağınız.'),
('Gizlilik Politikası', 'gizlilik-politikasi', '<h2>Gizlilik Politikası</h2><p>Bu gizlilik politikası, yorumhizmeti.tr web sitesinin kullanıcı verilerini nasıl topladığını, kullandığını ve koruduğunu açıklamaktadır.</p><p>Kişisel verileriniz 6698 sayılı KVKK kapsamında korunmaktadır.</p>', 'active', 'Gizlilik Politikası - NetVera', 'Yorum Hizmeti gizlilik politikası ve veri koruma bilgileri.'),
('Mesafeli Satış Sözleşmesi', 'mesafeli-satis-sozlesmesi', '<h2>Mesafeli Satış Sözleşmesi</h2><p>Bu sözleşme, 6502 sayılı Tüketicinin Korunması Hakkında Kanun ve Mesafeli Sözleşmeler Yönetmeliği kapsamında düzenlenmiştir.</p>', 'active', 'Mesafeli Satış Sözleşmesi - NetVera', 'Yorum Hizmeti mesafeli satış sözleşmesi.'),
('KVKK Aydınlatma Metni', 'kvkk', '<h2>KVKK Aydınlatma Metni</h2><p>6698 sayılı Kişisel Verilerin Korunması Kanunu kapsamında, kişisel verilerinizin işlenmesine ilişkin aydınlatma metnidir.</p>', 'active', 'KVKK Aydınlatma Metni - NetVera', 'KVKK kapsamında kişisel verilerin korunması hakkında bilgilendirme.'),
('İade ve Teslimat Politikası', 'iade-teslimat-politikasi', '<h2>İade ve Teslimat Politikası</h2><p>Hizmet başlamadan önce tam iade yapılmaktadır. Hizmet başladıktan sonra iade koşulları değerlendirilir.</p><h3>Teslimat</h3><p>Dijital hizmetlerimiz, sipariş onayından sonra belirtilen süre içinde teslim edilmektedir.</p>', 'active', 'İade ve Teslimat Politikası - NetVera', 'Yorum Hizmeti iade ve teslimat politikası.');

-- ─── Mail Şablonları ───
INSERT INTO `email_templates` (`template_key`, `name`, `subject`, `body`, `variables`, `status`) VALUES
('user_registered', 'Üye Kayıt', 'Hoş Geldiniz - {{site_name}}', '<h2>Hoş Geldiniz, {{user_name}}!</h2><p>{{site_name}} ailesine katıldığınız için teşekkür ederiz.</p><p>Hesabınız başarıyla oluşturuldu. Hemen giriş yaparak hizmetlerimizi inceleyebilirsiniz.</p>', 'user_name,user_email,site_name', 'active'),
('password_reset', 'Şifre Sıfırlama', 'Şifre Sıfırlama - {{site_name}}', '<h2>Şifre Sıfırlama</h2><p>Merhaba {{user_name}},</p><p>Şifrenizi sıfırlamak için aşağıdaki bağlantıya tıklayın:</p><p><a href="{{reset_link}}">Şifremi Sıfırla</a></p><p>Bu bağlantı 1 saat geçerlidir.</p>', 'user_name,reset_link,site_name', 'active'),
('order_created', 'Sipariş Oluşturuldu', 'Siparişiniz Alındı #{{order_number}} - {{site_name}}', '<h2>Siparişiniz Alındı</h2><p>Merhaba {{user_name}},</p><p>#{{order_number}} numaralı siparişiniz başarıyla oluşturuldu.</p><p>Toplam: {{total_amount}}</p><p>Ödeme yöntemi: {{payment_method}}</p>', 'user_name,order_number,total_amount,payment_method,site_name', 'active'),
('payment_received', 'Ödeme Alındı', 'Ödemeniz Alındı #{{order_number}} - {{site_name}}', '<h2>Ödemeniz Alındı</h2><p>Merhaba {{user_name}},</p><p>#{{order_number}} numaralı siparişiniz için ödeme başarıyla alınmıştır.</p>', 'user_name,order_number,site_name', 'active'),
('order_completed', 'Sipariş Tamamlandı', 'Siparişiniz Tamamlandı #{{order_number}} - {{site_name}}', '<h2>Siparişiniz Tamamlandı</h2><p>Merhaba {{user_name}},</p><p>#{{order_number}} numaralı siparişiniz başarıyla tamamlanmıştır.</p><p>Bizi tercih ettiğiniz için teşekkür ederiz.</p>', 'user_name,order_number,site_name', 'active'),
('order_cancelled', 'Sipariş İptal', 'Siparişiniz İptal Edildi #{{order_number}} - {{site_name}}', '<h2>Sipariş İptal Edildi</h2><p>Merhaba {{user_name}},</p><p>#{{order_number}} numaralı siparişiniz iptal edilmiştir.</p>', 'user_name,order_number,site_name', 'active'),
('support_ticket_created', 'Destek Talebi Oluşturuldu', 'Destek Talebiniz Alındı #{{ticket_number}} - {{site_name}}', '<h2>Destek Talebiniz Alındı</h2><p>Merhaba {{user_name}},</p><p>#{{ticket_number}} numaralı destek talebiniz oluşturuldu. En kısa sürede yanıt verilecektir.</p>', 'user_name,ticket_number,subject,site_name', 'active'),
('support_ticket_answered', 'Destek Talebi Yanıtlandı', 'Destek Talebiniz Yanıtlandı #{{ticket_number}} - {{site_name}}', '<h2>Destek Talebiniz Yanıtlandı</h2><p>Merhaba {{user_name}},</p><p>#{{ticket_number}} numaralı destek talebinize yanıt verildi. Panelinizdencevabı görebilirsiniz.</p>', 'user_name,ticket_number,site_name', 'active'),
('admin_new_order', 'Yeni Sipariş (Admin)', 'Yeni Sipariş #{{order_number}}', '<h2>Yeni Sipariş Geldi</h2><p>Sipariş No: #{{order_number}}</p><p>Müşteri: {{user_name}}</p><p>Toplam: {{total_amount}}</p>', 'user_name,order_number,total_amount,site_name', 'active'),
('bank_transfer_received', 'Havale Bildirimi Alındı', 'Havale Bildiriminiz Alındı - {{site_name}}', '<h2>Havale Bildirimi</h2><p>Merhaba {{user_name}},</p><p>#{{order_number}} numaralı siparişiniz için havale bildiriminiz alınmıştır. En kısa sürede kontrol edilecektir.</p>', 'user_name,order_number,site_name', 'active'),
('payment_pending', 'Ödeme Bekliyor', 'Ödemeniz Bekleniyor #{{order_number}} - {{site_name}}', '<h2>Ödeme Bekleniyor</h2><p>Merhaba {{user_name}},</p><p>#{{order_number}} numaralı siparişiniz için ödeme beklenmektedir.</p>', 'user_name,order_number,site_name', 'active'),
('order_processing', 'Sipariş İşlemde', 'Siparişiniz İşleme Alındı #{{order_number}} - {{site_name}}', '<h2>Siparişiniz İşlemde</h2><p>Merhaba {{user_name}},</p><p>#{{order_number}} numaralı siparişiniz işleme alınmıştır.</p>', 'user_name,order_number,site_name', 'active'),
('waiting_customer_info', 'Eksik Bilgi', 'Eksik Bilgi - Sipariş #{{order_number}} - {{site_name}}', '<h2>Eksik Bilgi Bekleniyor</h2><p>Merhaba {{user_name}},</p><p>#{{order_number}} numaralı siparişinizde eksik bilgi bulunmaktadır. Lütfen panelinizden eksik bilgileri tamamlayın.</p>', 'user_name,order_number,site_name', 'active');

-- ─── Ana Sayfa Bölümleri (home_sections) ───
INSERT INTO `home_sections` (`section_key`, `title`, `subtitle`, `content`, `button_text`, `button_url`, `icon_key`, `sort_order`, `status`, `seo_focus_keyword`) VALUES
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
