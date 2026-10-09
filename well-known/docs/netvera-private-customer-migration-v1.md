# Netvera → YorumHizmeti: Özel hesap, sipariş ve lisans geçişi
Durum: **beklemede — eski sistemin özel müşteri SQL'i / salt-okunur veritabanı bağlantısı gerekiyor**.

## Neden GitHub'dan henüz aktarılamaz?
Mevcut `database/netvera-public-catalog.json` müşteri kimliği ve satın alma verileri kasıtlı olarak dışarıda bırakılmış **herkese açık ürün/blog kataloğu**. Kaynak Netvera tam SQL'i ve uygulama PHP'si önceki çalışmada özel ZIP olarak incelenmiş ancak bu repo/oturum üzerinden okunabilir kaynak dosya değildir. GitHub'daki katalogdan müşteri, sipariş, profil, admin kimliği veya gerçek ürün lisansı üretilemez.

## Kesin ayrım
- **Kaynak müşteriler**: Netvera `users` / ilişkili profil tablosu; hedef `users` (benzersiz e-posta). Eski ve yeni numeric ID değerleri körü körüne aynı kabul edilmez.
- **Kaynak admin**: Eski admin tablosu ve istenen belirtilen müşteri e-postası kaydı. Hedef `admins` ile `users` ayrı tablolar. Mevcut `super_admin` erişimi, geri dönüş kapısı oluşmadan değiştirilmez.
- **Satış geçmişi**: Eski siparişlerin ödemesi, satırları ve ürün ilişkisi Netvera'nın gerçek kayıtlarından gelir. Hedef `orders` ve `order_items` kendi özgün hesaplamalarına sahip. Ödeme imzaları veya canlı webhooklar asla yeniden üretilmez.
- **Satın alınmış yazılım lisansları**: Müşteri/ürün/order eşleşmesi, anahtar kimliği, alan adı, durum ve geçerlilik tarihi gibi haklar ancak Netvera kaynak şemasına göre aktarılabilir. Mevcut `LicenseService` sitenin kendi ürün lisansını yönetir, müşteri satın alım haklarını **yönetmez**.
- **Profil ayrıntıları / destek talepleri**: Kaynak tabloları bulunduktan sonra eşlemeye alınır. Kullanıcıya ait destek mesajları başka kullanıcıya bağlanmaz.

## Adım 1 — kaynak inceleme (yalnızca özel/salt-okunur)
`php scripts/audit-netvera-private-accounts.php` komutunu **PHP CLI ve pdo_mysql** ile, kaynak Netvera DB bilgilerini `NETVERA_SOURCE_DB_HOST`, `_NAME`, `_USER`, `_PASSWORD`, gerekirse `_PORT` ile birlikte hedef hesap için `NETVERA_AUDIT_EMAIL` ortam değişkeniyle **kişisel çalışma ortamında** çalıştır. E-posta adresini, şifreyi veya veritabanı erişimini GitHub koduna yazma. Kaynak hesaba yalnız SELECT ve information_schema görüntüleme izni ver. Script:
1. Tablo/kolon listesini ve olası müşteri/ödeme/lisans tablolarını raporlar.
2. Netvera admin kayıtlarının sadece ad/e-posta/rol/durum alanlarını ve belirtilen e-postanın sadece güvenli meta alanlarını gösterir.
3. Şifre, parola hash'i, lisans anahtarı, ödeme tokenı veya telefon gibi gizli alanları **okumaz ve raporlamaz**.
4. Kaynak ya da hedef veritabanına **hiçbir INSERT/UPDATE/DELETE yapmaz**.

Rapor ve SQL yedeği özel tutulmalı; GitHub deposuna, Actions loguna veya herkese açık bir sayfaya yüklenmemelidir.

## Adım 2 — aktarım uygulanmadan önce
- Her iki veritabanı ve medyanın ayrı yedeği alınır.
- Kaynak tablo şemaları/gerçek satın alma ilişkileri tespit edilir; hiçbir tablo/alan varsayımıyla ürün hakkı tanımlanmaz.
- Yalnız yetkili ortamda staging üzerinde `old_user_id → new_user_id`, `old_order_id → new_order_id`, `old_product_id → new_product_id`, `old_license_id → new_license_id` geçiş eşleme tabloları hazırlanır.
- E-posta çakışmalarında var olan YorumHizmeti hesabı, hash/rol/durum ve ilişki denetlenmeden ezilmez. Uyumlu parola hash'i güvenli aktarma, farklı algoritma için parola sıfırlama uygulanır. Parolalar loglanmaz.
- Eski satışın ödeme durumu aynen korunur, yeni bir `paid` kararı veya kredi tanımlanmaz. Eski ürün lisanslarının durumu kaynağında neyse öyle kalır; lisans anahtarları güvenli depolanır.
- Aktarım tekrar çalıştırılabilir (idempotent); kullanıcı, sipariş, lisans çift oluşturulmaz.
- Toplam kullanıcı, satın alma, iptal/iade, lisans sayısı ve iki kritik hesabın eşleşmesi için önce/sonra mutabakat testi yapılır.

