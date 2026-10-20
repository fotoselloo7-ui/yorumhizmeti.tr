# NetVera Teknoloji Yazılım — Proje Talimatları

## Kapsam ve mimari

- Bu depo bir bütün olarak ele alınır: depo kökü, `.github`, testler, SQL ve `well-known/` uygulaması birlikte projedir. PHP MVC uygulamasının kökü `well-known/` dizinidir.
- Ürün, **NetVera Teknoloji Yazılım** kurumsal sitesi, dijital ajans, SEO/sosyal medya hizmetleri ve hazır yazılım satış kataloğudur. Kullanıcıya açık yeni metinlerde eski `yorumhizmeti` markasını kullanmayın.
- Eski marka URL'leri, indekslenmiş yollar ve çalışan backend uyumluluk katmanları SEO ve yönlendirme etkisi incelenmeden silinemez veya değiştirilemez. Slug değişiklikleri yalnızca uygun 301 eşlemesiyle yapılır.
- Mevcut mimariyi izleyin; gereksiz framework/dependency eklemeyin. Tekrarlanan iş mantığını controller/view kopyaları yerine service, helper veya bileşen seviyesinde birleştirin.

## Çalışma ağacı ve Git güvenliği

- Her işin başında `git status`, aktif dal ve ilgili son commitleri inceleyin. Kullanıcının yerel/değişmemiş ya da izlenmeyen dosyalarını silmeyin, resetlemeyin veya üzerine yazmayın.
- `git reset --hard`, `git clean -fd`, force-push ve `main` geçmişini yeniden yazmak yasaktır.
- Depoda oluşabilecek `.sync-conflict-*` dosyaları kullanıcıya ait yerel durum sayılır; yalnızca açık onayla ele alınır.
- Anlamlı değişiklikler için: sorunu yeniden üretin, kök nedeni bulun, minimal kalıcı çözümü uygulayın, test edin, diff'i inceleyin ve yalnızca kullanıcı izin/veri akışı gerektiriyorsa commit oluşturun. Test edilmemiş değişiklik push edilmez.

## Tasarım ve responsive

- Premium, modern, minimal ve kurumsal teknoloji dili kullanın; Poppins fontu, yaklaşık 1200px ana container ve 8px temelli boşluk sistemi esastır.
- Başlıklar dengeli ve genel olarak sol hizalı; kartlar kompakt, hover/animasyonlar ölçülü olmalıdır. Inline CSS kullanmayın; mevcut proje bilinçli olarak kullanmıyorsa inline JS de eklemeyin.
- Mobil yatay padding 20px, 420px ve altı için gerektiğinde 16px'tir. Mobil, desktop'ın ölçeklenmiş kopyası değildir; layout gerektiğinde yeniden akmalıdır.
- Her anlamlı frontend değişikliğinde gerçek tarayıcıyla 1440x900, 1280x720, 1024x768, 390x844 ve 360x800 görünümünü doğrulayın: overflow, çakışma, clipping, CTA, kart yüksekliği, whitespace, menü/modal/widget, slider ve görsel kalitesini kontrol edin.

## CSS ve görsel hata çözümü

- **Kök neden önce:** DOM ve computed CSS'i inceleyin; selector kaynağını ve specificity çatışmalarını bulun; gerçek component stylesheetinde ölü/çakışan kuralları konsolide edin.
- Aynı component için sürüm numaralı override birikimi, rastgele yeni stylesheet, ekran görüntüsüne özel hack ve sonuna eklenen `!important` yasaktır. Yeni CSS yaması ancak bunun neden yeterli ve güvenli olduğu gösterildiğinde son çaredir.
- Slider görsellerinde doğru aspect-ratio, object-fit, desktop/mobile ayrı varlık ve kırpma kurallarını koruyun; upload sırasında gereksiz sıkıştırma uygulamayın.

## Katalog, demo ve satın alma

- Hazır yazılımın kanonik yolları `/hazir-scriptler` ve `/hazir-scriptler/{slug}`'dır. Eski NetVera ürün URL/slug verileri korunur.
- Katalog yeni ürünleri kod değişikliği gerektirmeden admin verisiyle render etmelidir. Ürün alanları; medya, galeri, demo, modüller, teknik özellikler, lisans, SSS, yorum, SEO/GEO/AIO/OG ve satın alma durumunu desteklemelidir.
- Demo verisi zinciri `script_products` (varsa kaynak) → import → `nv_legacy_script_products.public_json` → `NetveraBridgeService` → `NetveraScriptController` → `netvera-script-detail.php` olarak doğrulanır.
- Demo credential'ları yalnızca demo aktif, public ve `demo_credentials_public` izni açık ürünlerde gösterilir. Gerçek müşteri/admin hesapları asla frontend'e, fixture'a veya loglara yazılmaz. Public demo şifresi varsayılan gizlidir; göster/kopyala ve yeni sekmede giriş UX'i veri odaklıdır.
- Satın alma, PayTR/callback/webhook/tutar/doğrulama/çift ödeme/test-canlı ayrımı mevcut çalışan akışları kırılmadan ele alınır.

## Veritabanı, yönetim ve güvenlik

- Tek kanonik kurulum dosyası `well-known/database/database.sql`'dir; temiz kurulum şema, gerekli başlangıç verisi ve katalogla çalışmalıdır. Eski migration/seeds dosyalarına runtime bağımlılığı oluşturmayın. Eski beklentili testleri silmek yerine tek-SQL gerçeğine göre güncelleyin.
- Frontend'de sunulan her yeni yönetilebilir özellik için admin parity'sini kontrol edin (menü, katalog, demo, ödeme, içerik, SEO, medya vb.). Yönetim arayüzünde okunaklı fontlar kullanın.
- SQL injection, XSS, CSRF, URL ve upload doğrulamalarını koruyun. Secret/API anahtarı veya hassas hata ayrıntısını kullanıcıya açık alana sızdırmayın. Mevcut müşteri/sipariş verisini silmeyin.

## SEO ve kalite doğrulaması

- Her içerikte title, meta description, canonical, OG, schema, anlamlı heading hiyerarşisi, alt metin ve iç bağlantıları değerlendirin. Spam SEO üretmeyin.
- PHP değişikliklerinde `php -l` çalıştırın; ilgili testleri çalıştırın. Kırık testleri “önemsiz” saymayın: önce nedeni araştırın.
- Bir işi tamamlandı diye bildirmeden önce gerçek çıktıyı doğrulayın. Görsel sorunlar screenshot/tarayıcı incelemesi olmadan çözülmüş kabul edilmez.
