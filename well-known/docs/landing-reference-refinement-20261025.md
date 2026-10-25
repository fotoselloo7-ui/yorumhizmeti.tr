# Landing tema referans düzenlemesi — 25 Ekim 2026

## Kapsam ve durum

Bu çalışma yalnız landing gövdesi ve ilgili admin alanlarına uygulanmıştır. Header/footer şablonları, ödeme, route, canonical ve SEO üreticileri değiştirilmedi. Yeni migration yok; isteğe bağlı şehir görseli mevcut sayfa JSON'u içinde saklanır. Eski sayfaların yayındaki tema veya görselleri topluca değiştirilmedi.

**Referanslarla piksel düzeyinde birebir eşleşme tamamlanmış değildir.** Özellikle özgün hero çizimleri ve bazı referans bölümleriyle farklılık sürmektedir. Bu rapor tamamlanmışlık iddiası değil, doğrulanmış geliştirme kaydıdır.

## Referans eşlemesi

Eklerde 9 dosya, 8 farklı tasarım var. 6 ve 8 aynı turuncu tasarımdır. Ayrı 06 ve 08 referansları bulunmadığı için belgedeki tarif korunmuştur.

| Tema | Referans | Bu turdaki sunum |
| --- | --- | --- |
| 01 Mavi Ufuk | b2abe6a5… | Mavi şehir hero, sol başlıklı yazılım vitrini, editoryal blog |
| 02 Gece Atlası | bb402ece… | Gece hero, arama kartı, koyu süreç bandı |
| 03 Yeşil Rota | f566167c… | Yeşil hero, koyu yeşil bölgesel kapsam, sağ şehir görseli |
| 04 Glass Studio | aa1fa36c… | Koyu üst gövde, cam paneller, yatay ürünler, açık alt gövde |
| 05 Editoryal | 8d57b819… | Adaçayı/krem, hizmet kartları, ürün galerisi, adımlı süreç |
| 06 Yerel Odak | Ayrı görsel yok | Belgedeki yeşil yerel alternatif; ters hero, satır kartları |
| 07 Turuncu İvme | 5dff7d40… ve 31698da2… | Lacivert/turuncu, geniş kapaklı iki ürün, editoryal blog |
| 08 SaaS Minimal | Ayrı görsel yok | Belgedeki minimalist SaaS; tarayıcı çerçevesi, çizgisel liste |
| 09 Prizma | 9a9a3520… | Mavi/mor, koyu tanıtım paneli, açık süreç, kartlı blog |
| 10 Boyut | efc8c2d3… | Lacivert 3D hero, geniş ürün kapağı, adımlı süreç |

## Kök neden ve kod

Önceki sunum çok fazla ortak kart yerleşimine dayanıyordu; bazı tema eşlemeleri referanstaki bölüm bileşiminden farklıydı. Şehir atmosferi için ayrı görsel alanı yoktu. Hero panelleri gereksiz yükseklik oluşturuyordu.

- `LandingThemeService`: referansa göre 05/07/09/10 bölüm eşlemeleri.
- `RegionalLandingService`, admin controller/form/editor: güvenli yerel şehir görseli, alt metin, upload ve isteğe bağlı preset.
- `themes/hero.php`, `block.php`, `page.php`: başlık vurgusu, şehir katmanı, gerçek kategori ikonları, ürün/blog ayrımı, süreç ve SSS düzeni.
- `landing-themes.css`: tek authoritative scoped stil; eski uzayan hero kuralları konsolide edildi. Yeni override dosyası veya `!important` eklenmedi. Tablet tema butonu paragrafın üzerinden alta taşındı.
- Layout dosyaları: yalnız ilgili CSS/JS cache anahtarları.

Alanya mavi saat ve İstanbul gündüz görselleri imagegen ile üretilmiş **temsili illüstrasyonlardır**, gerçek fotoğraf/kanıt değildir. Admin'de açıkça etiketlidir. Yalnız açık seçimle kullanılır, şehir/slug üzerinden kendiliğinden atanmaz. WebP çıktıları `public/assets/img/landing-scenes/` içindedir. Görseller, referanslardaki sağda şehir/ solda metin güvenli alanı kompozisyonunu temel alır; özgün görsellerin aynısı değildir.

## Doğrulama

- PHP lint ve JS syntax: başarılı.
- Tema registry/render: 156; yeni referans/alt/seyrek veri: 245; builder: 46; editorial: 65; SEO: 48 kontrol başarılı.
- İzole QA veritabanında HTTP/upload/izolasyon: 199 kontrol başarılı (önceki doğrulama turu).
- Chrome: 10 tema × 1440×900, 1280×720, 1024×768, 390×844, 360×800 ölçümü tamamlandı; yatay taşma yok, tek H1 ve sekiz mevcut blok korunuyor.
- Son yerleşim değişikliğinden sonra değişen altı tema aynı beş ölçüde yeniden kontrol edildi: 30/30 başarılı.
- Gerçek `/alanya-reklam-ajansi` içeriğiyle 10 tema × masaüstü/mobil: 20/20 taşma kontrolü başarılı. Sayfanın veritabanı kaydı değiştirilmedi.
- SSS klavyeden açılma doğrulandı; tam sayfa ekran görüntüsü alındı.
- Kanıtlar: `qa-artifacts/landing-reference-20261024/` (yerel, git dışında). 390 adlı ilk görüntüler 375px kırpılmıştır; ölçümler gerçek 390px viewport'tan alınmıştır.

## Kalan işler / riskler

- Birebir eşleşme için eksik iki benzersiz referans ve özgün hero görsel kaynakları gereklidir; mevcut görseller yaklaşık uyarlamadır.
- Referanstaki sahte müşteri sayıları, yıldızlar, partner logoları ve sonuç yüzdeleri eklenmedi. Gerçek seçili verisi olmayan ürün/blog blokları üretilmez.
- Admin'in mevcut hero görseli bütün temalarda korunur; bu nedenle tema değiştirmek otomatik olarak başka bir illüstrasyon seçmez.
- Şehir görseli eski kayıtlarda boş kalır; kullanmak için admin'de seçilip kaydedilmelidir.