## Adım 3 — admin hesabı ve kesim
Netvera admin kimliği güvenle doğrulanmadan **hedef `admins` şifresi/e-postası otomatik değiştirilmez**. Yönetici rolü ve ilgili hesabın kimliği kontrol edildikten sonra güvenli yönetici değişikliği ayrı onay ve kayıtla uygulanır; mevcut yönetici oturumları geçersiz kılınır, gerekirse şifre sıfırlama zorunlu tutulur. Hassas giriş bilgileri GitHub commitine konmaz.

**Kalan tek kaynak ihtiyacı:** netvera.tr'nin **özel SQL yedeği** (ve mümkünse lisans işlemlerini barındıran PHP kodu), ya da yetkili salt-okunur eski DB. Eski özel ZIP bu çalışmaya yeniden verilirse gerçek kaynak alanları üzerinden otomatik güvenli eşleme/importer tamamlanabilir.

## Uygulanabilir staging importer — 2026-10-09
SQL yedeği `alac6ayazilimtr_netvera.sql` eski sistemde **`admin_users`, `customers`, `orders`, `order_items`, `affiliate_accounts`, `affiliate_commissions` tablolarını içerir**. Kullanıcı hesapları ve satın alma hakları gerçek veriyle bu tablolardan eşleştirilmelidir.

`database/migrations/netvera-private-customer-v1.sql` eski satın alımları, hakları ve bayi komisyonlarını ayrı `nv_private_*` tablolarında tutar. Kaynak sistemden veri alacak komut:
`php scripts/import-netvera-private-customers.php`

### Kesin önkoşullar
1. Eski SQL yedeğini, internete açık olmayan **ayrı kaynak MySQL staging veritabanına** yükle. Kaynak bağlantı kullanıcısına yalnızca SELECT izni ver.
2. Yeni hedef de ayrı bir **`APP_ENV=staging`** test MySQL veritabanı ve geri döndürülebilir yedeğe sahip olmalı. Canlı `netvera.tr` veya `yorumhizmeti.tr` bağlantı bilgilerini kullanma.
3. Target `.env` MySQL parametreleri; özel ortam değişkenlerinde `NETVERA_SOURCE_DB_HOST`, `NETVERA_SOURCE_DB_NAME`, `NETVERA_SOURCE_DB_USER`, `NETVERA_SOURCE_DB_PASSWORD`, `NETVERA_ADMIN_EMAIL` ve güçlü `NETVERA_MIGRATION_SECRET` ayarlanmalı. Değerlerini GitHub'a, Actions loglarına veya ekran görüntülerine yazma.
4. Yukarıdaki komutu önce **`--apply` olmadan** çalıştır. Rapor kaynak tablo sayımlarını ve admin eşleşmesini doğrular, hedefe veri yazmaz.
5. Kaynak ile hedefte e-posta çakışmaları denetlendikten sonra yalnızca staging ortamında `NETVERA_PRIVATE_IMPORT_ALLOWED=1` ile `--apply` kullanılabilir. Gerçek hesap kimliği incelemesi yapıldıysa `NETVERA_MERGE_EXISTING_EMAILS=1` ayrıca etkinleştirilebilir.
6. Yeni native `users` satırları şifre sıfırlamadan, yalnız PHP'nin desteklediği mevcut hash ile aktarılır. E-posta çakışmasında mevcut hesap şifresi otomatik üzerine yazılmaz. Eski admin hesabı aynı e-posta ile oluşturulur/güncellenir ve parola değiştirmesi gerekir; *yedek admin silinmez*.
7. Eski sipariş/lisans ve bağlı bayilik/komisyon geçmişi **eski kimlikleriyle** ayrılmış tablolara aktarılır. Lisans anahtarları AES-256-GCM ile şifrelenir. Gerçek PayTR callback/webhook ve yeni sipariş tablolarına **dokunulmaz**.
8. Staging'de `/hesabim`, `/siparislerim`, `/admin/netvera-musteriler` kontrol edilir. Gerçek canlıya alma, kimlik ve ödeme hakları mutabakatı yapılmadan gerçekleşmez.

### Şimdiki sınır
Kaynak SQL kişisel Library arşivinde **metin olarak incelenebildi**, ancak bu çalışma ortamında ham SQL dosyasına erişim yetkisi bulunmadığından staging aktarımı *henüz çalıştırılmadı*. SQL dosyası mevcut konuşmaya yeniden eklendiğinde ham dosyanın kullanımı mümkün hale gelebilir. Yalnızca müşteri kullanıcılarını silip yeni hesap yaratmak eski lisans sahiplik bağlarını koparır; bu nedenle kitle silme yoktur. Eski bayi komisyonları görüntülenebilir hale gelecek şekilde tasarlandı, **yeni satış üzerinden canlı komisyon üretme ve ödeme mantığı henüz taşınmadı**.
