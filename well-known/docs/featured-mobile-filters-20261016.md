# Popüler Paketler — mobil filtre düzenlemesi

## Kapsam ve kök neden

Yalnız ana sayfadaki `#featured` hizmet/kategori seçimi, 768px ve altı.
Eski v24 mobil kuralları grup düğmelerini 2/1 sütunlu grid'e çeviriyordu.
v43 ve sonraki marka/parite katmanları 97–108px kategori kartlarını, iç içe
çerçeveleri ve ok kolonlarını mobilde de koruyordu. 390px başlangıç ölçümünde
gezinti yaklaşık 405px tüketiyordu. Controller'ın DB sırasındaki ilk Google
grubu ise açık kullanıcı seçimi olmadığı halde mobil başlangıç sayılıyordu.

Gerçek zincir: `HomeController` → `CatalogMenuService` / mevcut paket ve yazılım
grupları → `frontend/home.php` → `home-featured-tabs-v18.js` → mevcut CSS sırası.
Controller, sorgular, admin, kategori verileri ve SEO yolları değiştirilmedi.

## Değiştirilen dosyalar

- `resources/views/frontend/home.php`: mobil başlık, kısa etiketler, Tümü düğmeleri.
- `resources/views/layouts/app.php`: değişen mevcut assetlerin cache anahtarları.
- `public/assets/js/home-featured-tabs-v18.js`: mevcut pane seçimini tekrar
  kullanarak grup Tümü/alt kategori Tümü; mobil hover/otomatik kategori kaydırma
  yok; query/hash/SSR önceliği ve sekme oturumundaki kullanıcı seçiminin korunması.
- `public/assets/css/featured-groups-v24.css`: eski iki/tek sütun mobil grup grid'i kaldırıldı.
- `public/assets/css/featured-category-slider-v43.css`: tek authoritative mobil
  chip/çerçeve/spacing düzeni; mevcut ürün DOM'u tek yatay sonuç satırında kullanılır.
- Eski mobil kart sunumunu masaüstüyle sınırlayan mevcut dosyalar:
  `premium-hover-v55.css`, `social-platform-identity-v77.css`,
  `platform-inheritance-flat-v80.css`, `service-category-identity-v82.css`,
  `catalog-visual-parity-v84.css`, `category-pastel-palette-v85.css`,
  `subcategory-neutral-v86.css`. Ortak marka tokenları ve diğer sayfa kuralları korunur.
- `tests/home-featured-mobile-qa.cjs`: gerçek JS ile sentetik, DB'siz durum testleri.
- `tests/home-featured-mobile-probe.mjs`: salt okunur gerçek browser ölçüm/doğrulaması.
- `tests/social-platform-identity-qa.php`: eski sabit cache tarihini, güncel veya
  daha yeni tarih kabul eden kontrolle değiştirdi; ilk kırmızı testin nedeni buydu.
- Bu rapor.

Yeni override stylesheet, inline CSS/JS veya ürün klonu eklenmedi.

## Doğrulama

Gerçek Chrome browser, yerel `127.0.0.1:8006`:

| Viewport | Grup satırı | Gezinti | Gutter | Sonuç |
| --- | --- | --- | --- | --- |
| 430×932 | tek, 44px düğmeler | Tümü 134px / alt kategorili 188px | 20px | geçti |
| 390×844 | tek, 44px düğmeler | Tümü 134px / alt kategorili 188px | 20px | geçti |
| 360×800 | tek, 44px düğmeler | Tümü 134px / alt kategorili 188px | 16px | geçti |
| 1440×900 | mevcut masaüstü | mevcut kart/ok sistemi | mevcut container | referansla eşleşti |
| 1280×720 | mevcut masaüstü | mevcut kart/ok sistemi | mevcut container | geçti |
| 1024×768 | mevcut masaüstü | mevcut kart/ok sistemi | mevcut container | geçti |

Yatay sayfa taşması veya chip metin kesilmesi saptanmadı. Scrollbar gizli,
chip satırları native `overflow-x:auto`. Uzun alt kategori satırı klavyeyle
sona kaydırıldı (`scrollLeft=1253`); seçilen alt kategorinin pane'i açıldı.
Instagram → Instagram Takipçi paketleri, grup geçişleri, çocuksuz grup Tümü,
`?subcategory=google-harita` önceliği ve Ajans seçiminin reload sonrası korunması
gerçek browserda doğrulandı. İlk seçim yokken Sosyal Medya/Tümü başlar.

1440 görev öncesi/sonrası container, başlık, grup şeridi, kategori/alt kategori
çerçeveleri ve ürünlerin ölçü/font/renk/border değerleri karşılaştırıldı: fark yok
(0.5px tolerans; mevcut masaüstü otomatik rail scroll konumu normalize edildi).

Geçen testler:

- PHP lint: home.php, layouts/app.php, social-platform-identity-qa.php.
- JS syntax check ve `home-featured-mobile-qa.cjs`.
- platform-category-design, catalog-visual-parity-v84, platform-parent-flat-ui-v80,
  social-platform-identity, header-reputation-parity-v83, production-ui-copy,
  home-expertise, home-software-frame QA.
- Görev dosyalarında `git diff --check`.

Browser kanıtları (ignore edilen yerel QA çıktıları):
`qa-artifacts/featured-mobile-20261016/{430,390,360,desktop,1024}.png` ve
`geometry.json`.

## Kalan kapsam dışı durum

Mevcut sabit canlı destek balonu, küçük ekranlarda ürün CTA'sının üstüne
gelebiliyor. Destek widget'ı bu görevde değiştirilmedi. Gerçek telefon donanımında
test yapılmadı; belirtilen ölçüler gerçek browser viewportlarıdır.

Kullanıcının önceden mevcut zip silmesi ve staged `.sync-conflict-*` dosyaları
korundu; görevin commitine dahil edilmez.
