# İletişim sayfası ve Google harita — 20.10.2026

## Sonuç / kullanım

- Public: `/iletisim`; admin: `/admin/iletisim-ayarlari` (Ayarlar → İletişim & Harita). Site Ayarları iletişim kartından da bağlantı vardır.
- Google Haritalar → gerçek konumunuz → Paylaş → Harita yerleştir → HTML'yi kopyala. Kodu embed alanına yapıştırın, görünürlüğü Açık seçin, kaydedin.
- Tam iframe veya desteklenen HTTPS embed URL kabul edilir. Sadece doğrulanmış src saklanır; kopyalanan genişlik, stil, olaylar ve diğer HTML çalıştırılmaz. Google dışı host, script URL, normal kısa paylaşım linki, userinfo/port/fragment ve ek HTML reddedilir.
- Başlık (H1), giriş, yanıt süreci notu, adres/telefon/e-posta/WhatsApp, harita başlığı (H2) ve ulaşım notu adminden düzenlenir. İletişim bilgileri mevcut global settings anahtarlarıdır; footer ve konumsal sayfalarla ayrı kopyalar oluşturulmaz.
- Harita boş/kapalı olduğunda bölüm tamamen gizlenir. Başlangıçta kapalıdır; gerçek kurum konumu verilmediği için 8006'ya sahte veya örnek konum kaydedilmedi.
- Google bağlantısı ziyaretçi düğmeye basana kadar kurulmaz. JavaScript kapalıysa ayrı sekme bağlantısı vardır; normal çalışmada da kalıcı yedek bağlantı sunulur.

## Kök neden / dependency zinciri

Önceki view adresi hiç göstermiyordu; harita ayarı/veri akışı yoktu. Eski hero görseli ve yv-contact yapısı farklı sürüm stylesheetlerinden etkileniyordu. Eski view değiştirildi; yeni bileşen yalnızca `contact-page.css` tarafından stillenir. Global eski selectorlar diğer bileşenler açısından silinmedi, yeni iletişim DOM'una artık eşleşmez. Yeni !important veya inline stil/script yoktur.

`settings → ContactPageService → PageController → frontend/contact.php → contact-page.css / contact-map.js`.

`Admin middleware + CSRF → SettingsController::saveContact → ContactPageService::validate → transaction → SiteConfigService`.

Genel Site Ayarları save endpoint'i de embed doğrulamasını atlayamaz. Frontend mevcut DB'de geçersiz URL görürse haritayı göstermez. Yeni tablo/migration/DDL yok; kanonik database.sql settings tablosu yeterlidir. Formun mevcut POST /iletisim, KVKK, honeypot, CSRF, rate limit ve LeadCaptureService zinciri korundu.

Browser'da gizli lazy iframe'in tıklamadan sonra da about:blank kalabildiği görüldü; kullanıcı açıkça yüklemeyi istediğinde loading=eager ayarlanarak giderildi. Google haritasının kendisi ve harita kontrolleri gerçek Chrome'da görüldü.

## Değişen dosyalar

- Controller/routes: `PageController.php`, `Admin/SettingsController.php`, `Core/App.php`.
- Veri/güvenlik: `Services/ContactPageService.php`.
- Frontend: `frontend/contact.php`, `layouts/app.php`, `contact-page.css`, `contact-map.js`.
- Admin: `admin/settings/contact.php`, `admin/settings/site.php`, `layouts/admin.php`, mevcut `admin.css` içinde yalnızca contact-settings namespace'i ve cache anahtarı.
- QA: `tests/contact-page-qa.php`, `tests/contact-page-http-qa.php`.

## Doğrulama

- 44 offline embed/content/form/layout kontrolü.
- 26 gerçek HTTP kontrolü: admin auth, CSRF, iframe normalizasyonu, XSS temizliği, invalid veride tüm kaydın reddi, genel ayardan bypass koruması, aç/kapat/sil, ortak veri, formun CSRF/honeypot/onay/persistence/başarı/rate-limit davranışı.
- İlgili regresyonlar: SEO 48, editorial landing 65, inner-pages-premium; ayrıca konumsal sayfalar HTTP 80 ve sosyal satış HTTP 359 başarılı.
- PHP lint, JS syntax ve diff whitespace kontrolü.
- Gerçek Chrome: 1440×900, 1280×720, 1024×768, 390×844, 360×800. Haritasız gerçek sayfa, sentetik haritalı sayfa ve admin: toplam 15 görünüm; yatay taşma, ekran dışı kontrol veya public H1 çoğalması yok. Mobil tek sütun ve 16 px input metni, desktop iki sütun.
- Mobilde harita düğmesi tıklandı, Google'ın Alanya haritası ve kontrolleri görüldü. Admin kaydet düğmesiyle başarı bildirimi alındı. Mobil mesaj kısayolu formu kaydırdı.
- Kanıtlar (git dışında): `qa-artifacts/contact-page-20261020/contact-1440.png`, `contact-form-390.png`, `map-360.png`, `admin-map-1440.png`, `responsive-results.json`.

Yazma testleri yalnızca sentetik `netvera_member_qa_20261018` / localhost:8019 üzerinde, ödeme/e-posta kapalıyken çalışır. Normal test kendi ayarlarını geri alır, kendi mesaj ID'lerini temizler. Açık `--keep-fixtures` ile yalnızca QA ortamında sahte iletişim bilgileri ve Alanya şehir haritası browser için bırakıldı. 8006 gerçek iletişim ayarları değiştirilmedi. Gerçek müşteriye mesaj/e-posta veya provider/ödeme isteği gönderilmedi.

## Sınırlar / notlar

Harita Google ve ziyaretçi ağına bağlıdır; engellenirse ayrı sekme bağlantısı kullanılabilir. Gerçek embed konumunu adminin eklemesi gerekir. API v1 embed kullanılırsa anahtarın public browser kullanımına uygun ve alan adı kısıtlı olması gerekir; [Google resmi embed rehberi](https://developers.google.com/maps/documentation/embed/embedding-map) boyut ve referrer politikasının kaynağıdır. Harita en dar testte 311×320 px'tir, 200×200 minimumunun üzerindedir.

Mevcut global adres yalnızca ekrana çıkarılır; gerçek olmayan şube, koordinat veya LocalBusiness şeması eklenmedi. Satış/katalog/üye paneli tasarımı değiştirilmedi. Önceden staged sync-conflict dosyaları ve ZIP silme değişikliği korunur ve commit dışında bırakılır. Push/canlı dağıtım yoktur; commit hash teslim mesajındadır.
