# NetVera Teknoloji Yazılım — Sosyal Medya / Dijital Hizmet SEO, GEO ve AIO Yayın Kontrolü

**Kapsam:** Yalnızca mevcut sosyal medya, Google, reklam, içerik, grafik ve SEO hizmetleri kategorileri. **Hazır Yazılımlar /hazir-scriptler/, lisanslar ve satın alınan yazılım ürünleri kesinlikle kapsam dışıdır.**

## Canlıya geçmeden önce

1. \`APP_URL\` ve \`site_url\` değerlerini gerçek cPanel hedefi olan \`https://netvera.tr\` ile eşleştirin. Eski siteden domain taşınıyorsa, önce eski URL → yeni URL yönlendirme eşlemesini planlayın. Sadece marka adını değiştirmek URL taşımak değildir.
2. \`APP_ENV=production\`, \`APP_DEBUG=false\`, lisans bypass kapalı olmalıdır. Eski SQL yedeğindeki müşteriler ve lisanslar erişim kontrolü doğrulanmadan aktarılmaz.
3. Admin → Site Ayarları: yeni NetVera renk paletini veya alternatif paleti seçip kaydedin. Kaydetme \`theme_preset_enabled=1\` ayarını etkinleştirir; site header, footer, ana paket CTA, kategori CTA, logonun SVG renkleri ve vurgular bu palete bağlanır.
4. Admin → NetVera Gelen Kutusu: masaüstünde konuşma listesi / mesaj geçmişi / yanıt formu; mobilde ayrı \`/admin/cep\` paneli. İkisi de aynı mesajları kullanır; PWA bunları değiştirmez.
5. Admin → Kategoriler → **SEO / GEO Profillerini Uygula**: Mevcut URL'leri ve SEO'su elle hazırlanmış alanları koruyup yalnız eski marka veya boş meta alanlarını doldurur. İşlem tekrarlanabilir; yeni kategori oluşturmaz. SEO alanları sayfa açılırken de kademeli güvenli varsayılanlarla hazırlanır.
6. \`php tests/netvera-brand-theme-seo-chat-qa.php\` ve GitHub Actions PHP Lint başarılı olmalıdır. Browser QA ekran genişliği 390/768/1440 için incelenmelidir.
7. Gerçek ürün özellikleri, gerçek müşteri değerlendirmeleri ve gerçek teslimat koşulları korunmalıdır. Deneyim ve değerlendirme uydurulmaz. Satış platformu politikaları dikkate alınmalıdır.

## Arama niyeti haritası (sayısal Semrush verisi olmadan hazırlanan editoryal küme)

| Kategori | Odak ifade | Yakın ticari aramalar | Niyet |
| --- | --- | --- | --- |
| Instagram Takipçi | instagram takipçi al | instagram takipçi satın al; instagram takipçi paketleri; fiyatları | Ticari/işlemsel |
| Instagram Beğeni | instagram beğeni al | instagram beğeni satın al; gönderi beğenisi | Ticari/işlemsel |
| Instagram Reels | instagram reels izlenme | reels izlenme al; instagram video izlenme | Ticari/işlemsel |
| TikTok Takipçi | tiktok takipçi al | tiktok takipçi satın al; takipçi hizmeti | Ticari/işlemsel |
| TikTok İzlenme | tiktok izlenme al | tiktok video izlenme; izlenme satın al | Ticari/işlemsel |
| YouTube Abone | youtube abone | youtube abone al; youtube abone paketleri | Ticari |
| YouTube İzlenme | youtube izlenme | youtube izlenme hizmeti; video tanıtımı | Ticari/araştırma |
| Sosyal Medya Yönetimi | sosyal medya yönetimi | instagram yönetimi; sosyal medya ajansı | Ticari |
| Google İşletme | google işletme profili optimizasyonu | google harita seo; yerel seo | Ticari |
| SEO | seo hizmeti | teknik seo; seo danışmanlığı | Ticari |
| Dijital Reklam | google ads yönetimi | meta reklam yönetimi; dijital reklam ajansı | Ticari |
| İçerik Üretimi | sosyal medya içerik üretimi | reels düzenleme; sosyal medya tasarımı | Ticari |

**Metodoloji:** Bu ifadeler arama niyeti ve hizmet semantiğine göre editoryal taslaklardır; Semrush aranma hacmi/KD sıralaması **değildir**. Kullanıcının kurduğu Semrush eklentisi mevcut sohbetin kullanılabilir sorgu araçları arasında görünmediği için canlı aylık hacim/KD/CPC metrikleri çekilmedi. Uydurma veya eski tarihli rakamlar yayımlanmadı.

Semrush Keyword Magic Tool'da ülke **Türkiye**, arama dili **Türkçe**, karşılaştırma metrikleri **Volume, KD %, PKD %, Intent, CPC** seçilmeli. Yüksek hacim tek başına tercih sebebi değildir: yüksek rekabet, markanın mevcut otoritesi, arama niyeti ve gerçek hizmet uyumu birlikte değerlendirilmelidir.

## İç SEO ve AI erişilebilirliği

- Kategori URL'si aynı kalır: \`/kategori/{orijinal-slug}\`.
- SEO başlığı, meta açıklaması, OG başlığı/açıklaması ve odak kelime mevcut anlamlı içerikler silinmeden doldurulur.
- Kategoriye özel editoryal sorular ve doğrudan açıklayıcı cevaplar insanlara da görünür.
- \`BreadcrumbList\` ve \`CollectionPage / ItemList\` şemaları yalnız gerçek mevcut kategori ve yayındaki ürünlerden oluşturulur; hayali fiyat, stok, review veya garanti eklenmez.
- Yazılım ürünleri için \`CategorySearchBlueprint::isSoftware()\` koruması vardır.
- Yeni URL'ler kontrolsüz üretilmez; kopya anahtar kelime kapı sayfaları açılmaz.
- Gerçek görsellerin alt metinleri ve ürün özellikleri editörden yönetilir. Ana sayfa ve kategori ürünlerine sahte puan veya açıklama eklenmez.
- Birinci sıra/AI Overview gösterimi için teknik bir garanti verilemez. İyileştirmeler yayınlandıktan sonra Search Console gösterim, tıklama, CTR, indeks kapsamı ve dönüşüm verileriyle değerlendirilmelidir.

## Test edilen ve edilmemiş ortamlar

GitHub Actions PHP syntax + çevrimdışı regresyon kontrolleri otomatik çalışır. Canlı cPanel veritabanı, ödeme callback, gerçek müşteri bilgileri, mobil PWA bildirimleri ve gerçek Semrush hesabındaki anahtar kelime metrikleri kod testiyle doğrulanamaz; yayından önce gerçek uçtan uca kabul testleri gerekir.

Kaynak: Google Search Central Search Essentials / Structured Data Quality Guidelines; Semrush Keyword Magic Tool ve resmi Keyword Reports API dokümanları.
