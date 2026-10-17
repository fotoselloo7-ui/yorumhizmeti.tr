# Public storefront visual polish — 17 Ekim 2026

## Kapsam ve kök neden

Mevcut Poppins, lacivert/mor/pembe palet, yaklaşık 1200px container, veri odaklı
kategoriler ve satış akışları korundu. Yeni sürüm/override stylesheet oluşturulmadı.

Gerçek Chrome incelemesinde:

- SSS soru stilleri altı eski CSS katmanından etkileniyordu. 390px ekranda computed
  font 12px, dikey padding 0px idi; yeni normal stiller eski `!important` kurallara
  yeniliyordu. SSS soruları ayrıca mobilde tanıtım görsellerinin ve destek kartının
  altına itilmişti.
- Blog kategorileri dar kutulara bölünüyor, mobilde uzayan bir menü oluşturuyordu.
  Kart başlıklarını v19'daki sabit 13px kural eziyordu. Kapak üstündeki büyük genel
  ikonlar, gerçek görselin yazı ve materyallerini örtüyordu.
- Katalogdaki sınırsız özet satırları kartları uzatıyordu; CTA yalnızca küçük bir
  metin bağlantısıydı. Sabit görsel yüksekliği farklı sütun genişliklerinde kırpıyordu.
- Dizüstünde dikey WhatsApp/telefon ve geniş sohbet etiketi sağ ürün sütununu örtüyordu.
- Eski `.fade-in-up` içeriği JavaScript/observer çalışmadan önce görünmez yapıyordu.

## Uygulanan çözüm

- SSS stilleri `storefront-design-language-v89.css` içinde tek sahibi olan, 14px
  okunur yazılı, en az 56px yüksekliğinde, klavye odağı görünen panellere toplandı.
- Kategori, paket ve makaledeki üç tekrarlı inline SSS handler kaldırıldı. `app.js`
  tek aç/kapat sahibi; ARIA, mevcut/üretilen panel ID'leri ve `hidden` birlikte güncellenir.
- Mobilde SSS soruları destek kartının önüne alındı. SSS/iletişim/blog/kategori
  dekoratif hero materyali 600px altında gizlendi; ürün kapakları ve ana slider korunur.
- Blog kategori bağlantıları desktop'ta doğal satır akışı, mobilde yatay kaydırma
  kullanır. Başlıklar 16px; kapaklar 16:9, metadata tabanda hizalıdır.
- Blog kapaklarının büyük genel ikonları ve veriye bağlı olmayan sabit `4.8` puan /
  `24 yorum` dekorasyonu kaldırıldı. Gerçek yayın tarihi ve okunma verileri korundu.
- Katalog kapakları 16:9; kısa açıklama kartta üç satırla sınırlı, tam açıklama ürün
  detayında korunur. Fiyatlar ve en az 44px CTA'lar hizalıdır.
- Makalenin başlık, gövde ve içindekiler alanındaki ilgili inline stiller mevcut
  makale stylesheet'ine taşındı; içindekiler semantik `nav` ve okunur linkler kullanır.
- İletişim alanlarında label/ID eşleşmesi ve ad/e-posta otomatik tamamlama eklendi.
- Tek mevcut IntersectionObserver üzerinden bölüm başlıklarına 480ms, 12px'lik
  tek seferlik giriş eklendi. İçerik başlangıçta görünür; observer yoksa gizlenmez.
  `prefers-reduced-motion` ve canlı tercih değişimi animasyonları durdurur.
- Ortak CTA'larda hafif gölge ve 3px ikon hareketi yalnızca hassas işaretçi/hover'da
  kullanılır. Auth/katalog hover yükselmesi dokunmatik cihazlarda tetiklenmez.
- 769–1280px aralığında destek kısayolları 44px yatay grup; sohbet paneli açılınca
  diğer balonlar gizlenir. Mobil alt menü ve sohbet davranışı korunur.

## Değiştirilen dosyalar

Uygulama köküne (`well-known/`) göre:

- `public/assets/css/storefront-design-language-v89.css`: ortak tasarım ve SSS sahibi.
- `public/assets/css/premium.css`, `storefront-v4.css`, `approved-v9.css`,
  `visual-consistency-v10.css`, `visual-fidelity-v11.css`,
  `typography-readability-v16.css`: eski SSS override'ları kaldırıldı; başlık token'ı.
- `public/assets/css/responsive-fluid-v13.css`, `responsive-repair-v19.css`:
  eski blog grid/font çatışması kaldırıldı, dekoratif görseller için mevcut kurala token.
- `public/assets/css/netvera-legacy-premium-v40.css`: kapak oranı, hover kapsamı.
- `public/assets/css/netvera-article-v42.css`: makale ve içindekiler stilleri.
- `public/assets/css/app.css`, `motion-v22.css`, `premium-hover-v55.css`,
  `auth-premium-v25.css`: görünür başlangıç, ölçülü hareket ve CTA etkileşimleri.
