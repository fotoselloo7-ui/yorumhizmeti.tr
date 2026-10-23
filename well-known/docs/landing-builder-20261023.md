# Konumsal landing tasarımı ve içerik editörü

Tarih: 23 Ekim 2026. Başlangıç: `main`, `5fefd05`; çalışma ağacı temizdi.

## Kök neden

Önceki düzen uzun ve tek parça bir formdu. Hizmet/kategori seçimi gerçek yazılım ürünlerini ve blog yazılarını ayrı birer içerik kaynağı olarak sunmuyordu. Sayfa bölümlerinin sırası sabitti; mevcut iki tasarım yaklaşımı da yeterince ayrışmıyordu. Bu nedenle içerik yönetimi ile ziyaretçi deneyimi birlikte ele alındı.

## Uygulama

- Sekiz adımlı editör: genel bilgiler, görünüm, sayfa içeriği, hizmetler, hazır yazılımlar, blog, referanslar, SEO/yayın.
- Dört bağımsız içerik seçicisi: Türkçe arama, seçilenler/kütüphane ayrımı, sayaç, kaldırma ve yukarı/aşağı sıralama. Her türde en fazla 24 kayıt; mevcut içerik kayıtları değiştirilmez.
- Sekiz sayfa bölümünün görünürlüğü ve sırası sayfa bazında yönetilir. İçeriksiz bölümler ve bunların gezinme bağlantıları gösterilmez. H2/H3 içerik blokları ihtiyaca göre açılır; en fazla 12 blok desteklenir.
- Studio: koyu premium hero. Editorial: açık kurumsal görünüm. Üç vurgu rengi, ayrı mobil/masaüstü görseli, okunaklı CTA, hizmet kartları, yazılım/blog seçkileri, portföy, yerel kapsam, kısa yanıt ve SSS.
- İletişim bağlantıları mevcut ortak site ayarlarından alınır. Tek H1; mevcut canonical, robots, OG, schema ve 301 mekanizmaları korunur. Sahte yerel şube, değerlendirme veya sıralama garantisi eklenmedi.
- Kayıt hatasında girilen metinler kaybolmadan HTTP 422 ile aynı form gösterilir. Eski revision korunur; eşzamanlı düzenlemenin üzerine yazılmaz. Kayıtlı yayın durumu ve kaydedilmemiş değişiklikler ayrı gösterilir.

## Veri ve güvenlik

Mevcut `pages.content` veri modeli genişletildi; yeni tablo veya migration yok. Eski kayıtlar boş yazılım/blog seçkisi ve etkin bölüm varsayımlarıyla uyumludur. Gerçek 8006 veritabanındaki sayfa içeriklerine, ürünlere veya bloglara yazılmadı.

`LandingCatalogService`, mevcut public katalog kaynaklarından yalnızca kart/seçici metadatasını projekte eder. Ürünlerin ham `public_json` alanı ve demo credential'ları HTML'e taşınmaz. Blog snapshot kayıtlarının negatif kimlikleri, aynı slug DB'ye aktarıldığında korunur; taslak/pasif DB karşılığı snapshot'ı bastırır. Kaybolan veya yayından kaldırılan seçimler editörde açıklanır, public sayfada gösterilmez. Yerelde bulunmayan katalog görselleri kırık resim yerine ikonla karşılanır.

CSRF, prepared SQL, output escaping, upload doğrulaması, yayın kontrolleri ve URL/revision güvenliği mevcut zincirde korunur. Ödeme, sipariş ve sağlayıcı akışları değiştirilmedi.

## Dosyalar ve kaldırılan eski yapı

- `app/Controllers/Admin/LandingController.php`, `app/Controllers/RegionalLandingController.php`
- `app/Services/RegionalLandingService.php`, yeni `app/Services/LandingCatalogService.php`
- `resources/views/admin/landing/form.php`, yeni `picker.php`
- `resources/views/frontend/landing/show.php`, yeni `sections.php`
- `public/assets/css/editorial-landings.css`, `landing-editor.css`, `public/assets/js/landing-editor.js`
- `resources/views/layouts/app.php`, `admin.php`: yalnızca ilgili mevcut assetlerin cache kimliği.
- Yeni `tests/landing-builder-qa.php`, `landing-builder-http-qa.php`.

