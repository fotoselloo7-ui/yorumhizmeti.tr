<?php
namespace App\Services;

use App\Core\Database;

/** One category/subcategory banner model. No URL, product or schema mutations. */
final class CategoryHeroService
{
    private static array $stored = [];

    public static function families(): array
    {
        return [
            'audience'=>['Takipçi / abone / üyeler','Profil ve kitle','profil kartları ve bağlantılı topluluk',
                'Hedefinize uygun miktarı seçin. Hizmet türünü, profil veya kanal bilgisini ve teslimat koşullarını sipariş öncesinde karşılaştırın.',
                [['users','Miktarı belli paketler'],['link','Profil / kanal hedefi'],['check-circle','Sipariş takibi']]],
            'engagement'=>['Gönderi etkileşimi','İçerik etkileşimi','gönderi kartı, beğeni, yorum ve kaydetme simgeleri',
                'Gönderiniz için uygun etkileşim hizmetini bulun. Paket kapsamını, içerik bağlantısını ve teslimat şartlarını inceleyerek seçiminizi yapın.',
                [['heart','Gönderi odaklı hizmet'],['link','İçerik bağlantısı'],['layers','Paket karşılaştırması']]],
            'video'=>['Video / canlı yayın','Video hizmetleri','video oynatıcı ve düzenleme zaman çizelgesi',
                'Video, kısa içerik veya yayın hedefinize uygun hizmeti seçin. Desteklenen bağlantı türlerini, miktarı ve uygulama koşullarını karşılaştırın.',
                [['play','Video / yayın hedefi'],['link','Bağlantı kontrolü'],['check-circle','Sipariş takibi']]],
            'audio'=>['Müzik / ses','Müzik ve içerik','müzik oynatıcı, kulaklık ve ses dalgaları',
                'Sanatçı profiliniz, parçanız veya çalma listeniz için sunulan seçenekleri inceleyin. İçerik bağlantısı ve hizmet kapsamını birlikte değerlendirin.',
                [['music','Parça / liste hedefi'],['link','İçerik bağlantısı'],['layers','Kapsam karşılaştırması']]],
            'community'=>['Kanal / topluluk','Topluluk hizmetleri','kanal pencereleri ve bağlı topluluk kartları',
                'Kanalınız veya topluluğunuz için uygun çözümü bulun. Grup bağlantısı, erişim gereksinimleri ve hizmet koşullarını sipariş öncesinde inceleyin.',
                [['users','Kanal / topluluk'],['link','Erişim bilgileri'],['layers','Hizmet kapsamı']]],
            'web'=>['Web tasarım / geliştirme','Web çözümleri','dizüstü bilgisayar ve mobil uyumlu web arayüzü',
                'Markanıza uygun web deneyimini planlayın. Sayfa kapsamı, mobil uyumluluk, yönetim paneli ve teslim seçeneklerini projenize göre karşılaştırın.',
                [['monitor','Responsive arayüz'],['code','Yönetilebilir yapı'],['layers','Net proje kapsamı']]],
            'ecommerce'=>['E-ticaret / entegrasyon','E-ticaret çözümleri','online mağaza, alışveriş sepeti ve ürün kutusu',
                'Online mağazanız için doğru altyapıyı seçin. Ürün yönetimi, ödeme, sipariş ve pazaryeri entegrasyonu kapsamlarını birlikte değerlendirin.',
                [['shopping-cart','Mağaza deneyimi'],['layers','Entegrasyon kapsamı'],['check-circle','Sipariş yönetimi']]],
            'mobile'=>['Mobil uygulama','Mobil çözümler','iki akıllı telefon ve uygulama arayüzleri',
                'Uygulama fikrinizi işlevsel bir deneyime dönüştürün. Ekran akışları, iOS / Android desteği, API bağlantıları ve yayınlama kapsamını inceleyin.',
                [['smartphone','iOS / Android'],['code','API bağlantıları'],['layers','Ekran ve özellik kapsamı']]],
            'design'=>['Grafik / marka tasarımı','Tasarım çözümleri','tasarım çalışma alanı, renk kartları ve kalem',
                'Markanız için tutarlı bir görsel dil oluşturun. Tasarım ölçülerini, revizyon kapsamını, teslim formatlarını ve kullanım haklarını karşılaştırın.',
                [['palette','Marka odaklı tasarım'],['layers','Teslim formatları'],['check-circle','Revizyon kapsamı']]],
            'content'=>['İçerik / metin üretimi','İçerik çözümleri','içerik editörü, yayın takvimi ve yazı araçları',
                'Hedef kitlenize uygun, açık ve özgün içerikler planlayın. Konu, üslup, kelime veya içerik adedi, revizyon ve teslim kapsamını inceleyin.',
                [['file-text','Özgün içerik planı'],['layers','Konu ve format'],['check-circle','Revizyon kapsamı']]],
            'search'=>['SEO / arama optimizasyonu','Arama görünürlüğü','arama penceresi, site denetimi ve analiz paneli',
                'Sitenizin arama görünürlüğünü veriye dayalı çalışmalarla geliştirin. Teknik denetim, içerik, site içi yapı ve raporlama kapsamını değerlendirin.',
                [['search','Arama niyeti'],['code','Teknik inceleme'],['bar-chart','Ölçüm ve raporlama']]],
            'ads'=>['Dijital reklam','Reklam çözümleri','kampanya paneli, hedef işareti ve megafon',
                'Reklam hedeflerinize uygun kampanya yönetimini seçin. Hedefleme, kreatif, bütçe planlama ve dönüşüm ölçümleme kapsamını karşılaştırın.',
                [['target','Hedef kitle planı'],['palette','Kreatif kapsamı'],['bar-chart','Dönüşüm ölçümü']]],
            'reputation'=>['İtibar / geri bildirim','Marka iletişimi','koruma kalkanı ve müşteri geri bildirim kartları',
                'Marka iletişiminizi gerçek müşteri geri bildirimleriyle güçlendirin. İzleme, profesyonel yanıt ve şikâyet yönetimi süreçlerini inceleyin.',
                [['shield','Marka iletişimi'],['message-circle','Geri bildirim yönetimi'],['check-circle','Şeffaf süreç']]],
            'local'=>['Yerel işletme / harita','Yerel görünürlük','harita işaretçisi, mağaza ve işletme profili',
                'İşletmenizin doğru bilgilerle bulunmasını kolaylaştırın. Profil, kategori, harita, iletişim bilgileri ve yerel görünürlük kapsamını inceleyin.',
                [['map-pin','Harita ve profil'],['store','İşletme bilgileri'],['search','Yerel arama niyeti']]],
        ];
    }

