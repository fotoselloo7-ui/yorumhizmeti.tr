# Ana sayfa uzmanlık bölümü — 15 Ekim 2026

Başlangıç: main / b052c53. Kapsam yalnızca “Tek platform, üç güçlü uzmanlık alanı” bölümü ve ilgili test/materyallerdir.

## Kök neden

Eski kartlar 64/36 metin-görsel kolonlarına ayrılıyordu. Görsel kolonu overflow:hidden kullanıyor; kişi görseli %116 genişliğe, dekorlar negatif left/right koordinatlara taşırılıyordu. Böylece kaynak görseller kesiliyor, metin ile dekor farklı dar alanlara sıkışıyordu. Bu kurgu yeni bir crop ayarıyla korunmadı; markup ve tasarım tamamen yenilendi.

## Yeni yapı

- Üç açık renkli, tek yüzeyli kart; üstte bütün bir vektör sahnesi, altta metin/CTA ve ayrı kategori bağlantıları. Ortak flex satırları, tablet orphan kartını da karta özel selector olmadan ortalar.
- 1024px ve üzerinde üç kolon; 701–900px iki kolon; 700px ve altında tek kolon. Mobil kartların yüksekliği içeriğe göre doğal akar; metin veya kategori adı ellipsis ile kesilmez.
- Aynı ailede çizilmiş üç yeni yerel SVG: sosyal etkileşim, yazılım altyapısı, arama/görünürlük. Her sahne 360×140; object-fit:contain ile tamamı görünür. Raster sıkıştırma, büyütülmüş kişi kesiti veya dışarı taşan dashboard parçaları yok.
- Eski negatif konumlu dekorlar, kişi görseli markup'ı, mini browser/telefon/analytics markup'ı ve nv106 compatibility sınıfları bu bölümden kaldırıldı. Başka yerde kullanılan eski görsel dosyaları silinmedi.
- Aynı home-service-groups-v29.css tek stil sahibi olarak yeniden yazıldı. Yeni override dosyası ve !important eklenmedi; stylesheet cache anahtarı güncellendi.
- Hover yalnızca kenarlık/gölge değiştirir; kartların CTA çizgisi hover sırasında da kaymaz. Klavye focus halkası ve reduced-motion korunur.

## Veri ve kapsam güvenliği

Zincir: CatalogMenuService → HomeController/homePromoGroups → frontend/home.php → home-service-groups-v29.css ve img/expertise/*.svg.

Controller, admin ayarları ve kategori sorguları değiştirilmedi. Başlıklar mevcut brand_sector_* ayarlarından; kategori chipleri mevcut aktif kategori ağacından gelmeye devam eder. Üç grup, 12 alt kategori ve “Tüm Hizmetleri Keşfet” dahil 16 bağlantı önce/sonra metin ve href bazında birebir karşılaştırıldı. Slug/SEO/checkout/demo verisi değişmedi. Yeni SVG'ler dekoratiftir, aria-hidden/boş alt kullanır.

Ana sayfanın bölüm dışındaki kaynak içeriği (EOF newline normalizasyonu hariç) aynı kaldı. Slider, diğer home bileşenleri, iç sayfalar, header/footer ve canlı destek CSS'i değiştirilmedi. Sync-conflict artıkları ve önceden silinmiş ZIP korunur; commit'e dahil edilmez.

## Doğrulama

Mevcut sentetik QA şeması ve yalnızca loopback PHP preview kullanıldı; production/geliştirme DB'sine yazılmadı.

| CSS viewport | Sonuç |
| --- | --- |
| 1440×900 | Üç eşit kart, yaklaşık 489px; CTA ve footer aynı çizgide |
| 1280×720 | Üç eşit kart, yaklaşık 489px |
| 1024×768 | Üç eşit kart, yaklaşık 512px; uzun chip adı CTA hizasını bozmaz |
| 768×1024 | İki kolon, son kart ortalı; aynı ortak layout |
| 430×932 | Tek kolon, yaklaşık 477px kartlar |
| 390×844 | Tek kolon; metin/görsel/CTA taşması yok |
| 360×800 | Tek kolon; uzun chip gerektiğinde okunabilir şekilde sarılır |

21/21 kart/viewport kontrolü temiz: document/card overflow, text clipping, body/art/footer kesişimi, sıra, aynı satırda CTA/footer/yükseklik hizası ve üç SVG'nin yüklenmesi/oranı kontrol edildi. Üç ana CTA Enter ile doğru kanonik grup sayfasına açıldı; yeni konsol hatası görülmedi. Ekran görüntüleri görsel olarak incelendi.

25/25 offline QA; değişen üç PHP kaynağında lint; iki JS test dosyasında syntax; git diff --check başarılı. Yeni home-expertise-qa.php sentetik template render ile veri eşlemesi/escaping/tek görsel ve responsive CSS sözleşmesini kontrol eder. home-expertise-probe.mjs gerçek tarayıcıda çalıştırılan read-only ölçüm/doğrulama kodudur. visual-smoke.mjs eski yan-kolon/model görseli beklentilerinden yeni tasarıma uyarlandı; tam legacy CI smoke (ayrı ürün fixture'ları gerektirir) bu turda çalıştırılmış sayılmaz.

Yerel kanıtlar Git dışında qa-artifacts/expertise-redesign-20261015/ içindedir: measurements-final.json, navigation.json, final-section-{width}.jpg, desktop-proof-final.jpg. Doğrudan document-clip ekran görüntülerinde fixed barlar viewport konumunda görünebilir; teslim görseli gerçek desktop viewport kaydından bölüm sınırına kırpılmıştır.

## Değişen dosyalar ve sınırlar

home.php (yalnızca bölüm), layouts/app.php (cache key), home-service-groups-v29.css; img/expertise/social.svg, agency.svg, marketing.svg; tests/home-expertise-qa.php, home-expertise-probe.mjs, visual-smoke.mjs ve bu rapor.

Gerçek iOS Safari/Firefox, ekran klavyesi ve her olası gelecekteki çok uzun admin içeriği test edilmedi. SVG materyaller repo kaynaklarıdır; mevcut kategori/admin içerik kontrolünü değiştirmez. Push/deploy yapılmaz.
