# NetVera Teknoloji Yazılım — AGENTS.md

## 1. Proje kapsamı

Bu repository tek bir bütün olarak ele alınır.

Repository kökü:

C:\Projeler\yorumhizmeti.tr

Asıl PHP MVC uygulaması:

C:\Projeler\yorumhizmeti.tr\well-known

`.github`, testler, SQL, dokümantasyon ve `well-known/` uygulaması birlikte değerlendirilmelidir.

Projenin güncel markası:

**NetVera Teknoloji Yazılım**

Proje;

- kurumsal web sitesi,
- dijital ajans hizmetleri,
- SEO / GEO / AIO hizmetleri,
- sosyal medya hizmetleri,
- hazır yazılım kataloğu,
- yazılım demo sistemi,
- satış / teklif / ödeme altyapısı

bileşenlerinden oluşur.

Kullanıcıya açık yeni içeriklerde eski `yorumhizmeti` markasını kullanmayın.

Ancak eski marka URL'leri, indexlenmiş yollar, backlink alan adresler veya backend uyumluluk katmanları SEO ve yönlendirme etkisi incelenmeden silinemez.

Slug değişikliği gerekiyorsa 301 eşlemesi olmadan değiştirmeyin.

---

## 2. İlk çalışma kuralı

Her görevden önce:

1. `git status`
2. aktif branch
3. ilgili son commitler
4. değiştirilecek componentin gerçek dependency zinciri
5. ilgili testler

kontrol edilmelidir.

Mevcut implementasyonu otomatik olarak doğru kabul etmeyin.

Bu repository geçmiş geliştirmeler nedeniyle:

- eski CSS override'ları,
- sürüm numaralı stylesheetler,
- eski migration beklentileri,
- geçici compatibility katmanları,
- eski NetVera import kodları

içerebilir.

Bir dosyanın adının yeni olması onun authoritative kaynak olduğu anlamına gelmez.

Gerçek runtime davranışını belirleyin.

---

## 3. Çalışma ağacı ve Git güvenliği

Kullanıcının local değişikliklerini koruyun.

Aşağıdakiler yasaktır:

- `git reset --hard`
- `git clean -fd`
- force push
- main geçmişini rewrite etmek
- kullanıcı dosyalarını izinsiz silmek
- local değişikliklerin üzerine körlemesine yazmak

`.sync-conflict-*` dosyaları gerçek uygulama kaynağı olarak kullanılmamalıdır.

Bunlar kullanıcıya ait local senkronizasyon artıkları kabul edilir.

Açıkça istenmedikçe:

- silmeyin,
- merge etmeyin,
- aktif source olarak referans almayın.

Çalışma ağacındaki gerçek değişiklikleri `.sync-conflict-*` dosyalarından ayrı değerlendirip raporlayın.

Her anlamlı görevde:

sorunu yeniden üret → kök nedeni bul → minimal kalıcı çözüm → test → browser doğrulaması → diff inceleme → commit

akışı kullanılmalıdır.

Kullanıcı açıkça aksi yönde istemediği sürece doğrulanmış anlamlı çalışma commitlenebilir.

Test edilmemiş değişiklik push edilmez.

---

## 4. Kök neden önceliği

Görsel veya davranışsal bir problem geldiğinde önce:

- DOM
- computed styles
- CSS source order
- selector specificity
- JS event zinciri
- PHP veri akışı
- veritabanı kaynağı

incelenmelidir.

Şunları yapmayın:

- mevcut sorunun üzerine yeni bir `!important` yığını eklemek
- sürekli yeni `v108`, `v109`, `v110` override stylesheetleri üretmek
- sadece tek ekran görüntüsünü düzelten selector yazmak
- üçüncü karta veya tek ürüne özel hack yapmak
- hatayı başka bir CSS katmanıyla gizlemek

Tercih edilen çözüm:

eski ve çakışan kuralları kaldır / konsolide et → component için tek authoritative stil bırak.

Yeni override dosyası son çaredir.

---

## 5. Tasarım sistemi

Genel dil:

- premium
- modern
- minimal
- kurumsal
- teknoloji şirketi hissi

Font:

