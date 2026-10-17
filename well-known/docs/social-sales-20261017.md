# Sosyal medya sabit paket satış akışı — 17.10.2026

## Sonuç ve kök neden

Eski kategori ekranı aynı listede hizmet türlerini ve miktar paketlerini karıştırıyordu. Paket detayı, aynı kategorinin ilk üç ürününü yeniden “Paket Seçimi” olarak gösteriyor; `min_quantity` / `max_quantity` ise teslim miktarı yerine paket adedi çarpanı olarak kullanılıyordu. Bazı import edilmiş ürünlerde `name` boş olduğundan hedef alanı üretimi de yanlış platform yorumlayabiliyordu.

Yeni sosyal medya akışı: platform → hizmet türü → sabit miktarlı paket → kullanıcı adı / bağlantı → sepet / ödeme. Örneğin 64,90 TL'lik 100 takipçi paketi, bir kez ücretlenir ve teslim miktarı 100 olarak kaydedilir. Aynı paketin farklı profillere alınması ayrı sepet satırları ve ayrı hedef alanları oluşturur. Aynı hedefe tekrar ekleme miktarı çoğaltmaz.

Kapsam kategori kökünden belirlenir. Instagram Reklam gibi pazarlama kategorileri, ajans, SEO ve hazır yazılımlar bu sabit sosyal paket sistemine dahil değildir. Ürün/kategori ID'leri, mevcut sluglar, fiyatlar ve ödeme entegrasyonu değiştirilmedi. Bayigram'ın platform/hizmet/miktar ayrımı araştırıldı: https://bayigram.com/instagram/ ve https://bayigram.com/instagram-turk-takipci-satin-al/ . Pazarlama vaatleri veya ürün fiyatları kopyalanmadı.

## Tek veri kaynağı ve admin

`app/Services/SocialPackageService.php` kapsam, hizmet gruplama, teslim miktarı, sepet adedi ve sipariş snapshot'ının ortak sahibidir. Yeni tablo veya migration gerekmez; metadata mevcut `settings` tablosunda `social_package_{id}` anahtarıyla saklanır.

Admin paket formunda Hizmet Türü, Paketin Teslim Miktarı ve Miktar Birimi alanları bulunur. Sosyal medya paketinde üç alan zorunludur. Fiyat alanı bu miktarın toplam fiyatıdır; min/max paket adedi 1'e sabitlenir. Bot takipçi veya yeni bir hizmet, gerçek ürün/veri/fiyat eklenince bu hizmet adıyla ayrı gruplandırılır. Mevcut katalogda bot takipçi ürünü yoktur; yapay ürün veya fiyat eklenmedi.

Mevcut paketler için öncelik: açık admin metadata → SMM eşlemesindeki teslim miktarı → açıklamanın başındaki kesin “100 takipçi için …” biçimi. Fiyat veya rastgele açıklama içindeki sayı miktar kabul edilmez. Yapılandırılmış yerel katalogdaki 185 sosyal paketin miktar/birim bilgisi çözümlendi; belirsiz paket bulunmadı. Yeni veya belirsiz pakette miktar yoksa satış kapatılır. Admin miktarı ile SMM eşlemesi farklıysa satış engellenir.

Siparişin `order_fields` kayıtları, sunucu tarafından oluşturulan hizmet ve teslim miktarı snapshot'ını saklar. Müşterinin gönderdiği aynı adlı alanlar bu değerleri değiştiremez. Sonradan admin değişikliği eski sipariş miktarını değiştirmez. Sağlayıcı işi kendi mevcut kuyruğunda 100 gibi gerçek teslim miktarıyla oluşturulur; ödeme kontrolü ve worker akışı korunur.

## Değiştirilen dosyalar / kaldırılan davranış

- `app/Services/SocialPackageService.php`: ortak sabit paket servisi.
- `app/Controllers/{Category,Package,Cart,Checkout}Controller.php`: hizmet/miktar ayrımı, yalnızca sosyal adet normalizasyonu, hedef bazlı sepet satırı, normal CSRF'li Hemen Satın Al gönderimi.
- `app/Services/{OrderService,SmmFulfillmentService,SmmOrderFields}.php`: teslim snapshot'ı, tam sağlayıcı miktarı, boş legacy isim için güvenilir başlık fallback'i.
- `app/Controllers/Admin/PackageController.php`, `resources/views/admin/packages/form.php`: admin metadata doğrulaması ve kaydı.
- `resources/views/frontend/{category-detail,package-detail,cart,checkout}.php`, `partials/social-{category-filters,service-cards}.php`: hizmet kartları, net miktar gösterimi, ayrı hedef alanları.
- `public/assets/css/storefront-design-language-v89.css`, `resources/views/layouts/app.php`: mevcut stil sahibinde yeni component kuralları ve cache anahtarı. Yeni override stylesheet veya `!important` katmanı eklenmedi.
- `tests/social-sales-{audit,qa,integration,http-qa,router}.php`: salt okunur katalog audit'i, offline sözleşme testleri, izole DB/HTTP testleri ve loopback QA sunucusu.

