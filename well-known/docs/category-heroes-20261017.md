# Kategori banner yenilemesi

Tamamlanma: 18 Ekim 2026. Kapsam: kategori / alt kategori üst banner'ları; ana sayfa slider'ı, satın alma, SMM sağlayıcısı ve ödeme akışı değiştirilmedi.

## Kök neden ve çözüm

Hizmet banner'ında kategori görselinden bağımsız sabit `hero-woman-cutout.png` kullanılıyordu. Aynı görsele eski v5 CSS katmanları uygulanıyor; tüm hizmetlere aynı teslimat / organik büyüme iddiaları basılıyordu. SEO önerisi alt kategorilerde üst kategori profilini kullanabiliyordu. Yazılım katalog banner'ı ise filtrelenen kategori ne olursa olsun aynı H1 ve metni gösteriyordu.

Yeni tek banner modeli `CategoryHeroService`, tek görünüm `frontend/partials/category-hero.php`, tek stil sahibi mevcut `storefront-design-language-v89.css` içindeki `nv-category-hero*` bileşenidir. Yeni override stylesheet veya `!important` eklenmedi. Eski portre DOM'u, animasyon katmanları, hayali +1.5K rozeti ve genel sonuç vaatleri banner'dan kaldırıldı. Başka bileşenlere ait eski CSS / görseller güvenlik amacıyla topluca silinmedi; eski v5 banner selectorları yeni DOM ile eşleşmiyor.

108 hizmet kategorisi / alt kategorisi, 3 gerçek yazılım kategorisi, katalog kökü ve 34 mevcut yazılım türü sayfası hizmete uygun başlık / açıklama / görsel sistemini kullanır. 146 ayrı görsel üretilmedi: aynı hizmet amacını paylaşan sayfalar 14 tutarlı görsel ailesinden eşleşir; kategori adı ve platform kimliği HTML olarak ayrıca gösterilir.

## Görsel üretimi

Yerleşik `image_gen` modu kullanıldı; CLI/API fallback kullanılmadı. 14 ayrı üretim: profil / kitle, etkileşim, video, ses, topluluk, web, e-ticaret, mobil, tasarım, içerik, arama, reklam, itibar, yerel işletme.

- Nihai klasör: `public/assets/img/category-heroes/`
- Tam prompt seti: [category-hero-prompts-20261017.json](category-hero-prompts-20261017.json)
- Native çözünürlük: 1536×1024, gerçek alpha.
- Yalnızca format dönüşümü: WebP kalite 95 / alpha 100; crop, resize ve konu üzerinde programatik değişiklik yok.
- 14 dosya toplamı: 3.176.030 bayt; her sayfa yalnızca ilgili görseli yükler.
- `object-fit: contain`, intrinsic width / height, eager ve yüksek fetch önceliği; okunabilir başlıklar görsele gömülü değil.

## İçerik, SEO ve admin

Gerçek kategori adına özgü H1, title, açıklama, odak ve ikincil sorgu önerileri, OG görseli, açıklayıcı alt metin ve üst kategori bağlantıları kullanılıyor. Görünen breadcrumb ile hizmet JSON-LD hiyerarşisi eşleşiyor. Yazılım kategorilerinin mevcut editoryal meta başlığı / açıklaması korunup kullanılıyor. Hizmetlerde anlamlı özel SEO alanları korunuyor; yalnızca boş / eski marka / bilinen otomatik başlangıç metinleri güncel fallback alıyor. Slug, canonical yol, indekslenmiş yazılım alt kategori yolu ve ürün / sipariş kimlikleri değiştirilmedi.

Admin → Kategoriler → Düzenle: ilk satır, vurgulu satır, açıklama, otomatik / 14 hizmet görseli / yüklenen kategori görseli ve alt metin. Yazılım Kategorileri formunda görsel ailesi ve alt metin bulunur; kategori adı / açıklaması banner metnini yönetir. Ayarlar mevcut `settings` tablosunda saklanır. Hizmet ve ithal yazılım ID'leri ayrı namespace kullanır; yeni migration gerekmez. Boş alanlar otomatiğe döner, hatalı veri reddedilir, çıktılar escape edilir. Özel yüklenen görsel yalnızca mevcut yerel uploads dizinindeki dosyadan alınır.

