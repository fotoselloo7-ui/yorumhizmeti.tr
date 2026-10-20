# Ana sayfa bilgi alanı ve konumsal hizmet sayfaları — 19.10.2026

## Sonuç ve kapsam

Ana sayfada yan yana üç bilgi kartı; mobilde tek sütun. Ayrı admin içerik merkezi eklendi. Konumsal sayfalarda sayfa sayısı için uygulama sınırı yoktur; listeleme 20'li sayfalanır. İki kurumsal şablon (Studio / Editorial), üç renk teması, ayrı masaüstü/mobil banner, bölgesel kapsam, içerik bölümleri, hizmet ve referans seçimi, soru-cevap, iletişim ve sayfa bazlı SEO yönetimi vardır.

Ajans/yazılım/sosyal medya kataloglarının tasarımı, siparişler, fiyatlar, ödeme, sağlayıcılar ve üye paneli değiştirilmedi. Ortak header/footer kullanılır. Footer'daki Hizmet Bölgeleri keşif bağlantısı ancak en az bir konumsal sayfa yayınlandığında görünür; yayın kontrolü bu bağlantıyı da dolaylı yönetir.

## Başlangıç bulgusu ve mimari karar

Mevcut `pages` modülü düz HTML ve `/sayfa/{slug}` sunumu içindi. Kök URL'de konumsal hizmet, yapılandırılmış yerel kapsam, taslak önizleme veya bu sayfalara özgü editör yoktu. Ana sayfada yönetilebilir üçlü editoryal bilgi alanı bulunmuyordu. Var olan SEO metadata, graph ve sitemap katmanları yeniden kullanıldı.

- **Ana sayfa:** `settings.home_knowledge_v1 → HomeKnowledgeService → frontend/partials/home-knowledge.php → editorial-landings.css`.
- **Konumsal sayfa:** mevcut `pages` tablosunda `content.module = regional_landing` JSON'u → `RegionalLandingService` → `RegionalLandingController` → `frontend/landing/*` → mevcut layout/metadata/graph.
- **301:** her eski slug için aynı `pages` tablosunda `regional_redirect` türünde pasif kayıt. `slug` unique index'i korunur; hedef URL değil sayfa ID'sidir. Böylece birden çok ad değişikliğinde doğrudan güncel adrese gidilir; tek bir TEXT JSON eşleme boyutu sınırı yoktur.
- Yeni tablo veya runtime DDL yoktur. Kanonik `database/database.sql` içindeki mevcut `pages` ve `settings` yeterlidir. Eski migration dosyası veya ikinci temiz kurulum SQL'i eklenmedi.
- Sayfa başına içerik 180 KB, ana sayfa ayarı 60 KB ile sınırlandırılır. Bu sınırlar sayfa sayısı sınırı değildir. Bölüm/SSS/iç bağlantı sayıları 12; seçilen hizmet ve referanslar 24'er adetle sınırlıdır.

Kök fallback route en sonda ve yalnızca tek path segmentindedir. Eski statik/import rotaları önceliğini korur. Sistem rotalarının ilk segmentleri Router'dan alınarak slug rezervasyonu yapılır. MySQL'in case/accent-insensitive slug eşleşmesi ayrıca kontrol edilir; tam canonical yazımı farklıysa 301 döner. Bilinmeyen URL 404, pasif sayfa ve pasif hedefe ait eski alias 404 kalır.

## Admin kullanımı

### Ana sayfa bilgi alanı

`/admin/bilgi-alani`

- Alanı ve her kartı aç/kapat; bölüm başlığı/açıklaması; üç kartın başlığı, metni, rengi ve ikonu.
- Bölüm H2, kart başlıkları H3'tür. Ana sayfanın mevcut H1'i korunur. Kartların ayrı URL'si olmadığı için ayrı meta title/description üretilmez; ana sayfa metadata'sı mevcut Site Ayarları → SEO'dadır.
- Öne çıkan soru/kısa yanıt, ek soru-cevap, iç bağlantılar, odak/ikincil anahtar kelime, konular, GEO özeti, AIO kısa yanıt, kaynaklar, hazırlayan/kontrol eden ve tarih.
- Odak ifadenin editoryal metindeki kullanımı gösterilir; bağlantı hedefleri bu sayıya katılmaz. Gizli anahtar kelime metni, otomatik kelime tekrarı veya yanıltıcı yoğunluk/sıralama puanı üretilmez.

### Konumsal sayfalar

`/admin/konumsal-sayfalar`