Sosyal paketlerde ikinci varyant seçimi, adet stepper'ı ve başarısız istekten sonra da ödeme sayfasına yönlendiren eski inline `buyNow` kodu etkisizleştirildi. Sosyal medya dışı varyant/adet akışı korundu. Eski dar ve çakışan kategori chip sistemi yalnızca sosyal medya ekranlarında yeni component ile değiştirildi. Filtre URL'leri admin `index` ayarını devralmaz: `noindex,follow` ve özgün kategori canonical'ı kullanılır. CollectionPage şeması görünen hizmet/miktar listesiyle uyumludur.

## Doğrulama

- Değiştirilen/yeni 22 PHP dosyasında `php -l`; `git diff --check`: geçti.
- `social-sales-qa.php`: 20 offline miktar, Unicode, hizmet, admin, SMM eşleme ve kapsam kontrolü geçti.
- `social-sales-integration.php`: gerçek DB'de 9999 paket adedinin 1'e normalizasyonu, 64,90 TL fiyat, sahte teslim alanının reddi, admin metadata kalıcılığı, değişmez eski sipariş miktarı ve sağlayıcı kuyruğuna 100 miktar aktarımı geçti.
- `social-sales-http-qa.php`: gerçek HTTP'de CSRF, miktar kurcalama, aynı/farklı hedefler, 64,90 / 129,80 TL toplamları, native satın alma yönlendirmesi, hizmet şeması ve filtre canonical/noindex geçti.
- SMM hedef/UI (21), API güvenlik (12), platform/category tasarım, production UI copy, public slug, inner-page premium, platform parent UI, catalog parity, service visibility, social identity ve storefront premium polish testleri geçti.
- PayTR testleri izole QA veritabanında sentetik anahtarlarla geçti: tutar doğrulama, yanlış imza, başarısız ödeme, başarı ve mükerrer callback. Gerçek ödeme yapılmadı.

Chrome gerçek browser kontrolü: 1440×900, 1280×720, 1024×768, 390×844, 360×800. Her boyutta hizmet kataloğu, Türk takipçi 100/500 miktar listesi, 100 takipçi detayı, sepet ve ödeme kontrol edildi (25 kombinasyon). Yeni componentlerde yatay taşma veya metin kesilmesi saptanmadı. Desktop hizmet kartları üç sütun, tablet iki, mobil tek sütun; filtreler mobilde kendi içinde kayar. Mobilde miktar ve satın alma CTA'ları görünür, ikinci paket/adet seçimi yoktur. Aynı paketin iki hedefi checkout'ta ayrı alanlar ve iki adet 100 takipçi etiketiyle doğrulandı. Ajans, SEO ve hazır yazılım sayfaları ayrıca kontrol edildi; ajans paketinin adet alanı korundu.

Ekran görüntüleri ve ölçüm çıktıları (Git dışı): `qa-artifacts/social-sales-20261017/`. Özellikle `services-1440.png`, `purchase-final-1440.png`, `paket-360.png`, `targets-360.png`, `browser-results.json`.

## Kalan sınırlar ve güvenlik

- Gerçek sağlayıcıya sipariş gönderilmedi ve gerçek ödeme yapılmadı. Test kuyruğu / ödeme kayıtları yalnızca `netvera_social_sales_qa_20261017` izole DB'sindedir; geri alınabilir QA verileri korunur.
- Yapılandırılmış yerel DB'de mevcut SMM eşlemesi yok; otomatik gönderim için admin'den gerçek servis eşlemesi gerekir. Mevcut manuel sipariş akışı korunur.
- Legacy miktarlar admin kaydı yapılana kadar kesin açıklama biçiminden okunur. Admin formu eklendi; kalıcılık gerçek servis/DB üzerinden test edildi, admin GUI giriş/save işlemi browser'da yürütülmedi.
- Kullanıcının `.sync-conflict-*` dosyaları, önceden staged 22 dosyası ve silinmiş ZIP değişikliği commit kapsamına alınmadı. Push yapılmadı.
