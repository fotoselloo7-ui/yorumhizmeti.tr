# NetVera — hizmet kataloğu, rakip fiyatları ve SEO/GEO/AIO hazırlık denetimi
Tarih: 27 Ekim 2026. Kapsam: hizmetler, sosyal medya paketleri, kategoriler ve bölgesel landing sayfaları. **Hariç:** ithal NetVera hazır yazılımları, eski yazılım slug/canonical'ları, lisans ve ödeme kayıtları.

## Doğrudan kontrol edilen perakende fiyat örnekleri (TRY)
| Hizmet | Kaynak | 100 | 250 | 500 | 1.000 | 2.500 | 5.000 |
| --- | --- | ---: | ---: | ---: | ---: | ---: |
| Instagram takipçi — standart | Takipevi | 149,90 | 249,90 | 449,90 | 849,90 | 1.249,90 | 1.899,90 |
| Instagram takipçi — Türk | TurkInstagram | — | 364,78 | 501,58 | 866,38 | — | 3.191,98 |
| TikTok takipçi — standart | SosyalTaktik | 44,90 | 109,00 | 219,00 | 399,00 | 989,00 | 1.899,00 |

Kaynaklar:
- https://www.takipevi.com.tr/instagram-takipci-satin-al
- https://www.turkinstagram.com/paketler/
- https://www.sosyaltaktik.com/tiktok-takipci
- Karşı örnek: https://socishop.com/tr/blog/takipci-fiyatlari (Instagram standard 1.000 için 62,99 TL listesi, kalite ve garanti koşulları farklı)
- Karşı örnek: https://takipella.com/blog/instagram-1000-takipci-kac-tl (global/Türk ayrımı, fiyatlar değişken)
- Karşı örnek: https://takipella.com/blog/tiktok-1000-takipci-kac-tl (global/Türk ayrımı)
Fiyat örnekleri aynı kalite veya gerçek teslimat garantisini kanıtlamaz; güncellenebilir. Kaynaksız “en ucuz” sözü, müşteri ve sonuç garantisi kullanılmaz.

**Fiyat kararı:** `SocialFollowerBenchmarkService` yalnız platform + paket tipi + tam miktar eşleşmesini önerir; rakibe kıyasla yaklaşık %2 daha düşük hedef *üst sınır* hesaplar. TRY para biriminde doğrulanmış aktif sağlayıcının birim maliyeti olmadıkça veya %30 brüt marj tabanı sağlanmadıkça satış fiyatı otomatik güncellenemez. Marj hesaplaması KDV, kur, ödeme kesintisi ve iade maliyeti öncesidir; onaylı nihai rakamı ayrıca değerlendirin. `php bin/netvera-follower-market-audit.php` güvenli, özel alanda denetim raporu üretir; veritabanını değiştirmez. Sosyal medya *üretim / içerik yönetimi* gibi proje bazlı hizmetler teklif üzerinden konumlanır.

## Ajans / SEO / tasarım hizmetleri fiyatlandırması
- https://www.nullmedya.com/fiyatlar/web-sitesi : kurumsal site 25.000 TL'den, aylık SEO 15.000 TL; e-ticaret proje bazlı.
- https://www.protan.com.tr/web-tasarim-fiyatlari/ : kurumsal web 35–220 bin TL arasında paket kapsamına bağlı.
- https://www.comsuite.com.tr/blog/kurumsal-web-sitesi-fiyatlari : kurumsal web için 15–45 bin TL ve üzeri farklı kapsamlar.
- https://www.protan.com.tr/web-tasarim-fiyat-raporu-2026/ : özel yazılım ve e-ticaret ayrı teslim segmentleri.

Bu fiyatlar NetVera teklifi değildir. SEO, Google Ads, web tasarım, içerik üretimi, özel yazılım, markalaşma ve benzeri profesyonel hizmetlerin kapsamı iş bazında değişir. Kurumsal hizmetlerde site üzerinde **“Teklif Al / Bizimle İletişime Geçin”** gösterilir; sabit `packages.price` ödeme toplamı veya `Offer.price` gibi yayımlanmaz. Eski sepetlerde fiyat güvenliği kontrolü yapılır. Sabit miktarlı, sağlayıcıya bağlı sosyal paketlerin satın alma ve fiyat akışı korunur.

