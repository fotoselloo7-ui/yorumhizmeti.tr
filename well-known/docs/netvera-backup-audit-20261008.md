# Netvera.tr → YorumHizmeti premium arayüzü — Kaynak envanteri (2026-10-08)

**Çalışma dalı:** `staging/netvera-source-integration`. Bu dal canlı siteyi ve `main` üretim akışını değiştirmez.

### Gönderilen canlı yedek üzerinde yapılan yerel okuma

- Kaynak ZIP: 545 öğe, PHP uygulaması ve yüklenmiş medya. Dosyalar sohbetin **özel çalışma alanında**; ham ZIP repo içerisine eklenmeyecek.
- SQL: 60 tablo. Başlıca: `script_products`, `script_categories`, `script_product_images`, `script_product_versions`, `blog_posts`, `blog_categories`, `content_reviews`, `live_chat_messages`, `support_tickets`, `customer_offers`, `page_seo`, `payment_settings`, `orders`, `payment_webhook_logs`.
- Görünen içerik adayları: 16 aktif hazır yazılım, 3 aktif mevcut yazılım kategorisi, 41 aktif galeri görseli, 5 yayımlanmış blog yazısı, 9 blog kategorisi; değerlendirme kayıtları ayrı tabloda.
- Netvera adres yapısı: `/hazir-scriptler`, `/hazir-scriptler/{slug}`, `/hazir-scriptler/{mainSlug}/{subSlug}`, `/blog/{slug}`. Yeni temadaki `/hazir-yazilimlar` bu eski rotaların yerini almamalıdır.
- Ürün zengin alanları: demo link ve hesapları, ürün galerisi, sürüm ve güncellemeler, kurulum/lisans/destek süresi, teknik özellikler, SSS, SEO başlık/açıklama, odak/ikincil kelimeler, fiyat/indirim, satış ilişkileri.
- Blog zengin alanları: orijinal slug, HTML, kategori, tarih, canonical, robots, OG, yazar, SEO odak/yardımcı kelimeleri.

### KESİNLİKLE KORUNACAK KIRMIZI ALANLAR

1. **Netvera gerçek PayTR**: `app/services/payment/PayTRProvider.php`, `PaymentGateway.php` ve `/payment/callback/paytr` iş mantığı **değiştirilmeyecek**; merchant ID/key/salt, callback, imza kontrolü, idempotency ve ödeme kayıtları **aktarılmadan önce birebir test edilecek**.
2. **YorumHizmeti ödeme**: `/payment/paytr/callback` dahil mevcut callback rotaları da korunacak; aynı URL'de iki ayrı ödeme sağlayıcısı çalıştırılmayacak.
3. Netvera'nın canlı kaynakları, SQL ve yedeği bu çalışmada **okunur**, canlı sitede yazma işlemi yapılmaz.
4. Ham SQL, kullanıcılar, sipariş geçmişi, IP kayıtları, destek mesajları, demo şifreleri, Telegram tokenları veya payment keys açık GitHub'a **YÜKLENMEZ**.
5. `/blog/{slug}` mevcut YorumHizmeti blogunu ezmeden **slug temelinde ekleme ve çakışma raporu** ile aktarılacak. Blog detay şablonu premium olacak, kanonikler korunacak.
6. `/hazir-scriptler/{slug}` yazılım ürünü için **hizmet paketlerinden ayrı** şablon kullanacak; sahte kullanıcı, sahte yorum ve sahte puan eklenmeyecek.

### Entegrasyon sırası ve kabul kriteri
- **A. Ayrılmış staging:** öncesinde veritabanı yedeği, anonimleştirilmiş test içeriği, gizli bilgi taraması ve kaynak-kod bağımlılık haritası.
- **B. İçerik + URL uyumluluğu:** 16 aktif ürün, 5 blog yazısı, 41 galeri görseli, kategori ve SEO alanlarını idempotent aktarım; gerçek görsel yolları, canonical ve OG korunur.
- **C. Yazılım detayı:** galeri, teknik modüller, sürüm, lisans, demo, yorum görünümü, üyelik/sipariş bağlantıları. Yorum yayınlama **müşteri kimliği eşleştirmesi ve moderasyon** tamamlanmadan aktif yapılmaz.
- **D. Admin:** Netvera'da var olan zengin ürün/blog editörleri, galeri, SEO/GEO, sürüm, indirim ve teklif yönetimi; yeni tema ile uyum.
- **E. Destek ve satış:** canlı chat, Telegram, WhatsApp, müşteri teklifleri ve destek talepleri; rol/CSRF/origin kontrolü ve kötüye kullanım testleri.
- **F. PayTR sertifikasyonu:** callback hash, durum geçişleri, tekrar webhook, iade/hata, ödeme dönüşleri. **Gerçek PayTR çağrısı yapılmayacak**; test fixture/sandbox kullanılacak.
- **G. Canlı geçiş:** eski→yeni URL matrisi, sayfa başlığı, meta, canonical, JSON-LD, görseller ve Search Console 404 raporu karşılaştırmaları; onaydan sonra ayrı deploy.

### Mevcut net durum
Bu commit bir envanter ve **staging başlangıcıdır**. Canlı PayTR/Telegram/kullanıcı verisi taşınmadı; tam üretim entegrasyonu tamamlanmış değildir.
