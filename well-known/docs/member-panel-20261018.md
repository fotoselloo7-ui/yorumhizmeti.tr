# Üye paneli yenilemesi — 18.10.2026

## Sonuç ve kapsam

NetVera müşteri alanı, mevcut PHP MVC, sipariş, ödeme, SMM ve legacy yazılım kayıtları korunarak yenilendi. Yeni tablo veya migration gerekmez; temiz kurulum kaynağı `database/database.sql` olarak kaldı. Public ürün URL'leri, fiyatlandırma, PayTR callback ve sağlayıcıya sipariş gönderme davranışı değiştirilmedi. Canlı ortama dağıtım veya push yapılmadı.

| Alan | Gerçek veriyle çalışan özellik |
| --- | --- |
| `/hesabim` | Sipariş özeti, devam eden işler, ödeme/bilgi bekleyen siparişler, yanıtlanan destek talepleri, son kayıtlar |
| `/siparislerim` | Sipariş/paket arama, durum/ödeme/tarih filtresi, onarlı sayfalama |
| `/siparis/{id}` | Sahiplik kontrolü, sipariş içeriği, müşteri alanları, müşteriye açık durum notları, teslimat ilerlemesi, mevcut havale bildirimi, yazdırma, siparişe bağlı destek |
| `/hesabim/yazilimlar` | Ödenmiş yerel yazılım siparişleri ve doğrulanmış legacy satın alma kayıtları; ayrı teslimat/arşiv görünümü |
| `/hesabim/odemeler` | Siparişlerden türetilmiş ödeme özeti ve filtrelenebilir kayıtlar |
| `/hesabim/hareketler` | Son sipariş/destek durumları; okunmamış bildirim sayacı değildir |
| `/hesabim/profil` | Ad/telefon doğrulaması; e-posta salt okunur |
| `/hesabim/guvenlik` | Mevcut şifre doğrulama, yeni şifre tekrarı, bcrypt bayt sınırı, oturum kimliği yenileme |
| `/destek`, `/destek/yeni`, `/destek/{id}` | Arama/sayfalama, sahip olunan siparişi bağlama, özel dosya eki, atomik mesaj/yanıt, talebi kapatma |
| `/bayilik` | Mevcut özellikler korunarak ortak panel kabuğuna taşındı |

## Kök nedenler ve çözüm

- Eski panel stilleri `app.css` ve sonradan gelen `storefront-v4.css` kurallarıyla çatışıyordu; çok küçük yazı ve sıkışık kontroller üretiyordu. Eski yaklaşık 870 satırlık panel bloğu, yaklaşık 53 satırlık sonradan eklenmiş override ve 22 satırlık eski panel JS kaldırıldı. Aktif panel tek `member-panel.css` / `member-panel.js` bileşenini kullanır; yeni `!important`, inline stil veya inline script eklenmedi.
- İşlemler tek yoğun hesap sayfasında toplanmıştı. Ayrı, veri odaklı ekranlar ve ortak navigasyon/kart/filtre/sayfalama partial'ları oluşturuldu. 1200px container, Poppins, 20px/16px mobil boşluk, kontrollü hareket ve reduced-motion desteği korundu.
- Destek yanıtı eski `admin_reply` değerini yazıyordu; kanonik şema `answered` bekliyor. Ortak servis artık kanonik değeri atomik yazar, eski kayıtların etiketi/filtrelemesi uyumlu kalır. Admin ekranı da aynı servisi kullanır.
- Sipariş seçimi, kapalı talebe yanıt ve dosya eki erişimi controller/transaction katmanlarında kullanıcı sahipliğiyle doğrulandı. Şifre tekrar alanı sunucuda doğrulanır. Pasif kullanıcıların eski oturumları korumalı ekranlara giremez.
- Yazılım kütüphanesi sadece legacy kaynağa bırakılmadı: mevcut yazılım taksonomisindeki ödenmiş yerel siparişler de gösterilir. Ürün adına bakarak hak üretilmez; lisans/indirme yetkisi uydurulmaz.
- Admin sipariş durum formunda açıkça “Müşteriye Görünür Not” olarak tanımlanan admin notları korunur. Özel `admin_note`, sağlayıcı hataları ve sistem log notları gösterilmez.

## Kaynak ve bağımlılık zinciri

`App/Router → aktif Auth → AccountController/SupportController → MemberPanelService/MemberSupportService → mevcut orders/order_items/order_fields/smm_order_jobs/support_* tabloları → frontend/account ve frontend/support → layouts/app → member-panel.css/js`.

Legacy yazılım geçmişi mevcut `LegacyCustomerService` kaynağını kullanır. Yazılım sınıflandırması `SoftwareCatalogService::ROOT_SLUG` ile aynı taksonomiyi izler. SMM görünümü yalnızca izin verilen son kayıt alanlarını okur; sağlayıcıya API isteği veya yeni sipariş göndermez. Bilinmeyen/çelişkili kalan miktardan sahte yüzde üretilmez.

Değişiklikler: iki controller, admin destek yanıtı, route/auth/privacy çekirdekleri, ikon servisi, panel layout/sidebar/account/support view'ları, CSS/JS modülleri, destek özel depolama koruması, QA testleri ve bu rapor. Eski hesap/bayilik modüllerinin ilgili selector'ları ortak kabuğa uyarlandı. Yeni ekran varlıkları yalnızca üye alanında yüklenir.