## Canlı sunucuda veri tabanı uygulaması — güvenli son adımlar
- Kod güncellemeleri otomatik olarak `packages` fiyatlarını veya admin tarafından düzenlenmiş SEO alanlarını değiştirmez; bu, canlı siparişleri korumak içindir.
- Admin > Paketler > **Hizmet SEO / GEO / AIO Tamamla**: süper yöneticinin tek tıklamasıyla yalnızca boş/legacy marka taşıyan hizmet paketi ve kategori meta alanları doldurulur. Paket metadata değişikliklerinden önce özel yedek `storage/backups/` içine yazılır. Hazır scriptlerin isimleri, fiyatları, SEO URL'leri ve orijinal blog kayıtları korunur.
- `php bin/netvera-follower-market-audit.php`: rakip karşılaştırmasını, aktif TRY tedarikçi maliyetini ve en az %30 brüt marj koşulunu kontrol eden **salt okunur** rapor üretir.
- `php bin/netvera-follower-market-audit.php --apply-safe`: yalnızca tam hizmet tipi+miktar karşılaştırması ve aktif eşleştirilmiş TRY maliyeti doğrulanırsa, rakibin görünür fiyatından yaklaşık %2 düşük hedefe çekilebilen mevcut *takipçi* paketlerini günceller. Önce özel yedek alır; işlemi transaction ile yürütür. Maliyeti doğrulanmayan/uyumsuz başka hiçbir ürün fiyatı güncellenmez.
- GitHub üzerinde koda ekleme, cPanel'in kendi SQL verisini otomatik doldurmaz. Admin POST'u veya CLI uygulaması canlı ortama dağıtıldıktan sonra çalıştırılmalıdır.
- `sitemap.xml`, yeni grup URL'lerini ve eski bağlantıların kalıcı yönlendirmelerini canlı ortamda denetle. Search Console'a güncel sitemap'i sun. 

## SEO ve yapay zekâ arama görünürlüğü
- Google URL ilkeleri: https://developers.google.com/search/docs/crawling-indexing/url-structure
- İnsan için faydalı içerik: https://developers.google.com/search/docs/fundamentals/creating-helpful-content
- Yeniden yönlendirmeler: https://developers.google.com/search/docs/crawling-indexing/site-move-with-url-changes
- Yapılandırılmış veri kalite ilkeleri: https://developers.google.com/search/docs/appearance/structured-data/sd-policies
- Yalnız devlet/sağlık odaklı FAQ rich-result desteği: https://developers.google.com/search/docs/appearance/structured-data/faqpage

**Kanoniğe sahip tek URL**: Sosyal `/sosyal-medya-hizmetleri`, ajans `/ajans-yazilim-hizmetleri`, SEO/dijital `/seo-dijital-pazarlama-hizmetleri`. Eski `/kategoriler/grup/{anahtar}` kalıcı 301 ile yeni adrese gider. Bu yalnız grup dizinlerini kapsar; `/kategori/...`, `/paket/...`, `/blog/...`, `/hazir-scriptler/...` ve şehir landing slug'ları korunur.
- Kategori ve ürün detaylarında H1 tek, görünen özgün açıklama/SSS ve internal links; konu tekrarından kaçın.
- Gerçekte olmayan adres, müşteri istatistiği, Google partnerliği veya kanıtlanmamış %98 memnuniyet bilgisi yazma.
- Bir şehir landing sayfası o şehirde ofis bulunduğu imasını yapmasın; hizmet kapsamı açık olsun.
- FAQ kullanıcıya görünür olsun; zengin sonuçların Google'da gösterileceği iddia edilmesin.
- Yerel hizmet sayfasında alanlar boşsa konum/hizmet niyetine göre başlangıç rehberi üret; admin tarafından doldurulmuş özgün yazının önceliği korunur.
- İçerik, sayfa başlıklarını veya linkleri şehir isimleriyle kopyalayarak seri değiştirip “Google 1. sıra” garantisi verme.
