# Netvera.tr → Yeni Premium Tasarım / Korumalı Teknik Aktarım
**Güncelleme:** 8 Ekim 2026. **Dal:** `staging/netvera-source-integration`. **Canlı `main` değiştirilmedi.**

## Doğrulanmış taşınan herkese açık içerikler
- Eski Netvera SQL yedeğinden beyaz listeyle ayıklanmış **16 aktif hazır yazılım**.
- **3 aktif yazılım kategorisi**, **41 galeri görseli kaydı**.
- **5 orijinal blog yazısı** ve **9 blog kategorisi**.
- **2 mevcut onaylı ürün yorumu** (müşteri kullanıcı ID'leri, e-postaları veya isimleri aktarılmadı).
- Toplam **63 özgün kapak, galeri ve blog medya dosyası**, orijinal `/uploads/scripts/` / `/uploads/blog/` yollarında: GitHub Actions orijinal `https://netvera.tr` üzerinden indirip dosyaları staj dalına işledi.
- `database/netvera-public-catalog.json` public-only kaynak dosyası; `scripts/import-netvera-public.php` ile kontrollü DB kurulumu.
- `tests/netvera-link-parity.php` ile 16 orijinal `/hazir-scriptler/{slug}` + 5 `/blog/{slug}` ve canonical eşleştirme kapısı.
- Eski kök kısa adreslerden ilgili ürünlere 301 kuralları.

## Staging'e entegre edilen modüller
- Hazır yazılım listesi, fiyat aralığı, kategori, onaylı yıldız filtresi ve sıralama.
- Ayrı zengin yazılım editörü, kategori yönetimi, görsel galeri ve sürüm/destek/lisans alanları, SEO/OG verileri.
- Admin: **/admin/netvera-yazilimlar**, **/admin/netvera-kategoriler**, **/admin/netvera-gelen-kutusu**.
- Public ürün sayfasında teklif formu, site genelinde oturum bazlı iki yönlü sohbet ve admin yanıt akışı.
- Opsiyonel Telegram bildirimi: yalnız **ortam değişkenleri** NETVERA_TELEGRAM_BOT_TOKEN / NETVERA_TELEGRAM_CHAT_ID üzerinden; hiçbir gerçek token, demo şifresi veya ticari anahtar açık GitHub'a taşınmaz.
- CSRF, oturum sahipliği, oturum başı istek limiti, HTML güvenliği ve ayrılmış veritabanı tabloları.
- `database/migrations/netvera-inquiries-v1.sql`: yalnız yeni `nv_public_inquiries` ve `nv_public_inquiry_replies` tabloları.

## Staging/MySQL doğrulaması
1. GitHub Actions **Netvera Public Migration QA**: repo dosyasını iki defa ayrı MySQL 8 veritabanına import eder; 16/3/41/5/2 sayılarını ve orijinal indeksli rotaları doğrular.
2. Öncesi/sonrası mevcut `orders`, `order_items`, `payment_gateways` tablolarının kayıt sayıları kontrol edilir.
3. `tests/netvera-inquiry-e2e.php`: gerçek HTTP formu, CSRF negatif senaryosu, ayrı oturum erişim engeli, teklif kaydı ve admin erişim sınırı.
4. **PHP Lint** ve **Responsive & Browser QA** workflow'ları ayrıca çalışır.
5. Çalışmanın sonunda GitHub Actions sonuçları mutlaka incelenmeli; başarısız test varsa canlıya alınmamalı.

## ÖNEMLİ: Canlı ödeme ve kaynak PHP sınırı
- Orijinal Netvera kodunun **PayTRProvider.php**, **PaymentGateway.php**, ödeme callback'i, sipariş/webhook/lisans mantığı bu açık GitHub dalında **yoktur**. Eski kaynak PHP ZIP'i farklı bir sohbetten bu oturuma ham byte olarak okunamadı.
- Bu nedenle "orijinal PayTR tamamen taşındı" veya "yeni tema canlı ödemeye hazır" denemez. Yeni ürün detayındaki ödeme düğmesi staging'de mevcut Netvera satış sayfasına yönlendirilir; callback işlenmez.
- Kaynak PHP'yi birebir port etmek için yetkili geliştiricinin orijinal temizlenmiş PHP ZIP'ini kontrollü çalışma ortamına vermesi ve orijinal SHA256 ödeme dosyalarının karşılaştırılması gerekir. Orijinal `NETVERA` ödeme kodu yeniden yazılmamalı, mevcut PayTR merchant anahtarları değiştirilmemelidir.
- Mevcut YorumHizmeti ödeme yolları (`/payment/paytr/callback`, `/payment/iyzico/callback`) korunmuştur; eski Netvera `/payment/callback/paytr` bu staging dalında ayrı aktif callback olarak kurulmamıştır.
- Eski canlı müşteri, ödeme, teklif, sohbet ve kullanıcı verileri bilinçli olarak aktarılmadı; sipariş veri geçişi ayrıca, şifrelenmiş/erişim kontrollü kapalı süreç gerektirir.

## Güvenli kurulum ve son cutover
- Eski Netvera canlı dosyaları, DB ve indeksli asset yolları yedeklenmeden deploy yapılmaz.
- Ayrı PHP 8.3+ ve MySQL 8 staging kurulumu aç; `.env` için `APP_ENV=staging`, `NETVERA_IMPORT_ALLOWED=1` ve ayrı DB kullan.
- Staging projesinde `php scripts/import-netvera-public.php database/netvera-public-catalog.json` ile **dry-run** yap. Onaylı test DB'sinde `--apply` ile gerçekleştir. İşlem idempotenttir, var olan kayıtları ezmez.
- Ortamda Netvera görselleri yoksa sadece doğrulanmış public medya listesini, `NETVERA_MEDIA_MIRROR_ALLOWED=1 php scripts/mirror-netvera-public-images.php --apply` ile al. Bu işlemi `netvera.tr` kaynak URL'leri cevap verirken çalıştır.
- Yalnızca Netvera alanına nihai geçişte `APP_URL=https://netvera.tr` olmalıdır. Eski `/hazir-scriptler/*`, `/blog/*`, sitemap, canonical, robots ve OG/Schema karşılaştırması bitirilir; Google Search Console 404/5xx takip edilir.
- Gerçek PayTR testleri, satın alma, callback imzası, tekrar webhook, iade ve lisans akışı aynı sunucu staging alanında geçmeden canlı trafik yönlendirilmez.
- Rollback: eski kod/DB ve uploads yedeğini geri yükle; eski URL ve route sözleşmesi korunur. Bu aşamada main'e merge veya netvera.tr canlı deploy yapılmaz.

## Kesin durum ayrımı
- **Repo içeriği / görseller:** aktarıldı.
- **Staging SQL ve URL regresyonu:** GitHub CI testleri ile doğrulanır.
- **Canlı Netvera site/DB ve gerçek PayTR:** **değişmedi**.
