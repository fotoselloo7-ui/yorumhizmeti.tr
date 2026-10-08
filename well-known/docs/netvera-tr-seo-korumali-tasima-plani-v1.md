# Netvera.tr → Yeni Yazılım Mağazası: SEO Korumalı Geçiş Planı

**Durum (8 Ekim 2026):** Araştırma ve taşıma tasarımı. Netvera.tr canlı dosyaları, veritabanı, kullanıcılar ve cPanel yedeği **henüz edinilmedi; canlı siteye hiçbir değişiklik yapılmadı**.

## Neden doğrudan tema değiştirmiyoruz?
- Netvera.tr organik trafik, indekslenmiş ürün/blog URL'leri, lisans, ürün açıklamaları, destek ve ödeme süreçleri taşıyor.
- YorumHizmeti.tr'nin hazır yazılım mağazası yeni bir vitrindir; mevcut Netvera veritabanı şemasıyla aynı olduğu varsayılmamalıdır.
- Bir tema değişikliği sırasında URL, canonical, 301, şema verisi, linkler veya müşteri verisi kaybolmamalı.

## Gözlemlenen URL sözleşmesi (canlı, kamuya açık sayfalar)
- Ana sayfa: `/`
- Hazır script listesi: `/hazir-scriptler`
- Ürün detay: `/hazir-scriptler/{eski-slug}` **AYNEN KORU**
- Örnek: `/hazir-scriptler/haber-sitesi-scripti`
- Blog: `/blog/{eski-slug}` **AYNEN KORU**
- Örnek: `/blog/yapay-zeka-haber-yazilimi-otomatik-haber-sitesi`

Diğer gerçek URL'ler sitemap.xml + Search Console dışa aktarımı + mevcut routes tablosu üzerinden bulunmalıdır. Bu liste tam URL envanteri değildir.

## Yapılacak taşıma (canlıya dokunmadan)
1. **Güvenli yedek**: cPanel'den dosyalar + SQL + yüklenen ürün kapakları + medya; `.env` ve ödeme/SMTP/lisans anahtarlarını ayrı tut.
2. **Ayrı özel ortam**: sadece erişim kontrollü özel GitHub repo/branch veya staging sunucusu. Ham DB, gerçek müşteri kayıtları, kişisel bilgiler veya secret'ları public GitHub'a ASLA yükleme.
3. **URL envanteri**: Search Console en yüksek trafik alan URL'leri, sitemap URL'leri, mevcut rotalar ve canonical/redirect listeleri JSON/CSV olarak çıkar.
4. **Veri eşleme**: Netvera kategorileri/alt kategorileri, script ürünleri, görsel yolu, slug, fiyat/indirim, demo linki, lisans ve güncelleme süresi; hem içerik hem JSON-LD alanlarını koru.
5. **Route uyumluluğu**: yeni şablonda `/hazir-scriptler`, `/hazir-scriptler/{slug}`, `/blog/{slug}` eski anlamını korur. Zorunlu değişen URI'ler yalnız birebir 301 ve güncellenmiş internal link/canonical ile yönlendirilir. `/hazir-yazilimlar` YorumHizmeti projesine aittir, Netvera canlıda mecburi yeni adres değildir.
6. **Modüller**: mobil ve masaüstü navigasyon, canlı destek sohbeti, WhatsApp, teklif formları, ödeme, sepet, müşteri hesapları, lisans, SEO/meta/OG/schema, ürün demo bağlantıları, blog, referanslar, Search Console.
7. **Staging test**: eski→yeni URL ve status karşılaştırması, head/meta/schema diff, ürün sayısı ve fiyat tutarlılığı, responsive kırılmalar, destek/ödeme/lisans uçtan uca testleri.
8. **Cutover**: tam DB ve dosya yedeği, geçici içerik dondurma, kısa bakım penceresi, DNS yerine dosya/deploy değişimi, geri dönüş (rollback) planı, 7–14 gün 404/500/indeks kontrolü.

## İhtiyaç duyulan gerçek dosyalar
- Netvera.tr'nin kod zip'i (kişisel sırlar ve kullanıcı verisi arındırılmış)
- Veritabanı **şema + örnek anonimleştirilmiş içerik** SQL'i (canlı müşteri verilerini değil)
- public uploads ürün kapak görselleri
- Mevcut URL/redirect/sitemap listesi veya Search Console export

Bu dosyalar sağlandığında, önce **özel staging projesinde** yeni premium temayı entegre et. YorumHizmeti.tr canlı hizmet satış sitesi ve Netvera.tr kurumsal yazılım markası birbirinin veritabanı üzerine yazılmamalıdır.

## İlgili herkese açık referanslar
- https://netvera.tr/
- https://netvera.tr/hazir-scriptler
- https://netvera.tr/hazir-scriptler/haber-sitesi-scripti
- https://netvera.tr/blog/yapay-zeka-haber-yazilimi-otomatik-haber-sitesi
