# Uzmanlık kartları — referans uygulaması (15.10.2026)

## Sonuç ve sınır

Kategori sistemi ve 16 hedef bağlantı korunarak gönderilen pastel tasarımın HTML/CSS düzeni uygulandı. Bu bir piksel-eşitlik iddiası değildir: referans yalnızca birleşik bir PNG olarak sağlandı; fotoğraf/3D nesneler built-in imagegen ile yeniden oluşturuldu. Birebir aynı kişiler, nesne geometrisi ve pikseller için orijinal ayrı görseller/tasarım kaynakları gerekir.

## Kök neden ve tek stil sahibi

Önceki tasarım, referanstaki fotoğraf/3D malzemeler yerine yatay SVG sahneleri ve üst görsel/alt içerik düzeni kullanıyordu. İlk browser kontrolünde global `.container` kuralları da yeni bölümün genişliğini 1200px'e sıkıştırıyordu. Yalnız bu bölümün wrapper'ı `.nv29-container` olarak ayrıldı; diğer sayfaların container sistemi değişmedi. Referansın geniş kompozisyonu için bölüm üst sınırı 1544px; mobil kenar boşluğu 20px, 420px altında 16px.

Tek authoritative stylesheet mevcut `home-service-groups-v29.css` olarak kaldı. Eski komponent kuralları bunun içinde değiştirildi; yeni override dosyası, `!important`, tek-karta özel layout veya inline CSS/JS eklenmedi. Önceki üç SVG runtime'da kullanılmıyor; dosyalar silinmedi.

## Değişiklikler

- `app/Controllers/HomeController.php`: referanstaki rozet ve açıklama metinleri. Eski varsayılan sosyal başlık kısaltıldı; farklı admin başlıkları korunuyor. DB/slug/kategori seçme mantığına dokunulmadı.
- `resources/views/frontend/home.php`: ortak kart gövdesi, şeffaf artwork, canlı CTA ve 2×2 kategori kutuları.
- `public/assets/css/home-service-groups-v29.css`: pastel yüzeyler, dalgalar, gradient CTA, Poppins, ortak responsive düzen; mobilde artwork altında ayrı CTA güvenli alanı.
- `resources/views/layouts/app.php`: yalnız mevcut stylesheet cache anahtarı.
- `tests/home-expertise-qa.php`, `tests/home-expertise-probe.mjs`, `tests/visual-smoke.mjs`: yeni malzeme/düzen sözleşmeleri; eski 360×140 SVG/üst şerit beklentileri kaldırıldı.

## Görseller ve kullanılan beceri

[imagegen SKILL.md](C:/Users/pc/.codex/skills/.system/imagegen/SKILL.md) yönergeleriyle **built-in tool** kullanıldı; CLI/API fallback kullanılmadı. Yerleşik araç üç ayrı referans koşullu şeffaf PNG üretti. Alpha ve orijinal çözünürlük korundu; ek sıkıştırma yapılmadı. Metinler ve bağlantılar görsele düzleştirilmedi.

- [social.png](C:/Projeler/yorumhizmeti.tr/well-known/public/assets/img/expertise/social.png): 1149×1369, 2,135,451 byte.
- [agency.png](C:/Projeler/yorumhizmeti.tr/well-known/public/assets/img/expertise/agency.png): 1312×1199, 991,723 byte.
- [marketing.png](C:/Projeler/yorumhizmeti.tr/well-known/public/assets/img/expertise/marketing.png): 1328×1184, 1,375,091 byte.

Referans: kullanıcının `codex-clipboard-fb9e1e98-8410-403f-8fd1-c37a2a7d4647.png` eki. Görsellerdeki 12.4K / %150 değerleri bu referansın dekoratif öğeleridir; ölçülmüş NetVera sonucu veya performans garantisi değildir.

## Doğrulama

- Değişen dört PHP dosyası için `php -l`: başarılı.
- 25 çevrimdışı `*-qa.php`: başarılı. PayTR callback ve dealer testleri yazan özel CI fixture ortamı gerektirdiği için çalıştırılmadı.
- İki JavaScript test dosyası için `node --check`: başarılı.
- Chrome gerçek browser: 1672×941 referans, 1440×900, 1280×720, 1024×768, 390×844, 360×800.
- Her boyutta üç kart, overflow/text clipping, görsel yükleme/contain, kategori sayısı, aynı satır CTA/footer yükseklikleri ve mobil CTA güvenli alanı kontrol edildi; probe hata listeleri boş.
- Önce/sonra 16 bağlantı metni ve href birebir korundu. Üç ana CTA gerçek browser ile açıldı; grup URL/canonical doğrulandı. Browser konsolunda error yok.
- Görseller masaüstü/tablet/mobil screenshotlarla incelendi. Uzun mobil bölüm screenshot'u araçta zaman aşımı verdi; mobil için viewport ve kart bazlı kanıtlar kullanıldı. Sabit canlı destek/alt navigasyon clip screenshotlarında belge ortasına yansıyabilir; bunlar komponent kesilmesi değildir.
- Tam `visual-smoke.mjs` CI senaryosu çalıştırılmadı; güncellenen geometri kontrolü gerçek browser'da ayrı probe ile çalıştırıldı.
- Scope'lu diff kontrolü yapıldı. Önceden var olan sync-conflict dosyaları, staged içerikler ve ZIP silinmesi commit kapsamı dışında bırakıldı.

