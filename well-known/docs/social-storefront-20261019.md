# Sosyal medya vitrin tasarımı — 19.10.2026

## Kapsam ve sonuç

Sosyal medya ana dizini, gerçek platform kategorileri, alt kategoriler, hizmet/miktar listeleri ve sosyal paket detayları yenilendi. Son ek taleple ana sayfanın alt CTA'sı kısaltıldı ve popüler paketlerin açılış seçimi düzeltildi. Ajans, yazılım, SEO/pazarlama sayfa tasarımları, header ve footer değiştirilmedi. Ortak kategoriler dizini `/kategoriler` eski görünümünü korur; yeni sosyal ana dizin `/kategoriler/grup/social` adresindedir.

Referanslar yalnızca tasarım yönü için incelendi: kompakt yatay hizmet seçimi, platform kimliği, renkli banner, net miktar/fiyat kartları, sipariş özeti ve sayfa altı rehber/SSS. Rakip markası, görseli, metni, kullanıcı sayısı, değerlendirmesi veya sahte profil analizi kopyalanmadı.

## Kök neden ve kalıcı çözüm

Eski ortak kategori şablonu sosyal hizmet türleri için yaklaşık 283px yüksekliğinde dikey kartlar üretiyordu. Aynı uzun şablon platform, alt kategori ve paket seviyelerini yeterince ayırmıyordu; genel görsel/sahte cihaz, değerlendirme ve garanti sunumları sosyal satış deneyimini zayıflatıyordu.

Sosyal sınıflandırması ürün adına değil mevcut `SocialPackageService::isSocialCategory` kök kategorisine dayanır. Yalnızca bu koşulda eski genel view sosyal alandan çıkar ve yeni bileşen kullanılır:

`CatalogMenuService / gerçek kategori → CategoryController veya PackageController → SocialPackageService (miktar/hizmet) + SocialStorefrontService (sunum/içerik) → frontend/social/* → social-storefront.css/js`.

Eski ortak `.yv-*` ve `.nv-social-*` tasarım kuralları yeni sosyal DOM'una eşleşmez. Yeni dosya bir override katmanı değildir; bu component'in tek stil kaynağıdır, `!important` veya yeni inline CSS/JS içermez. Eski genel şablonların ajans/yazılım tarafı korunur. Eski sosyal hizmet kartı şablonu ve genel ürün galeri/garanti/değerlendirme blokları sosyal runtime'da devre dışıdır; diğer bölümlere toplu CSS temizliği uygulanmadı.

- Platform sayfası: yaklaşık 100px yatay hizmet kartları, gerçek başlangıç fiyatı ve paket sayısı, hizmet türü filtresi, arama/sıralama ve gerçek alt kategori navigasyonu.
- Alt kategori: Türk/düşmeyen/kurumsal gibi mevcut hizmet ayrımı korunur; seçilen hizmetin farklı miktarları ayrı paketlerdir. Yeni ürün/tür veya miktar uydurulmaz.
- Paket listesi: miktar/birim önde, admin özellikleri, gerçek toplam fiyat, varsa gerçek badge/indirim. İlk karta otomatik “en popüler” etiketi veya hayali değerlendirme eklenmez. Tekrarlanan teslimat satırı kaldırıldı.
- Paket detayı: miktarı açık sipariş özeti, mevcut hedef alanları ve native CSRF sepet/checkout POST'u. İkinci miktar çarpanı yoktur; diğer miktarlar ayrı sayfa bağlantılarıdır.
- Mobil: aynı sayfanın küçültülmüş hali değil, tek sütun ve sipariş formu öncelikli akış. Çift container padding'i kaldırıldı. Alt kategori navigasyonu açılabilir; SSS native details ile çalışır.

## Admin kullanımı

### Son ek talep: CTA, açılış sekmesi ve hover