## Güvenlik ve yönetim uyumu

Prepared SQL, escaped çıktı ve CSRF korunur. Kullanıcı ID'si istemciden alınmaz. Yeni destek ekleri 10 MB MIME allowlist ile `storage/member-support` içine, public kökün dışında rastgele adla kaydedilir. Yalnızca talebin sahibi veya aktif admin `/destek/dosya/{id}` üzerinden indirebilir. İndirme private/no-store, nosniff ve attachment başlıkları taşır. Başarısız işlemde yalnızca o işlemde oluşturulmuş özel dosya temizlenir.

Eski `/uploads/support` dosyaları silinmedi/taşınmadı; Apache doğrudan erişimi engellenir, yetkili indirme eski dosyayı okuyabilir. Profil/destek/üye ekranları noindex ve private/no-store'dur; `/bayilik` dahil. Yeni admin ayarı gerektiren sahte özellik oluşturulmadı; destek admin işlemleriyle çift yönlü uyum test edildi.

## Doğrulama

- Değişen/yeni 41 PHP dosyasında `php -l`: geçti. İki JavaScript dosyasında `node --check`: geçti. `git diff --check`: geçti.
- `member-panel-qa.php`: **43** sözleşme geçti.
- `member-panel-http-qa.php`: **116** kontrol geçti. Misafir/pasif/diğer üye izolasyonu, arama/filtre/sayfalama, profil/şifre, XSS escaping, yabancı sipariş, kapalı talep, özel PDF yükleme/indirme, admin yanıtı ve dosya erişimi dahil.
- `member-panel-payment-qa.php`: mevcut PayTR imza, tutar, bilinmeyen sipariş, başarısız/başarılı/tekrarlanan callback ve yerel önizleme regresyonları geçti. Sentetik anahtarlarla tek sefer çalıştı; gateway yapılandırması `finally` içinde eski haline döndürüldü. Gerçek ödeme/get-token çağrısı yapılmadı.
- SEO offline **48** kontrol; mevcut SEO HTTP taraması **403 URL / 3713 kontrol** geçti.
- Logo, iç sayfa premium, public UI copy, auth görseli, uzmanlık kartları, yazılım çerçevesi, hero slider, kategori hero, sosyal satış, SMM hedef/UI, kategori görünürlüğü ve public demo regresyonları geçti.
- Gerçek Chrome: **1440×900, 1280×720, 1024×768, 390×844, 360×800**; 13 üye ekranında **65 senaryo / 1840 kontrol geometrisi** incelendi. Yatay taşma, ekran dışına çıkan kontrol veya H1 eksikliği görülmedi. Desktop/tablet/mobil ekran görüntüleri ayrıca gözle incelendi. Mobil filtrelerin tek sütun açılması, aktif filtrenin açık kalması, menü/Escape, gerçek GET araması ve boş şifre alanlarında göster/gizle kontrol edildi.
- Görsel kanıtlar (git dışında): `qa-artifacts/member-panel-20261018/` altında dashboard 1440/390, sipariş/teslimat 1280, profil 1024, destek/filtre 360 PNG'leri.

Testler yeni `netvera_member_qa_20261018` veritabanı ve localhost:8019 üzerinde sentetik kullanıcılarla yapıldı. Bootstrap mevcut DB'yi ezmez. SMTP ve gerçek ödeme kapalıdır. HTTP testleri yalnızca bu sabit QA şemasında kayıt üretir; test şifresi/profil değişiklikleri geri alınır. SEO taraması mevcut localhost:8017 QA ortamını kullandı; public blog GET sayaçları yalnızca o QA verisini etkileyebilir. Üretim müşteri/ödeme/credential verisine dokunulmadı.

## Dağıtım koşulları ve kalan riskler

- Production web sunucusu bu görevde doğrulanmadı. Document root `public` olmalı. Apache koruması eklendi; **Nginx kullanılıyorsa `/uploads/support/` doğrudan erişimini ayrıca reddeden eşdeğer kural gerekir**. Private storage public alias ile yayınlanmamalı. PHP upload/post limitleri ve storage yazma izinleri 10 MB akışına uygun ayarlanmalı.
- Kütüphane yerel taksonomideki son 100 satın alma kalemini gösterir. Harici checkout tek başına yerel sahiplik kaydı oluşturmaz; mevcut import/teslimat süreci gerekir. Lisans, indirme ve e-fatura üretim altyapısı eklenmedi.
- Cüzdan/bakiye, otomatik sağlayıcı refill/iptal/iade, MFA, tüm cihazlardan çıkış ve okunmamış bildirim sistemi ayrı veri modeli/entegrasyon gerektirir; çalışmayan buton olarak eklenmedi. Yazdırma sipariş özetidir, fatura değildir.
- Global legacy dosyalarda artık bu panel DOM'una eşleşmeyen `.panel-*` kalıntıları bulunabilir; bu görevde başka sayfaların kaynaklarına toplu temizlik uygulanmadı.
- Kullanıcının önceden staged 22 sync-conflict dosyası, diğer senkronizasyon artıkları ve ZIP silme değişikliği korunur; bu commit'e alınmaz. Secret/conflict dosyaları aktif kaynak yapılmadı.

Commit kimliği son teslim mesajında belirtilir; rapor commit'in parçasıdır.