**Poppins**

Ana container:

Kullanıcıya açık tüm sayfalarda **fluid**: viewport genişliği eksi **40px**.

Sağda ve solda her cihazda **20px** dış boşluk korunur; ana container 1200px veya başka bir sabit maksimum genişlikle sınırlandırılmaz.

Bu ölçünün tek authoritative kaynağı `well-known/public/assets/css/app.css` içindeki `--page-gutter`, `--page-width` ve `.container` kurallarıdır. Bölümlere özel dış container genişliği eklemeyin. İç içe container ikinci kez dış boşluk oluşturmaz.

Form, paragraf ve detay metni gibi iç alanlarda okunabilirlik için max-width kullanılabilir; bu sınır sayfanın dış frame'ine uygulanmaz.

Spacing:

**8px tabanlı**

Yatay dış boşluk (desktop/tablet/mobile):

**20px**

Kartlar gereksiz uzun olmamalıdır.

Başlıklar genel olarak sola hizalı ve dengeli olmalıdır.

Hover ve animasyonlar ölçülü kullanılmalıdır.

Yeni inline CSS ve inline JS eklemekten kaçının.

Mevcut legacy inline kod, ilgili component yeniden düzenlenirken mümkünse stylesheet / script dosyasına taşınmalıdır.

---

## 6. Responsive doğrulama

Mobil görünüm desktop'ın küçültülmüş hali değildir.

Layout gerektiğinde yeniden akmalıdır.

Anlamlı frontend değişikliklerinde en az:

- 1440x900
- 1280x720
- 1024x768
- 390x844
- 360x800

viewportları gerçek browser ile kontrol edilmelidir.

Kontrol listesi:

- horizontal overflow
- element overlap
- text clipping
- CTA hizası
- kart yüksekliği
- whitespace
- header/menu
- modal
- fixed/sticky widget
- slider
- image crop
- image sharpness
- mobile reflow

Görsel problem browser veya screenshot doğrulaması yapılmadan çözülmüş kabul edilmez.

---

## 7. Header ve ana navigasyon

Ana hizmet yapısı:

- Ajans Hizmetleri
- SEO Hizmetleri
- Sosyal Medya Hizmetleri
- Hazır Yazılımlar

Hazır Yazılımlar ana yolu:

`/hazir-scriptler`

Legacy URL'ler SEO analizi yapılmadan kaldırılmaz.

Admin kontrollü menü açma / kapama özellikleri korunmalıdır.

---

## 8. Ana sayfa

Özellikle aşağıdaki componentleri dikkatle yönetin:

- Hero Slider
- Tek platform, üç güçlü uzmanlık alanı
- Öne çıkan paketler
- Hazır yazılımlar
- Referanslar
- Müşteri yorumları
- Blog
- SSS
- Footer
- Canlı destek

### Hero Slider

- admin tarafından yönetilebilir olmalı
- desktop/mobile ayrı görsel desteklemeli
- bulanıklık ve gereksiz yeniden sıkıştırma olmamalı
- doğru aspect ratio kullanılmalı
- `object-fit` ve `object-position` bilinçli ayarlanmalı
- metin güvenli alanı korunmalı
- desktop/mobile crop ayrı değerlendirilmeli

### Uzmanlık kartları

Üç kart aynı layout sistemini kullanmalıdır.

Text / visual / CTA / chip alanları birbirine girmemelidir.

Tek karta özel layout hack'i kullanılmamalıdır.

Eski CSS specificity çatışmaları kökten temizlenmelidir.

---

## 9. Hazır yazılım kataloğu

Kanonik public yollar:

`/hazir-scriptler`

`/hazir-scriptler/{slug}`

Mevcut NetVera ürün slugları SEO açısından korunmalıdır.

Ürün yapısı en az şunları desteklemelidir:

- isim
- kategori
- kısa açıklama
- detaylı açıklama
- fiyat
- eski fiyat
- kapak
- galeri
- demo
- admin demo
- modüller
- teknik özellikler
- lisans
- SSS
- yorumlar
- SEO
- GEO
- AIO
- OG
- satın alma durumu
- demo yayın durumu

