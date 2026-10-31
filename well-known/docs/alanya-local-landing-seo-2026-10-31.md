# NetVera Alanya konumsal landing planı (31 Ekim 2026)

## Hedef
Gerçek hizmet alanı ve müşterinin karar sürecini yansıtan, birbirine rakip olmayan yerel sayfalar. Hedef yüksek organik görünürlük ve nitelikli teklif; sıralama garantisi yoktur.

## Yayın ve sorgu sahipliği sırası
1. `alanya-sosyal-medya-yonetimi` — organik hesap, Instagram, Reels planlaması.
2. `alanya-google-reklam-yonetimi` — Google Ads arama ve ölçüm.
3. `alanya-meta-reklam-yonetimi` — Meta Ads ve ücretli kampanyalar.
4. `alanya-web-tasarim` — kurumsal site, mobil UX, içerik yönetimi.
5. `alanya-seo-geo-optimizasyonu` — teknik SEO, yerel sorgular, anlaşılır AI yanıtları.
6. `alanya-video-kurgu-icerik-uretimi` — video kurgu/çekim/teslim.
7. `alanya-yapay-zeka-cozumleri` — işletme AI otomasyonu.
8. `alanya-grafik-tasarim` — kimlik, görsel tasarım.
9. `alanya-web-yazilim-gelistirme` — özel CRM, iş uygulaması.
10. `alanya-mobil-uygulama-gelistirme` — iOS/Android mobil geliştirme.
Çatı: `alanya-reklam-ajansi`, sadece genel kapsam; uzmanlık sayfalarını kopyalamamalı.

## Mevcut editör ve veri modeline uyum
`app/Services/RegionalLandingService.php` konumsal sayfaları `pages.content` içinde JSON olarak saklar. Meta, OG ve canonical ayrıca `pages` sütunlarına yazılır. Kayıt/slug değişikliklerinde mevcut editörün revision/301 kontrolleri kullanılmalı. `service_ids`, `blog_ids`, `script_ids`, `reference_ids` yalnız canlı katalogdan gerçek kimliklerle doldurulmalı; hayali kayıtlar eklenmemeli.

İlk sayfa için kurulan kapsam: Alanya sosyal medya yönetimi; otel, restoran, emlak, yerel hizmet işletmeleri, organik içerik, Reels, raporlama, Meta Ads ile kapsam sınırı. H1 ve canonical niyet: `alanya-sosyal-medya-yonetimi`. **Önemli:** Aynı niyete hizmet eden `alanya-sosyal-medya-uzmani` yayındaysa ikinci sayfa açılmamalı; eski URL sahipliği ve 301 planlanmalı.

## Kaynak ve uyum
- https://www.rafinemedia.com/
- https://www.edymz.com.tr/sosyal-medya.php
- https://nexusmedya.com/tr/reklam/google/alanya
- https://webalanya.com/alanya-google-ads/
- https://www.alitarhan.com.tr/alanya-produksiyon-ajansi
- https://developers.google.com/search/docs/fundamentals/creating-helpful-content
- https://developers.google.com/search/docs/essentials/spam-policies
- https://developers.google.com/search/docs/appearance/structured-data/local-business

Semrush'da mevcut API birimleri yeterli olmadığından 31 Ekim 2026 tarihli doğrulanmış hacim/KD/CPC değerleri yoktur. Uydurma hacim kullanılmadı.

## İthalat güvenliği
Herhangi bir canlı SQL içe aktarmadan önce tam DB yedeği ve slug çakışması kontrolü gerekir. SQL dosyaları müşteri/sipariş/lisans verilerini güncellememelidir. Sayfayı yayınladıktan sonra HTTP 200, tek H1, robot/index, canonical, JSON-LD, mobil görünüm, bağlantılar, Search Console URL inspection ve sitemap kontrolü yapılmalıdır.
