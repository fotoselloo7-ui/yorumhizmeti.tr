# Slider responsive framing — 14 Ekim 2026

Kapsam: aktif `.nv94-hero-slider` componenti, üç admin-managed slide. Kaynak zinciri `HeroSliderService → frontend/home.php → layouts/app.php → home-hero-slider-v94.css / home-hero-slider-v94.js`. Repository aramasında nv94 stilleri yalnızca bu stylesheet'te bulundu. SQL, admin kayıtları, slider JS'i, CTA URL'leri ve görsel dosyaları değiştirilmedi.

## Kök neden

Mobil HD dosyaları 1080×1920; üst yaklaşık %75'lik alan metin için boş bırakılmış. Eski stylesheet bu alanı tüm slidera yayıyor, 620/650px minimum yüksekliği ve 250/285px alt padding ile artwork'ü aşağıda tutuyordu. Aynı dosyada V100/V101/V103/V107 blokları object-fit, konum, yükseklik ve padding'i tekrar eziyordu. Tablet 768px'te geniş desktop banner, çok uzun dikey wrapper içinde merkezden kırpılıyordu.

## Çözüm

- Aynı authoritative stylesheet yeniden düzenlendi; yeni override dosyası yok. Slider içindeki `!important` sayısı 94 → 0.
- Stacked grid bütün slide'ların en uzun metnini hesaba katıyor; slide değişiminde yükseklik sıçramıyor.
- Desktop: 460px taban, sağ odaklı cover; büyüme boş sol arka planı kırpıyor. Kontroller metnin altında solda, ana artwork'ün önünde değil. 901–1100px aralığında metin-safe karartma ayrıca dengelendi.
- Tablet: gerçek metin ve medya satırları. Desktop HD asset sağ odakla kullanılıyor; yüz ve cihazlar merkez crop'unda kaybolmuyor.
- Mobil: gerçek medya satırı; ortak composed portrait çerçevesi üstteki boşluğu ve sol fonu dışarıda bırakıyor. `width:165%; height:auto` intrinsic oranı koruyor; transform/scale/filter kullanılmıyor. Kritik yüz/cihazlar üç slide'da screenshot ile kontrol edildi. Kenardaki dekoratif fon crop edilebilir.
- Mobil/tablet oklar ve dots medya satırının altında ayrı akışta. Floating widget'ın CSS'i ve konumu değiştirilmedi.
- Eski mobil açıklama üç satır sınırı korundu; CTA/quick links ve admin içerik kontrolü korunuyor.

## Doğrulama

Gerçek Chromium tabanlı uygulama tarayıcısı, `http://127.0.0.1:8006/`. Üç slide bütün boyutlarda tek tek açıldı; geçişler tamamlandıktan sonra ölçüm ve screenshot alındı.

| Viewport | Üçünde ortak slider yüksekliği | Sonuç |
| --- | ---: | --- |
| 1440×900 | 460px | Sağ artwork, okunabilir metin ve açık CTA |
| 1280×720 | 460px | Dengeli desktop framing |
| 1024×768 | yaklaşık 464px | Metin-safe alan ve yüz/cihazlar korundu |
| 768×1024 | yaklaşık 706px | Ayrı metin/medya satırları |
| 430×932 | yaklaşık 679px | Artwork yaklaşık %65 daha büyük |
| 390×844 | yaklaşık 651px | Artwork yaklaşık %65 daha büyük |
| 360×800 | yaklaşık 638px | Artwork yaklaşık %52 daha büyük |

Büyüme oranı, doğal görsel pikselinin CSS ekranındaki ölçeğinin önce/sonra karşılaştırmasıdır; dosya çözünürlüğü artışı değildir. Önce mobil slider 620px'ti. Yeni toplam yükseklik metin + daha büyük gerçek medya + ayrı kontrol satırından oluşur; eski boş spacer kaldırıldı. Böylece küçük mobilde toplam artış yaklaşık 18–31px iken artwork belirgin büyüdü ve yukarı geldi.

21/21 slide/viewport kontrolünde document overflow, CTA/kontrol, quick-link/kontrol veya chat/kontrol çakışması bulunmadı. Mobil/tablette controls/media çakışması yok. Desktop'ta medya background'u tüm slide'ı kapsadığından geometrik kesişim beklenir; screenshot'ta kontroller boş sol safe area'dadır. Final konumda canlı destek panel düğmesi ile medya kesişimi de ölçülen yedi boyutta sıfır.

Orijinal desktop 2400×840, mobil 1080×1920 asset seçimi korundu. Browser currentSrc, natural dimensions, rendered dimensions, intrinsic oran ve `transform:none / scale:none / filter:none` kontrol edildi. Kaynaklar yeniden üretilmedi/sıkıştırılmadı. Retina kalitesi cihaz DPR'ına bağlıdır; gerçek iOS Safari cihaz testi yapılmadı.

Testler: 23/23 offline QA; değişen iki PHP dosyasında lint; `git diff --check`. Hero QA eski V107 yorum marker'ı yerine ortak grid, gerçek medya satırı, oran koruması, ayrı kontroller ve eski spacer/override'ların dönmemesini kontrol ediyor.

Yerel kanıtlar (Git dışında): `qa-artifacts/slider-responsive-20261014/measurements.json`, `before-{viewport}-{slide}.jpg`, `after-{viewport}-{slide}.jpg`. Önceki screenshotlarda kontrole tıklamanın browser auto-scroll etkisi olabilir; sayısal karşılaştırmalar elementlerin kendi ölçülerine dayanır. Final screenshotlar yukarı scroll edilerek alındı.

Değişen uygulama dosyaları: `public/assets/css/home-hero-slider-v94.css`, `resources/views/layouts/app.php` (cache key), `tests/home-hero-slider-v94-qa.php`. Kullanıcının sync artıkları ve önceki ZIP silinmesi kapsam dışındadır. Push yapılmaz.
