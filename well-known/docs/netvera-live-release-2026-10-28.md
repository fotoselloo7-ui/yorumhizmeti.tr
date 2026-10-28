# NetVera Teknoloji Yazılım — 28 Ekim 2026 canlıya çıkış kontrol listesi

## Veritabanı: İKİ farklı SQL senaryosu

- **Temiz boş kurulum** için repodaki tek SQL: `well-known/database/database.sql`. Bu SQL **DROP TABLE** içerir; çalışan NetVera veritabanına import edilmesi **yasaktır**.
- **Güncel yerel katalog** için: `localhost:8006` tarafından kullanılan MySQL veritabanını phpMyAdmin > Dışa Aktar > SQL ile **şimdi** dışa aktarın. 278 yeni alternatif bu yerel veritabanına sonradan eklenip aktif edildi; GitHub'daki başlangıç SQL'sinde bu 278 kayıt **bulunmaz**.
- **Mevcut NetVera müşteri ve lisans kayıtları** için: eski canlı veritabanını phpMyAdmin'den tamamen yedekleyin. İki ayrı veritabanını **silip üzerine yazmadan** birleştirecek özel taşıma gerekiyorsa, boş kurulum SQL'si bunun yerine geçmez.

## Sunucu dosyaları

1. Mevcut `netvera.tr` dosyalarını ve MySQL veritabanını ayrı ayrı, geri yüklenebilir şekilde yedekleyin.
2. İlk yüklemeyi ayrı bir alan adı/alt alan adı ve **ayrı test veritabanına** yapın.
3. PHP web kökünü `well-known/public` olarak ayarlayın; `app`, `storage`, `database`, `resources` ve `.env` HTTP üzerinden indirilebilir OLMAMALI.
4. Üretim `.env`: `APP_URL=https://netvera.tr`, `APP_ENV=production`, `APP_DEBUG=false`, gerçek cPanel `DB_*` bilgileri, üretim SMTP, ödeme ve lisans anahtarları. Gizli değerleri GitHub'a yüklemeyin.
5. Gerçek görselleri koruyun: `public/uploads` klasörü ve sunucuda sonradan oluşan dosyalar eski yedekten yeni ortama taşınmalıdır. `storage/backups`, yerel raporlar, `.sync-conflict*` ve `public/error_log` dağıtım ZIP'ine eklenmemelidir.
6. Composer bağımlılıklarını barındırma ortamına uygun kurun; `vendor/` için `composer install --no-dev --optimize-autoloader`.
7. Eski hizmet marka metinleri mevcut veritabanında kalmışsa, tam SQL yedeği aldıktan sonra aşağıdaki özel ve idempotent işlemi kullanın (blog/slug/müşteri hesapları değişmez):

   ```bash
   php bin/netvera-brand-finalize.php
   php bin/netvera-brand-finalize.php --apply --ack=NETVERA-BRAND-REVIEWED
   ```

8. SMM hizmetlerinin sağlayıcı eşlemelerini ve TRY kâr marjlarını, sipariş e-postalarını, lisans akışını, destek ve referans modüllerini doğrulayın. Hazırlanan yeni alternatifler, sağlayıcı onayı yoksa teklif/usulünde kalır.

## Yayından önce başarısız testler

28 Ekim 2026, son temel tarayıcı QA `40650777815`: dört kritik başarısız kontrol
- Hazır yazılım kategori düzenleyicisinin açılması
- Satın alma/checkout testinde zaman aşımı
- Ürün detayında güncellenmiş demo yönetim adresinin görünmemesi
- Mobil alt kategori çubuğunda sol marka vurgusunun kaybolması

PHP Lint başarılı olması, bu dört sorun düzelmeden tüm sistemin üretime hazır olduğu anlamına gelmez. Özellikle PayTR/iyzico callback, sipariş ve lisans üzerinde gerçek veya güvenli sandbox testi gerekir.

## Bilinçli korunan eski teknik adlar

GitHub depo klasör adı, lisans ürün kodu ve eski marka metinlerini *algılamak için* yazılmış uyumluluk regex'leri değiştirilmez; bunlar ziyaretçiye gösterilen marka adı değildir. Mevcut yönetici e-posta hesapları da kendiliğinden değiştirilmez. Yeni temiz SQL'de kurumsal ad **NetVera Teknoloji Yazılım**, site alan adı **netvera.tr** olarak ayarlanmıştır.
