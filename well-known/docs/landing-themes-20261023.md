# Landing modülüne özel 10 tema

23 Ekim 2026 · Başlangıç: `main`, `32c6fed`, temiz çalışma ağacı.

## Kapsam ve kaynaklar

`NETVERA-LANDING-PAGE-10-TEMA-CODEX-PROMPT.md` baştan sona okundu. Tekrarlanan yapıştırılmış eklerin iki farklı metin içeriği de okundu; kullanıcının açık 10 tema talebi esas alındı. Ekli görseller tek tek değerlendirildi. İletide **9 görsel dosyası** bulunuyor; görsel 6 ve 8 aynı turuncu tasarımın tekrarları. Dolayısıyla 10 ayrı referans varmış gibi raporlanmadı.

| Anahtar | Adı | Ayrı görsel kompozisyon / referans ilişkisi |
| --- | --- | --- |
| landing-01 | Mavi Ufuk | Görsel 1: açık mavi ajans, arama kartlı hero, kompakt hizmetler, editoryal blog |
| landing-02 | Gece Atlası | Görsel 2: lacivert/mor, arama sahnesi, planlama paneli, koyu süreç bölümü |
| landing-03 | Yeşil Rota | Görsel 3: yeşil konum ağı, organik çerçeve, açık süreç kartları, iki sütun SSS |
| landing-04 | Glass Studio | Görsel 4: cam paneller, koyu hizmetler, yatay ürünler, beyaz içerik geçişleri |
| landing-05 | Editoryal | Görsel 5'in kurumsal yaklaşımı + belgedeki krem/editoryal karakter: çerçeveli vitrin, çizgisel kartlar, numaralı anlatım |
| landing-06 | Yerel Odak | Görsel 3/5'in yeşil yerel dili + belgedeki alternatif: ters hero, konum şeridi, satır hizmetler, liste blog |
| landing-07 | Turuncu İvme | Görsel 6/8: lacivert/turuncu, perspektifli vitrin, teklif vurgusu, yatay ürünler |
| landing-08 | SaaS Minimal | Bağımsız karşılık görseli yok; belgedeki minimalist SaaS tarifi: merkezî hero, uygulama penceresi, çizgisel hizmetler |
| landing-09 | Prizma | Görsel 7: mavi-mor gradyan, yüzen konum kartı, inset kapsam ve bento yüzeyler |
| landing-10 | Boyut | Görsel 9: lacivert/beyaz, katmanlı illüstrasyon sahnesi, perspektif, hedef objesi, ferah bölümler |

Referanslar düz bir sayfa resmi olarak basılmadı. Gerçek HTML, CSS ve mevcut PHP görüntü katmanı kullanıldı. Görseller admin kaydından gelir; tema seçimi görseli veya şehir adını değiştirmez. Maket telefonları, sayılar, yorumlar, Google Partner logoları ve başarı oranları eklenmedi. Numarasız dekoratif planlama panelleri açıkça temsili arayüz olarak etiketlendi.

## Kök neden ve mimari

Önceki modül iki sunum ve üç vurgu rengine sahipti; sayfa bazlı bir 10 tema registry'si veya ziyaretçi tercihi yoktu. Mevcut içerik/editör/SEO sistemi yeniden yazılmadı.

Akış: `pages.content` → `RegionalLandingService::validate` → `LandingThemeService` registry/resolver + normalize blok akışı → hero ve section component mapping → ortak site layout'u.

