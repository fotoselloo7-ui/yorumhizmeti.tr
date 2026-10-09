<?php
namespace App\Services;
use App\Core\Database;
/** Editorial intent blueprint, not measured keyword volumes. Never modifies URLs. */
final class CategorySearchBlueprint {
  public static function profiles(): array {
    return [
  'instagram-hizmetleri' => [
    'slug' => 'instagram-hizmetleri',
    'title' => 'Instagram Takipçi, Beğeni ve Reels Hizmetleri | NetVera',
    'desc' => 'Instagram takipçi, beğeni, Reels izlenme ve etkileşim hizmetlerini inceleyin. Paket kapsamı, teslimat şartları ve sipariş detaylarını NetVera\'da karşılaştırın.',
    'focus' => 'instagram takipçi al',
    'secondary' => 'instagram takipçi al, instagram beğeni, instagram reels izlenme',
    'question' => 'Instagram hizmetlerinde paketler nasıl seçilir?',
    'answer' => 'İhtiyacınıza göre takipçi, beğeni veya Reels izlenme seçeneklerini karşılaştırın; açıklama, teslimat ve hizmet koşullarını sipariş öncesinde inceleyin.'
  ],
  'instagram-takipci' => [
    'slug' => 'instagram-takipci',
    'title' => 'Instagram Takipçi Al | Takipçi Paketleri – NetVera',
    'desc' => 'Instagram takipçi paketlerinin kapsamını, adetlerini ve teslimat koşullarını karşılaştırın. Profilinize uygun seçenekler ve şifresiz sipariş bilgileri NetVera\'da.',
    'focus' => 'instagram takipçi al',
    'secondary' => 'instagram takipçi hizmeti, instagram takipçi paketleri, instagram takipçi fiyatları',
    'question' => 'Instagram takipçi hizmeti seçerken nelere dikkat edilmeli?',
    'answer' => 'Paket adedini, hizmetin kapsamını, hesap gereksinimlerini ve olası teslimat değişkenlerini inceleyin. Hesap şifrenizi paylaşmadan sipariş hedefini belirtin.'
  ],
  'instagram-begeni' => [
    'slug' => 'instagram-begeni',
    'title' => 'Instagram Beğeni Al | Gönderi Beğeni Paketleri – NetVera',
    'desc' => 'Instagram gönderi beğenisi hizmetlerinin kapsamını ve paket seçeneklerini karşılaştırın. Hedef gönderi bağlantısını ve teslimat koşullarını sipariş öncesinde inceleyin.',
    'focus' => 'instagram beğeni al',
    'secondary' => 'instagram beğeni paketleri, instagram gönderi beğeni, instagram beğeni hizmeti',
    'question' => 'Instagram beğeni paketi için ne gerekir?',
    'answer' => 'Genellikle hedef gönderinin bağlantısı gerekir; hangi bağlantıların desteklendiğini, miktar sınırlarını ve teslimat koşullarını seçtiğiniz paket üzerinden kontrol edin.'
  ],
  'instagram-reels-izlenme' => [
    'slug' => 'instagram-reels-izlenme',
    'title' => 'Instagram Reels İzlenme Hizmetleri – NetVera',
    'desc' => 'Instagram Reels izlenme paketlerini, kapsamlarını ve video bağlantısı koşullarını inceleyin. Seçenekleri şeffaf hizmet bilgileriyle karşılaştırın.',
    'focus' => 'instagram reels izlenme',
    'secondary' => 'instagram reels izlenme al, reels görüntülenme, instagram video izlenme',
    'question' => 'Reels izlenme hizmeti için hangi bilgi gerekir?',
    'answer' => 'Hedef Reels videosunun bağlantısını belirtin. Hizmet kapsamı ve tahmini teslimat süresi seçilen pakete göre değişebilir.'
  ],
  'tiktok-hizmetleri' => [
    'slug' => 'tiktok-hizmetleri',
    'title' => 'TikTok Takipçi, Beğeni ve İzlenme Hizmetleri – NetVera',
    'desc' => 'TikTok takipçi, beğeni ve video izlenme hizmetlerini karşılaştırın. Paket miktarlarını, bağlantı gereksinimlerini ve teslimat detaylarını inceleyin.',
    'focus' => 'tiktok takipçi al',
    'secondary' => 'tiktok izlenme, tiktok beğeni al, tiktok takipçi paketleri',
    'question' => 'TikTok hizmetleri nasıl seçilir?',
    'answer' => 'Takipçi, beğeni veya video izlenme ihtiyacınızı belirleyin. Her paketin miktarını, gerekli kullanıcı adı veya bağlantı alanını ve teslimat şartlarını kontrol edin.'
  ],
  'tiktok-takipci' => [
    'slug' => 'tiktok-takipci',
    'title' => 'TikTok Takipçi Al | Takipçi Paketleri – NetVera',
    'desc' => 'TikTok takipçi hizmetlerini adet, kapsam ve teslimat koşullarına göre karşılaştırın. Paket detaylarını inceleyerek hesabınıza uygun seçeneği belirleyin.',
    'focus' => 'tiktok takipçi al',
    'secondary' => 'tiktok takipçi hizmeti, tiktok takipçi fiyatları',
    'question' => 'TikTok takipçi paketleri nasıl karşılaştırılır?',
    'answer' => 'Paket adetleri, teslimat koşulları ve hesap hedefi için istenen bilgiler hizmetler arasında değişebilir. Siparişten önce açıklamaları karşılaştırın.'
  ],
  'tiktok-izlenme' => [
    'slug' => 'tiktok-izlenme',
    'title' => 'TikTok İzlenme Al | Video İzlenme Paketleri – NetVera',
    'desc' => 'TikTok video izlenme paketlerinin adet, teslimat ve bağlantı gereksinimlerini karşılaştırın. Hedef videonuza uygun hizmeti seçin.',
    'focus' => 'tiktok izlenme al',
    'secondary' => 'tiktok video izlenme, tiktok görüntülenme',
    'question' => 'TikTok izlenme hizmetinde hangi bilgi gerekir?',
    'answer' => 'İzlenme hizmeti için video bağlantısını hazırlayın ve seçilen paketin kapsamını, miktarını ve teslimat süresini kontrol edin.'
  ],
  'youtube-hizmetleri' => [
    'slug' => 'youtube-hizmetleri',
    'title' => 'YouTube Abone, İzlenme ve Video Hizmetleri – NetVera',
    'desc' => 'YouTube abone, video izlenme ve kanal hizmetlerini inceleyin. Paket kapsamı, kanal veya video bağlantısı ve teslimat koşullarını karşılaştırın.',
    'focus' => 'youtube abone',
    'secondary' => 'youtube abone paketleri, youtube izlenme, youtube video tanıtımı',
    'question' => 'YouTube hizmetleri seçerken hangi bilgiler önemlidir?',
    'answer' => 'Abone ve video izlenme hizmetlerinin hedef bilgileri farklı olabilir. Kanal veya video bağlantısını doğrulayın, hizmet koşullarını ve platform kurallarını inceleyin.'
  ],
  'youtube-abone' => [
    'slug' => 'youtube-abone',
    'title' => 'YouTube Abone Hizmetleri | Abone Paketleri – NetVera',
    'desc' => 'YouTube abone paketlerinin kapsamını, sipariş şartlarını ve kanal bağlantısı gereksinimlerini inceleyin. Mevcut seçenekleri NetVera\'da karşılaştırın.',
    'focus' => 'youtube abone',
    'secondary' => 'youtube abone al, youtube abone paketleri',
    'question' => 'YouTube abone paketlerinde hangi bilgiler gerekir?',
    'answer' => 'Seçilen hizmetin kanal bağlantısı ve kapsam koşullarını inceleyin; performans veya kalıcılık konusunda doğrulanmamış vaatlere güvenmeyin.'
  ],
  'facebook-hizmetleri' => [
    'slug' => 'facebook-hizmetleri',
    'title' => 'Facebook Beğeni ve Sayfa Hizmetleri | NetVera',
    'desc' => 'Facebook sayfa beğenisi, gönderi etkileşimi ve dijital yönetim seçeneklerini inceleyin. Paket özelliklerini ve hizmet koşullarını karşılaştırın.',
    'focus' => 'facebook beğeni',
    'secondary' => 'facebook sayfa beğeni, facebook hizmetleri',
    'question' => 'Facebook hizmetleri nasıl karşılaştırılır?',
    'answer' => 'Sayfa ve gönderi hizmetleri farklı hedef bağlantıları kullanır. Sipariş öncesinde ilgili hizmetin kapsamını ve gerekli bilgileri kontrol edin.'
  ],
  'google-hizmetleri' => [
    'slug' => 'google-hizmetleri',
    'title' => 'Google İşletme Profili ve Harita Hizmetleri | NetVera',
    'desc' => 'Google İşletme Profili optimizasyonu, harita görünürlüğü ve dijital işletme yönetimi hizmetlerini inceleyin. NetVera ile kapsam ve teklif seçeneklerini karşılaştırın.',
    'focus' => 'google işletme profili optimizasyonu',
    'secondary' => 'google harita seo, google işletme profili düzenleme, yerel seo hizmeti',
    'question' => 'Google İşletme Profili optimizasyonu neleri kapsar?',
    'answer' => 'Doğru işletme kategorileri, adres ve iletişim bilgilerinin güncellenmesi, fotoğraflar ve yerel arama görünürlüğüne yönelik iyileştirmeler hizmet kapsamına göre uygulanır.'
  ],
  'seo-hizmetleri' => [
    'slug' => 'seo-hizmetleri',
    'title' => 'SEO Hizmeti ve Teknik SEO Çözümleri | NetVera',
    'desc' => 'Teknik SEO, site içi optimizasyon, içerik analizi ve arama görünürlüğü çalışmalarını inceleyin. Hizmet kapsamı ve raporlama seçeneklerini karşılaştırın.',
    'focus' => 'seo hizmeti',
    'secondary' => 'teknik seo, site içi seo, seo danışmanlığı',
    'question' => 'SEO hizmeti neleri kapsar?',
    'answer' => 'Site içi yapı, teknik erişilebilirlik, içerik kalitesi, arama niyeti ve ölçümleme çalışmaları ihtiyaçlara göre planlanır. Belirli bir sıralama garanti edilemez.'
  ],
  'dijital-reklam' => [
    'slug' => 'dijital-reklam',
    'title' => 'Google Ads ve Sosyal Medya Reklam Yönetimi – NetVera',
    'desc' => 'Google Ads, Meta reklamları ve dijital kampanya yönetim hizmetlerini inceleyin. Hedefleme, ölçümleme ve optimizasyon kapsamını karşılaştırın.',
    'focus' => 'google ads yönetimi',
    'secondary' => 'meta reklam yönetimi, dijital reklam ajansı, sosyal medya reklamları',
    'question' => 'Dijital reklam yönetimi hangi çalışmaları kapsar?',
    'answer' => 'Hedef kitle planlama, kampanya kurulumu, dönüşüm takibi ve performans analizleri hizmet kapsamına göre yapılır; sonuçlar bütçe ve pazar koşullarına bağlıdır.'
  ],
  'itibar-yonetimi' => [
    'slug' => 'itibar-yonetimi',
    'title' => 'Online İtibar Yönetimi ve Müşteri Deneyimi | NetVera',
    'desc' => 'Dijital itibar izleme, müşteri geri bildirimi yönetimi ve marka iletişimi hizmetlerini inceleyin. NetVera ile şeffaf ve etik çözümleri karşılaştırın.',
    'focus' => 'online itibar yönetimi',
    'secondary' => 'müşteri deneyimi, marka itibar yönetimi, yorum yanıtlama',
    'question' => 'Online itibar yönetimi nasıl yürütülür?',
    'answer' => 'Gerçek müşteri geri bildirimleri takip edilir, yorumlara profesyonel yanıtlar hazırlanır ve işletme iletişim süreçleri iyileştirilir. Sahte değerlendirme oluşturulmaz.'
  ],
  'sosyal-medya-hizmetleri' => [
    'slug' => 'sosyal-medya-hizmetleri',
    'title' => 'Sosyal Medya Hizmetleri ve Paket Seçenekleri | NetVera',
    'desc' => 'Instagram, TikTok, YouTube ve diğer platformlara yönelik sosyal medya hizmet seçeneklerini kapsam, hedef ve sipariş şartlarına göre karşılaştırın.',
    'focus' => 'sosyal medya hizmetleri',
    'secondary' => 'sosyal medya paketleri, sosyal medya etkileşim hizmetleri',
    'question' => 'Sosyal medya hizmeti seçerken nelere bakılmalı?',
    'answer' => 'Platformu, hedeflenen içerik türünü, gerekli hesap veya bağlantı bilgisini ve hizmetin teslimat şartlarını karşılaştırın.',  ],
  'sosyal-medya-yonetimi' => [
    'slug' => 'sosyal-medya-yonetimi',
    'title' => 'Sosyal Medya Yönetimi ve İçerik Planlama | NetVera',
    'desc' => 'Markalar için sosyal medya yönetimi, içerik planlama, topluluk iletişimi ve performans ölçümleme hizmetlerini inceleyin.',
    'focus' => 'sosyal medya yönetimi',
    'secondary' => 'instagram yönetimi, kurumsal sosyal medya, sosyal medya ajansı',
    'question' => 'Profesyonel sosyal medya yönetimi hangi işleri kapsar?',
    'answer' => 'Marka hedeflerine göre içerik planlama, yayın takvimi, topluluk yönetimi, ölçümleme ve gerekiyorsa reklam koordinasyonu yapılır.',  ],
  'instagram-yorum' => [
    'slug' => 'instagram-yorum',
    'title' => 'Instagram Yorum Hizmetleri ve Gönderi Etkileşimi | NetVera',
    'desc' => 'Instagram gönderi yorumlarına ilişkin hizmet kapsamını, hedef bağlantısını ve uygulama koşullarını inceleyin. Gerçek kullanıcı etkileşimini esas alan seçenekleri karşılaştırın.',
    'focus' => 'instagram yorum hizmeti',
    'secondary' => 'instagram gönderi yorumları, instagram yorum yönetimi',
    'question' => 'Instagram yorum ve etkileşim yönetiminde nelere dikkat edilmeli?',
    'answer' => 'Yorumların özgünlüğü ve platform ilkelerine uygunluğu önemlidir. Paket kapsamını ve izin verilen kullanım senaryolarını sipariş öncesinde kontrol edin.',  ],
  'youtube-izlenme' => [
    'slug' => 'youtube-izlenme',
    'title' => 'YouTube İzlenme Hizmetleri ve Video Tanıtımı | NetVera',
    'desc' => 'YouTube video izlenme ve tanıtım seçeneklerini içerik hedefi, video bağlantısı ve hizmet koşullarına göre değerlendirin.',
    'focus' => 'youtube izlenme',
    'secondary' => 'youtube video izlenme, youtube tanıtım hizmeti',
    'question' => 'YouTube video hizmeti seçerken hangi bilgiler gerekir?',
    'answer' => 'Hedef video bağlantısı ve içerik erişimi doğrulanır; kapsam, raporlama ve teslimat şartları seçilen hizmette belirtilir.',  ],
  'facebook-begeni' => [
    'slug' => 'facebook-begeni',
    'title' => 'Facebook Sayfa ve Gönderi Beğeni Hizmetleri | NetVera',
    'desc' => 'Facebook sayfa veya gönderi beğenisi hizmet seçeneklerini ve hedef bağlantısı koşullarını inceleyin.',
    'focus' => 'facebook beğeni',
    'secondary' => 'facebook sayfa beğeni, facebook etkileşim',
    'question' => 'Facebook beğeni hizmetinde hangi bağlantı kullanılır?',
    'answer' => 'Hedefin sayfa mı gönderi mi olduğunu seçin. Gerekli URL ile hizmetin kapsamı ve teslimat bilgileri pakete göre değişir.',  ],
  'twitter-hizmetleri' => [
    'slug' => 'twitter-hizmetleri',
    'title' => 'X (Twitter) Etkileşim ve Hesap Hizmetleri | NetVera',
    'desc' => 'X (Twitter) hesabı ve gönderileri için mevcut etkileşim hizmet seçeneklerini ve sipariş koşullarını karşılaştırın.',
    'focus' => 'twitter hizmetleri',
    'secondary' => 'x etkileşim hizmeti, twitter takipçi, x gönderi etkileşimi',
    'question' => 'X için hangi hizmet türü uygun?',
    'answer' => 'Hesap profili ve gönderi hedefleri farklıdır. Kullanıcı adı veya gönderi bağlantısı gereksinimlerini hizmet ayrıntılarında inceleyin.',  ],
  'threads-hizmetleri' => [
    'slug' => 'threads-hizmetleri',
    'title' => 'Threads Etkileşim ve Sosyal Medya Hizmetleri | NetVera',
    'desc' => 'Threads profil ve gönderi odaklı mevcut sosyal medya hizmetlerini kapsamları ve hedef bilgileriyle karşılaştırın.',
    'focus' => 'threads hizmetleri',
    'secondary' => 'threads etkileşim, threads profil hizmetleri',
    'question' => 'Threads hizmetlerinde hangi hedef bilgisi gerekir?',
    'answer' => 'Profil veya gönderi bağlantısını doğru belirtin; hizmetin desteklediği hedef türünü ve teslimat koşullarını doğrulayın.',  ],
  'telegram-hizmetleri' => [
    'slug' => 'telegram-hizmetleri',
    'title' => 'Telegram Kanal ve Topluluk Hizmetleri | NetVera',
    'desc' => 'Telegram kanal yönetimi ve topluluk iletişimiyle ilgili mevcut hizmet seçeneklerini inceleyin; kapsam ve gerekli kanal bağlantısı bilgilerini karşılaştırın.',
    'focus' => 'telegram kanal hizmetleri',
    'secondary' => 'telegram grup yönetimi, telegram kanal yönetimi',
    'question' => 'Telegram kanal hizmetlerinde neye dikkat edilmeli?',
    'answer' => 'Kanal veya grup erişim koşullarını, kullanılacak bağlantıyı ve hizmetin uygulama sınırlarını önceden değerlendirin.',  ],
  'spotify-hizmetleri' => [
    'slug' => 'spotify-hizmetleri',
    'title' => 'Spotify Sanatçı ve İçerik Tanıtım Hizmetleri | NetVera',
    'desc' => 'Spotify sanatçı profili, içerik görünürlüğü ve tanıtım hizmetleri için sunulan seçeneklerin kapsam ve bağlantı koşullarını inceleyin.',
    'focus' => 'spotify tanıtım',
    'secondary' => 'spotify sanatçı tanıtımı, spotify müzik pazarlaması',
    'question' => 'Spotify tanıtımı nasıl planlanır?',
    'answer' => 'Dinleyici kitlesi, müzik türü, tanıtım bütçesi ve ölçülebilir kampanya hedefleri temel alınır; garanti dinlenme vaatlerinden kaçınılır.',  ],
  'discord-hizmetleri' => [
    'slug' => 'discord-hizmetleri',
    'title' => 'Discord Sunucu ve Topluluk Yönetimi | NetVera',
    'desc' => 'Discord sunucu kurulumu, topluluk yönetimi ve etkileşim planlamasıyla ilgili hizmet seçeneklerini inceleyin.',
    'focus' => 'discord sunucu hizmeti',
    'secondary' => 'discord topluluk yönetimi, discord sunucu kurulumu',
    'question' => 'Discord topluluk hizmetleri neleri içerir?',
    'answer' => 'Sunucu yapılandırması, rol ve kanal düzeni, moderasyon süreçleri ve topluluk iletişimi ihtiyaçlara göre planlanabilir.',  ],
  'linkedin-hizmetleri' => [
    'slug' => 'linkedin-hizmetleri',
    'title' => 'LinkedIn Profil, İçerik ve Kurumsal Yönetim | NetVera',
    'desc' => 'LinkedIn şirket sayfası, profesyonel içerik ve kurumsal görünürlük hizmetlerini inceleyin.',
    'focus' => 'linkedin hizmetleri',
    'secondary' => 'linkedin şirket sayfası yönetimi, linkedin içerik yönetimi',
    'question' => 'LinkedIn hizmetleri işletmelere nasıl yardımcı olur?',
    'answer' => 'Şirket sayfası, hedef kitle, içerik dili, profesyonel profil ve performans ölçümleme kapsamları ihtiyaçlara göre değerlendirilir.',  ],
  'twitch-hizmetleri' => [
    'slug' => 'twitch-hizmetleri',
    'title' => 'Twitch Yayın ve Kanal Tanıtım Hizmetleri | NetVera',
    'desc' => 'Twitch kanal tanıtımı ve yayın topluluğu yönetimi için mevcut hizmetlerin kapsamlarını ve uygulama koşullarını inceleyin.',
    'focus' => 'twitch kanal tanıtımı',
    'secondary' => 'twitch yayın tanıtımı, twitch kanal hizmeti',
    'question' => 'Twitch kanalı için tanıtım nasıl planlanır?',
    'answer' => 'Yayın kategorisi, zamanlama, hedef kitle ve içerik türüne uygun tanıtım kanalları değerlendirilir.',  ],
  'icerik-uretimi' => [
    'slug' => 'icerik-uretimi',
    'title' => 'Sosyal Medya İçerik Üretimi ve Video Düzenleme | NetVera',
    'desc' => 'Markalar için sosyal medya görselleri, kısa video düzenleme ve içerik planlama hizmet seçeneklerini inceleyin.',
    'focus' => 'sosyal medya içerik üretimi',
    'secondary' => 'reels video düzenleme, sosyal medya tasarım, içerik planlama',
    'question' => 'İçerik üretimi hizmetinin kapsamı nasıl belirlenir?',
    'answer' => 'İçerik adedi, ölçüler, platformlar, marka dili, revizyon sayısı ve teslim biçimi iş başlamadan önce netleştirilir.',  ],
  'grafik-tasarim' => [
    'slug' => 'grafik-tasarim',
    'title' => 'Grafik Tasarım ve Sosyal Medya Görsel Hizmetleri | NetVera',
    'desc' => 'Kurumsal tasarım, sosyal medya görselleri ve dijital reklam kreatifleri için hizmet kapsamını ve teslim seçeneklerini karşılaştırın.',
    'focus' => 'grafik tasarım hizmeti',
    'secondary' => 'sosyal medya görsel tasarım, dijital tasarım, reklam kreatifi',
    'question' => 'Profesyonel grafik tasarım hizmeti neleri kapsar?',
    'answer' => 'Teslim formatları, görsel ölçüleri, tasarım dosyaları, revizyon kapsamı ve kullanım hakları proje ihtiyaçlarına göre belirlenir.',  ],
  'yerel-seo' => [
    'slug' => 'yerel-seo',
    'title' => 'Yerel SEO ve Google Harita Optimizasyonu | NetVera',
    'desc' => 'Yerel arama görünürlüğü, Google İşletme Profili, işletme kategorileri ve bölgesel hizmet sayfalarına yönelik SEO çalışmalarını inceleyin.',
    'focus' => 'yerel seo',
    'secondary' => 'google harita seo, google işletme profili optimizasyonu',
    'question' => 'Yerel SEO hangi çalışmalardan oluşur?',
    'answer' => 'İşletme bilgilerinin tutarlılığı, yerel içerik uygunluğu, kategori seçimi ve organik müşteri geri bildirimleri değerlendirilir.',  ],
  'e-ticaret-hizmetleri' => [
    'slug' => 'e-ticaret-hizmetleri',
    'title' => 'E-Ticaret Yönetimi ve Dijital Mağaza Hizmetleri | NetVera',
    'desc' => 'E-ticaret içerik yönetimi, ürün listeleme, dönüşüm analizi ve dijital mağaza süreçleri için hizmet seçeneklerini inceleyin.',
    'focus' => 'e-ticaret hizmetleri',
    'secondary' => 'ürün listeleme yönetimi, e-ticaret seo, e-ticaret danışmanlığı',
    'question' => 'E-ticaret yönetimi hizmetinde hangi alanlar önemlidir?',
    'answer' => 'Ürün verileri, kategori yapısı, fiyat ve stok yönetimi, ödeme deneyimi, analitik ve müşteri hizmetleri birlikte değerlendirilir.',  ],
];
  }
  /** Server-side launch defaults for unedited service categories. URL, product data and software categories remain untouched. */
  public static function decorate(array $category): array {
    if(self::isSoftware($category))return $category;
    $profile=self::profiles()[strtolower((string)($category['slug']??''))]??null;
    if(!$profile)return $category;
    $title=trim((string)($category['seo_title']??''));
    if($title==='' || preg_match('/Yorum\s*Hizmeti|YorumHizmeti/iu',$title))
      $category['seo_title']=$profile['title'];
    $description=trim((string)($category['seo_description']??''));
    if($description===''||mb_strlen($description,'UTF-8')<65)
      $category['seo_description']=$profile['desc'];
    if(trim((string)($category['seo_focus_keyword']??''))==='')$category['seo_focus_keyword']=$profile['focus'];
    if(trim((string)($category['og_title']??''))==='')$category['og_title']=$category['seo_title'];
    if(trim((string)($category['og_description']??''))==='')$category['og_description']=$category['seo_description'];
    return $category;
  }