    public static function family(array $category): string
    {
        $s = slugify((string)($category['slug'] ?? '').' '.($category['name'] ?? ''));
        if (preg_match('/itibar|sikayet|olumsuz|google-(yorum|5-yildiz)/',$s)) return 'reputation';
        if (preg_match('/yerel|harita|isletme|google-profil|qr-yorum|google-hizmetleri/',$s)) return 'local';
        if (preg_match('/seo|backlink/',$s)) return 'search';
        if (str_contains($s,'reklam-banner')) return 'design';
        if (preg_match('/reklam|ads/',$s)) return 'ads';
        if (preg_match('/e-ticaret|eticaret|pazaryeri|opencart|woocommerce/',$s)) return 'ecommerce';
        if (preg_match('/mobil-uygulama|ios|android|app-store/',$s)) return 'mobile';
        if (preg_match('/web|landing|wordpress/',$s)) return 'web';
        if (preg_match('/grafik|logo-tasarim|sosyal-medya-gorsel/',$s)) return 'design';
        if (preg_match('/icerik|blog-makale|urun-aciklama/',$s)) return 'content';
        if (preg_match('/takipci|abone|uye|baglanti|profil-ziyareti/',$s)) return 'audience';
        if (str_contains($s,'spotify')) return 'audio';
        if (preg_match('/izlenme|goruntulenme|canli|shorts|reels|youtube|tiktok|twitch/',$s)) return 'video';
        if (preg_match('/begeni|yorum|retweet|reaksiyon|kaydetme/',$s)) return 'engagement';
        if (preg_match('/discord|telegram/',$s)) return 'community';
        if (SocialPlatformIdentity::fromCategory($category) !== null) return 'engagement';
        $parent = (string)($category['parent_category_name'] ?? '');
        return $parent !== '' ? self::family(['name'=>$parent]) : 'web';
    }

