# Minimal uzmanlık kartları — 2026-10-15

## Kök neden ve çözüm

Önceki kartlarda görseller absolute konumdaydı; açıklamalar desktop'ta %58, mobilde %46 genişlikle sınırlanıyordu. Yükseklikleri azaltmak metinlerin dar sütunda gereksiz kırılmasını ve görsel ile aynı alana girmesini çözmüyordu.

Kart içi yapı değiştirildi: normal akışlı başlık + 80px thumbnail, tam genişlik açıklama, CTA ve kompakt kategori bağlantıları. İçerik kesilmez; özel admin metinleri doğal olarak akabilir. Mevcut 1200px dış container ve iki satırlı bölüm başlığı korunur.

Değişen dosyalar:

- `resources/views/frontend/home.php`: thumbnail başlık satırına taşındı; tekrarlayan üst slogan etiketi kaldırıldı. Başlık, açıklama, CTA ve kategoriler aynı veri kaynağından gelir.
- `public/assets/css/home-service-groups-v29.css`: componentin tek stil kaynağı yeniden düzenlendi. Absolute artwork, dekoratif dalgalar/pseudo elementler, dar açıklama yüzdeleri, yüksek gövde minimumları ve cqi yazı ölçüleri kaldırıldı. Yeni override stylesheet yok.
- `resources/views/layouts/app.php`: stylesheet cache anahtarı güncellendi.
- `tests/home-expertise-qa.php`, `tests/home-expertise-probe.mjs`: normal akış, tam genişlik açıklama, görsel/metin ayrımı ve minimal yükseklik kontrolleri.

## Doğrulama

Gerçek Chrome `http://127.0.0.1:8006/`, beş viewport:

| Viewport | Kart yükseklikleri (px, yaklaşık) |
| --- | --- |
| 1440×900 | 358 / 358 / 358 |
| 1280×720 | 358 / 358 / 358 |
| 1024×768 | 337 / 337 / 337 |
| 390×844 | 358 / 370 / 337 |
| 360×800 | 358 / 370 / 382 |

Önceki desktop yüksekliği 456px idi; yaklaşık %21 azaldı. Browser probe beş boyutta geçti: kategori link/metinleri aynı, yatay taşma ve metin kırpılması yok, thumbnail metinden ayrı, CTA ve kategori alanları çakışmıyor; aynı satırdaki kartlar/CTA'lar hizalı. Desktop, tablet ve iki mobil ekran görüntüsü incelendi. Kanıtlar: `qa-artifacts/expertise-minimal-20261015/`.

PHP lint, JS syntax ve 26 offline QA testi başarılı. Fixture yazan PayTR/bayi testleri ve tam CI visual-smoke çalıştırılmadı. DB, ürünler, SEO yolları, header/footer ve diğer bölümler değişmedi. Mevcut fixed destek widget'ı bu görevde değişmedi; konumuna göre arka plandaki kartların önünden geçebilir. Push yapılmadı.