Yeni sayfa oluşturun veya Alanya reklam ajansı / sosyal medya uzmanı / SEO uzmanı başlangıç şablonlarından birini açın. Bunlar kayıt veya yayın oluşturmaz; içerik başlangıçlarıdır. Şehir, gerçek kapsam ve hizmet koşullarını kontrol ederek kaydedin. Taslak kopyalama yeni slug, pasif durum, noindex ve boş canonical ile başlar.

Alanlar: H1, kök slug, şehir/ilçe, il, hizmet, ayrıntılı hizmet bölgesi, çalışma biçimi, şablon, renk, banner açıklaması, özgün bölgesel bağlam, görseller/alt metin, CTA metinleri. H2/H3 içerik blokları sıralanabilir; bölüm görselleri Medya Kütüphanesi'ndeki yerel raster yollarıyla eklenebilir. Banner için doğrudan upload da vardır. Aktif kategori ve portföy kayıtları seçilir; bu kayıtların asıl içeriği/fiyatı değiştirilmez. Referanslar, otomatik yerel müşteri iddiası olmadan genel portföy örneği olarak gösterilir.

Metin düz metindir. Güvenli iç bağlantı biçimi: `[Instagram hizmetlerini inceleyin](/kategori/instagram-hizmetleri)`. Serbest HTML/JS, üçüncü taraf embed veya keyfi CSS çalıştırılmaz. Boş bloklar gösterilmez; soru ve yanıt, bölüm başlığı ve metni birlikte doldurulur.

İletişim: `/iletisim`, `site_phone`, `site_email`, `whatsapp_number` ve mevcut marka/header/footer. Sayfa başına farklı telefon veya sahte şube verisi oluşturulmaz.

### Yayın ve URL koruması

Yeni sayfalar taslaktır; robots varsayılanı `noindex,follow` olur. Yayın ve indeksleme ayrı seçimlerdir. Yayın için H1, şehir, hizmet, açıklama, özgün yerel kapsam, içerik bölümü ve metadata zorunludur. Yalnızca şehir adı değiştirilmiş aynı gövde yayına alınamaz; bu kontrol tüm özgünlük veya doğruluk sorunlarını otomatik çözdüğü iddiasında değildir.

Taslak önizleme `/{slug}?onizleme=1` yalnızca geçerli admin oturumuyla çalışır, private/no-store ve noindex taşır. Parametre tek başına yetki vermez. Sayfa yayınlandığında `/{slug}` çalışır. Slug değişikliği transaction içinde ID-bağlı 301 kaydı oluşturur. Genel `/sayfa/{slug}` bu yeni sayfalarda kök adrese yönlenir; eski genel editör yeni JSON'u veya 301 kaydını bozamaz.

## SEO / GEO / AIO

Sayfa düzeyinde title, description, focus/secondary keywords, canonical, robots, OG, snippet/image/video preview, konular, GEO özeti, ana soru/doğrudan yanıt, kaynaklar, hazırlayan türü/adı, inceleyen ve kontrol tarihi yönetilebilir.

`Service + City/AdministrativeArea areaServed`, `WebPage` ve breadcrumb üretilir. Kaynaklar, hazırlayan, inceleyen ve kontrol tarihi görünür metinle tutarlı olarak graph'a bağlanır. Şube/koordinat, puan veya değerlendirme uydurulmaz. Kurumsal landing blog Article şemasına dönüştürülmez. SSS native HTML'dir; özel FAQ rich-result sözü verilmez.

Sitemap yalnızca yayındaki, indexable ve kendi canonical adresini kullanan kök sayfaları içerir. Pasifler, 301 kayıtları, noindex ve başka canonical hedefi olan sayfalar dışarıdadır. `/hizmet-bolgeleri` gerçek keşif dizinidir; arama sonuçları ve boş dizin noindex olur. Sitemap bütün uzun gövdeleri değil, JSON-valid korumalı kısa metadata kolonlarını okur. Genel Sayfalar admin listesi de yeni büyük içerikleri SQL tarafında dışlar. Tür filtrelerinde `_` karakteri SQL wildcard olarak kullanılmaz.

Tasarım yaklaşımı Google'ın [spam politikalarındaki doorway/ölçekli içerik sınırlarını](https://developers.google.com/search/docs/essentials/spam-policies) dikkate alır. [AI özellikleri rehberindeki](https://developers.google.com/search/docs/appearance/ai-features) temel SEO, görünür metin ve tutarlı veri ilkeleri izlenir; özel AI dosyası veya sıralama garantisi eklenmedi. [Local business rehberi](https://developers.google.com/search/docs/appearance/structured-data/local-business) doğrultusunda gerçek olmayan ofis/adres üretilmez.