    public static function defaults(array $category): array
    {
        $name = trim((string)($category['name'] ?? 'Dijital Hizmetler'));
        $slug = (string)($category['slug'] ?? '');
        $family = self::family($category);
        $spec = self::families()[$family];
        $platform = SocialPlatformIdentity::fromCategory($category);
        $lead = $name;
        $accent = '';
        if ($platform !== null && str_contains(mb_strtolower($name),' '.mb_strtolower('Hizmetleri'))) {
            $lead = preg_replace('/\s+Hizmetleri$/u','',$name);
            $accent = 'Hizmetleri';
        } elseif ($platform !== null) {
            $parts = preg_split('/\s+/u',$name,2);
            $lead = $parts[0]; $accent = $parts[1] ?? 'Hizmetleri';
            if (preg_match('/takipci|abone|begeni|izlenme|goruntulenme|uye/',$slug)) $accent .= ' Paketleri';
        } else {
            $parts = preg_split('/\s+/u',$name);
            if (count($parts) > 1) {
                $lead = implode(' ',array_slice($parts,0,-1));
                $accent = end($parts);
            }
        }
        $description = $spec[3];
        if ($platform !== null && empty($category['parent_id']) && $family !== 'local') {
            $description = $lead.' hesabınız ve içerikleriniz için uygun hizmeti bulun. Paket miktarlarını, hedef bilgilerini ve teslimat koşullarını tek yerde karşılaştırın.';
        }
        if (str_contains($slug,'abone')) $description = 'Kanalınız için sunulan abone paketlerini kapsam ve miktara göre karşılaştırın. Kanal bağlantısı ve teslimat koşullarını seçtiğiniz pakette inceleyin.';
        if (str_contains($slug,'kaydetme')) $description = 'İçeriğiniz için kaydetme seçeneklerini ve paket miktarlarını karşılaştırın. Desteklenen içerik bağlantısını ve hizmet şartlarını sipariş öncesinde inceleyin.';
        if (str_contains($slug,'profil-ziyareti')) $description = 'Profil ziyareti paketlerini miktar ve kapsam açısından inceleyin. Hedef kullanıcı adı, erişim gereksinimleri ve teslimat koşullarını karşılaştırın.';
        if (str_contains($slug,'teknik-seo')) $description = 'Tarama, indeksleme, site mimarisi ve performans sorunlarını görünür hâle getirin. Teknik inceleme, önceliklendirme ve uygulama kapsamını karşılaştırın.';
        if (str_contains($slug,'backlink')) $description = 'Bağlantı profilinizi kalite, alaka ve risk açısından değerlendirin. Çalışma kapsamını inceleyin; otomatik sıralama veya otorite artışı garanti edilmez.';
        if (str_contains($slug,'qr-yorum')) $description = 'Gerçek müşterilerinizin geri bildirim bırakmasını kolaylaştıran QR kart çözümlerini inceleyin. Tasarım, yönlendirme ve baskı / dijital teslim kapsamını karşılaştırın.';
        if (str_contains($slug,'logo-tasarim')) $description = 'Markanızın karakterini anlatan bir logo sistemi planlayın. Konsept, revizyon, vektörel dosya, renk ve kullanım rehberi kapsamını karşılaştırın.';
        return ['lead'=>$lead,'accent'=>$accent,'description'=>$description,'visual'=>$family,
            'alt'=>$name.' için '.$spec[2].' illüstrasyonu','facts'=>$spec[4],
            'eyebrow'=>$category['parent_category_name'] ?? $spec[1],
            'cta'=>in_array($family,['audience','engagement','video','audio','community'],true) ? 'Paketleri İncele' : 'Hizmetleri İncele'];
    }

    /** Search copy stays specific to the actual category, never a parent's title. */
    public static function seoProfile(array $category): array
    {
        $d = self::defaults($category);
        $name = trim((string)($category['name'] ?? 'Dijital Hizmetler'));
        $title = trim($d['lead'].' '.$d['accent']);
        if (str_ends_with($title,' Paketleri')) $title .= ' ve Fiyatları';
        $description = $name.': '.$d['description'];
        return ['slug'=>$category['slug'] ?? '', 'title'=>$title.' | NetVera',
            'desc'=>$description,'focus'=>mb_strtolower($name,'UTF-8'),
            'secondary'=>mb_strtolower($name,'UTF-8').' kapsamı, '.mb_strtolower($name,'UTF-8').' seçenekleri',
            'question'=>$name.' seçerken nelere dikkat edilmeli?',
            'answer'=>$d['description']];
    }

