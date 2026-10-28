# NetVera cPanel ana dizin yayın paketi

**Hedef klasör:** cPanel Dosya Yöneticisi > `/home/<hesap>/netvera.tr`. Alt alan adları bu dizinin içinde (`blog.netvera.tr`, `lisans.netvera.tr` vb.) ayrı klasörlerdir ve ZIP bunları içermez.

## GitHub'dan indir

GitHub Actions > **NetVera cPanel Root Release ZIP** > son başarılı çalışma > **Artifacts: NetVera-cPanel-Root-Release**. GitHub size dışta bir artifact ZIP verir. **Önce bilgisayarınızda bu dış ZIP'i açın.** İçindeki `netvera-tr-cpanel-root-YYYYMMDD-HHMMSS.zip` dosyası cPanel'e yükleyeceğiniz gerçek arşivdir.

Alternatif: Localde `well-known` klasöründeyken:
```powershell
php bin/build-netvera-root-zip.php
```
Üst klasörde arşiv hazırlanır. İçindeki `app/`, `public/`, `resources/`, `.htaccess` doğrudan ana dizine açılır; ekstra `well-known/` klasörü oluşturmaz.

## Dosya koruması

- **Önce tüm mevcut `netvera.tr` ve kaynak MySQL veritabanı yedeklerini ayrı yerlerde alın.**
- Mevcut canlı `.env`, `public/uploads`, `storage` ve alt alan adı klasörlerine dokunmayın. Arşiv bunları bilerek içermez.
- Kök `.htaccess`, yalnız `netvera.tr` veya `www.netvera.tr` hostunu `public/` içine yönlendirir. Dosya erişimi doğrudan yapılan `app/`, `database/`, `resources/`, `storage/` istekleri yasaktır.
- Arşiv **müşteri veritabanını içermez**, otomatik import/kurulum yapmaz. Yerel 278 paket veya eski NetVera kullanıcıları cPanel'e yalnız dosya ZIP'i açınca taşınmaz.
- **`database/database.sql` güncelleme SQL'i değildir:** temiz kurulumda kullanılır ve `DROP TABLE` içerir. Mevcut NetVera canlı veritabanının üzerine import etmeyin.
- `.env` değerleri mevcut sunucu için doğru olmalı: `APP_URL=https://netvera.tr`, `APP_ENV=production`, `APP_DEBUG=false`, doğru MySQL sunucusu/kullanıcı/parola, gerçek ödeme/lisans ve SMTP ayarları. Parolaları GitHub'a koymayın.

## Yönetici ve eski NetVera süper admini

Yeni panelde `/admin/admin-kullanicilari` > **Hesabımı Düzenle** veya **Düzenle** ile e-posta/ad/şifre güncellenebilir. Bu işlem **mevcut admin şifresi** ile onaylanır. Yetkili süper admin diğer hesapların rollerini ayarlayabilir; son süper admin devre dışı bırakılamaz.

Eski NetVera hesabının **birebir orijinal parola hash'ini** taşımak için `bin/netvera-original-admin-transfer.php` hazırlanmıştır. Eski `admin_users` tablosuna erişim, hedef `admins` tablosuna erişim ve özel ortamda `NETVERA_ADMIN_EMAIL`, `NETVERA_ADMIN_LOGIN_PROOF`, `NETVERA_SOURCE_DB_HOST`, `NETVERA_SOURCE_DB_NAME`, `NETVERA_SOURCE_DB_USER`, `NETVERA_SOURCE_DB_PASSWORD` gerekli. Şifreler loglanmaz; önce **dry run**, sonra yalnız kimlik doğrulaması geçerse:

```bash
php bin/netvera-original-admin-transfer.php
php bin/netvera-original-admin-transfer.php --apply --ack=TRANSFER-VERIFIED-NETVERA-ADMIN
```

Bu işlem sadece doğrulanan **tek eski NetVera admin hesabını** `super_admin` yapar; hash değiştirmez. Yalnız tanınan varsayılan demo admin `admin@netvera.tr` başka hesapsa devre dışı bırakılır. Müşteri, sipariş, yorum, lisans ve diğer gerçek yönetici kayıtları değiştirilmez.

**Canlı yayın onayı için:** yeni ve eski DB müşterileri, lisansları, ödemeleri, 278 paket ve sağlayıcı eşlemeleri ayrı olarak mutabık olmalı. Dosyaların doğrudan açılması bu aktarımın gerçekleştiği anlamına gelmez.
