# YorumPanel Pro

**yorumhizmeti.tr** için geliştirilen, API kullanmadan çalışan, manuel sipariş yönetimli, SEO güçlü, modern paket satış yazılımı.

## Gereksinimler

- PHP 8.1+
- MySQL 5.7+
- Laragon (önerilen)

## Kurulum

### 1. Veritabanı Oluştur

```sql
CREATE DATABASE yorumpanel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2. Veritabanı Kurulumu

```bash
# Tabloları kur
mysql -u root yorumpanel < database/install.sql

# Seed verileri yükle
mysql -u root yorumpanel < database/seeds.sql
```

### 3. Ortam Değişkenleri

`.env.example` dosyasını `.env` olarak kopyalayın ve düzenleyin:

```bash
cp .env.example .env
```

### 4. Autoload

```bash
composer dump-autoload
```

### 5. Sunucuyu Başlat

```bash
php -S 127.0.0.1:8006 -t public
```

Site adresi: [http://127.0.0.1:8006](http://127.0.0.1:8006)

## Admin Panel

- URL: `/admin/giris`
- Varsayılan giriş: `admin@yorumhizmeti.tr` / `admin123`

## Proje Yapısı

```
├── app/
│   ├── Controllers/          # Frontend & Admin controller'lar
│   │   └── Admin/            # Admin panel controller'lar
│   ├── Core/                 # MVC çekirdek sınıfları
│   └── Services/             # İş mantığı servisleri
├── database/
│   ├── install.sql           # Tablo şemaları (24 tablo)
│   └── seeds.sql             # Başlangıç verileri
├── public/
│   ├── index.php             # Giriş noktası
│   └── assets/               # CSS, JS dosyaları
├── resources/
│   └── views/                # PHP view şablonları
│       ├── frontend/         # Ön yüz view'ları
│       ├── admin/            # Admin panel view'ları
│       └── layouts/          # Layout şablonları
├── storage/
│   ├── uploads/              # Dosya yüklemeleri
│   ├── logs/                 # Hata logları
│   └── cache/                # Önbellek
├── .env                      # Ortam değişkenleri
├── .htaccess                 # Apache yönlendirme
└── composer.json             # PSR-4 autoload
```

## Özellikler

- **Paket Satışı**: Kategori bazlı dijital hizmet paketleri
- **Modüler Ödeme**: PayTR, iyzico, Havale/EFT (admin panelden açılıp kapatılabilir)
- **Sipariş Yönetimi**: Durum takibi, aktivite logları, e-posta bildirimleri
- **Müşteri Paneli**: Profil, sipariş geçmişi, destek talepleri
- **Admin Paneli**: Dashboard, sipariş/paket/kategori/blog CRUD, ayarlar
- **SEO**: 14 noktalı SEO puanlama, Schema.org, sitemap.xml, robots.txt
- **Güvenlik**: CSRF koruması, input validasyonu, XSS koruması
- **Responsive**: Mobil uyumlu modern tasarım
- **CSV İçe Aktar**: Toplu paket yükleme
- **Destek Sistemi**: Mesajlaşma tabanlı destek talepleri

## Lisans

Bu proje özel kullanım için geliştirilmiştir.

## cPanel / Paylaşımlı Hosting Kurulumu

Eğer projeyi cPanel veya paylaşımlı bir sunucuda yayınlayacaksanız aşağıdaki adımları izleyin:

1. **Dosyaları Yükleme**: Proje dosyalarını public_html içerisine atın. Güvenlik için `public` dışındaki dosyaları (app, database, resources vb.) public_html'in bir üst dizinine taşıyıp `public/index.php` dosyasındaki path'leri güncelleyebilirsiniz (Önerilen). Ya da direkt public_html içerisine yükleyecekseniz `public` klasörünün root olması için `.htaccess` yapılandırmasını kullanın.
2. **.htaccess Düzenlemesi (Root için)**: Eğer siteyi `public` klasörü olmadan doğrudan ana domainden çalıştırmak isterseniz root dizinde şu `.htaccess` dosyasını oluşturun:
   ```apache
   <IfModule mod_rewrite.c>
       RewriteEngine On
       RewriteRule ^(.*)$ public/$1 [L]
   </IfModule>
   ```
3. **Veritabanı**: cPanel MySQL Veritabanları menüsünden yeni bir DB ve kullanıcı oluşturun. `database/install.sql` ve `database/seeds.sql` dosyalarını phpMyAdmin üzerinden içe aktarın.
4. **.env Ayarları**: Oluşturduğunuz veritabanı bilgilerini `.env` dosyasına girin. `APP_URL` ayarını kendi domaininiz ile değiştirin.
5. **Dosya ve Klasör İzinleri**: Aşağıdaki izinler bölümüne göre chmod izinlerini ayarlayın.

## Dosya ve Klasör İzinleri (cPanel & Linux)

cPanel paylaşımlı hosting veya Linux sunucularda güvenli ve hatasız bir çalışma için izinler şu şekilde yapılandırılmalıdır:

### 1. Klasör ve Dosya İzinleri
- **Tüm Klasörler (Genel)**: `755` (drwxr-xr-x)
- **Tüm Dosyalar (Genel)**: `644` (-rw-r--r--)
- **Yazma Yapılacak Klasörler (Dosya Yüklemeleri ve Loglar)**: `755`
  * Eğer hosting sunucusunun php/web sunucusu yetki grubu ile dosya sahibi grubu farklıysa ve yükleme hatası (permission denied) alırsanız bu klasörleri geçici olarak `775` yapın.
  * **UYARI**: Sunucu güvenliği için hiçbir dosyayı veya klasörü kalıcı olarak `777` yapmayın!

### 2. İzin Verilmesi Gereken Klasörler Listesi
Aşağıdaki klasörler yazılabilir olmalıdır (chmod 755):
- `public/uploads`
- `public/uploads/blog`
- `public/uploads/pages`
- `public/uploads/packages`
- `public/uploads/categories`
- `public/uploads/settings`
- `public/uploads/editor`

### 3. Görseller Görünmüyorsa Kontrol Edilecekler
Eğer yüklediğiniz logolar veya makale içi resimler kırık/boş görünüyorsa:
1. **APP_URL Yapılandırması**: `.env` dosyasındaki `APP_URL` değerinin sitenizin tam URL'si (ör: `https://yorumhizmeti.tr`) olduğundan ve sonunda `/` karakteri olmadığından emin olun.
2. **Double Path Kontrolü**: Görsel yollarının veritabanında `/uploads/blog/...` veya benzeri formatta kaydedildiğini ve şablonlarda `/uploads/` + `/uploads/blog/...` şeklinde çakışmadığını (`upload_url` helper'ı kullanıldığını) teyit edin.
3. **Htaccess Kontrolü**: Root dizindeki veya `public/` dizindeki `.htaccess` dosyalarının görsel formatlarını (`.jpg`, `.png`, `.webp`, vb.) engellemediğinden emin olun.
4. **Resim Dosyası Kontrolü**: Dosyanın `public/uploads/` altındaki ilgili klasörde fiziksel olarak var olduğunu ve izinlerinin `644` olduğunu kontrol edin.

### 4. Güvenlik Notları
- `.env`, `app`, `database`, `resources`, `storage` ve `vendor` gibi kritik klasörler doğrudan public URL üzerinden erişilebilir olmamalıdır.
- Sadece `public` klasörünün içeriği (veya root üzerindeki `.htaccess` yönlendirmesiyle yönlendirilen istekler) dış dünyaya açık olmalıdır.

## Lisans Client-Kit Entegrasyonu

Yazılım, farklı domainlere kurulum yapıldığında merkezi lisans doğrulama sistemiyle çalışır. Varsayılan olarak lisans kontrolü **kapalıdır** (`LICENSE_ENABLED=false`).

### 1. Lisans Kontrolünü Açma
`.env` dosyasına aşağıdaki satırı ekleyin veya değerini `true` olarak değiştirin:
```env
LICENSE_ENABLED=true
```

### 2. Lisans Sunucu URL Yapılandırması
Lisans doğrulama sunucusunun adresini `.env` dosyasına girin:
```env
LICENSE_SERVER_URL=https://lisans.example.com
```
Alternatif olarak **Admin > Lisans Yönetimi** sayfasından da girebilirsiniz.

### 3. Lisans Anahtarı Girme
`.env` dosyasında veya Admin > Lisans Yönetimi sayfasında lisans anahtarınızı girin:
```env
LICENSE_KEY=XXXX-XXXX-XXXX-XXXX
```

### 4. Local Geliştirme Ortamında Bypass
Local geliştirme ortamında lisans kontrolünün siteyi kilitlememesi için:
```env
APP_ENV=local
LICENSE_LOCAL_BYPASS=true
```
Bu iki ayar birlikte aktifse lisans kontrolü hiçbir sayfayı engellemez. `APP_ENV=production` yapıldığında bypass devre dışı kalır.

### 5. cPanel Canlı Kurulumda Lisans Aktivasyonu
1. `.env` dosyasında `LICENSE_ENABLED=true`, `LICENSE_SERVER_URL` ve `LICENSE_KEY` ayarlarını yapın.
2. `APP_ENV=production` olarak ayarlayın.
3. Admin panelde **Lisans Yönetimi** sayfasına gidin.
4. "Lisansı Aktifleştir" butonuna basın.
5. Başarılı aktivasyon sonrası lisans durumu "Aktif" olarak görünür.

### 6. Grace Period (Bağışlama Süresi)
- Lisans sunucusuna ulaşılamazsa ve son başarılı lisans cache'i mevcutsa, sistem `LICENSE_GRACE_DAYS` (varsayılan: 3 gün) boyunca çalışmaya devam eder.
- Grace süresi dolduğunda frontend sayfalarında lisans uyarı ekranı gösterilir.
- Admin panel asla tamamen kilitlenmez; lisans yönetimi sayfasına her durumda erişilebilir.

### 7. Admin > Lisans Yönetimi Ekranı
- **URL:** `/admin/lisans`
- Lisans durumu, ürün kodu, domain, install ID, son/sonraki kontrol tarihleri görüntülenir.
- Lisans anahtarı ve sunucu URL'si bu sayfadan düzenlenebilir.
- "Şimdi Kontrol Et" ile anlık lisans doğrulama yapılabilir.
- "Devre Dışı Bırak" ile lisans temizlenebilir.

### 8. .env Lisans Değişkenleri
| Değişken | Varsayılan | Açıklama |
|---|---|---|
| `LICENSE_ENABLED` | `false` | Lisans kontrolünü açar/kapar |
| `LICENSE_SERVER_URL` | (boş) | Lisans sunucu adresi |
| `LICENSE_PRODUCT_CODE` | `yorum_panel_pro` | Ürün kodu |
| `LICENSE_KEY` | (boş) | Lisans anahtarı |
| `LICENSE_INSTALL_ID` | (otomatik) | Kurulum kimliği |
| `LICENSE_CHECK_INTERVAL_HOURS` | `24` | Kontrol aralığı (saat) |
| `LICENSE_GRACE_DAYS` | `3` | Sunucu ulaşılamaz ise bağışlama süresi (gün) |
| `LICENSE_LOCAL_BYPASS` | `true` | Local ortamda bypass |