Yerel kanıtlar: [masaüstü](C:/Projeler/yorumhizmeti.tr/qa-artifacts/expertise-reference-20261015/desktop-proof.png), [390px](C:/Projeler/yorumhizmeti.tr/qa-artifacts/expertise-reference-20261015/390x844-viewport.png), [360px](C:/Projeler/yorumhizmeti.tr/qa-artifacts/expertise-reference-20261015/360x800-viewport.png), [geometri](C:/Projeler/yorumhizmeti.tr/qa-artifacts/expertise-reference-20261015/geometry.json), [navigasyon](C:/Projeler/yorumhizmeti.tr/qa-artifacts/expertise-reference-20261015/navigation.json). QA dosyaları Git tarafından ignore edilir.

## Kalan risk / mevcut teknik borç

- Piksel düzeyinde %100 eşitlik için orijinal artwork kaynakları eksik. Mevcut çıktı referans koşullu yeniden üretimdir.
- PNG toplamı yaklaşık 4.5 MB; lazy loading/async decoding korundu. Görsel keskinliği için lossy yeniden sıkıştırma yapılmadı.
- Bu bölümün görsel/rozet/açıklamaları daha önce de kod kontrollüydü; özel artwork upload alanı admin'de yok. Başlıklar ve aktif kategori bağlantıları admin/veri odaklı kalır. Eski varsayılan sosyal başlığın kısa sunumu yalnız bu komponentte yapılır; DB değeri değiştirilmez.
- Ödeme, demo, SEO URL'leri, header, diğer ana sayfa bölümleri ve diğer sayfalar kapsam dışı bırakıldı.

## Son prompt seti

### Social

```text
Use case: background-extraction.
Asset type: transparent website card artwork extracted from supplied design.
Input image 1 is the edit target. Extract ONLY the artwork of the LEFT pink social-media card: the same smiling woman with wavy brown hair in a purple knit sweater looking down at her black phone, together with the floating Instagram, TikTok and YouTube 3D icons and small tilted 12.4K analytics pill.
Keep the woman identity, pose, clothing, exact colors, glossy icon style and relative positions as unchanged as possible. Preserve the full phone, hands, hair and sweater. Do not redesign or reinterpret.
Remove the card background, all headings, paragraphs, badges, buttons, bottom category chips, neighboring cards and all page decoration. Genuinely transparent background, clean alpha edges, no white rectangle, no checkerboard baked in, no extra text or objects. Tightly frame the isolated artwork as a portrait asset, with all floating icons fully inside the canvas and the torso ending naturally along its bottom edge. This is an extraction, not a new website mockup.
```

### Agency

```text
Use case: background-extraction.
Asset type: transparent website card artwork.
Input image 1 is the edit target. Extract ONLY the 3D artwork of the MIDDLE blue software card: the same silver laptop angled in perspective, its lavender/blue generic web interface, the upright black smartphone in front of its right side, and the floating white rounded code-symbol tile above. Preserve exact perspective, shapes, proportions, colors, UI details, and premium soft 3D lighting. Do not redesign.
Remove all card background waves, headings, paragraphs, top badges, CTA buttons, bottom category chips, neighboring cards, and page decoration. Truly transparent background with clean alpha and soft contact shadows, no white rectangle or baked checkerboard. All laptop/phone/code tile edges must remain inside the canvas, tightly framed as a portrait composition matching the source. Only the isolated artwork, not a website mockup. No added text or objects.
```

### Marketing

```text
Use case: background-extraction.
Asset type: transparent website card artwork.
Input image 1 is the edit target. Extract ONLY the 3D artwork from the RIGHT mint marketing card: the same tilted white/mint analytics panel with turquoise rising bars and green rising line, the floating white magnifying-glass tile on its left, white target tile at bottom right, and the small top floating growth badge showing "%150" and "Daha Fazla Görünürlük". Preserve original proportions, perspective, glossy soft 3D style, exact green/aqua palette and relative arrangement. Do not redesign.
Remove all card backgrounds and mint decorative waves, headings, body text, top category pill, CTA and bottom chips, neighboring cards and page decoration. Genuinely transparent background with clean alpha edges, no opaque rectangular background and no checkerboard baked in. Tight portrait framing with all tiles and panel edges fully visible inside the canvas. Only isolated artwork, no website mockup or additional elements. Badge text must be verbatim.
```
