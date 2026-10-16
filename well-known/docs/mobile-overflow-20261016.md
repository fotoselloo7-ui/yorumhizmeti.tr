# Mobil kesilme ve çakışma kontrolü — 16 Ekim 2026

## Kök neden ve kapsam

- `yh18-favorite` etiketi `top:-11px` ile kartın dışında duruyordu. Grup/Tümü
  sonuçlarının yatay scroll alanı bu negatif üst konumu kesiyordu.
- Mobilde de çalışan iki eski kart hover kuralı kartı yukarı taşıyabiliyordu.
- Sabit canlı destek balonu paket CTA'larını kapatıyordu.
- `responsive-repair-v19.css`, alt menü için ayrılan 72px sayfa boşluğunu sıfırlıyordu.

Etiket aynı PHP koşuluyla üretilir; yalnız konumu başlık grubu içine taşındı.
Mobilde kategori adı/etiket aynı grid satırında, ürün başlığı ayrı satırda kalır.
Kart yüksekliği 460.53px olarak korundu. Masaüstü etiketinin eski dış konumu korunur.
Mobil hover taşıması kaldırıldı; masaüstü hover davranışı devam eder.

Sohbet erişimi mobil alt menüde altıncı, 44px yüksekliğinde bir düğme oldu.
Önceki beş bağlantı korunur; readiness koşulu eskisiyle aynıdır. Kapalı launcher
artık içerik üzerinde yüzmez. Açılan panel mobil gutter içinde ve alt menünün
üstündedir; kısa ekranda panelin tamamı dikey kaydırılabilir. Kapatma, Escape,
aria-expanded ve odağın launcher'a dönmesi test edildi.

## Dosyalar

- `resources/views/frontend/home.php`
- `resources/views/frontend/partials/netvera-live-chat.php`
- `resources/views/layouts/app.php`
- `public/assets/css/featured-category-slider-v43.css`
- `public/assets/css/featured-footer-v18.css`
- `public/assets/css/motion-v22.css`
- `public/assets/css/responsive-repair-v19.css`
- `public/assets/css/netvera-inquiries.css`
- `public/assets/js/netvera-live-chat.js`
- `tests/home-featured-mobile-probe.mjs`
- `tests/mobile-overflow-qa.cjs`
- Bu rapor.

Sohbetin inline JS'i ve layout'taki sohbet profilinin inline CSS'i kendi
kaynak dosyalarına taşındı. Yeni override stylesheet veya inline kod eklenmedi.
Mesaj endpointleri, CSRF formu, kimlik kontrolleri, güvenli textContent render'ı,
telefon doğrulaması, bildirim ve mesaj taşıma mantığı değiştirilmedi.

## Doğrulama

- Gerçek Chrome: 360×800, 390×844, 430×932, 1024×768, 1280×720, 1440×900.
- Mobil etiketin kart sınırında kalması, başlıkla çakışmaması, fiyat/CTA
  ayrımı ve ikon kutuları kontrol edildi. Yatay sayfa taşması bulunmadı.
- 360px'de alt menünün altı kontrolünün her biri yaklaşık 51px genişliğinde.
- 640×360 kısa ekranda sohbet paneli 264px alan içinde dikey scroll ile çalışır.
- 360px public sayfa taraması: `/kategoriler`, `/kategori/google-hizmetleri`,
  `/hazir-scriptler`, `/hazir-scriptler/insaat-firmasi-scripti`, `/sss`,
  `/iletisim`, `/sepet`, `/giris`. Sayfa genişliği 345px; yatay taşan ana
  başlık, metin, input veya düğme bulunmadı. Bilinçli yatay carouseller hariç tutuldu.
- PHP lint: değişen üç PHP view. JS syntax: yeni sohbet scripti.
- QA: platform-category-design, catalog-visual-parity-v84,
  platform-parent-flat-ui-v80, social-platform-identity, reviews-mobile-desk,
  inner-pages-premium, production-ui-copy, home-expertise, home-software-frame.
- Node: home-featured-mobile-qa, mobile-overflow-qa. Eski kesik etiket geometrisi
  sentetik regresyon testinde kırmızı; yeni yerleşim yeşil.
- Diff whitespace kontrolü; kullanıcı staging'i ve mevcut zip silmesi korundu.

Kanıtlar: ignore edilen `qa-artifacts/mobile-overflow-20261016/` klasöründe
mobil sayfa görüntüleri, etiketli kart, sohbet paneli ve ölçümler.

## Sınırlar

Gerçek telefon klavyesi/safe-area donanımı ve oturum gerektiren hesap ekranları
test edilmedi. Canlı destekten gerçek mesaj gönderilmedi; taşıma kodu korunup
sentetik testle kontrol edildi. Bu rapor, her ürün/veri kombinasyonunda tüm
mobil durumların hatasız olduğu iddiası değildir.