  public static function preview(): array {
    $db=Database::getInstance();
    $categories=$db->fetchAll("SELECT id,name,slug,seo_title,seo_description,seo_focus_keyword FROM categories");
    $profiles=self::profiles();$found=[];
    foreach($categories as $row) {
      $slug=strtolower((string)$row['slug']);
      if(self::isSoftware($row))continue;
      if(isset($profiles[$slug]))$found[]=array_merge($row,['suggestion'=>$profiles[$slug]]);
    }
    return $found;
  }
  private static function isSoftware(array $row): bool {
    return (bool)preg_match('/(yaz[iı]l[iı]m|haz[iı]r.script|software|cms|web.site|tema|plugin|eklenti)/iu',
        (string)($row['slug']??'').' '.(string)($row['name']??''));
  }
  public static function apply(): array {
    $db=Database::getInstance();
    $list=self::preview();$counts=['categories'=>0,'fields'=>0,'seo_profiles'=>0];
    foreach($list as $cat){
      $suggest=$cat['suggestion'];$update=[];
      $current=trim((string)($cat['seo_title']??''));
      if($current==='' || preg_match('/Yorum\s*Hizmeti|YorumHizmeti/iu',$current))$update['seo_title']=$suggest['title'];
      $current=trim((string)($cat['seo_description']??''));
      if($current===''||mb_strlen($current)<65){
          $update['seo_description']=$suggest['desc'];
      }
      if(trim((string)($cat['seo_focus_keyword']??''))==='')$update['seo_focus_keyword']=$suggest['focus'];
      if($update){$db->update('categories',$update,'id=?',[(int)$cat['id']]);$counts['categories']++;$counts['fields']+=count($update);}
      $currentSeo=NetveraSeoBridge::get('category',(int)$cat['id']);
      if(empty($currentSeo['secondary_keywords'])||empty($currentSeo['main_question'])){
        $input=[];
        foreach(['secondary_keywords','geo_summary','entity_topics','main_question','direct_answer','content_intent','author_name','author_type'] as $key){
          $input['nvseo_'.$key]=$currentSeo[$key]??'';
        }
        if($input['nvseo_secondary_keywords']==='')$input['nvseo_secondary_keywords']=$suggest['secondary'];
        if($input['nvseo_geo_summary']==='')$input['nvseo_geo_summary']=$suggest['desc'];
        if($input['nvseo_entity_topics']==='')$input['nvseo_entity_topics']=$suggest['focus'].', '.$cat['name'];
        if($input['nvseo_main_question']==='')$input['nvseo_main_question']=$suggest['question'];
        if($input['nvseo_direct_answer']==='')$input['nvseo_direct_answer']=$suggest['answer'];
        if($input['nvseo_content_intent']==='')$input['nvseo_content_intent']='satin-alma';
        if($input['nvseo_author_name']==='')$input['nvseo_author_name']='NetVera Teknoloji Yazılım';
        if($input['nvseo_author_type']==='')$input['nvseo_author_type']='Organization';
        // Do not erase previously saved robots, author URLs or other metadata.
        foreach($currentSeo as $key=>$value){
          if(is_string($value)&&!isset($input['nvseo_'.$key]))$input['nvseo_'.$key]=$value;
        }
        NetveraSeoBridge::save('category',(int)$cat['id'],$input);
        $counts['seo_profiles']++;
      }
    }
    return $counts;
  }
}
