# Netvera staging içeriği nasıl güvenle yüklenir

Bu işlem `main` dalında veya Netvera'nın canlı sunucusunda çalıştırılmamalıdır.

1. `staging/netvera-source-integration` dalını ayrı bir staging test klasörüne al.
2. Test için **ayrı** bir MySQL veritabanı kullan. Canlı MySQL'i veya canlı cPanel veritabanını bağlama.
3. Çalışma ortamının `.env` dosyasında `APP_ENV=staging` olsun. Test ortamına özel `NETVERA_IMPORT_ALLOWED=1` ayarla.
4. Sohbette verilen **`netvera_public_migration_payload.zip`** dosyasını proje web kökünün **dışına** koy. Ham canlı SQL'i veya `.env` dosyasını GitHub'a yükleme.
5. **Önce dry-run**: `php scripts/import-netvera-public.php /guvenli/yol/netvera_public_migration_payload.zip`
6. Çıkan sayıları kontrol et: 16 yazılım, 41 galeri görseli, 5 blog, 9 blog kategorisi, 3 yazılım kategorisi, 2 onaylı anonim yorum. Dry-run sırasında hiçbir şey yazılmamalıdır.
7. **Yalnızca staging**: `NETVERA_IMPORT_ALLOWED=1 php scripts/import-netvera-public.php /guvenli/yol/netvera_public_migration_payload.zip --apply`.
8. `/hazir-scriptler`, `/hazir-scriptler/haber-sitesi-scripti`, `/blog/yapay-zeka-haber-yazilimi-otomatik-haber-sitesi` rotalarını ve alt galeri görsellerini kontrol et.
9. Mevcut hizmet paketleri, menü, `/payment/paytr/callback` ve diğer ödeme/üyelik modülleri yazma dışı olmalıdır.

**Koruma:** Aktarım kodu `APP_ENV=production` durumunda uygulamayı durdurur; ayrıca `--apply` olmadan veri yazmaz. Kategori/blog çakışmalarında mevcut native blog yazısı güncellenmez.

**Eksik kalan ve ayrı geliştirilmesi gerekenler:** Netvera'nın zengin admin editörü (demo hesapları özel erişimle), canlı destek/Telegram, satış teklifi ve sipariş iş mantığı, blog SEO ekstra alanlarının sayfa başlığında tam yansıtılması, verified-purchase yorum gönderimi, Netvera PayTR ile üretim uyumluluk testleri. Bu alanlar şu anda **taşınmış kabul edilmiyor**.

**PayTR güvenlik:** Canlı merchant key/salt, callback imzası, ödeme webhook'u, lisans ve sipariş yazma mantığı değiştirilmedi. Final cutover öncesinde safetey-payments testleri ve canlı sistemle uyumluluk kontrolü zorunlu.
