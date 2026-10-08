# Hazır Yazılımlar & Scriptler — Kategori ve vitrin planı (V31)

**Amaç:** YorumHizmeti ana menüsündeki Ajans & Yazılım ailesine, tek ürün/fiyat sistemi kullanan yeni bir `Hazır Yazılımlar & Scriptler` kategorisi eklemek. İçerik, YorumHizmeti'nin tek seviyeli alt kategori mimarisine uyar. Yeni paket veya referans uydurulmaz.

## Mimari kararlar

- Ana kategori: **Hazır Yazılımlar & Scriptler** — `/kategori/hazir-yazilim-scriptleri`.
- Sektör ve kullanıcı amacı odaklı 23 alt kategori, slug/id çakışmasını önleyen idempotent admin kurulumu ile eklenir.
- Ana menü otomatik olarak gerçek, aktif admin kategorilerini okur.
- Ürünler, mevcut **Paketler** modülünden girilir. Görsel, açıklama, SEO, fiyat, indirim, sipariş ve ödeme akışları korunur.
- Ayrı vitrin: **Admin → Hazır Yazılım Vitrini**. Sadece yazılım ailesine bağlı ürünler seçilir; kendi sırasını saklar, normal `is_featured` seçeneğini değiştirmez.
- Referanslar: **Admin → Referanslarımız**. Gerçek proje adı, kategori, açıklama, kapak görseli, URL, aktif/pasif ve sıra; boşken ana sayfada hiçbir hayali referans görünmez.
- Mevcut kurulumda **Admin → Hazır Yazılım Vitrini → Kategorileri Kur** bir kez çalıştırılır. Bu işlem fiyatları, paketleri ve zaten mevcut kategori kayıtlarını güncellemez.
- Ana sayfanın iki bölümü, admin verisi bulununcaya kadar gizlidir. Veriler eklendiğinde otomatik görünür.

## Araştırmaya dayalı alt kategori yapısı

| Alan | Alt kategori örnekleri | İçerik açısı |
|---|---|---|
| İçerik ve medya | Haber Sitesi Yazılımı, Blog ve İçerik CMS, Forum ve Topluluk | Yayınlama, editör, kategoriler, medya |
| Şirket vitrini | Kurumsal Web Sitesi, İnşaat ve Hafriyat, Temizlik, Nakliye ve Lojistik | Teklif, hizmet, proje, bölgesel SEO |
| E-ticaret | E-Ticaret, Dijital Ürün Satış, Pazaryeri ve İlan | Ürün, ödeme, panel, sipariş |
| İlan ve portföy | Emlak, Oto Galeri, İş İlanı ve Kariyer | Listeleme, filtreleme, yönetim |
| Rezervasyon ve hizmet | Otel ve Rezervasyon, Randevu, Restoran QR Menü, Güzellik Salonu, Tur ve Seyahat | Takvim, işletme hizmetleri, rezervasyon |
| Yönetim sistemleri | CRM ve Müşteri Yönetimi, Teknik Servis | Takip, raporlama, talep |
| Niş platformlar | SMM Panel, SMS Onay, Eğitim ve Kurs | Platform ve sektör özelinde operasyon |

Kodda tek tek 23 alt kategori, kalıcı slug, meta başlığı, açıklaması, icon ve sort_order tanımlıdır. Liste **kapsamlı bir editoryal taksonomi önerisidir**, otomatik satış ürünü kataloğu veya doğrulanmış arama hacmi listesi değildir.

## Piyasa referansları ve kapsam notu

CodeCanyon'daki PHP Script kategorileri; alışveriş sepeti/e-ticaret, rezervasyon ve randevu, otel rezervasyonu, ilan portalı gibi ayrı ürün sınıfları kullanır:
- https://codecanyon.net/category/php-scripts?term=ecommerce
- https://codecanyon.net/category/php-scripts?term=appointment+booking+system
- https://codecanyon.net/category/php-scripts?term=hotel+booking+system
- https://codecanyon.net/category/php-scripts?term=classified

Bu sayfalar **ürün türlerini** doğrulamak içindir; buradan fiyat, satış sayısı veya SEO arama hacmi tahmin edilmez. Kategori isimleri Türkçe arama niyetine göre editoryal olarak özelleştirilmiştir. Gerçek Türkiye arama hacmi/rekabet puanı için ayrıca Search Console, Semrush veya Google Keyword Planner verisi gerekir.

## Yayın akışı

1. GitHub Desktop'tan main dalını güncelle ve localhost'ta uygulamayı aç.
2. **Admin → Hazır Yazılım Vitrini** ekranında **23 Kategoriyi Kur** butonuna tıkla (aynı işlem tekrarlanabilir).
3. **Paketler → Paket Ekle** ile ürünleri uygun alt kategoriye kaydet; gerçek kapak görselini, fiyatı, detaylarını ve SEO alanlarını doldur.
4. Vitrin modülünde öne çıkacak ürünleri işaretle, sıralama numaralarını gir ve kaydet.
5. **Admin → Referanslarımız** ekranından gerçek müşteri projelerini ekle; kapak görsellerini bilgisayardan yükle.
6. Ana sayfada yazılım kartlarını ve referansları kontrol et.

## Güvenlik ve içerik ilkeleri

- Yönetim işlemleri admin oturumu + CSRF korumasıyla yapılır.
- Referans dış bağlantıları sadece `http`/`https` kabul eder; yeni sekme güvenli `noopener noreferrer` ile açılır.
- Görseller Upload sınıfının MIME/tür ve dosya boyutu kontrolünden geçer.
- Sıralama yetkisi admin içindir; pasif ürün/kategori vitrinde görünmez.
- Yeni kategori kurulumu yalnızca eksik slug kayıtlarını ekler, mevcut müşterinin verilerini silmez.