- Uzun CTA yalnızca `frontend/home.php` içinde bulundu. “İşinize uygun çözümü bulun.” başlığı, kısa açıklama ve tek aksiyonla değiştirildi. `home-faq-final-v47.css` içindeki eski final-CTA kuralları değiştirilerek `.home-service-cta` tek kaynak yapıldı. Eski global `.yh6-final` override'ları yeni bileşene eşleşmez; yeni `!important` veya sürümlü override dosyası eklenmedi. Gerçek yükseklik masaüstünde 149–157px, mobilde 294px.
- Açılış seçiminin kök nedenleri: `HomeController` ilk grubu veritabanı `sort_order` değerinden alıyordu; mobil JS eski `sessionStorage` seçimini geri yüklüyordu; mouse/focus olayı da seçim değiştirebiliyordu. Sunucu ilk sosyal kökü öne alır. Eski oturum seçimi okuma/yazma ve otomatik hover/focus seçimleri kaldırıldı. Sekmeler bilinçli tıklama/Enter ile değişir; ilk açılış/F5 sosyal grubudur. Açık kategori/grup URL deep-link'leri korunur.
- Sosyal ana dizin, hizmet ve miktar kartları, alt kategori ve yan menü bağlantılarında marka rengi/beyaz ters dönüşü eklendi. Paket başlığı renkleri de tersine döner. Hover yalnızca hassas mouse üzerinde çalışır; klavye focus aynı görünümü alır. Hareket azaltma tercihi korunur. Diğer iki bölümün CSS'i değişmedi.

Ek değişiklik dosyaları: `HomeController.php`, `frontend/home.php`, `home-faq-final-v47.css`, `home-featured-tabs-v18.js`, `layouts/app.php`, `social-storefront.css` ve ilgili QA testleri. Sosyal ana işin dosya aileleri: `SocialStorefrontService`, kategori/paket public-admin controller'ları, `frontend/social/*`, admin `social-landing` editörü ve üç yerel WebP. Eski seçimi restore eden JS ve eski final CTA kuralları kaldırıldı; genel eski kategori view'ları yalnızca sosyal dalda bypass edilir.

### İçerik yönetimi

1. **Kategoriler → Sosyal Medya Tasarımı** (`/admin/sosyal-medya-tasarimi`): sosyal ana dizinin banner'ı ve alt içerikleri.
2. **Sosyal kategori/alt kategori → Düzenle → Sosyal medya sayfa tasarımı ve içerikleri**: o kategorinin banner ve rehberi.
3. **Sosyal paket → Düzenle → aynı bölüm**: o paketin banner ve rehberi.

Yönetilebilir alanlar: H1, banner açıklaması, ayrı masaüstü/mobil görsel, alt metin; en fazla 12 H2 içerik bölümü, her bölümde paragraf, H3 ara başlık, ek açıklama, liste, görsel/alt metin; en fazla 12 soru/yanıt. **Özel içerikleri yayınla** seçeneği ile editör metinleri kullanılır. Hazır içerik veya alt içerik/SSS'yi gizleme modu da vardır. HTML yazılması gerekmez; başlık hiyerarşisi şablondan üretilir. Boş bloklar gösterilmez.

Kategori/paket meta title, description, canonical ve GEO/AIO alanları mevcut SEO editöründe kalır. Ana dizinin meta başlığı H1'den, açıklaması banner metninden türetilir. Eski kategori hero alanlarına açıkça girilmiş başlık/açıklama/görsel seçimleri korunur; yeni sosyal editör alanı doluysa öncelik yeni alandadır. Yeni ürünlerin sunumu veri odaklıdır; ek kategori/paket için yeni PHP/CSS gerekmez.

Veriler mevcut `settings` tablosunda `social_landing_directory_1`, `social_landing_category_{id}`, `social_landing_package_{id}` ad alanlarında tutulur. Yeni tablo, migration veya runtime DDL yoktur. Kanonik `database/database.sql` yeterlidir. Legacy form submit'i yeni alanları içermiyorsa kayıt silinmez. Sosyal olmayan kategoriye taşınan paketlerin sosyal içerik ayarı diğer tasarıma uygulanmaz.

## Güvenlik ve SEO/GEO yaklaşımı

Admin login/aktif yönetici, CSRF, prepared SQL ve çıktı escaping kullanılır. İçerik alanları, adetleri ve toplam boyut sınırlandırılır. Script/HTML çalıştırılmaz. Görseller yalnızca kendi sitenizdeki raster dosyalarıdır; harici URL, SVG/script yolu ve traversal reddedilir. Upload gerçek HTTP yüklemesi, görsel içeriği/MIME/uzantı ve 5 MB sınırıyla mevcut Upload servisini kullanır. İçerik görseline açıklayıcı alt metin gerekir. Kaynak dosyaları admin alanı temizlendiğinde izinsiz silinmez.

Sluglar ve kanonik yollar korunur. Hizmet/arama/sıralama varyantları mevcut noindex/canonical politikasını izler. Görünen rehber doğrudan sunucu HTML'inde H2/H3, paragraflar, listeler ve görsellerle sunulur; ayrı blog URL'si veya Article kimliği oluşturulmaz. CollectionPage/ItemList ve Product fiyat/stok sözleşmeleri gerçek katalogdan gelir. OG kategori görseli yeni banner'ı izler; pakette açık OG override korunur. Sahte rating, satış/organik sonuç garantisi veya özel “AI schema” eklenmez.

