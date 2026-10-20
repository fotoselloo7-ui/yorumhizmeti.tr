# NetVera SEO / GEO / AIO altyapı incelemesi

Tarih: 18 Ekim 2026. Kapsam: mevcut PHP MVC uygulamasının SEO sunum ve keşif katmanı. Başlangıç: `main`, `3850861`.

## Sonuç ve sınırlar

İndeksleme politikası, canonical/OG çıktıları, JSON-LD ilişkileri ve sitemap üretimindeki tutarsızlıklar giderildi. Mevcut SEO Merkezi'ne salt okunur teknik kontroller; kategori/paket/blog editörlerine isteğe bağlı önizleme kontrolleri; site ayarlarına Google/Bing doğrulama kodu alanları eklendi.

Bu çalışma sıralama, trafik veya AI alıntı artışı ölçümü değildir. Doğrulama yerel QA ortamındadır; canlı Google/Bing hesap verileri okunmadı, site gönderimi veya canlı yayın yapılmadı. Google birinci sayfa ya da AI yanıtlarında yer alma garantisi verilmez.

Korunanlar:

- URL ve slug kimlikleri, mevcut canonical ürün yolları ve legacy uyumluluğu. Yeni SEO sayfası, slug değişikliği veya yönlendirme eklenmedi.
- Frontend tasarımı, kategori hiyerarşisi, sipariş/adet/fiyat hesaplama, SMM sağlayıcıları, PayTR callback/webhook, oturum ve güvenlik akışları.
- Admin tarafından girilmiş içerik ve yayın tercihleri. Toplu anahtar kelime/metin üretimi yapılmadı.
- Kanonik `database/database.sql`: yeni tablo veya migration yok. Yeni önizleme tercihleri mevcut `nv_editorial_seo.extra_json` içinde, doğrulama kodları mevcut `settings` içinde saklanır.
- Kullanıcıya ait staged/unstaged senkronizasyon artıkları ve ZIP silme değişikliği. Bunlar görev commitine alınmaz.

## Araştırma: uygulanabilir bulgular

Kaynaklar resmi doküman ve yayınlardır; erişim tarihi 18 Ekim 2026. Aşağıdaki kararlar bu projeye yönelik mühendislik yorumudur, arama motoru onayı değildir.