Eski uzun form ve eski landing stil bloğu yerinde değiştirildi. Yeni sürüm/override stylesheeti veya `!important` katmanı eklenmedi. Ana sayfa bilgi alanının paylaşılan stil ve editör davranışları korundu. `.sync-conflict-*` dosyalarına dokunulmadı.

## Doğrulama

- 12 PHP dosyasında `php -l`: başarılı.
- `landing-builder-qa.php`: 46 başarılı kontrol.
- `landing-builder-http-qa.php`: 44 başarılı kontrol.
- `editorial-landings-qa.php`: 65 başarılı kontrol.
- `editorial-landings-http-qa.php`: 80 başarılı kontrol.
- `seo-infrastructure-qa.php`: 48 başarılı kontrol.
- `social-storefront-http-qa.php`: 359 başarılı kontrol; kendi QA değişikliklerini geri aldı.
- `inner-pages-premium-qa.php`, `public-slug-url-qa.php`, `home-featured-mobile-qa.cjs`, JS sözdizimi ve `git diff --check`: başarılı.

HTTP testleri yalnızca izole `netvera_member_qa_20261018` / 8019 ortamında çalıştırıldı. Test başlangıcında sentetik veri, kapalı ödeme ve kapalı SMTP doğrulanır. Normal test koşusu yalnızca kendi oluşturduğu kayıtları temizler. Tarayıcı doğrulaması için önceden `--keep-fixtures` ile bırakılan QA sayfası 65 ve bu koşunun sentetik kayıtları QA ortamında tutuldu; gerçek sitede yeni sayfa yayınlanmadı.

Gerçek Chrome kontrolleri:

| Alan | Boyutlar | Sonuç |
| --- | --- | --- |
| Editörün sekiz paneli | 1440×900, 1280×720, 1024×768, 390×844, 360×800 | 40 panel/boyut kontrolünde yatay taşma, dışarı çıkan alan veya kırık görünür görsel yok; tek panel görünür |
| Studio + Editorial public sayfa | Aynı beş boyut | 10 kontrolde taşma/kırık görsel yok; tek H1 |
| Gerçek Alanya sayfası | 1440×900, 390×844 | Hero, görseller ve mobil yeniden akış uygun |
| Ana sayfa bilgi alanı | 1440×900, 390×844 | Üçlü bilgi içeriği korunuyor; taşma yok |

Tarayıcıda gerçek native form ile ayrı yazılım/blog seçimi, Türkçe arama, kayıt sonrası kalıcılık, kart sırası, bölüm sırası, içerik ekleme/temizleme, iki şablon arasında geçiş ve mobil SSS açılması ayrıca doğrulandı. Geçici viewport override'ı sıfırlandı.

Kanıtlar: repository kökündeki `qa-artifacts/landing-builder-20261023/` (Git dışı). `responsive-results.json`, masaüstü/mobil hero, admin yazılım seçicisi, yazılım/blog kartları ve SSS ekran görüntüleri bulunur.

## Kalan sınırlar

- Mevcut sayfalara otomatik yazılım veya blog seçilmez; yönetici ilgili sekmelerden ekler.
- Otomatik kayıt ve serbest HTML/drag-and-drop sayfa oluşturucu yoktur; mevcut güvenli blok modeli korunur.
- Seçiciler mevcut katalog metadata listesini yükler. Çok büyük kataloglarda sunucu taraflı sayfalama ayrı bir performans işi olabilir.
- İçerik doğruluğu ve özgün bölgesel kapsam yayınlayan yöneticinin sorumluluğundadır. SEO/GEO/AIO görünürlüğü garanti edilmez.
- Commit hash teslim mesajında verilir; push yapılmadı.