Yeni ürün admin panelden eklendiğinde yeni PHP/CSS kodu gerektirmemelidir.

Frontend veri odaklı çalışmalıdır.

---

## 10. Demo sistemi

Demo veri zinciri özellikle doğrulanmalıdır:

`script_products` veya eski kaynak

→ import

→ `nv_legacy_script_products.public_json`

→ `NetveraBridgeService`

→ `NetveraScriptController`

→ `netvera-script-detail.php`

Beklenen public ürün deneyimi:

### Canlı Demo

- Canlı Siteyi Görüntüle
- Demo Yönetim Paneli

Eğer ürün için yayınlanmasına açık public demo hesabı bulunuyorsa:

### Demo Yönetim Paneli

Admin E-posta / Kullanıcı Adı

`[değer] [Kopyala]`

Admin Şifresi

`[••••••••] [Göster] [Kopyala]`

`[Giriş Yap]`

Giriş Yap admin demo URL'sini yeni sekmede açmalıdır.

Credential gösterimi yalnızca:

- `demo_is_active = 1`
- `demo_is_public = 1`
- `demo_credentials_public = 1`

durumlarında yapılmalıdır.

Dolu credential alanının bulunması tek başına frontend'de gösterim izni değildir.

Gerçek müşteri, gerçek production admin, ödeme hesabı veya servis credential'ı frontend'e çıkarılamaz.

Sadece açıkça ürün tanıtımı için oluşturulmuş PUBLIC DEMO hesapları yayınlanabilir.

Public demo credential'ları source code içine hard-code edilmemelidir.

Mümkün olduğunda admin-managed database içeriği olarak saklanmalıdır.

Test fixturelarında gerçek credential kullanmayın; sentetik demo credential kullanın.

---

## 11. Demo import mimarisi

Demo import mantığı birden fazla PHP/CLI dosyasında birbirinden farklı şekilde tekrar edilmemelidir.

Tek authoritative import service tercih edilmelidir.

Controller, CLI veya admin import UI aynı service'i çağırmalıdır.

Import sırasında yalnızca gerekli ürün demo alanları okunmalıdır.

Customer/order/payment/API secret tablolarına dokunulmamalıdır.

Import eşleştirmesinde mevcut SEO kimliği korunmalıdır.

Legacy ID + slug eşleştirmesi kullanılıyorsa bunun güvenilirliği test edilmelidir.

Import sonrası:

- admin URL
- admin username/email
- admin password
- demo note
- public flags

gerçek DB kaydında doğrulanmalıdır.

---

## 12. Satış sistemi

Üründe veri durumuna göre:

- Satın Al
- Teklif Al
- Canlı Demo

aksiyonları gösterilebilir.

Satın Al ancak ürün gerçekten satışa açıksa görünmelidir.

Admin tarafından en az:

- price
- old price
- purchase enabled
- purchase/checkout destination
- demo enabled
- demo public

kontrol edilebilmelidir.

PayTR entegrasyonunda:

- callback
- webhook
- duplicate callback
- amount validation
- order state
- test/live mode

bozulmamalıdır.

Secret değerleri browser'a gönderilmemelidir.

---

## 13. Veritabanı

Tek kanonik temiz kurulum dosyası:

`well-known/database/database.sql`

olmalıdır.

Yeni bir ortamın kurulumu yalnızca bu dosya ile mümkün olmalıdır.

Runtime kod:

- silinmiş migration dosyalarını
- `database/migrations`
- eski `install.sql`
- eski `seeds.sql`

dosyalarını zorunlu olarak okumamalıdır.

Migration davranışı gerekiyorsa:

- canonical schema içine entegre edin
veya
- runtime kodunu mevcut tablo yapısına göre düzenleyin.

Silinmiş migration dosyasını sırf testi yeşile çevirmek için geri oluşturmayın.

Eski migration beklentili testleri yeni tek-SQL mimarisine göre güncelleyin.

Temiz kurulum sonrasında:

- admin açılmalı
- frontend açılmalı
- katalog çalışmalı
- ödeme yapılandırması bozulmamalı
- gerekli başlangıç verileri bulunmalı

---

## 14. Admin / frontend parity