- `public/assets/css/featured-footer-v18.css`, `netvera-inquiries.css`: destek grubu.
- `public/assets/js/app.js`: tek SSS handler ve tek giriş observer'ı.
- `resources/views/frontend/blog/index.php`, `blog/detail.php`, `category-detail.php`,
  `package-detail.php`, `contact.php`, `partials/netvera-live-chat.php`:
  ilgili markup, eski inline kodun kaldırılması, erişilebilir adlar.
- `resources/views/layouts/app.php`, `layouts/auth.php`: mevcut asset cache anahtarları.
- `tests/inner-pages-premium-qa.php`: SSS sahibinin yeni ortak kapsamına uyarlama.
- `tests/storefront-premium-polish-qa.cjs`, `storefront-premium-probe.mjs`: davranış
  ve gerçek browser geometrisi regresyon testleri.

## Doğrulama

Gerçek Chrome, mevcut `http://127.0.0.1:8006` sunucusunda:

- 1440×900, 1280×720, 1024×768, 390×844, 360×800.
- 16 public URL × 5 boyut = 80 geometri kontrolü; document yatay taşması,
  görünür kırık görsel, katalog fiyat/CTA çakışması veya kart dışına çıkan CTA yok.
- URL'ler: ana sayfa, kategoriler, Google/Instagram hizmetleri, Google başlangıç
  paketi, hazır script kataloğu, inşaat script detayı, blog, yapay zekâ haber makalesi,
  SSS, iletişim, boş sepet, giriş, hakkımızda, KVKK, gizlilik politikası.
- Desktop/blog kartları eşit yükseklik ve 16px başlıkla; katalogda tüm 16 üründe
  kapak/fiyat/CTA ölçüldü. Mobilde dekoratif hero yok, gerçek ürün görselleri korunuyor.
- SSS fare tıklaması ve Enter ile aç/kapat, mobil menü/Escape, mobil sohbet aç/kapat
  ve alt menüden ayrık panel, dizüstü destek grubu ve sohbet paneli doğrulandı.
- Ana sayfa mobil Sosyal Medya ve SEO/Dijital filtrelerinin geometri testleri geçti;
  önceki ikon/favori etiketi/CTA düzeltmeleri korunuyor.
- 14 PHP QA geçti: inner-pages-premium, platform-category-design,
  catalog-visual-parity-v84, platform-parent-flat-ui-v80, social-platform-identity,
  reviews-mobile-desk, production-ui-copy, home-expertise, home-software-frame,
  home-hero-slider-v94, header-reputation-parity-v83, public-slug-url,
  netvera-blog-media, netvera-blog-quality.
- 3 Node QA geçti: home-featured-mobile, mobile-overflow, storefront-premium-polish.
- Değişen 8 PHP view/layout `php -l`, `app.js` syntax ve `git diff --check` geçti.
- Yeni testler azaltılmış hareket, canlı tercih değişimi, observer yokluğu, tek
  seferlik giriş, SSS ARIA/ID/hidden senkronunu sentetik verilerle doğrular.

Screenshot ve ölçümler: `qa-artifacts/premium-site-20261016/` (Git dışı yerel QA).
Örnekler: `blog-desktop.png`, `catalog-desktop.png`, `catalog-1024.png`,
`faq-390.png`, `blog-390.png`, `contact-360.png`, `geometry.json`.

## Korunanlar ve kalan riskler

- Backend, SQL, fiyat/veri kaynağı, slug/SEO URL'leri, ödeme, sipariş, demo izinleri
  ve sohbet transport kodu değiştirilmedi. Gerçek form/ödeme/mesaj gönderilmedi.
- Hesap/admin/gerçek ödeme oturumu açılmadı; bu private ekranlar browser'da bu
  çalışmanın doğrulama kapsamına dahil değildir.
- Gerçek iOS/Android klavyesi, donanım safe-area ve eski tarayıcılar fiziksel cihazda
  test edilmedi. Azaltılmış hareket davranışı sentetik test + CSS denetimiyle doğrulandı.
- Admin tarafından sonradan eklenen farklı oranlı kapaklar 16:9 alanında kırpılabilir;
  mevcut gerçek kapaklar incelendi. İçindekiler yalnızca verisi bulunan makalede görünür.
- Diğer legacy CSS katmanları bütünüyle yeniden yazılmadı. Blogun mevcut statik
  yan yorum zamanları/alt CTA sayacı bu görsel düzenlemenin dışında kaldı.
- Kullanıcının `.sync-conflict-*` staging'i ve ZIP silme değişikliği korunur;
  bu dosyalar çalışma commit'ine dahil edilmez. Push yapılmaz.