## Güvenlik ve eski davranışlar

Admin middleware, CSRF, prepared SQL, text/URL/media doğrulaması ve escaping korunur. Upload raster türü, gerçek görsel içeriği ve mevcut 5 MB sınırıyla kontrol edilir. Başarısız kayıtta yalnızca o isteğin yeni yüklediği dosyalar temizlenir; önceki görseller silinmez. Revision hash/row lock eski editörün yeni kaydı ezmesini önler. Audit log yalnızca aksiyon/ID içerir; içerik veya credential loglanmaz.

Eski şablonlar kaldırılmadı. Yalnızca yeni türde kayıtlar genel HTML gösteriminden ve editöründen ayrıldı; normal yasal/kurumsal sayfalar aynı kalır. Yeni CSS dosyaları yeni bileşenlerin tek kaynaklarıdır; `!important`, yeni inline CSS/JS veya sürümlü override yığını yoktur. Bilinmeyen book-open/compass ikonları yerine kayıtlı file-text/target kullanılır. Browser'da bulunan referans `/assets` yoluna yanlış `/uploads` ekleme problemi giderildi ve testlendi.

## Doğrulama

- `editorial-landings-qa.php`: **65** içerik/URL/SEO/taslak/özgünlük/media/ikon/SQL türü sözleşmesi.
- `editorial-landings-http-qa.php`: **80** gerçek HTTP kontrolü: admin koruması/CSRF, üç kart kaydı, H1/H2, iç bağlantı/FAQ/XSS, taslak önizleme, yayın, kaynak/schema tutarlılığı, referans/hizmet, gerçek görsel yanıtı/upload, stale revision, eski editörden koruma, indexed alias ve zincirsiz 301, sitemap/noindex/canonical ve eski rotalar.
- `social-storefront-http-qa.php`: **359** mevcut sosyal satış/admin/sepet regresyonu başarılı.
- `seo-infrastructure-http-qa.php`: **403** sitemap URL / **3713** kontrol başarılı.
- İlgili offline SEO (48), sosyal (54/20), hero (111), üye (43), auth (22), iç sayfa, uzmanlık kartı, public slug, home featured JS ve premium polish testleri başarılı.
- Gerçek Chrome: 1440×900, 1280×720, 1024×768, 390×844, 360×800. Ana sayfa, üç landing, dizin, bilgi alanı admini, landing editörü ve liste: **40/40** senaryo. Yatay taşma, ekran dışı kontrol, yüklenmiş bozuk görsel veya public H1 çoğalması yok. Native soru-cevap ve admin bölüm sıralaması gerçek UI ile doğrulandı; sıralama denemesi kaydedilmedi.
- PHP lint, JS syntax ve diff whitespace kontrolleri son teslimde çalıştırıldı. Kanıtlar `qa-artifacts/editorial-landings-20261019/` altındadır (git dışı).

HTTP yazma testleri yalnızca `netvera_member_qa_20261018`, localhost:8019, sentetik admin/üye ile; ödeme ve SMTP kapalıyken yapıldı. Normal testler kendi sayfa/alias/ayar değişikliklerini geri alır, kendi yeni upload'larını temizler. Browser için açık `--keep-fixtures` ile bırakılan üç örnek ve sentetik portföy yalnızca QA verisidir. 8006'da gerçek landing içerikleri otomatik yayınlanmadı; ana sayfa bilgi alanı defaults ile çalışır. Gerçek ödeme, provider çağrısı veya e-posta yoktur.

## Kalan sınırlar / kullanım notları

İndekslenme, Google sırası ve AI gösterimi garanti edilemez. Şehir/hizmet doğruluğu, kaynakların güvenilirliği, görsel hakları ve iç bağlantı hedefleri yayın öncesi editörce kontrol edilmelidir. Otomatik kayıt veya sınırsız revizyon geçmişi yoktur; kaydetmeden çıkmayın. Şablonlar yapılandırılmıştır; serbest HTML/CSS page-builder değildir. Çok büyük kurulumlarda mevcut tablo üzerinde publish özgünlük taramasının yükü ayrıca ölçülmelidir; kamuya açık slug erişimi unique index ile, yönetim listesi sayfalı çalışır.

Kullanıcının staged senkronizasyon artıkları ve ZIP silme değişikliği commit dışında korunur. Push veya canlı dağıtım yapılmadı. Commit hash'i teslim mesajındadır.