Frontend'de yönetilebilir olan her özellik admin tarafında kontrol edilebilir olmalıdır.

Özellikle kontrol edin:

- menüler
- kategoriler
- paketler
- yazılımlar
- yazılım kategorileri
- demo
- fiyat / satış durumu
- blog
- SEO
- hero slider
- referanslar
- yorumlar
- footer
- KVKK
- gizlilik
- iletişim
- sosyal medya
- logo
- favicon
- görsel upload
- canlı destek
- ödeme
- SMM sağlayıcıları
- kuponlar
- siparişler

Frontend özelliği var ama admin kontrolü yoksa bunu teknik borç olarak raporlayın.

---

## 15. SMM sistemi

Mevcut mimari bozulmadan birden fazla sağlayıcı desteklenmelidir.

Amaç:

- provider API
- servisleri toplu çekme
- servis eşleme
- custom fiyat
- paket açıklaması
- target field tipi
- Instagram
- YouTube
- Shorts
- Reels

gibi farklı ürün davranışlarını desteklemektir.

Provider credential'ları frontend'e sızdırılmamalıdır.

---

## 16. SEO / GEO / AIO

SEO sistemleri kritik işlevdir.

Kontrol edilmesi gerekenler:

- title
- meta description
- canonical
- slug
- OG title
- OG description
- OG image
- schema
- H1/H2/H3
- alt
- internal links
- focus keyword
- secondary keywords

Spam SEO üretmeyin.

Indexed URL değişikliği yapmayın.

Gerekirse 301 mapping kullanın.

---

## 17. Footer ve destek

Footer premium ve işlevsel olmalıdır.

KVKK, Gizlilik, Kullanım Şartları ve İletişim sayfaları gerçekten çalışmalıdır.

Ödeme marka görselleri için kaliteli/local SVG veya güvenilir asset kullanın.

Canlı destek:

- desktop/mobile çalışmalı
- ekranı kapatmamalı
- aç/kapat düzgün olmalı
- kullanıcı bilgilerini güvenli işlemeli
- Telegram/WhatsApp entegrasyonlarını bozmayın

---

## 18. Güvenlik

Korunması zorunlu:

- prepared SQL / injection koruması
- output escaping
- CSRF
- upload validation
- URL validation
- secret isolation
- production error sanitization

Aşağıdakileri loglamayın:

- passwords
- API keys
- payment secrets
- customer secrets
- private credentials

Public demo credential bile gereksiz yere loglanmamalıdır.

---

## 19. Test standardı

PHP değişikliğinde:

`php -l`

çalıştırın.

İlgili unit/integration/QA testlerini çalıştırın.

Mevcut kırık testleri otomatik kabul edilen baseline saymayın.

Her kırmızı test için:

- gerçekten regresyon mu
- eski mimari beklentisi mi
- yanlış fixture mı

belirleyin.

Eski beklentili test ise testi gerçek güncel mimariye göre güncelleyin.

Testi sırf yeşil yapmak için production davranışını bozmayın.

---

## 20. Bilinen başlangıç teknik borçları

İlk audit sırasında görülen durumlar:

- çalışma ağacında çok sayıda `.sync-conflict-*` dosyası olabilir
- bunlara dokunulmayacaktır
- bazı testler eski migration dosyalarını bekliyor olabilir
- bazı runtime sınıfları kaldırılmış `database/migrations` klasörüne bağlı olabilir
- bazı UI testleri eski `nv75-*` selectorlarını bekliyor olabilir
- demo import kodu birden fazla yerde tekrarlanmış olabilir
- eski CSS sürüm/override katmanları bulunabilir

Bunları yeni hacklerle örtmeyin.

Tek tek kök neden çözümü uygulayın.

---

## 21. Görev tamamlama raporu

Her önemli iş sonunda kısa rapor verin:

- kök neden
- değiştirilen dosyalar
- kaldırılan/etkisizleştirilen eski kod
- yapılan testler
- desktop sonucu
- mobile sonucu
- bilinen kalan riskler
- commit hash

“Düzeltildi” ifadesini yalnızca doğrulama tamamlandıysa kullanın.