- `theme_key` ve `theme_switcher` mevcut sayfanın JSON içeriğinde saklanır. **Yeni tablo, SQL veya migration gerekmez.**
- Yeni sayfanın güvenli başlangıcı `landing-01`dir. Eski sayfada anahtar yoksa veya geçersizse eski Studio/Editorial renderer'ı korunur. Mevcut kayıtlar topluca güncellenmedi.
- Sekiz mevcut blok türü (kapsam, hizmetler, yazılımlar, referanslar, içerik, kısa yanıt, blog, SSS), sırası ve görünürlüğüyle aynı veri kaynağından gelir. CTA ve kaynak/iletişim bilgileri de korunur. Ortak renderer varyantları registry'den eşlenir; bilinmeyen component için escaped generic sunum bulunur.
- Her temanın ayrı hero DOM kompozisyonu vardır. Hizmet, ürün, içerik/süreç, blog, FAQ ve CTA varyantları da farklıdır; yalnızca renk değişimi değildir. Az içerikte auto-fit grid; tek kartta sınırlandırılmış genişlik; mobilde iki sütun hizmet, yeniden akan ürün/FAQ düzeni kullanılır.
- Ziyaretçi paneli native dialog: sol kompakt düğme; mobilde 65dvh sınırında panel; Escape, odak geri dönüşü, erişilebilir adlar ve seçili durum. Her açılışta üstten başlar; kapatma başlığı kaydırırken erişilebilir kalır.
- Tarayıcı tercihi `landing_theme_<page-id>` anahtarıyla saklanır. Sunucuya yazılmaz; slug/history/meta/canonical değiştirilmez. Engelli storage durumunda SSR çalışmaya devam eder. Yetkili admin önizlemesi tarayıcı tercihlerini okumaz veya yazmaz.
- Admin: 10 yerel SVG düzen önizlemesi, radyo seçimi, aktif durum, her tema için gerçek masaüstü/mobil iframe önizlemesi. Önizleme yalnızca yetkili `onizleme=1` akışında tema parametresini kabul eder; private/no-store ve noindex korunur. Yeni sayfa önce taslak kaydedilir. Eski iki görünümün ayarları ayrı açılır bölümdedir.

## İzolasyon, SEO ve eski kod

Tema CSS/JS yalnızca `frontend/landing/show` için yüklenir. Yeni stiller `.nv-landing` veya `.landing-theme-tools` altında; admin stilleri `.regional-editor` altında kapsamlıdır. Genel `body/header/footer/.card/.button` selector'ı, inline script/style veya `!important` eklenmedi. Yeni CSS, eski renderer'ı override etmez; yeni isteğe bağlı renderer'ın authoritative stilidir.

Eski public görünüm `legacy.php`ye birebir taşındı; eski `sections.php` ve stilleri bırakıldı. Diğer sayfalar, header/footer içerikleri, ürün/kategori/blog controller'ları, ödeme ve veritabanı şeması değiştirilmedi. Layout dosyalarında yalnızca scoped asset koşulu/cache kimliği vardır. Eski HTTP testinin DOM parser'ı inert `<template>` içeriğini gerçek kart sanıyordu; parser bu testte template düğümlerini dışarıda bırakacak şekilde düzeltildi. Tarayıcı kontrolleri ayrıca gerçek DOM'da tek H1 ve tek blok kümesini doğruladı.

Inert hero şablonlarında H1 yerine başlık yer tutucusu kullanılır; yalnızca etkin hero gerçek H1 olur. Sunucu ve ziyaretçi tema değişimlerinde title, meta, canonical, OG, robots, schema, H1/H2/H3, blok metinleri ve gerçek bağlantılar eşit kaldı. Kaydetme sırasında mevcut `dateModified` davranışı doğal olarak devam eder.

## Değişen dosyalar

- `app/Services/LandingThemeService.php`: yeni registry/resolver/blok eşleme.
- `app/Services/RegionalLandingService.php`: geri uyumlu tema alanları ve hata sonrası form kurtarma.
- `app/Controllers/Admin/LandingController.php`, `RegionalLandingController.php`: yeni sayfa varsayılanı ve yetkili görsel önizleme.
- `resources/views/frontend/landing/show.php`, yeni `legacy.php`, `themes/{page,hero,block,generic,switcher}.php`.
- `resources/views/admin/landing/form.php`, yeni `theme-picker.php`.
- `public/assets/css/landing-themes.css`, `landing-editor.css`; `public/assets/js/landing-themes.js`, `landing-editor.js`.
- `public/assets/img/landing-themes/landing-01.svg` … `landing-10.svg`: yerel kod tabanlı küçük düzen önizlemeleri.
- `resources/views/layouts/app.php`, `admin.php`.
- Yeni `tests/landing-themes-qa.php`, `landing-themes-http-qa.php`; mevcut `landing-builder-http-qa.php` parser düzeltmesi.