    /** Replace only launch/default/legacy-brand metadata; preserve authored fields. */
    public static function decorateSeo(array $category): array
    {
        $profile = self::seoProfile($category);
        $legacy = CategorySearchBlueprint::profiles();
        $oldTitles = array_column($legacy,'title');
        $oldDescriptions = array_column($legacy,'desc');
        foreach (['seo_title'=>'title','og_title'=>'title','seo_description'=>'desc','og_description'=>'desc'] as $field=>$key) {
            $current = trim((string)($category[$field] ?? ''));
            $old = $key === 'title' ? $oldTitles : $oldDescriptions;
            if ($current === '' || preg_match('/Yorum\s*Hizmeti|YorumHizmeti/iu',$current) || in_array($current,$old,true)
                || CategorySeoCompleteService::isGeneric($field,$current,$category)) $category[$field] = $profile[$key];
        }
        if (empty($category['seo_focus_keyword'])) $category['seo_focus_keyword'] = $profile['focus'];
        return $category;
    }

    public static function options(int $id): array
    {
        if ($id < 1) return [];
        if (!array_key_exists($id,self::$stored)) {
            $row = Database::getInstance()->fetch('SELECT setting_value FROM settings WHERE setting_key=?',['category_hero_'.$id]);
            $data = $row ? json_decode((string)$row['setting_value'],true) : [];
            self::$stored[$id] = is_array($data) ? $data : [];
        }
        return self::$stored[$id];
    }

    public static function resolve(array $category, array $options = []): array
    {
        $data = self::defaults($category);
        foreach (['lead','accent','description','alt'] as $key) {
            if (isset($options[$key]) && is_string($options[$key]) && trim($options[$key]) !== '') $data[$key] = trim($options[$key]);
        }
        if (isset(self::families()[$options['visual'] ?? ''])) $data['visual'] = $options['visual'];
        $data['src'] = asset('img/category-heroes/'.$data['visual'].'.webp');
        $data['custom'] = false;
        if (($options['visual'] ?? '') === 'upload') {
            $image = (string)($category['image'] ?? '');
            $path = str_starts_with($image,'/uploads/') ? $image : '/uploads/'.ltrim($image,'/');
            $real = realpath(PUBLIC_PATH.$path);
            $uploads = realpath(PUBLIC_PATH.'/uploads');
            if ($real && $uploads && str_starts_with($real,$uploads.DIRECTORY_SEPARATOR) && is_file($real)) {
                $data['src'] = upload_url($image); $data['custom'] = true;
                if (empty($options['alt']) && !empty($category['image_alt'])) $data['alt'] = $category['image_alt'];
            }
        }
        return $data;
    }

    public static function get(array $category): array
    {
        return self::resolve($category,self::options((int)($category['id'] ?? 0)));
    }

    public static function validate(array $input): array
    {
        $data = [];
        foreach (['lead'=>120,'accent'=>120,'description'=>500,'alt'=>200] as $key=>$limit) {
            $value = trim(strip_tags((string)($input['hero_'.$key] ?? '')));
            if (mb_strlen($value) > $limit) throw new \RuntimeException('Banner alanı izin verilen uzunluğu aşıyor: '.$key);
            $data[$key] = $value;
        }
        $visual = (string)($input['hero_visual'] ?? 'auto');
        if (!in_array($visual,array_merge(['auto','upload'],array_keys(self::families())),true)) throw new \RuntimeException('Geçersiz banner görseli.');
        $data['visual'] = $visual;
        return $data;
    }

    public static function save(int $id, array $input): void
    {
        $data = self::validate($input);
        Database::getInstance()->query('INSERT INTO settings(setting_key,setting_value,setting_group) VALUES(?,?,?) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value),setting_group=VALUES(setting_group)',
            ['category_hero_'.$id,json_encode($data,JSON_UNESCAPED_UNICODE),'categories']);
        self::$stored[$id] = $data;
    }
}
