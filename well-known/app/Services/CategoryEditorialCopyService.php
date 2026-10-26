<?php
namespace App\Services;

/**
 * Search-intent editorial reference for NetVera SERVICE taxonomy.
 * Curated descriptive copy, not keyword volume, guaranteed rankings, or fake proof.
 * Read-only category presentation fallback; original database rows/slugs remain owned by admin.
 * Research context: Google Search Central (people-first), SEOEvaluate, SEOBUL,
 * MOWA social media management, WeClick/Alpa software services (October 2026).
 */
final class CategoryEditorialCopyService
{
    private const ROOTS=[
            'google-hizmetleri'=>'Google İşletme Profili, Haritalar ve arama hizmetlerinde doğrulanmış firma bilgileri, kategori düzeni, fotoğraf alanları ve yerel kullanıcı sorularını birlikte değerlendirin. İşletmenizin dijital görünürlüğüne uygun kapsamı inceleyin.',
            'instagram-hizmetleri'=>'Instagram gönderi, Reels, hikâye ve profil hizmetlerini içerik hedeflerine göre keşfedin. Yönetim çalışmaları ile miktar bazlı paketlerin farklı olduğunu, her seçeneğin teslim ve kullanım koşullarını inceleyin.',
            'tiktok-hizmetleri'=>'TikTok kısa video, profil ve canlı yayın hizmetlerinde hedef bağlantısına, içerik biçimine ve paket kapsamına göre karar verin. Üretim stratejisi ile miktar odaklı etkileşim paketlerini ayrı değerlendirin.',
            'youtube-hizmetleri'=>'YouTube kanalı ve videoları için içerik, Shorts, abone ve izlenme seçeneklerini karşılaştırın. Kanal veya video bağlantısı, paket kapsamı ve platform kuralları kararınızın parçası olsun.',
            'facebook-hizmetleri'=>'Facebook sayfası, gönderi ve video içerikleri için sunulan hizmet seçeneklerini keşfedin. Sayfa yönetimi, reklam ve ölçülebilir içerik hedeflerini ayrı ayrı değerlendirerek uygun kapsamı belirleyin.',
            'seo-hizmetleri'=>'Teknik SEO denetimi, arama niyeti analizi, sayfa içi iyileştirme, yerel arama görünürlüğü ve içerik stratejisini birlikte planlayın. Search Console ve dönüşüm verileriyle önceliklerinizi belirleyin.',
            'dijital-reklam'=>'Google Ads ve sosyal medya reklamlarında hedef kitlenizi, teklif stratejisini, reklam görsellerini ve dönüşüm izlemeyi birlikte planlayın. Bütçe ve sonuçları şeffaf biçimde değerlendirin.',
            'itibar-yonetimi'=>'Markanız hakkında oluşan gerçek müşteri geri bildirimlerini takip edin, şikâyetlere tutarlı yanıtlar hazırlayın ve iletişim süreçlerinizi iyileştirin. Sahte değerlendirme yerine güvenilir müşteri deneyimini önceliklendirin.',
            'x-twitter-hizmetleri'=>'X üzerindeki hesap, gönderi ve etkileşim hizmetlerini hedeflediğiniz içerik ve hesap türüne göre karşılaştırın. Paket miktarlarını, geçerli bağlantıları ve teslimat koşullarını inceleyin.',
            'threads-hizmetleri'=>'Threads hesabınızın gönderileri, etkileşimi ve topluluk iletişimi için sunulan seçenekleri karşılaştırın. Hedef gönderi veya profil bağlantısı ile hizmet koşullarını sipariş öncesinde gözden geçirin.',
            'telegram-hizmetleri'=>'Telegram kanal ve grupları için topluluk, üye, görüntülenme ve reaksiyon hizmetlerini ayrı seçenekler olarak inceleyin. Davet bağlantıları, erişim koşulları ve topluluk yönetimi ihtiyaçlarını belirleyin.',
            'spotify-hizmetleri'=>'Spotify sanatçı profilleri, parça ve çalma listeleri için sunulan keşif ve etkileşim seçeneklerini inceleyin. Parça bağlantısı, dinlenme kapsamı ve platform politikalarına uygunluk koşullarını kontrol edin.',
            'discord-hizmetleri'=>'Discord sunucusu ve topluluk düzeni için üyelik, çevrimiçi etkileşim ve sunucu özelliklerini karşılaştırın. Davet bağlantısı ve yetki gereksinimleri gibi teknik ayrıntıları önceden kontrol edin.',
            'linkedin-hizmetleri'=>'LinkedIn profesyonel profil ve şirket sayfası hizmetlerini, B2B hedefleriniz ve içerik stratejiniz kapsamında değerlendirin. Profil, ağ ve gönderi etkileşimlerinin farklı amaçlara hizmet ettiğini unutmayın.',
            'twitch-hizmetleri'=>'Twitch yayıncıları için takipçi, yayın izlenmesi ve canlı izleyici seçeneklerini kanal hedefi ve yayın akışına göre değerlendirin. Paketlerin teknik koşullarını ve platform kurallarını karşılaştırın.',
            'web-site-hizmetleri'=>'Kurumsal web sitesi, kampanya landing page ve WordPress çözümlerini mobil uyumluluk, hız, içerik yönetimi ve teknik SEO ihtiyaçlarınıza göre karşılaştırın. Tasarım ve bakım kapsamını önceden netleştirin.',
            'e-ticaret-hizmetleri'=>'Online mağaza kurulumu, ürün ve stok yönetimi, ödeme-kargo akışları ve pazaryeri bağlantılarını iş modelinize göre planlayın. Kullanıcı deneyimini ve teknik entegrasyon gereksinimlerini birlikte değerlendirin.',
            'mobil-uygulama-hizmetleri'=>'iOS ve Android mobil uygulamalarında ekran akışları, kullanıcı hesapları, bildirimler, API bağlantıları ve mağaza yayın hazırlığını proje kapsamına göre planlayın. Geliştirme ve bakım adımlarını önceden netleştirin.',
            'icerik-uretimi'=>'Web sitesi, blog ve sosyal medya için özgün içerik üretimini hedef kitle, arama niyeti ve yayın kanalıyla birlikte planlayın. Editoryal doğruluk, başlık yapısı, görseller ve revizyon sürecini değerlendirin.',
            'grafik-tasarim'=>'Kurumsal kimlik, logo, reklam görseli ve sosyal medya tasarımlarında tutarlı marka dili oluşturun. Kullanım alanı, teslim dosyaları, ölçüler, revizyon ve lisans kapsamını netleştirin.',
            'yerel-isletme-hizmetleri'=>'Yerel işletmelerin Google profil bilgileri, harita görünürlüğü, QR iletişim materyalleri ve bölgesel reklam çalışmalarını birlikte değerlendirin. Gerçek adres ve iletişim bilgileri temel alınır.',
    ];
    private const SPECIAL=[
            'google-harita'=>'Google Haritalar konum kaydı, adres doğruluğu, kategori ve çalışma saati bilgilerinin tutarlılığı gibi yerel keşfi etkileyen alanları inceleyin. Harita erişimini güncel işletme bilgileriyle destekleyin.',
            'google-isletme-profili'=>'Google İşletme Profili kurulum ve optimizasyonunda doğrulama, kategori, hizmet alanı, telefon, görseller ve işletme güncellemelerini kontrol edin. Bilgiler gerçek faaliyetinizle tutarlı olmalıdır.',
            'google-yorum'=>'Google müşteri yorumlarını takip etme, kurallara uygun yanıt hazırlama ve gerçek geri bildirimlerden hizmet iyileştirme konusunda bilgi alın. Sahte değerlendirme üretimini önermiyoruz.',
            'google-5-yildiz'=>'Google değerlendirme puanı kullanıcıların gerçek deneyimlerinden oluşur. Değerlendirme politikalarını, tarafsız geri bildirim isteme yöntemlerini ve şikâyetlere yanıt süreçlerini öğrenin.',
            'google-yorum-begeni'=>'Google yorum etkileşimlerinin platform kuralları ve görünürlük açısından ne anlama geldiğini inceleyin. Yapay beğeni ve değerlendirme yerine gerçek müşteri iletişimine odaklanın.',
            'seo-analizi'=>'SEO analizi ile taranabilirlik, indeksleme, sayfa hızı, mobil deneyim, içerik niyeti ve iç bağlantı yapısını denetleyin. Hataları etki ve uygulama maliyetine göre önceliklendirin.',
            'backlink-paketi'=>'Backlink çalışmasında bağlantıların alaka düzeyi, editoryal niteliği ve kaynak güvenilirliği önemlidir. Yapay bağlantı şemaları yerine dijital PR ve doğal marka referanslarını değerlendirin.',
            'yerel-seo'=>'Yerel SEO stratejisinde hizmet bölgeleri, Google İşletme Profili, gerçek adres bilgileri ve yerel arama niyetine uygun içerikleri birlikte planlayın. Aynı metni şehir adını değiştirerek çoğaltmayın.',
            'teknik-seo'=>'Teknik SEO kapsamında tarama ve indeksleme, canonical etiketleri, yönlendirmeler, Core Web Vitals, site mimarisi ve yapılandırılmış veri sorunlarını ele alın.',
            'icerik-seo'=>'İçerik SEO çalışmalarında arama niyetini, kullanıcı sorularını, bilgi doğruluğunu, konu kapsamını ve anlamlı iç bağlantıları temel alın. Metinleri anahtar kelime tekrarı için değil kullanıcı için üretin.',
            'google-ads'=>'Google Ads arama, görüntülü reklam ve performans kampanyalarında dönüşüm hedeflerini, ölçüm kodlarını, hedeflemeyi, negatif anahtar kelimeleri ve bütçe kontrolünü birlikte değerlendirin.',
            'meta-reklam'=>'Meta reklam çalışmalarında Facebook ve Instagram hedef kitlesi, kreatif çeşitleri, reklam hesapları, piksel veya dönüşüm API kurulumunu ve raporlamayı planlayın.',
            'instagram-reklam'=>'Instagram reklamlarında Reels, hikâye ve akış yerleşimlerine uygun görseller, hedef kitle, bütçe ve dönüşüm olayları belirleyerek kampanyaları ölçülebilir hedeflere bağlayın.',
            'tiktok-reklam'=>'TikTok reklam kampanyalarında dikey video formatı, ilk saniye kurgusu, hedef kitle ve dönüşüm ölçümü öne çıkar. Kreatif testlerini ve bütçe sınırlarını planlayın.',
            'youtube-reklam'=>'YouTube reklamlarında video formatlarını, izleme hedefini, kitle segmentlerini ve dönüşüm takibini bir arada değerlendirin. Mesajınızı reklam süresine ve yayın alanına uyarlayın.',
            'marka-itibar-yonetimi'=>'Marka itibarını gerçek müşteri deneyimleri, sosyal dinleme ve profesyonel yanıt süreçleriyle yönetin. Olumlu ve olumsuz görüşleri değerlendiren şeffaf bir iletişim akışı kurun.',
            'sikayet-yonetimi'=>'Şikâyet yönetiminde başvuruları sınıflandırın, yanıt sorumlularını ve çözüm sürelerini belirleyin. Geri bildirimi müşteriye açık ve izlenebilir biçimde sonuçlandırın.',
            'olumsuz-yorum-yonetimi'=>'Olumsuz yorumlara sakin, açıklayıcı ve çözüm odaklı yanıtlar hazırlayın. Gerçek yorumları gizlemek veya manipüle etmek yerine sorunları kayıt altına alıp iyileştirme fırsatına dönüştürün.',
            'online-itibar-paketi'=>'Online itibar yönetimi kapsamını izleme, yanıt taslakları, müşteri memnuniyeti takibi ve kriz iletişimi başlıklarına ayırın. Hangi çıktının sağlanacağını paket şartlarında açıkça görün.',
            'web-tasarim'=>'Profesyonel web tasarımında mobil öncelikli arayüz, erişilebilirlik, hızlı yükleme, dönüşüm odaklı sayfa akışı ve yönetilebilir içerik yapısını birlikte planlayın.',
            'landing-page'=>'Kampanya landing page tasarımında tek ana hedef, net değer önerisi, anlaşılır form, mobil hız ve ölçülebilir dönüşüm olayları belirleyin. Reklam metni ile açılış sayfası tutarlı olmalıdır.',
            'wordpress-site'=>'WordPress web sitesi kurulumunda tema ve eklenti seçimi, güvenlik, yedekleme, mobil performans ve içerik yönetimi ihtiyaçlarını değerlendirin. Sürdürülebilir bakım planı oluşturun.',
            'eticaret-site'=>'E-ticaret sitesi geliştirmede ürün katalogları, filtreler, sepet, ödeme, kargo, mesafeli satış bilgileri ve mobil alışveriş deneyimini birlikte tasarlayın.',
            'pazaryeri-entegrasyonu'=>'Pazaryeri entegrasyonunda ürün, fiyat, stok, sipariş ve kargo durumlarının sistemler arasında nasıl senkronize edileceğini belirleyin. Hata yönetimi ve veri eşleştirmesi planlayın.',
            'opencart-woocommerce'=>'OpenCart ve WooCommerce altyapılarını ürün sayısı, ödeme seçenekleri, eklenti gereksinimleri, hız ve bakım maliyeti açısından karşılaştırın; mağazanıza uygun çözümü seçin.',
            'mobil-uygulama'=>'Mobil uygulama geliştirmede kullanıcı akışları, Android/iOS hedefleri, API yapısı, bildirimler, güvenlik ve mağaza yayını için gerekli adımları netleştirin.',
            'ios-android-app'=>'iOS ve Android uygulama geliştirmede ortak kod tabanı ile platforma özel gereksinimleri karşılaştırın. Test, erişilebilirlik, bildirim ve dağıtım süreçlerini planlayın.',
            'app-store-yayinlama'=>'App Store ve Google Play yayını için uygulama içi gizlilik metinleri, ekran görüntüleri, mağaza açıklamaları, hesap gereksinimleri ve inceleme sürecini hazırlayın.',
            'blog-makale'=>'Blog makalesi üretiminde kullanıcı sorularını, konunun güncelliğini, doğrulanabilir kaynakları, H2/H3 düzenini ve ilgili hizmetlere doğal iç bağlantıları değerlendirin.',
            'sosyal-medya-icerik'=>'Sosyal medya içerik üretiminde kanal bazlı formatlar, Reels/Shorts kurguları, marka dili, yayın takvimi ve görsel kalite standartlarını belirleyin.',
            'urun-aciklama'=>'Ürün açıklamalarında kullanım amacı, teknik özellikler, karşılaştırma kriterleri ve gerçek avantajları açıkça anlatın. Tekrarlanan veya abartılı satış metinlerinden kaçının.',
            'logo-tasarim'=>'Logo tasarımında markanın sektörü, karakteri, kullanım ölçüleri, renk sistemi ve vektörel dosya teslimlerini önceden tanımlayın.',
            'sosyal-medya-gorsel'=>'Instagram, Facebook ve diğer platformlara yönelik görsellerde format ölçülerini, tipografi hiyerarşisini, marka renklerini ve okunabilirliği esas alın.',
            'reklam-banner'=>'Reklam banner tasarımında hedef mesaj, platform ölçüsü, yüksek kaliteli görsel, açık harekete geçirici ifade ve marka bütünlüğüne odaklanın.',
            'google-profil-kurulum'=>'Google İşletme Profili kurulumu sırasında kategori, web adresi, çalışma saatleri, hizmet alanları ve doğrulama adımlarını işletmenin gerçek bilgilerine göre tamamlayın.',
            'qr-yorum-karti'=>'QR yorum kartları müşterilerin işletme profilindeki gerçek değerlendirme alanına kolay erişmesini sağlar. Tarafsız geri bildirim isteme ve platform politikalarına uyum önemlidir.',
            'harita-optimizasyon'=>'Harita optimizasyonunda konum bilgilerinin doğruluğu, işletme kategorileri, hizmet açıklamaları ve görsel güncellemelerini inceleyin. Yanlış adres veya sahte konum kullanmayın.',
            'yerel-reklam'=>'Yerel reklam kampanyalarında hizmet verilebilen gerçek bölgeleri, hedef müşteri kitlesini ve iletişim dönüşümlerini dikkate alın. Reklam performansını uygun raporlarla takip edin.',
    ];

