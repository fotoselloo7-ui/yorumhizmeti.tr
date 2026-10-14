# NetVera site-wide UI audit ve kök neden onarımı

Tarih: 13–14 Ekim 2026. Başlangıç: `main`, `2483ace`.

Mevcut NetVera renkleri, Poppins, içerik, URL/slug, SEO metadata ve iş akışları korundu. Framework, bundler veya yeni sürüm override stylesheet'i eklenmedi. `.sync-conflict-*` artıkları ve önceden silinmiş ZIP bu çalışmanın dışında tutuldu.

## Kapsam ve dependency haritası

Router: 237 route; 113 GET (43 public, 70 admin). 96 template/partial, 80 kalan CSS ve 19 public UI JavaScript dosyası kaynak bağımlılıklarıyla tarandı. Tam kaynak sırası, CSS sınıfları, route/controller ve template listesi: [dependency envanteri](ui-dependency-inventory.md).

Tarayıcı ana matrisi 93 adres/senaryo: 37 public, 56 admin. Blog kategori, admin giriş ve gelen kutusu detayıyla toplam 96 farklı adres/senaryo kontrol edildi. Her veri kaydı ve her olası URL parametresi ayrı sayfa sayılmadı. JSON/API/export/redirect uçları görsel sayfa değildir; route haritasında ayrı gösterilir.

| Sayfa ailesi | Kontrol edilen örnekler |
| --- | --- |
| Ana sayfa | Hero, kategori marquee, uzmanlık, paket/yazılım kartları, yorum, referans, adımlar, blog, SSS |
| Hizmetler | Dizin, hizmet grubu, ana/alt kategori, paket detay |
| Yazılımlar | Katalog, kategori, tür, iç içe kategori, detay/galeri/tab/demo |
| İçerik | Blog liste/kategori/detay, iletişim, SSS, hakkımızda ve dört yasal metin |
| Kimlik | Giriş, kayıt, şifremi unuttum, geçerli sentetik token ile reset, admin giriş |
| Ticaret/hesap | Dolu sepet, checkout, başarı/hata, localhost PayTR tasarım önizlemesi, hesap, sipariş liste/detay, bayi |
| Destek | Ticket liste/oluştur/detay, canlı destek aç/kapat |
| Admin katalog | Kategoriler, paketler/alanlar, eski yazılım formu, NetVera katalog/kategoriler, import |
| Admin içerik | Menü, home sections, slider, popup, kupon, yorum, referans, blog/kategori, sayfa, SSS, SEO |
| Admin işlemler | Dashboard, sipariş, ödeme/havale, banka/ödeme modülleri, PayTR/Iyzico, SMM, bayiler, üyeler/müşteriler |
| Admin sistem/destek | Site/logo/favicon ayarları, SMTP, admin kullanıcıları, log, lisans, mesajlar, gelen kutusu liste/detay, ticket, cep paneli |

## Kök nedenler ve kalıcı çözümler

1. **Container:** v13 sınırsız genişlik ve sabit margin; v14 ayrıca 1440px ve breakpoint override'ları uyguluyordu. Ortak `--page-max-width:1200px` / `--page-gutter` kullanıldı; v14'teki tekrarlar kaldırıldı. Mobil 20px, 420px altında 16px.
2. **Uzmanlık kartları:** v29/v45/v49/v55/v101/v106 aynı kartta grid/flex/absolute, sabit yükseklik ve CTA konumunu değiştiriyordu. Layout, artwork, palette, CTA ve breakpointler v29'da toplandı. Tek ortak grid, eşit satır yüksekliği, doğal metin akışı, ayrı görsel kolonu ve chip footer var. Üçüncü karta özel layout yok. Geniş `.yh-home-v6 p` kuralının griye çevirdiği açıklama component specificity ile düzeltildi.
3. **Mobil katalog:** dosya sonundaki desktop sidebar kolonu mobil media query'yi eziyor, ürün alanını birkaç on piksele indiriyordu. Desktop kuralı temel konumuna taşındı. Küçük mobilde okunabilir tek kolon; fiyat/CTA aynı flex akışında.
4. **Navigasyon:** mobil menü normal akışta/yanlış yükseklikte kalıyor ve body lock yoktu. Viewport tabanlı scrollable overlay, ortak z-index, Escape/focus ve desktop resize temizliği eklendi. v27/v33 dropdown yükseklik override'ları kaldırıldı; yükseklik v26 tarafından yönetiliyor.
5. **Sepet/checkout:** beş çocuk için dört kolon ve mobil otomatik yerleşim, kaldır ikonunu daraltıyor ve toplam/miktarı yanlış satıra atıyordu. Mevcut authoritative responsive kurallarında kolonlar ve mobil grid yerleri düzeltildi.
6. **Admin mobil:** kategori/paket mobil listeleri ortak gizleme/flex kolon grubuna dahil değildi. Desktop/mobil kopyalar bir arada görünüyor, kartlar yatay sıkışıyordu. Ortak sınıflara dahil edildi; aksiyonlar eşit grid hücreleri oldu. Topbar ikonları ve uzun başlıklar yeniden akıyor; sidebar tek JS kontrolüne bağlandı.
7. **Tablolar:** `overflow:hidden` ve grid min-content genişliği tabloyu kesiyor veya komşu formu büyütüyordu. Tablo seviyesinde `.adm-table-scroll`, grid hücrelerinde `min-width:0`, sarılan sayfa aksiyonları kullanıldı. Tablolar 360px'e sıkıştırılmadı.
8. **Blog Select2:** genel input/select min-height ve transition kuralları gizli native select'e uygulanıyordu. Alan genişlik animasyonu sırasında sayfa dışına taşıyordu. Visible-field selectorlarından çıkarıldı; plugin sınırında 1px/transition:none korundu.
9. **Demo:** dolu hesap bilgisi yayın izni gibi değerlendiriliyordu. Üç bağımsız public flag zorunlu oldu. İlk HTML render maskeli; blur yerine nokta maskesi, hizalı copy/show/login, erişilebilir durumlar ve harici event binding kullanıldı. Clipboard başarısızlığı artık başarı diye gösterilmiyor.
10. **Gizli tab ölçümü:** `#demo` ile açılan üründe açıklama yüksekliği görünmezken sıfır ölçülüyordu. Ölçüm aktif tab ve resize sırasında yapılıyor; açıklama genişletme kaybolmuyor.
11. **Referans JS:** kaldırılmış hint öğesine koşulsuz `textContent` yazımı konsol hatası yaratıyordu. Kod harici dosyaya taşındı ve optional DOM öğesi korundu.

