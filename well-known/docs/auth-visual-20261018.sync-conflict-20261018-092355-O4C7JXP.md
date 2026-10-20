# Üyelik paneli görsel / logo düzenlemesi — 18 Ekim 2026

## Sonuç ve kök neden

Giriş / kayıt paneli eski ortak kahverengi bluzlu portreyi kullanıyordu. Koyu mavi logo doğrudan lacivert-mor zeminde kaldığı için okunmuyordu. Ayrıca `auth-brand-logo-v94.css` yalnızca public app layout'unda yükleniyor, gerçek auth layout'unda çalışmıyordu; bu dosyada renkleri beyaza çeviren filter ve gereksiz `!important` kuralları bulunuyordu.

- Yeni fuşya ceket / kobalt üst / tabletli yetişkin kadın portresi: `public/assets/img/auth-woman-fuchsia.webp`.
- Yerleşik `image_gen` modu, tek yeni üretim; CLI/API fallback yok. 1024×1536 native RGBA → aynı çözünürlükte WebP kalite95 / alpha100, 224.058 bayt. Konu üzerinde kodla değişiklik yapılmadı, alpha korundu.
- CSS'te bilinçli üst gövde kadrajı; baş ve tablet ayrılmış alt-sağ bölgede. Üst metin, rozet, güven kartı ve geri bağlantısıyla çakışma yok.
- Logo orijinal renkleriyle beyaz yüzeyde gösteriliyor; renk filtresi yok. `SiteLogoService` üzerinden admin tarafından yüklenen logo seçimi korunuyor.
- Giriş ve kayıt ortak `frontend/auth/brand.php` partial'ını kullanıyor. Kayıttaki eski Y placeholder'ı da kaldırıldı.
- Auth stilinin tek sahibi mevcut `auth-premium-v25.css`. İşlevsiz `auth-brand-logo-v94.css` dosyası ve app layout'taki yüklemesi kaldırıldı; yeni override stylesheet / `!important` eklenmedi. Silinen stylesheet Git geçmişinden kurtarılabilir.

## Değişen dosyalar

`frontend/auth/{login,register,brand}.php`, `layouts/{auth,app}.php`, `public/assets/css/auth-premium-v25.css`, kaldırılan `auth-brand-logo-v94.css`, yeni WebP, `tests/auth-visual-qa.php` ve ortak partial dependency'sine göre güncellenen `tests/home-hero-slider-v94-qa.php`.

## Doğrulama

- 6 PHP dosyasında `php -l`, scoped `git diff --check`: başarılı.
- `auth-visual-qa.php`: 22 saf render sözleşmesi; özel admin logosu, escaping, gerçek logo, eski placeholder / portre yokluğu, form endpoint ve method, CSRF alanı, email / password kısıtları, dekoratif görsel semantiği.
- `auth-visual-qa.php --http`: toplam32 sözleşme; yalnızca mevcut yerel QA sunucusunda GET, gerçek CSRF / logo / portre / noindex. Yeni hesap oluşturulmadı ve form gönderilmedi.
- Mevcut home-hero-slider-v94, site-logo-v90, inner-pages-premium, production-ui-copy ve storefront-premium-polish testleri başarılı.
- Gerçek Chrome: giriş ve kayıt, 1440×900 / 1280×720 / 1024×768 / 390×844 / 360×800. Ek geçiş kontrolleri: 811×720, 900×720, 1150×766. 16 durumda yatay taşma / metin clipping / portre-metin veya rozet çakışması yok. Logo görseli yükleniyor; filtre yok ve yüzey beyaz.
- Masaüstü: portre canlı renkli ve daha belirgin üst gövde kadrajında; logo kontrastı net.
- Mobil: önceki davranış korunarak portre gizli, form öncelikli; gerçek logo ve CTA okunaklı / erişilebilir. Geçici viewport sıfırlandı.
- Şifre göster / gizle gerçek tarayıcıda kontrol edildi; input tipi ve `aria-pressed` doğru değişiyor. Auth controller, şifre politikası, oturum, CSRF, ödeme ve satın alma kodları değiştirilmedi.
- Kanıtlar Git dışındaki `qa-artifacts/auth-visual-20261018/login-1440.png` ve `login-390.png`.

## Sınırlar

Yeni portre mevcut auth panelindeki statik asset modelini kullanır; bağımsız admin portre yükleme alanı yoktur (mevcut teknik borç). Admin logo yükleme / sıfırlama özelliği korunmuştur. Canlı ortama yayın veya push yapılmadı. Kullanıcının `.sync-conflict-*` dosyaları, staged içeriği ve silinmiş ZIP kaydı değiştirilmedi.

## Üretim promptu

```text
Use case: photorealistic-natural.
Asset type: transparent portrait cutout for the lower-right illustration zone of a premium technology company's login and registration panel.
Primary request: one stylish, beautiful adult woman around 28-35 years old, warm confident natural smile, looking slightly toward the viewer, wearing a vivid fuchsia tailored blazer over a cobalt blue top and dark tailored trousers. Shoulder-length wavy dark brown hair, realistic skin and fabric texture. Holding a slim silver tablet naturally with both hands at waist level. Contemporary polished commercial studio photography, flattering soft daylight with a subtle cool rim light; colorful and eye-catching yet professional, not sexualized.
Composition: vertical portrait, head to upper thighs completely visible, both shoulders and elbows fully inside the frame, centered silhouette, only a small clear margin around the person. Natural proportions and anatomy, clean detailed hair edges. No crop at head, hands or elbows.
Background: genuinely transparent alpha, no background scenery, no solid white rectangle, no floor, no colored backdrop, no decorative shapes or text.
Constraints: one person only; no logos, letters, watermark, extra devices, floating graphics, or UI. Do not reuse the brown-blouse woman from the website.
```