    public static function description(array $category):string
    {
        $slug=(string)($category['slug']??'');
        if(isset(self::SPECIAL[$slug]))return self::SPECIAL[$slug];
        if(isset(self::ROOTS[$slug]))return self::ROOTS[$slug];
        $name=trim((string)($category['name']??'Dijital hizmet'));
        $parts=explode('-', $slug);
        $network=(string)($parts[0]??'');
        $brands=[
            'instagram'=>'Instagram','tiktok'=>'TikTok','youtube'=>'YouTube',
            'facebook'=>'Facebook','x'=>'X (Twitter)','threads'=>'Threads',
            'telegram'=>'Telegram','spotify'=>'Spotify','discord'=>'Discord',
            'linkedin'=>'LinkedIn','twitch'=>'Twitch'
        ];
        $brand=$brands[$network]??'';
        $action=implode('-',$parts);
        if($brand!==''){
            $focus=match(true){
                str_contains($action,'takipci'),str_contains($action,'abone'),str_contains($action,'uye'),str_contains($action,'baglanti')=>'hesap, profil veya topluluk hedefi',
                str_contains($action,'yorum')=>'yorum içeriği ve gönderi bağlantısı',
                str_contains($action,'begeni'),str_contains($action,'retweet'),str_contains($action,'reaksiyon')=>'gönderi etkileşimi ve içerik bağlantısı',
                str_contains($action,'kaydetme')=>'içerik kaydetme hedefi',
                str_contains($action,'reels'),str_contains($action,'shorts'),str_contains($action,'izlenme'),str_contains($action,'goruntulenme')=>'video, parça veya yayın bağlantısı',
                str_contains($action,'canli'),str_contains($action,'online')=>'canlı yayın ya da çevrimiçi topluluk koşulları',
                str_contains($action,'boost')=>'sunucu seviyesi ve teknik erişim gereksinimleri',
                str_contains($action,'playlist')=>'çalma listesi bağlantısı ve parça yerleşimi',
                default=>'hesap veya içerik bağlantısı'
            };
            $usage=match(true){
                str_contains($action,'profil-ziyareti')=>'profil ziyaretlerinin hizmet kapsamı ve ölçüm yöntemi',
                str_contains($action,'yorum')=>'değerlendirme politikaları ve özgün kullanıcı geri bildirimi',
                str_contains($action,'playlist')=>'çalma listesi keşfi ve platformun içerik kuralları',
                str_contains($action,'canli')=>'yayın zamanı, katılım biçimi ve platform kuralları',
                default=>'miktar, süre ve hizmetin platform koşulları'
            };
            return $name.' seçeneğinde '.$brand.' için '.$focus.' belirleyin. Paket açıklamasında '.$usage.' hakkında sunulan bilgileri inceleyin; gerçek kullanıcı katılımı veya kalıcı sonuç garantisi varsaymayın.';
        }
        return CategoryHeroService::defaults($category)['description'];
    }
}