Z-index: sticky 100, dropdown 200, floating 300, overlay 400, modal 500, toast 600. Var olan input/button/card/radius/spacing temeli korundu; admin 7–10px temel metinleri okunabilir 11–13px aralığına çıkarıldı. Tüm siteyi tek tip kart görünümüne zorlamak yerine aileler korundu.

## Kaldırılan ve sınırlandırılan CSS

- Silinen üç kaynak: `home-service-alignment-v45.css`, `home-service-cards-v101.css`, `home-expertise-v106.css`. Git geçmişinden geri alınabilir; sync kopyaları silinmedi.
- v49/v55 içindeki nv29 override'ları; v14 container tekrarları; v27/v33 dropdown height tekrarları kaldırıldı.
- Home-only, katalog/detail ve account-only saf component stilleri/JS'leri ilgili template koşullarına alındı; kalan legacy kaynak sırası korundu.
- CSS dosyası: 83 → 80. Ham CSS: 1.060.346 → 1.014.968 bayt. `!important`: 5.857 → 5.109. Bunlar kaynak toplamlarıdır, gzip/network/Lighthouse ölçümü değildir.
- Kalan 80 CSS'in hepsinde aktif kaynak referansı var. Bu, her selector'ın kullanıldığı anlamına gelmez; medya/state/dinamik admin içeriğine kör purge uygulanmadı.

## Gerçek tarayıcı doğrulaması

İzole `netvera_ui_audit_20261013` şeması canonical `database/database.sql` ile oluşturuldu. Production/geliştirme veritabanı değiştirilmedi. Sipariş, ticket, login, demo ve sohbet verileri sentetik. Harici ödeme, e-posta, Telegram/WhatsApp mesajı veya gerçek demo girişi yapılmadı.

| CSS viewport | Ana matris | Final sonuç |
| --- | ---: | --- |
| 1440×900 | 93 | Temiz |
| 1280×720 | 93 | Temiz |
| 1024×768 | 93 | Temiz |
| 768×1024 | 93 | Temiz |
| 430×932 | 93 | Temiz |
| 390×844 | 93 | Temiz |
| 360×800 | 93 | Temiz |

651 final sayfa/viewport ölçümü. Oturumsuz auth ve blog kategori için 35; admin giriş için 7; gelen kutusu detayı için 7 ek ölçüm yapıldı. Ana matris oturumlu olduğu için yönlenen login/register adresleri, oturumsuz ek turla ayrıca doğrulandı. Son düzeltmelerde 63 hedefli yeniden kontrol ve açık menü/popup için 28 kontrol de yapıldı.

Programatik kontroller: document width, normal-flow viewport taşması, kesilen buton/başlık, tamamlanmış görsellerde yükleme hatası. Intentional carousel/table scroll ve kapalı drawer hariç. `tests/ui-layout-probe.mjs`, tarayıcıda kullanılan aynı read-only kontrolün yeniden kullanılabilir sürümüdür; absolute/fixed çakışmalarını veya görsel keskinliği tek başına kanıtlamaz.

Görsel/etkileşim kontrolleri: desktop/tablet/mobile screenshot, header drawer/body lock/Escape/resize, üç mega menü, hero dots/arrows, kategori filtre/tabları, SSS, ürün tab/klavye, açıklama expand, demo copy/show/login hedefi, admin drawer, popup close/body lock ve canlı destek paneli. Popup modal katmanı floating chat'in üzerinde; mobil panel ekran sınırları içinde.

