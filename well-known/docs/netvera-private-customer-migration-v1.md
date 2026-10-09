# Netvera → YorumHizmeti: Özel hesap, sipariş ve lisans geçişi
Durum: **beklemede — eski sistemin özel müşteri SQL'i / salt-okunur veritabanı bağlantısı gerekiyor**.

## Neden GitHub'dan henüz aktarılamaz?
Mevcut `database/netvera-public-catalog.json` müşteri kimliği ve satın alma verileri kasıtlı olarak dışarıda bırakılmış **herkese açık ürün/blog kataloğu**. Kaynak Netvera tam SQL'i ve uygulama PHP'si önceki çalışmada özel ZIP olarak incelenmiş ancak bu repo/oturum üzerinden okunabilir kaynak dosya değildir. GitHub'daki katalogdan müşteri, sipariş, profil, admin kimliği veya gerçek ürün lisansı üretilemez.

## Kesin ayrım
- **Kaynak müşteriler**: Netvera `users` / ilişkili profil tablosu; hedef `users` (benzersiz e-posta). Eski ve yeni numeric ID değerleri körü körüne aynı kabul edilmez.
- **Kaynak admin**: Eski admin tablosu ve istenen `fotoselloo7@gmail.com` kaydı. Hedef `admins` ile `users` ayrı tablolar. Mevcut `super_admin` erişimi, geri dönüş kapısı oluşmadan değiştirilmez.
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