## Doğrulama

- 18 PHP dosyası `php -l`, iki JS dosyası syntax check, `git diff --check`: başarılı.
- Yeni offline tema testleri: **156** kontrol; 10 temada sekiz blok, bağlantılar, tek H1, güvenli fallback ve escaping.
- Yeni HTTP tema testleri: **159** kontrol; native admin kayıtları, 10 SSR görünüm, SEO eşitliği, ayrı sayfa izolasyonu, guest/yetkili önizleme, seçiciyi kapatma ve normal route izolasyonu.
- Mevcut testler: landing builder offline **46**, HTTP **44**; editorial offline **65**, HTTP son koşu **86**; SEO **48**; social storefront HTTP **359**; public slug, inner pages ve home featured mobile: başarılı.
- Chrome: **10 tema × 5 viewport = 50** ölçüm. 1440×900, 1280×720, 1024×768, 390×844, 360×800. Yatay taşma, dışarı çıkan kontrol/görsel ve kırık görsel yok; her görünümde bir H1 ve aynı blok sırası.
- Admin tema galerisi aynı beş boyutta kontrol edildi; 10 kartın tamamı mevcut, taşma yok. Gerçek 390px mobil ve 1359px masaüstü iframe önizlemeleri doğrulandı.
- Ziyaretçi testi: QA A (id 90) admin varsayılanı `landing-02`; tarayıcıda `landing-04` seçildi ve yenilemede kaldı. QA B (id 91) `landing-08` olarak kaldı. A'ya dönünce 04 tercihi korundu; yayınlanan 02 değişmedi.
- Kararlı DOM üzerinde 10 ziyaretçi tema geçişinde SEO, başlık, blok metinleri, bağlantılar ve gerçek `.site-header` / `.site-footer` HTML'i eşit. Mobil panel aç/kapat, Escape/odak ve native SSS testleri başarılı.
- Gerçek 8006 Alanya sayfasında eski görünüm → tema 10 → yayınlanan görünüme dönüş test edildi. Gerçek sayfa kaydı değiştirilmedi; geçici tarayıcı tercihi sıfırlandı. Viewport override kaldırıldı.

Kanıtlar (Git dışı): `qa-artifacts/landing-themes-20261023/`. `responsive-results.json`, `admin-responsive-results.json`, `isolation-results.json`, tema ekranları, gerçek site kartları ve admin önizleme ekranları.

HTTP yazma testleri yalnızca sentetik, ödeme/SMTP kapalı `netvera_member_qa_20261018` / 8019 ortamındadır. `--keep-fixtures` koşusunun sayfaları 90/91/fallback browser incelemesi için QA'da bırakıldı. Normal test koşuları kendi kayıtlarını temizler. Kullanıcının gerçek içeriklerine ve önceki QA sayfası 65'e yazılmadı. `.sync-conflict-*` dosyalarına dokunulmadı.

## Sınırlar

- Referans eksiği: 9 ek / 8 farklı tasarım. Tema 08'in bağımsız görsel karşılığı yoktur; belge tarifi uygulandı. Bu, 10 ayrı referansın birebir eşleştiği iddiası değildir.
- Tema seçimi içeriği veya banner görselini otomatik yeniden üretmez; mevcut admin içeriği her temada korunur. Daha özel şehir görselleri mevcut görsel yükleme alanından seçilir.
- Önizleme son kayıtlı içeriği kullanır; kaydedilmemiş form verisini yayınlamaz. LocalStorage engelliyse tercih yalnızca açık sayfada sürer.
- Referanslarda görünen ama mevcut veri kaynağında doğrulanmamış istatistik/yorum/partner blokları üretilmedi.
- Mevcut sayfalar görünüm kaybetmemeleri için topluca yeni temaya geçirilmedi; seçim sayfa bazında yapılır. Push yapılmadı; teslim commit hash'i final yanıttadır.
