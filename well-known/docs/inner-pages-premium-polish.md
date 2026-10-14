# İç sayfa premium düzenlemesi — 14 Ekim 2026

Başlangıç: main / 6c49ea0. Kapsam kullanıcıya açık iç sayfa aileleridir; yönetim paneli ve ana sayfa tasarımı kapsam dışıdır. Her olası ürün/kategori slugı ayrı ayrı test edilmedi; aynı template ailesinin gerçek veriyle çalışan temsilcileri kontrol edildi.

## Kök neden

Kategori dizinindeki aynı kartlara v26, v77, v80, v82, v84, v87 ve v88 farklı geometri, pastel/dolu renk, ters hover ve metin rengi uyguluyordu. v88 bunları tekrar önemli selectorlarla bastırıyordu. Arama, sekmeler ve büyük renkli kartlar birbirleriyle yarışıyordu. Ortak v16 tipografi katmanı birçok iç sayfa metnini 7–11px'e sabitliyordu. Bilgilendirme sayfaları pazarlama sayfalarıyla aynı büyük dekoratif portreyi kullanıyordu.

## Uygulama

- Dizin tasarımı mevcut category-directory-v88.css içinde tek kaynağa toplandı. Diğer kaynaklardaki dizin selectorları kaldırıldı; ortak platform/sector renk tokenları ve ana sayfa/menü selectorları korundu. Eski catalog-contrast-v87.css kaldırıldı; Git geçmişinden geri alınabilir.
- Beyaz, kısa kartlar; platform renkli ikon alanı; tek ve erişilebilir kart bağlantısı; ayrı arama/grup paneli. Desktop dört, tablet üç/iki, mobil tek kolon. Başlık, grup açıklaması ve sayılar belirgin hiyerarşide.
- Dizin stylesheet'i yalnızca frontend/categories template'inde yüklenir. Admin-managed kategori sırası, çocuk sayıları, arama, grup yolları ve /hazir-scriptler bağlantısı korunur.
- layouts/app.php yalnızca ana sayfa dışına nv-inner-page sınıfı verir. Mevcut v89 ortak iç sayfa politikasıyla form, panel, kart, CTA, başlık ve boşluklar dengelendi. Yeni override stylesheet'i ve yeni !important eklenmedi.
- v16'daki mevcut sabit font değerleri değişken/fallback yapısına geçirildi. İç sayfalarda yardımcı metin 11–13px, temel eski 11px metin 14px olur; ana sayfa ve admin eski fallback değerlerini kullanır.
- Bilgilendirme/yasal sayfalarda dekoratif portre kaldırıldı, başlık sola hizalandı. Mevcut admin içeriği aynen render edilir. Mobil SSS destek kartı kısaltıldı.
- Mobil paket bilgi sekmeleri iki kolona yeniden akar. Ödeme hata başlığının küçük mobilde kelime taşması giderildi.
- Giriş/kayıt/parola sayfalarında daha sakin lacivert zemin; mobil inputlar 16px. Kimlik doğrulama davranışı değiştirilmedi.
- SSS inline script'i app.js içindeki yalnızca yardım merkezini hedefleyen handler'a taşındı. aria-expanded, aria-controls ve hidden durumları senkron tutulur; Enter ile açma/kapatma doğrulandı.
- Değişen 13 CSS kaynağında !important toplamı 1423 → 1284; dizin kaynağında sıfır. Bütün legacy CSS temizlenmiş sayılmaz.

## Değişen dosya grupları

- Ana stil sahipleri: category-directory-v88.css, storefront-design-language-v89.css, typography-readability-v16.css, auth-premium-v25.css.
- Dizin çatışmalarından temizlenen ortak kaynaklar: agency-catalog-v26.css, icon-alignment-v28.css, social-platform-identity-v77.css, platform-inheritance-flat-v80.css, service-category-identity-v82.css, catalog-visual-parity-v84.css. catalog-contrast-v87.css kaldırıldı.
- Mevcut hero/SSS parametreleri: visual-demo-v12.css ve section-rhythm-v17.css.
- Template/JS: frontend/categories.php, frontend/faq.php, frontend/page.php, layouts/app.php, layouts/auth.php, public/assets/js/app.js.
- Beş eski source-marker testi yeni stil sahipliğine/cache sürümüne uyarlandı; inner-pages-premium-qa.php yeni kapsam/izolasyon/regresyon kontrollerini ekler.

## Doğrulama

Mevcut sentetik netvera_ui_audit_20261013 veritabanı, loopback PHP preview ve gerçek Chromium tarayıcıları kullanıldı. Geliştirme/production DB, ödeme, SMM, SEO controllerları, sluglar, ürün görselleri, slider ve ana sayfa template'i değiştirilmedi. Yerel sentetik hesaba giriş ve session sepetine paket ekleme dışında ticari işlem yapılmadı; ödeme/iletişim/destek formu gönderilmedi.

- Ana matris: 33 oturumlu adres/senaryo × 5 boyut = 165; ana sayfa regresyonu 5; dört oturumsuz auth sayfası × 5 = 20. Toplam 190 kontrol.
- Son bilgilendirme/SSS düzenlemesinden sonra ilgili yedi adres + ana sayfa × 5 = 40 hedefli tekrar.
- CSS boyutları: 1440×900, 1280×720, 1024×768, 390×844, 360×800.
- Kategoriler/gruplar/arama/boş sonuç; ana ve alt hizmet; paket; iki yazılım kataloğu/kategori/detay; blog liste/detay; iletişim/SSS; beş bilgilendirme/yasal sayfa; hesap/sipariş/bayi/destek; dolu sepet/checkout; auth; hata sayfası örnekleri kapsandı.
- Son matrislerde document overflow, normal akışta viewport taşması, kesilen buton/başlık veya yüklenmesi tamamlanmış bozuk görsel bulunmadı. Intentional scroll alanları ve kapalı menüler ayrıdır; bu ölçümler bütün fixed-widget kesişimlerinin yokluğunu kanıtlamaz.
- Gerçek arama submit'i filtreli sonuca ulaştı. SSS Enter/tıklama ve aria/hidden durumu doğru. Satın alma tamamlanmadı, harici demo/WhatsApp açılmadı.
- 24/24 offline QA; değişen PHP kaynakları lint; app.js syntax; git diff --check başarılı. Canlı ödeme/provider testleri ve DB gerektiren dealer testi bu CSS turunda çalıştırılmadı.

Yerel kanıtlar Git dışındaki qa-artifacts/inner-premium-20261014 klasöründedir: final-measurements.json, auth-measurements.json, targeted-measurements.json ve sayfa ailesi/boyut ekran görüntüleri. Uygulama tarayıcısında DPR/ekran görüntüsü kırpılma sorunu görüldüğünden final görsel kanıtları ayrı Chrome QA sekmesinden alındı. Full-page capture fixed barları ilk ekranın konumunda gösterebilir; lazy görseller scroll edilmeden boş kalabilir. Bu bir piksel-kusursuz tüm cihaz sertifikası değildir.

## Kalan sınırlar

Gerçek iOS Safari/Firefox, ekran klavyesi, production CDN/cache ve her olası admin-authored uzun içerik ayrı doğrulama gerektirir. Site genelindeki eski CSS katmanları tamamen kaldırılmadı; bu görevde dizin çatışmaları kökten temizlendi, diğer ailelerde mevcut tasarım geliştirilip kapsam izole edildi. Senkronizasyon artıkları ve önceden silinmiş ZIP korunmuştur; commit'e dahil edilmez. Push/deploy yapılmaz.
