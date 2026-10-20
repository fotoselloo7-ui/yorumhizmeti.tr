# Uzmanlık bölümü: iki satırlı başlık ve kompakt kartlar

- Kök neden: mor başlık inline olduğundan siyah metnin yanından devam ediyordu. Kart gövdesinin 388–440px minimumu ve 152px chip alanı, 1200px düzende 542px kart üretiyordu.
- `public/assets/css/home-service-groups-v29.css`: mor başlık block; gövde minimumu 336px (mobil 352px), daha dengeli başlık/etiket boşlukları, 44px kategori satırları ve 116px alt alan. Bölümün üst/alt boşlukları 80/64px. Eski yüksek minimumlar mevcut kurallarda değiştirildi; yeni override veya dosya eklenmedi.
- `resources/views/layouts/app.php`: mevcut stylesheet cache anahtarı yenilendi.
- `tests/home-expertise-qa.php` ve `tests/home-expertise-probe.mjs`: başlığın ayrı satırı ve kompakt desktop yükseklik regresyon kontrolü eklendi.
- Mevcut 1200px container, üç kategori, admin metinleri, bağlantılar ve RGBA görseller korunuyor. Başka bölüm veya sayfa düzeni değiştirilmedi.

## Gerçek browser doğrulaması

Chrome, mevcut `http://127.0.0.1:8006/`, salt okunur ölçüm:

| Viewport | Kart yükseklikleri (yaklaşık px) | Sonuç |
| --- | --- | --- |
| 1440×900 | 456 / 456 / 456 | Başlık iki satır, hizalı CTA/chip alanları |
| 1280×720 | 456 / 456 / 456 | Aynı kompakt desktop düzen |
| 1024×768 | 502 / 502 / 476 | İki kolon + alt karta reflow |
| 390×844 | 498 / 487 / 478 | Tek kolon, okunabilir tam içerik |
| 360×800 | 496 / 532 / 511 | Uzun metinler doğal akıyor; sabit yükseklikle kesilmiyor |

Beş viewportta browser probe geçti: yatay taşma, kart/chip metin kırpılması, CTA/metin çakışması ve artwork/footer çakışması yok; kategori bağlantıları değişmedi. Screenshot kanıtları: `qa-artifacts/expertise-compact-20261015/`. Mobilde mor cümle alt satırdan başlar ve dar genişlikte doğal olarak iki satıra akabilir.

PHP lint ve JavaScript syntax başarılı. Uzmanlık QA ve site container regresyon testi başarılı. Mevcut fixed destek widget'ı bu görevde değiştirilmedi; ekran üzerindeki konumuna bağlı olarak alttaki chip'lerin önünden geçebilir. Ödeme/admin iş akışları bu görsel görevde uçtan uca test edilmedi. Push yapılmadı.