Varsayılan rehberler hizmet amacına göre profil/abone, gönderi beğenisi, yorum beğenisi, yorum, video/dinlenme, kaydetme, paylaşım ve erişim ayrımlarını açıklar. Bunlar editöre başlangıç sağlar; özgün işletme bilgisi ve gerçek paket koşullarıyla zenginleştirilebilir. İlk sayfa/indeksleme/AI görünürlüğü garantisi verilmez. Bu yaklaşım [Google'ın AI arama rehberindeki](https://developers.google.com/search/docs/fundamentals/ai-optimization-guide) yararlı içerik, okunabilir yapı ve teknik SEO ilkelerini; [yapılandırılmış veri kurallarındaki](https://developers.google.com/search/docs/appearance/structured-data/sd-policies) doğruluk koşulunu izler.

## Görseller

Yerleşik imagegen ile üç özgün görsel üretildi. Native 1536×1024 boyutu korundu; kırpma/yeniden boyutlandırma olmadan WebP'ye format dönüşümü yapıldı. Orijinal PNG'ler silinmedi. Kalıcı proje dosyaları:

- `public/assets/img/social-storefront/social-creator.webp` — 221.588 bayt; Instagram/Facebook gibi içerik odaklı platformlar.
- `public/assets/img/social-storefront/video-creators.webp` — 291.482 bayt; TikTok/YouTube/yayın platformları.
- `public/assets/img/social-storefront/social-community.webp` — 170.396 bayt; ana dizin, topluluk ve diğer platformlar.

### Üretim promptları (yerleşik araç, CLI/API fallback kullanılmadı)

**social-creator:** Use case: ads-marketing. Create ONE polished landscape 3:2 hero photograph/3D composite for NetVera's premium social media services website. A stylish confident adult woman content creator, wearing a vivid violet blazer over a white top, waist-up, holding a smartphone naturally, friendly subtle smile, authentic skin and believable hands. Beside her a large elegant smartphone with abstract profile tiles (no legible UI text), floating translucent pink heart and chat shapes, glossy coral-to-lavender rings. Subject composition across center and right, generous breathing room, no cropped head or hands. Seamless pale lavender studio background with soft pink cinematic lighting, modern editorial commercial photography blended with restrained premium 3D objects. Sharp high resolution, refined not cartoon, no numbers, no promises, no text, no watermark, no company logos. This is an original website illustration, not a screenshot or entire webpage.

**video-creators:** Use case: ads-marketing. ONE premium 3:2 landscape hero image for a Turkish technology company's short-video and video social media services. Original editorial commercial photograph blended with restrained high-end 3D: two adult content creators, a fashionable woman in an electric cyan jacket and man in a violet overshirt, waist-up, recording a vertical smartphone video at a sleek small creator studio. Large dimensional coral play button and cyan glass chat shape float subtly around them. Colorful deep navy, purple and turquoise cinematic lighting, modern refined style, real skin texture, correct natural hands. Centered subjects with headroom and margins, nothing cut off. Clean uncluttered navy-to-violet background, no legible text, no letters, no numbers, no logos, no watermark. Sharp production-quality hero artwork, NOT a webpage screenshot.

**social-community:** Use case: stylized-concept. A single premium 3:2 landscape original hero artwork for social community, messaging and music services on a technology company website. A beautiful sculptural smartphone floating at a slight angle, surrounded by rounded translucent community avatar discs, one cyan chat bubble, a lavender headphones shape, a small coral play symbol, and subtle violet ribbon orbit. Sophisticated high-end 3D product illustration with glass, satin ceramic and anodized metal textures. Midnight indigo to royal purple studio backdrop, vivid cyan and soft lilac edge lighting. Balanced centered composition with clean margins, strong depth, refined light and shadow. No people, no legible screen text, no numbers, no social platform or company logos, no watermark, no fake graph, no claims. Crisp sharp commercial website hero asset, not a UI mockup.

## Test ve görsel doğrulama

- `social-storefront-qa.php`: 54 offline içerik, güvenlik, ikon, görsel, hover/klavye ve miktar sözleşmesi.
- `social-storefront-http-qa.php`: 359 kontrol; sosyal sayfa hiyerarşisi, tek H1, görseller, canonical/noindex, gerçek sepet miktarı/CSRF/Buy Now, gerçek admin kayıtları ve upload, masaüstü/mobil source, H1/H2/H3/SSS'nin HTML'e yansıması; ana sayfanın SSR sosyal seçimi ve kısa CTA. Kategori/paket/içerik test değişiklikleri finally ile geri alınır.
- `category-hero-http-qa.php`: 146 sayfa / 1640 kontrol; 108 hizmet kategorisi, yazılım kategorileri/türleri ve katalog kökü. Eski tek-hero selector beklentisi yeni sosyal bileşeni de tanıyacak şekilde güncellendi; canonical, alt ve OG kontrolleri korundu.
- `seo-infrastructure-http-qa.php`: 403 sitemap URL / 3713 kontrol. Fiyat selector'ı yeni sosyal sipariş özetini de tanır; gerçek fiyat ve satışa açıklık koşulları değişmedi.
- Gerçek Chrome: 1440×900, 1280×720, 1024×768, 390×844, 360×800; sekiz sosyal sayfa türünde 40 ölçüm senaryosu, 2230 element geometrisi. Yatay taşma, ekran dışına çıkan kontrol, bozuk yüklenmiş görsel veya H1 çoğalması görülmedi. Ekran görüntüleri ayrıca gözle incelendi. Hizmet filtresi gerçek Beğeni sonucunu gösterdi; hizmet→miktar navigasyonu ve mobil paket sipariş formu doğrulandı. Admin editörü masaüstünde açılıp incelendi.
- Son Chrome taraması: aynı 40 sosyal sayfa + 5 CTA senaryosu = 45/45; yatay taşma, ekran dışı kontrol, yüklenmiş bozuk görsel veya H1 çoğalması yok. Klavye ile kart arka planı `rgb(206,36,111)`, başlık/fiyat beyaz; okunurluk ekran görüntüsüyle doğrulandı. Desktop 1440 ve mobil 390'da SEO grubu seçilip gerçek reload sonrası sosyal gruba dönüş doğrulandı. Eski sessionStorage tercihleri sentetik JS testiyle ayrıca reddedildi.
- `php -l` (28 PHP dosyası), `node --check` (iki runtime JS + asset script), `git diff --check` başarılı. Offline satış (20), hero (111), SEO (48), üye paneli (43), auth (22), platform kimliği/tasarımı, iç sayfalar, ana hero/uzmanlık kartları, logo ve kullanıcı metni testleri başarılı. `home-featured-mobile-qa.cjs` yeni varsayılan/yenileme/deep-link/keyboard/resize sözleşmeleri başarılı. Eski `social-sales-http-qa.php` selector'ı yeni gerçek DOM'a uyarlandı; gerçek sepet/CSRF/tamper/Buy Now testi başarılı.
- Kanıtlar: git dışındaki `qa-artifacts/social-storefront-20261019/` altında masaüstü/mobil PNG'ler ve responsive ölçüm JSON'u.

## Sınırlar / dağıtım

Doğrulama localhost:8019 / `netvera_member_qa_20261018` ve GET regresyonları localhost:8017 / `netvera_ui_audit_20261013` üzerinde yapıldı. Gerçek müşteri/production verisi, ödeme callback'i, sağlayıcı eşlemesi veya fiyat değiştirilmedi. Gerçek ödeme/sağlayıcı API çağrısı ve e-posta gönderilmedi. Önceki PayTR callback testi bu görevde yeniden çalıştırılmadı; satış formunun mevcut sunucu akışı test edildi.

Bu çalışma yerel commit'tir; push/canlı dağıtım yapılmadı. Yeni CSS/JS ve üç WebP dosyası birlikte dağıtılmalı. Web sunucusunun uploads alanında script çalıştırmama politikası ve 5 MB PHP upload limitleri korunmalı. Sonraki iş adımı, işletmeye özgü gerçek hizmet koşullarının admin editöründen gözden geçirilmesidir; otomatik metinler sıralama sözü değildir.

Kalan sunum teknik borcu: ana sayfanın kısa CTA metni ve sosyal sayfalardaki ortak dört adımlı süreç kod kontrollüdür; yeni kategori/paket banner'ı, rehber ve SSS editöründen bağımsızdır. Bunlar için ayrıca admin metin ayarı eklenmedi. Ürün/hizmet koşulları işletme tarafından teyit edilmelidir.

Kullanıcının staged sync-conflict dosyaları, senkronizasyon artıkları ve ZIP silme değişikliği bu göreve alınmaz. Diğer kategorilere yeni stil dosyası yüklenmediği HTTP kontrolüyle doğrulandı. Commit hash'i son teslim mesajındadır.