Ölçüm örneği: üç uzmanlık kartı 1440px'te yaklaşık 410,91px; CTA üst offseti üçünde de 248,98px. 360px'te yaklaşık 452,96px; CTA offseti üçünde de 295,02px. Hero kaynakları 2400×840, desktop render 1200×425; yeniden sıkıştırma veya yeni görsel üretimi yapılmadı. Blog kapakları 1200px kaynak ve `contain` davranışıyla korundu. Lazy içerikler seçili ailelerde scroll/screenshot ile incelendi; her ürün galerisinin her görseli için keskinlik sertifikası verilmez.

Yerel kanıtlar (Git dışında `qa-artifacts/ui-audit/`): `browser-measurements.json`, `dependencies.json`, `expertise-1440.jpg`, `expertise-360.jpg`, `admin-360.jpg`, `demo-360.jpg`. Breakpoint duyarlı navigasyon probe'u ayrıca 14 kontrolde temiz geçti.

## Testler ve baseline ayrımı

- 243/243 PHP dosyası lint başarılı (vendor/node_modules/sync artıkları hariç).
- 19/19 aktif public JS syntax başarılı; dependency script ve layout probe ayrıca syntax kontrol edildi.
- 23/23 offline `*-qa.php` başarılı; public demo render testi yeni eklendi.
- İzole PayTR callback testi başarılı: bozuk hash, tutar farkı, bilinmeyen sipariş, başarısız/başarılı bildirim, duplicate callback ve localhost tasarım önizlemesi. Canlı işlem yapılmadı.
- Baseline'daki dört eski test beklentisi onarıldı: sync dosyası taraması, silinmiş seeds/migration beklentisi ve eski nv75 hero marker'ları. Production davranışı test uğruna değiştirilmedi.
- Dealer DB guard testi özel izole fixture/env gerektirdiğinden bu UI turunda çalıştırılmadı. Eski `visual-smoke.mjs` farklı CI ürün fixture'larına bağlı; çalıştırılmış sayılmadı. Gerçek browser matrisi bunun yerine ayrıca uygulandı.
- Referans formu düzeltmesinden sonraki tarayıcı konsolunda yeni JS hatası gözlenmedi. `git diff --check` temiz.

## Yeniden üretme

Repository kökünde, yerel PHP/PDO MySQL ortamında:

```text
node well-known/scripts/audit-ui-dependencies.mjs
php well-known/tests/public-demo-render-qa.php
node --check well-known/tests/ui-layout-probe.mjs
php well-known/tests/ui-audit-bootstrap.php
php well-known/tests/ui-audit-fixtures.php
php -S 127.0.0.1:8017 -t well-known/public well-known/tests/ui-audit-router.php
```

Bootstrap mevcut QA şeması varsa üzerine yazmayı reddeder. Fixture yalnızca adı sabit test şemasına bağlanır; `--private-demo` yayın izni negatif testi, `--popup` yalnızca `/sss` üzerinde sentetik popup testi içindir. Test hesapları/şifreleri fixture kaynaklarında sentetiktir. Bu harness production giriş noktası değildir; sunucu sadece loopback adresinde çalıştırılmalıdır. Gerçek credential/servis ayarlarını kopyalamayın veya loglamayın.

## Kalan borç ve kapsam sınırları

- Legacy CSS'in tamamı sıfırlanmadı: 5.109 `!important` ve bazı inline stiller hâlâ mevcut. Kritik gözlenen component çatışmaları çözüldü; tüm selector'ları bir seferde yeniden yazmak tasarım/işlev riski taşır.
- `NetveraLocalSetup` ve `SmmCatalogService` içinde eski migration dosyası beklentileri; demo import tekrarları ayrı backend borcudur. Bu UI görevi için iş mantığı değiştirilmedi.
- İnceleme Chromium tabanlı uygulama tarayıcısında yapıldı; gerçek iOS Safari/Firefox, ekran klavyesi ve üretim CDN/cache davranışı ayrı cihaz/ortam doğrulaması gerektirir.
- E-posta/Telegram/WhatsApp teslimatı, canlı PayTR/Iyzico/SMM API'leri ve harici demo sistemleri test edilmedi.
- QA şeması yalnızca sentetik verilerle yerelde bırakıldı. Test sunucusu çalışma sonunda durdurulur; production deploy veya push yapılmaz.

## Değişiklik grupları

Global primitives/container/navigation; homepage kart konsolidasyonu; katalog/detail/demo; cart/checkout; admin table/form/drawer/reference JS; dependency/probe/fixture/QA ve dokümantasyon. Commitler yalnızca bu gruplara ait açık dosya listeleriyle oluşturulur; başlangıç indexindeki kullanıcı dosyaları dahil edilmez.

- `3f30cd9` — Consolidate NetVera storefront responsive UI and component styles.
- `0047423` — Repair admin responsive tables forms and drawer controls.
- Test ve rapor checkpoint'i: `Add isolated UI audit coverage and dependency documentation` (bu raporu içeren commit; hash `git log` ile bulunur).