SEO yaklaşımı [Google'ın insan odaklı içerik](https://developers.google.com/search/docs/fundamentals/creating-helpful-content), [görsel SEO](https://developers.google.com/search/docs/appearance/google-images) ve [başlık bağlantıları](https://developers.google.com/search/docs/appearance/title-link) rehberleriyle uyumludur. Anahtar kelime doldurma, sahte performans kanıtı veya Google birinci sayfa garantisi verilmez.

## Değişen dosya grupları

- Model: `app/Services/CategoryHeroService.php`, `CategorySeoCompleteService.php`.
- Veri akışı: public ve admin `CategoryController.php`, `NetveraScriptController.php`.
- Görünüm: kategori detayı, yazılım kataloğu, ortak banner partial'ı; hizmet ve yazılım admin kategori formları ile ortak görsel alanları partial'ı.
- Stil: mevcut `storefront-design-language-v89.css`; layout'ta yalnızca cache anahtarı.
- 14 yerel WebP; prompt kaydı; QA audit / export / unit-integration / HTTP testleri; mevcut SEO QA bootstrap bağımlılığı.

## Testler ve sonuç

17 değişen / eklenen PHP dosyasında `php -l`; export scriptinde `node --check`; scoped `git diff --check`: başarılı.

- `category-hero-qa.php`: 111 saf sözleşme başarılı.
- `category-hero-qa.php --integration`: 439 sözleşme; 108 kategori ve 14 aile, ayrı hizmet / yazılım namespace, admin override, geçersiz payload, manual SEO koruması. Yazımlar yalnızca önceden oluşturulmuş `netvera_social_sales_qa_20261017` üzerinde transaction içinde geri alındı.
- `category-hero-http-qa.php`: 146 yerel sayfada 1640 sözleşme; 200 / redirect yok, tek H1, benzersiz title, korunmuş canonical, açıklama, OG / alt / gerçek dosya, görünür ve yapılandırılmış breadcrumb. Bu script DB'ye yazmaz.
- Mevcut regresyonlar: category-complete-seo-v73, inner-pages-premium, catalog-visual-parity-v84, netvera-visual-brand-catalog-v76, netvera-brand-theme-seo-chat, platform-parent-flat-ui-v80, social-platform-identity, social-sales, production-ui-copy, public-slug-url, platform-category-design: başarılı.
- Gerçek Chrome'da 1440×900, 1280×720, 1024×768, 390×844, 360×800: 14 hizmet ailesi ve katalog / kategori / iç içe kategori / tür temsilcileri. Toplam 95 responsive durum; resim yükleme, gerçek viewport genişliği, tek H1, yatay taşma ve metin clipping kontrolü. Taşma / kırpılma bulunmadı. Masaüstünde dengeli iki kolon; mobilde başlık, açıklama, görsel, özellik ve CTA ayrı satırlara akar.
- Sentetik admin hesabıyla gerçek form kaydı → public banner'da alt metnin yansıması → otomatiğe dönüş doğrulandı. Yazılım kategori formlarında tüm yeni alanlar ve benzersiz input ID'leri doğrulandı. Sadece yerel `netvera_ui_audit_20261013` test verileri kullanıldı; production DB'ye yazılmadı.
- Banner CTA'sı paket / yazılım bölümüne kaydırır. Geçici viewport override'ı doğrulama sonunda sıfırlanır.

Ekran kanıtları (Git dışında): repository kökündeki `qa-artifacts/category-heroes-20261017/instagram-1440.png` ve `instagram-390.png`.

## Kalan sınırlar / teknik borç

Google sıralaması canlı indeksleme, içerik kalitesi, rekabet ve teknik / site dışı etkenlere bağlıdır; yerel testle sıralama doğrulanamaz. Varsayılan banner ailesi semantiğe göre atanır; editör farklı bir görseli admin'den seçebilir. Yazılım katalog kökü ve mevcut kod-tanımlı 34 tür facet'inin bağımsız bir admin editörü yoktur; mevcut type tanımları ortak modeli besler. Gerçek hizmet / yazılım kategorileri admin tarafından yönetilebilir. Bu mevcut facet yönetimi borcu raporlandı; yeni tablo / yönetim sistemi kapsamı açılmadı.

Banner dışındaki pazarlama panelleri, ürün detay medyası ve global eski stylesheet borcu bu görevin dışında bırakıldı. Kullanıcının staged `.sync-conflict-*` dosyaları ve silinmiş ZIP kaydı korunur, bu commit'e alınmaz. Push veya canlı yayın yapılmadı.