| Konu | Kaynağın desteklediği ilke | Projedeki karar |
| --- | --- | --- |
| Google AI görünürlüğü | AI araması temel SEO, taranabilirlik ve yararlı içerik üzerine kurulur; özel bir AI işaretlemesi veya `llms.txt` zorunluluğu yoktur. | Ayrı bir “AI şeması” üretmek yerine mevcut gerçek içeriği ve indeksleme tutarlılığını güçlendir. [Google AI optimization guide](https://developers.google.com/search/docs/fundamentals/ai-optimization-guide) |
| AI özelliklerine uygunluk | İndekslenebilir ve snippet gösterimine uygun olmak önemlidir; uygunluk görünme garantisi değildir. | İstemeden uygulanan kısıtları görünür kıl; önizleme alanlarını varsayılan olarak boş bırak. [AI features](https://developers.google.com/search/docs/appearance/ai-features) |
| Robots / önizleme | `noindex` ile snippet/görsel/video sınırları farklı tercihlerdir; çelişkilerde kısıtlayıcı talimat önemlidir. | Tek robots çözümleyicisi; controller noindex'i kaydedilmiş index ile aşılamaz. [Robots meta](https://developers.google.com/search/docs/crawling-indexing/robots-meta-tag) |
| Sitemap | Tercih edilen URL'ler ve gerçek güncelleme tarihleri kullanılmalıdır; tek dosya kapasitesi sınırlıdır. | Noindex/pasif içerikleri dışla, tekrarları temizle, geçersiz/gelecek tarihleri atla. Günlük sahte `lastmod` üretme. [Sitemap oluşturma](https://developers.google.com/search/docs/crawling-indexing/sitemaps/build-sitemap) |
| Canonical | Tutarlı sinyaller tercih edilen dokümanın anlaşılmasına yardımcı olur. | Özel canonical'ı koru, takip parametrelerini otomatik fallback canonical'a taşıma; başka URL'yi canonical yapan blog yazısını sitemap'e koyma. [Canonical rehberi](https://developers.google.com/search/docs/crawling-indexing/consolidate-duplicate-urls) |
| HTTP durumları | Bulunamayan içerik gerçek hata durumu döndürmelidir. | Mevcut frontend 404 görünümünün HTTP 200 dönmesini merkezi View katmanında önle. [HTTP durum kodları](https://developers.google.com/crawling/docs/troubleshooting/http-status-codes) |
| Yapılandırılmış veri | İşaretleme doğru, ilgili ve içerikle tutarlı olmalıdır. | Var olan ürün/yazı/breadcrumb düğümlerini koru; satışa kapalı yazılımı ücretsiz satış gibi göstermeyi ve görünür fiyatla schema fiyatının ayrışmasını önle. [Genel schema politikaları](https://developers.google.com/search/docs/appearance/structured-data/sd-policies) |
| Kurumsal kimlik | Gerçek kurum bilgisi, logo ve doğrulanabilir profiller kullanılmalıdır. | Aktif admin logosunu kullan; yazar profillerini sayfanın `sameAs` değeri yerine ilgili yazar varlığına bağla. [Organization](https://developers.google.com/search/docs/appearance/structured-data/organization) |
| Yazılım işaretlemesi | Uygulama işaretlemesinin desteklenen gereksinimleri ve görünüm koşulları vardır. | Mevcut SoftwareApplication yapısını koru; teklif/fiyat/satın alma durumu gerçek public CTA ile uyuşsun. Eksik rich-result alanlarını sahte veriyle doldurma. [Software app](https://developers.google.com/search/docs/appearance/structured-data/software-app) |
| SSS | FAQ zengin sonuçlarının gösterimi genel ticari siteler için vaat edilemez. | Mevcut gerçek SSS'yi koru; görünmeyen sorular veya sahte FAQ üretme. [Google FAQ değişiklikleri](https://developers.google.com/search/blog/2023/08/howto-faq-changes) |
| AI performans ölçümü | Bing AI Performance, alıntı ve grounding görünürlüğünü izlemeye yönelik araçlar sunar; bunlar bir sıralama puanı değildir. | Admin'de resmi araca bağlantı ver; hesap entegrasyonu/ölçüm yapılmış gibi gösterme. [AI Performance](https://blogs.bing.com/webmaster/2026/2/Introducing-AI-Performance-in-Bing-Webmaster-Tools-Public-Preview/), [yeni görünürlük ölçümleri](https://blogs.bing.com/search/2026/6/New-AI-Visibility-Insights-in-Bing-Webmaster-Tools-Intents-Topics-Citation-Share-Compare/) |

## Runtime zinciri ve kök nedenler

İncelenen gerçek zincir: route → controller → mevcut kategori/paket/blog/katalog verisi + `NetveraSeoBridge` → `View::render` → public layout → meta/JSON-LD. Sitemap aynı içerik tabloları, yayın kapıları ve katalog bridge/snapshot kaynaklarından üretilir. Yeni admin kontrolleri mevcut SettingsController ve editör kayıt yollarına bağlanır.

| Öncelik | Kök neden | Kalıcı çözüm |
| --- | --- | --- |
| P0 | Kaydedilmiş `index` controller'ın `noindex` kararını geçersiz kılabiliyordu; bazı template'lerde robots varsayılanı yoktu. | `SeoMetadataService` ile tek çözümleme; özel/hesap/ödeme ve hata sayfaları için kısıtlayıcı HTTP başlığı. Robots erişim kontrolü değildir; mevcut auth/CSRF değişmedi. |
| P0 | Frontend 404 görünümü bazı controller'larda HTTP 200 dönebiliyordu. | 404 template'i merkezi olarak gerçek 404 ve noindex döndürür; hata sayfasına canonical/keşif şeması verilmez. |
| P1 | Sitemap editoryal noindex, pasif üst kategori ve özel blog canonical tercihleriyle ayrışabiliyordu. | Yayın + parent + noindex + canonical kapıları; tekilleştirme ve XML escaping. URL'ler silinmedi veya değiştirilmedi. |
| P1 | Bağımsız GEO WebPage çıktısı mevcut controller şemalarıyla ikinci bir sayfa kimliği oluşturuyordu. | `SeoStructuredDataService` mevcut graph'a bir sayfa kimliği ekler/birleştirir. Ana varlık, breadcrumb, yayıncı ve site ilişkileri bağlanır. Editoryal GEO özeti korunur. |
| P1 | Yazarın resmi profilleri WebPage üzerinde `sameAs` olarak kullanılıyordu. | Doğrulanmış HTTP(S) profiller author varlığına taşındı. Blogda zaten gösterilen kaynak ve kontrol tarihi bağlanır; gizli kategori notları citation/FAQ olarak yayımlanmaz. |
| P1 | Blog schema logosu olmayan bir dosyayı; ana sayfa eski logo ayarını kullanıyordu. | Mevcut `SiteLogoService` aktif logo kaynağı kullanılır. OG yerel görselleri mutlak URL olur; locale, Twitter görseli ve açıklayıcı alt alanları eklenir. |
| P1 | Ürün schema fiyatı/uygunluğu public satış gösteriminden farklı olabiliyordu. | Yalnızca işaretleme düzeltildi: paket indirim koşulu görünür fiyatla; yazılım Offer koşulu mevcut fiyat/satış izni/geçerli satın alma URL'siyle eşleşir. |
| P1 | SEO kaydı her seferinde runtime DDL çalıştırıyordu. MySQL DDL çevreleyen transaction'ı commit edebiliyordu. | Zaten kanonik SQL'de bulunan tabloyu kayıt sırasında yeniden oluşturan kod kaldırıldı. Kayıt prepared INSERT kullanmaya devam eder. |
| P1 | Kategori eski SEO otomatik doldurucusu, açıkça boşaltılan yeni önizleme alanlarını eski değerleriyle dolduruyordu. | Gönderilmiş boş önizleme alanları “varsayılana dön” olarak korunur. Eksik alan gönderen eski toplu işlemler kaydedilmiş tercihleri korur. Gerçek browser testiyle bulundu ve yeniden doğrulandı. |
| P2 | Teknik sağlık, editoryal puan ve gerçek indeks/sıralama aynı şeymiş gibi yorumlanabiliyordu. | Ayrı salt okunur teknik sağlık bölümü; eski puan “Editoryal Kontrol Puanı” olarak etiketlendi. Tablo verilerinin runtime/Google ölçümü olmadığı açıklandı. |

Kaldırılan/etkisizleştirilen eski kod: layout'taki bağımsız GEO schema script'i ve ham ikinci schema çıktısı; kayıt sırasında metadata tablo DDL'si; sabit/eski logo fallback'leri. Eski SEO Merkezi grid inline stylesheet'i mevcut `admin-v4.css` içine taşındı. Yeni override stylesheet veya public tasarım katmanı eklenmedi.

## Yönetim ve kullanım

1. **SEO Merkezi → Teknik SEO / GEO / AIO Sağlığı:** alan adı uyumu, varsayılan robots, kurum kimliği, sitemap doğruluğu/kapasitesi, metadata JSON ve GEO/AIO modeli. Bu bölüm dış servise bağlanmaz; tercihleri otomatik değiştirmez.
2. **Kategori / paket / blog → Gelişmiş SEO → Arama / AI Önizleme Kontrolleri:** isteğe bağlı metin, görsel, video sınırları. Boş alan içerik/site robots tercihini kullanır. `0` önizlemeyi kısıtlayabilir; `-1` sınır belirtmez; `none` görsel önizlemesini kapatır. Bunları SEO artırıcı ayar sanarak doldurmayın. Varsayılan açık tercih korunmuştur.
3. **Site Ayarları → kurumsal SEO alanları:** Google Search Console ve Bing Webmaster doğrulamasının yalnızca `content` kodu girilir, HTML/URL değil. Kodun eklenmesi hesap doğrulamasının tamamlandığı anlamına gelmez; gerçek mülk sahibinin ilgili serviste doğrulaması gerekir. Hiçbir gerçek hesap anahtarı veya token eklenmedi.
4. Mevcut yazar, kaynak, odak/yardımcı konu, hizmet bölgesi ve GEO/AIO içerik alanları korunur. Kaynak, editör ve son kontrol tarihi yalnızca gerçekten doğrulandığında doldurulmalıdır.

## Değişen kaynaklar

- Yeni servisler: `app/Services/SeoMetadataService.php`, `SeoStructuredDataService.php`, `SeoHealthService.php`.
- Mevcut servis/core: `app/Core/View.php`, `app/Services/SitemapService.php`, `NetveraSeoBridge.php`.
- Controller'lar: `HomeController`, `BlogController`, `CategoryController`, `PackageController`, `NetveraScriptController`, `PageController`, `Admin/SettingsController`, `Admin/CategoryController`. Değişiklikler metadata, SEO tanılama ve yeni ayarların kayıt uyumluluğu ile sınırlı.
- View'lar: `layouts/app.php`, `layouts/admin.php`, `admin/partials/netvera-seo.php`, `admin/settings/seo.php`, `admin/settings/site.php`; mevcut `public/assets/css/admin-v4.css`.
- Testler: `tests/seo-infrastructure-qa.php`, `seo-infrastructure-integration.php`, `seo-infrastructure-http-qa.php`.

## Doğrulama

Yerel 8017 sunucusu `netvera_ui_audit_20261013` QA veritabanını kullanır. Transaction testi yalnızca mevcut sentetik `netvera_social_sales_qa_20261017` veritabanında çalışır. Production veritabanına yazılmadı; üretim credentials/ödeme sistemi kullanılmadı.

- Değişen ve yeni tüm PHP dosyalarında `php -l`: geçti.
- `seo-infrastructure-qa.php`: **47** offline sözleşme; robots, güvenli URL, preview, canonical, 404/özel sayfa, JSON-LD birleştirme, GEO özeti, provenance ve XML.
- `seo-infrastructure-integration.php`: **12** sözleşme; JSON kayıt/koruma/temizleme, gerçek kategori hazırlama zinciri, transaction'ın DDL ile bozulmaması ve sitemap yayın kapıları. Yazılar rollback ile geri alındı.
- `seo-infrastructure-http-qa.php`: **403 sitemap URL'sinin tamamı** localhost üzerinden kontrol edildi: HTTP 200, noindex çelişkisi yok, canonical path uyumu, tek geçerli JSON-LD graph. Ayrıca özel sayfa robots başlıkları, gerçek 404'ler, filtre varyantları, takip parametreleri ve aktif logo kontrol edildi. Bu bir Google Rich Results Test veya gerçek bot/canlı indeks testi değildir.
- `category-hero-http-qa.php`: **146 sayfada 1640** sözleşme; H1, başlık, açıklama, canonical, OG, breadcrumb ve kategori görselleri geçti.
- Mevcut regresyonlar: category-hero (111), brand-settings (22), brand-theme-seo-chat (32), category-complete-seo (30), social-sales (20), auth-visual (22), smm-target-and-ui (21), production-ui-copy (12); blog-quality, blog-media, public-demo-render, visual-brand-catalog, public-slug-url, retired-software-category ve site-logo geçti.
- Admin gerçek browser kaydı: `max-snippet:0`, `max-image-preview:standard`, `max-video-preview:-1` kategori çıktısında görüldü; sonrasında üçü temizlendi ve public çıktı yeniden `index,follow,max-image-preview:large` oldu. Sadece QA kaydı değişti; mevcut kategori editörünün boş SEO alanı doldurma davranışı QA'da doğal olarak çalıştı.
- Chrome: **1440×900, 1280×720, 1024×768, 390×844, 360×800**; SEO Merkezi, kategori editörü ve public kategori kontrol edildi. Yeni kontrollerde yatay taşma, metin kesilmesi veya üst üste binme yok. Son form düzeni beş ölçüde tekrar kontrol edildi. Mobil form tek sütuna akar; public tasarım değişmedi. Viewport sonunda sıfırlandı.
- `git diff --check`: geçti. Kullanıcının staged sync dosyaları ayrı tutuldu.

HTTP taraması GET kullanır; mevcut blog detaylarının QA görüntülenme sayacı gibi doğal yan etkileri olabilir. Bu nedenle “GET = veritabanına hiçbir yazı yok” varsayımı yapılmaz. Canlı ödeme/kart/provider çağrısı gerçekleştirilmedi.

Görsel kanıtlar Git dışında bırakılan yerel QA çıktısı olarak tutulur; source commitine eklenmez:

- `C:/Projeler/yorumhizmeti.tr/qa-artifacts/seo-infrastructure-20261018/seo-health-1440.png`
- `C:/Projeler/yorumhizmeti.tr/qa-artifacts/seo-infrastructure-20261018/seo-preview-390.png`

## Kalan öncelikler ve canlı ortam riskleri

1. **Canlı canonical origin kontrolü:** QA'da `site_url=https://netvera.tr` ile `APP_URL=http://127.0.0.1:8017` farkı beklenir. Canlı ortamda protokol/alan adı/tercih edilen host; canonical, sitemap ve yayıncı kimlikleriyle birlikte doğrulanmalıdır. Otomatik host/URL değiştirilmedi.
2. **Search Console / Bing mülkü:** gerçek alan adı sahipliği, canlı URL Inspection, sitemap işleme, indeks kapsamı ve varsa AI Performance raporları mülk sahibi hesabında incelenmeli. Tarayıcıda açık başka bir alan adına ait hesap, bu projeye yetki veya kanıt değildir; erişilmedi.
3. **Editoryal kalite:** eski kayıtların marka/SEO başlıkları, gerçek uzman/yazar bilgileri, güncel kaynaklar, hizmet koşulları ve özgün örnekler insan tarafından incelenmeli. Çok sayıda birbirine benzeyen anahtar kelime sayfası açmak önerilmez. Bu görevde toplu içerik veya legacy veritabanı marka migrasyonu yapılmadı.
4. **Gerçek performans:** canlı Core Web Vitals/CrUX, sunucu cache/CDN, tarayıcıya teslim edilen robots ve HTTP header'ları, gerçek Googlebot erişimi ayrıca ölçülmeli. Yerel responsive QA bunların yerine geçmez.
5. **Şema kurulum uyumu:** runtime DDL kaldırıldığı için eski/eksik kurulumda `nv_editorial_seo` yoksa gelişmiş kayıt önce kanonik `database.sql` ile uyumlu hale getirilmelidir. Public metadata okuma mevcut toleranslı fallback'ini korur; bu görev eksik kurulumları sessizce migrate etmez.
6. **Ölçek:** mevcut QA sitemap 403 URL ve yaklaşık 77 KB; bölümlendirme gerektirmiyor. Katalog büyürken 50.000 URL / 50 MB sınırından önce sitemap index planlanmalı. Şu an yalnızca uyarı/tanı var, otomatik parçalayıcı yok.
7. **Admin teknik borç:** eski editoryal puanlar DB anlık görüntüsüdür, runtime fallback başlıklarını veya indeks durumunu yansıtmayabilir. Statik SSS/iletişim ve katalog/taksonomi türlerinin mevcut SEO kontrol yüzeyleri aynı ayrıntıda değildir. Bunlar yeni bir küresel puan veya otomatik içerik üretimiyle gizlenmedi.

`llms.txt`, otomatik IndexNow gönderimi, AI eğitim/tarama izinlerinde toplu değişiklik, sahte yorum/puan/SSS, yapay güncellik ve garantili sıralama iddiası eklenmedi. Kod doğrulaması ile canlı arama performansı ayrı aşamalardır.
