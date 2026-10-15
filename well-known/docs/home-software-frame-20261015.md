# Ana sayfa hazır yazılım bölümü — 2026-10-15

## Kapsam ve kök neden

Kullanıcının son isteği doğrultusunda, tüm siteyi genişleten önceki commitlenmemiş çalışma geri alındı. Genel container, header/footer, iç sayfalar ve AGENTS.md tekrar `50582e8` durumunda bırakıldı. Kullanıcının mevcut local değişikliklerine ve `.sync-conflict-*` dosyalarına dokunulmadı.

Ana sayfadaki iki komşu bölüm farklı frame kullanıyordu: uzmanlık kartları mevcut `nv29-container` (en fazla 1544px), hazır yazılımlar genel `container` (en fazla 1200px). Sadece hazır yazılımların dış wrapper'ı uzmanlık bölümüyle aynı mevcut frame'e bağlandı. Genel container kuralları değiştirilmedi; yeni genişlik override dosyası üretilmedi.

## Dosyalar

- `resources/views/frontend/home.php`: yalnız hazır yazılım wrapper sınıfı değiştirildi.
- `public/assets/css/software-showcase-v31.css`: sadece ana sayfa kapaklarında sabit yükseklik yerine 16:10 oranı kullanıldı. Mevcut 4/3/2 kolon akışı korundu.
- `resources/views/layouts/app.php`: ilgili CSS cache anahtarı güncellendi.
- `tests/home-software-frame-qa.php`: kapsam, frame, grid ve inline stil regresyon kontrolü.
- `tests/home-software-frame-probe.mjs`: salt okunur browser geometrisi ve doğrulama yardımcıları.

Ürün verileri, fiyatlar, CTA adresleri, admin kontrolü ve diğer katalog sayfaları değiştirilmedi. Önceki genel genişlik çalışmasına ait iki geçici test dosyası kaldırıldı; başka eski kod silinmedi.

## Doğrulama

PHP lint: iki değişen PHP template ve yeni QA testi başarılı. JavaScript syntax kontrolü başarılı. Offline QA: 26/26 başarılı. PayTR ve bayi programı fixture yazan testleri çalıştırılmadı; tam CI visual-smoke çalıştırılmadı.

Gerçek Chrome, izole mevcut UI audit veritabanı, `127.0.0.1:8017`:

| Viewport | Uzmanlık / yazılım genişliği | Header genişliği |
| --- | --- | --- |
| 1920×1080 | 1544 / 1544 | 1200 |
| 1440×900 | 1297 / 1297 | 1200 |
| 1280×720 | 1137 / 1137 | 1200 |
| 1024×768 | 961 / 961 | 969 |
| 390×844 | 343 / 343 | 343 |
| 360×800 | 313 / 313 | 313 |

Altı boyutta aynı sol/sağ hizalama, yatay taşma olmaması, bölüm metinlerinde kırpılma olmaması ve kapak oranı doğrulandı. Desktop header/footer eski 1200px sınırında kaldı. Desktop CTA/fiyat satırları ve mobil iki kolon akışı görsel olarak incelendi.

Kanıtlar (Git dışında): `qa-artifacts/home-software-frame-20261015/geometry.json` ve aynı klasörde altı viewport PNG'si. Geometri ilk ölçümünde ekran dışında kalan lazy görseller henüz yüklenmemiş olabilir; bölüm görünürken kapakların yüklenmesi ayrıca ekran görüntülerinde doğrulandı.

## Kalan sınırlar

Mevcut genel CSS katmanları ve küçük mobildeki eski 16px boşluk kuralı bu dar kapsamda korundu. İki ana sayfa bölümü mevcut, yalnız ana sayfada yüklenen v29 frame stilini paylaşır. Bu değişiklik için tespit edilen açık bir görsel regresyon yok; ödeme ve admin iş akışları bu CSS görevinde uçtan uca yeniden test edilmedi. Push yapılmadı.
