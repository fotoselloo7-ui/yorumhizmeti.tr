<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class HomeController extends Controller
{
    public function index(): void
    {
        $db = Database::getInstance();

        // Kategoriler
        $categories = $db->fetchAll("SELECT * FROM categories WHERE status = 'active' AND parent_id IS NULL ORDER BY sort_order ASC, id ASC");

        // Admin paketleri aynı veritabanından gelir; manuel/demo paket listesi yoktur.
        // Kategori promosyonları aktif paketlerden; öne çıkan alanı ise yalnızca is_featured=1 kayıtlarından oluşur.
        // Küresel LIMIT, sonradan eklenen kategorileri görünmez kıldığı için kaldırıldı.
        $featuredPackages = $db->fetchAll("
            SELECT p.*, c.name AS category_name, c.slug AS category_slug,
                   COALESCE(parent.id, c.id) AS featured_group_id,
                   COALESCE(parent.name, c.name) AS featured_group_name,
                   COALESCE(parent.slug, c.slug) AS featured_group_slug,
                   COALESCE(parent.sort_order, c.sort_order) AS featured_group_sort
            FROM packages p
            INNER JOIN categories c ON p.category_id = c.id AND c.status = 'active'
            LEFT JOIN categories parent ON c.parent_id = parent.id
            WHERE p.status = 'active'
              AND (c.parent_id IS NULL OR parent.status = 'active')
            ORDER BY featured_group_sort ASC, featured_group_id ASC,
                     p.is_featured DESC, p.sort_order ASC, p.id ASC
        ");

        $featuredPackageGroups = [];
        foreach ($featuredPackages as $pkg) {
            if ((int)($pkg['is_featured'] ?? 0) !== 1) continue;
            $groupId = (int) $pkg['featured_group_id'];
            if ($groupId <= 0) continue;
            if (!isset($featuredPackageGroups[$groupId])) {
                $featuredPackageGroups[$groupId] = [
                    'category' => [
                        'id' => $groupId,
                        'name' => $pkg['featured_group_name'],
                        'slug' => $pkg['featured_group_slug'],
                    ],
                    'packages' => [],
                ];
            }
            // İlk dört kart masaüstünde görünür, gerisi kaydırıcıda tutulur.
            if (count($featuredPackageGroups[$groupId]['packages']) < 16) {
                $featuredPackageGroups[$groupId]['packages'][] = $pkg;
            }
        }
        $featuredPackageGroups = array_values($featuredPackageGroups);

        // Yalnızca sunum grubu: admin kategori ağacına, fiyatlara ve paketlere dokunma.
        // Reuse the same social/agency/marketing taxonomy as the mega menu and category page.
        // Display only categories whose active packages are marked as featured.
        // Drive this selector from the SAME active category tree as the mega menu.
        // Parent categories are top-level cards. Children are a second, horizontal
        // row, never flattened into the top-level rail.
        $featuredNavGroups=[];
        $featuredChildCategories=[];
        $featuredChildPackageGroups=[];
        $menuGroups=\App\Services\CatalogMenuService::groups();
        $knownFeaturedRoots=array_fill_keys(
            array_map(static fn(array $g):int=>(int)$g['category']['id'],$featuredPackageGroups),
            true
        );
        foreach($menuGroups as $catalogGroup){
            $key=$catalogGroup['key'];
            $featuredNavGroups[$key]=[
                'key'=>$key,'title'=>$catalogGroup['short'],
                'icon'=>$catalogGroup['icon'],'categories'=>[]
            ];
            foreach($catalogGroup['categories'] as $root){
                // The database-based package showcase created a second identical
                // software root. The true Netvera script root is added below.
                if (($root['slug']??'') === \App\Services\SoftwareCatalogService::ROOT_SLUG) continue;
                $id=(int)$root['id'];
                $root['kind']='package';
                $root['icon']=$root['icon']??'package';
                $featuredNavGroups[$key]['categories'][]=$root;
                // Unfeatured categories must still be reachable from the same
                // selector; show existing active products, never synthetic ones.
                if(!isset($knownFeaturedRoots[$id])){
                    $matches=array_values(array_filter($featuredPackages,
                        static fn(array $p):bool=>(int)$p['featured_group_id']===$id));
                    $featuredPackageGroups[]=[
                        'category'=>$root,'packages'=>array_slice($matches,0,16),
                        'unfeatured'=>true
                    ];
                    $knownFeaturedRoots[$id]=true;
                }
                foreach(($root['children']??[]) as $child){
                    $child['kind']='package';
                    $child['parent_featured_id']=$id;
                    $featuredChildCategories[$id][]=$child;
                    $products=array_values(array_filter($featuredPackages,
                        static fn(array $p):bool=>(int)$p['category_id']===(int)$child['id']));
                    $featuredChildPackageGroups[]=[
                        'category'=>$child,'packages'=>array_slice($products,0,16)
                    ];
                }
            }
        }

        $featuredSoftwareGroups=[];
        $netveraProducts=\App\Services\NetveraBridgeService::all();
        $netveraCategories=\App\Services\NetveraBridgeService::categories();
        if($netveraProducts){
            if(!isset($featuredNavGroups['agency'])){
                $featuredNavGroups['agency']=[
                    'key'=>'agency','title'=>'Ajans & Yazılım',
                    'icon'=>'layers','categories'=>[]
                ];
            }
            $softwareRoot=[
                'id'=>-100000,'name'=>'Hazır Yazılımlar & Scriptler',
                'slug'=>'hazir-scriptler','url'=>'/hazir-scriptler',
                'kind'=>'software','icon'=>'monitor'
            ];
            $featuredNavGroups['agency']['categories'][]=$softwareRoot;
            $featuredSoftwareGroups[]=['category'=>$softwareRoot,'products'=>$netveraProducts];
            foreach($netveraCategories as $nc){
                $catId=(int)$nc['legacy_id'];
                $parentLegacy=(int)($nc['parent_legacy_id']??0);
                $categoryProducts=array_values(array_filter(
                    $netveraProducts,
                    static function(array $product)use($catId,$netveraCategories):bool{
                        $productCat=(int)$product['category_legacy_id'];
                        if($productCat===$catId)return true;
                        foreach($netveraCategories as $child){
                            if((int)$child['legacy_id']===$productCat &&
                               (int)($child['parent_legacy_id']??0)===$catId)return true;
                        }
                        return false;
                    }
                ));
                $tab=[
                    'id'=>-100000-$catId,'name'=>$nc['name'],
                    'slug'=>$nc['slug'],
                    'url'=>'/hazir-scriptler?category='.rawurlencode((string)$nc['slug']),
                    'kind'=>'software','icon'=>$parentLegacy?'code':'layers',
                    'parent_featured_id'=>$parentLegacy?-100000-$parentLegacy:-100000
                ];
                $featuredChildCategories[$tab['parent_featured_id']][]=$tab;
                $featuredSoftwareGroups[]=['category'=>$tab,'products'=>$categoryProducts];
            }
            // The 34 existing ready-software type choices are actual menu links,
            // not invented Netvera product records. Keep them as nested links.
            foreach(\App\Services\SoftwareCatalogService::definitions() as $type){
                [$label,$slug,$description,$iconName]=$type;
                $featuredChildCategories[-100000][]=[
                    'id'=>0,'name'=>$label,'slug'=>$slug,
                    'url'=>'/hazir-yazilimlar?tur='.rawurlencode($slug),
                    'kind'=>'link','icon'=>$iconName,'parent_featured_id'=>-100000
                ];
            }
        }
        $featuredNavGroups=array_values(array_filter(
            $featuredNavGroups,static fn(array $g):bool=>!empty($g['categories'])
        ));
        $initialFeaturedNavGroup = $featuredNavGroups[0]['key'] ?? 'marketing';
        foreach ($featuredNavGroups as $navGroup) {
            foreach ($navGroup['categories'] as $category) {
                if ((int)$category['id'] === (int)($featuredPackageGroups[0]['category']['id'] ?? 0)) {
                    $initialFeaturedNavGroup = $navGroup['key'];
                    break 2;
                }
            }
        }

        // Balanced quick service links on the homepage: show social, agency and
        // marketing categories, instead of only the first eight database rows.
        $homeQuickCategories = [];
        $homeQuickIds = [];
        foreach (['social' => 4, 'agency' => 2, 'marketing' => 2] as $key => $limit) {
            foreach (\App\Services\CatalogMenuService::groups() as $group) {
                if ($group['key'] !== $key) continue;
                foreach (array_slice($group['categories'], 0, $limit) as $cat) {
                    $homeQuickCategories[] = $cat;
                    $homeQuickIds[$cat['id']] = true;
                }
            }
        }
        if (count($homeQuickCategories) < 8) {
            foreach (\App\Services\CatalogMenuService::groups() as $group) {
                foreach ($group['categories'] as $cat) {
                    if (isset($homeQuickIds[$cat['id']])) continue;
                    $homeQuickCategories[] = $cat;
                    $homeQuickIds[$cat['id']] = true;
                    if (count($homeQuickCategories) >= 8) break 2;
                }
            }
        }

        // Son blog yazıları
        $latestPosts = $db->fetchAll("SELECT bp.*, bc.name as category_name, bc.slug as category_slug FROM blog_posts bp LEFT JOIN blog_categories bc ON bp.blog_category_id = bc.id WHERE bp.status = 'active' ORDER BY bp.published_at DESC LIMIT 4");
        if (count($latestPosts) < 4) {
            foreach (demo_blog_posts() as $demoPost) {
                $latestPosts[] = $demoPost;
                if (count($latestPosts) >= 4) break;
            }
        }

        // Three permanent promotional service families, backed by the same live
        // catalog as the mega menu. Category chips only link to ACTIVE records.
        $promoConfig = [
            'social' => [
                'title' => setting('brand_sector_social','Instagram, TikTok ve YouTube Hizmetleri'),
                'eyebrow' => 'MARKANI SOSYALDE BÜYÜT',
                'description' => 'Instagram, TikTok, YouTube ve diğer platformlarda etkileşiminizi ve görünürlüğünüzü artıran çözümler.',
                'cta' => 'Paketleri İncele',
                'preferred' => ['instagram', 'tiktok', 'youtube', 'facebook'],
            ],
            'agency' => [
                'title' => setting('brand_sector_software','Hazır Yazılım ve Özel Web Çözümleri'),
                'eyebrow' => 'DİJİTAL ALTYAPINI KUR',
                'description' => 'Web sitesi, e-ticaret, özel yazılım, mobil uygulama ve tasarım için profesyonel çözümler.',
                'cta' => 'Hizmetleri İncele',
                'preferred' => ['web', 'ecommerce', 'mobileapp', 'graphic'],
            ],
            'marketing' => [
                'title' => setting('brand_sector_agency','Dijital Ajans, SEO ve Reklam Yönetimi'),
                'eyebrow' => 'DİJİTALDE DAHA GÖRÜNÜR OL',
                'description' => 'SEO, reklam yönetimi, yerel işletme ve dijital büyüme hizmetleriyle markanızı öne çıkarın.',
                'cta' => 'Çözümleri İncele',
                'preferred' => ['seo', 'ads', 'local', 'reputation'],
            ],
        ];
        $homePromoGroups = [];
        $menuGroups = [];
        foreach (\App\Services\CatalogMenuService::groups() as $group) {
            $menuGroups[$group['key']] = $group;
        }
        foreach ($promoConfig as $key => $config) {
            $groupCategories = $menuGroups[$key]['categories'] ?? [];
            $chips = [];
            $usedIds = [];
            // Prioritize well-known services, without assuming those records exist.
            foreach ($config['preferred'] as $preferredStyle) {
                foreach ($groupCategories as $cat) {
                    if (isset($usedIds[$cat['id']]) || $cat['style'] !== $preferredStyle) continue;
                    $chips[] = [
                        'name' => preg_replace('/\s+Hizmetleri?$/u', '', (string)$cat['name']),
                        'url' => $cat['url'],
                        'icon' => $cat['icon'],
                    ];
                    $usedIds[$cat['id']] = true;
                    break;
                }
            }
            foreach ($groupCategories as $cat) {
                if (count($chips) >= 4) break;
                if (isset($usedIds[$cat['id']])) continue;
                $chips[] = [
                    'name' => preg_replace('/\s+Hizmetleri?$/u', '', (string)$cat['name']),
                    'url' => $cat['url'],
                    'icon' => $cat['icon'],
                ];
                $usedIds[$cat['id']] = true;
            }
            $homePromoGroups[] = [
                'key' => $key,
                'title' => $config['title'],
                'eyebrow' => $config['eyebrow'],
                'description' => $config['description'],
                'cta' => $config['cta'],
                'url' => '/kategoriler?grup=' . $key,
                'chips' => array_slice($chips, 0, 4),
            ];
        }

        // Independent software showcase and real client references.
        // Both remain hidden on the storefront until the admin publishes data.
        // This public section sells REAL Netvera scripts, not the test package
        // named "NetVera Haber Sitesi Script Yazılımı" under Web Site Hizmetleri.
        // Existing package store /hazir-yazilimlar and its admin selection remain
        // intact; only this script showcase is sourced from the Netvera catalogue.
        $softwareHighlights = \App\Services\NetveraBridgeService::featured(12);
        $softwareRoot = \App\Services\SoftwareCatalogService::root();
        $softwareCatalogUrl = '/hazir-scriptler';
        $softwarePreviewCategories = [];
        if (($softwareRoot['status'] ?? '') === 'active') {
            $softwarePreviewCategories = $db->fetchAll(
                "SELECT name, slug, icon_key FROM categories
                 WHERE parent_id = ? AND status = 'active'
                 ORDER BY sort_order ASC, id ASC LIMIT 6",
                [(int)$softwareRoot['id']]
            );
        }
        // Show every published work across the two portfolio filters (max 36).
        $projectReferences = \App\Services\ReferencesService::all(true);

        // SSS
        $faqs = $db->fetchAll("SELECT * FROM faqs WHERE status = 'active' ORDER BY sort_order ASC LIMIT 6");

        // Ana sayfa bölümleri (home_sections)
        $sectionsRaw = [];
        try {
            $sectionsRaw = $db->fetchAll("SELECT * FROM home_sections WHERE status = 'active' ORDER BY sort_order ASC");
        } catch (\Exception $e) {
            // Tablo yoksa boş devam et
        }

        // section_key => section data
        $sections = [];
        foreach ($sectionsRaw as $sec) {
            $sec['extra'] = !empty($sec['extra_data']) ? json_decode($sec['extra_data'], true) : [];
            $sections[$sec['section_key']] = $sec;
        }

        // Schema.org JSON-LD
        $siteName = setting('site_name', 'NetVera Teknoloji Yazılım');
        $siteUrl = rtrim(setting('site_url', 'https://netvera.tr'), '/');
        $sitePhone = setting('site_phone', '');
        $siteEmail = setting('site_email', '');

        $type=setting('seo_org_type','Organization');
        if(!in_array($type,['Organization','ProfessionalService'],true))$type='Organization';
        $organization=[
            '@type'=>$type,'@id'=>$siteUrl.'/#organization',
            'name'=>$siteName,'url'=>$siteUrl,
            'description'=>setting('seo_org_description')
        ];
        $logo=trim((string)setting('site_logo',''));
        if($logo!==''){
            $logoUrl=upload_url($logo);
            $organization['logo']=filter_var($logoUrl,FILTER_VALIDATE_URL)
                ?$logoUrl:$siteUrl.'/'.ltrim($logoUrl,'/');
        }
        $area=trim((string)setting('seo_service_area'));
        if($area!=='')$organization['areaServed']=['@type'=>'AdministrativeArea','name'=>$area];
        $topics=array_values(array_filter(array_map('trim',explode(',',(string)setting('seo_entity_topics')))));
        if($topics)$organization['knowsAbout']=array_slice($topics,0,24);
        $profiles=\App\Services\NetveraBrandSettings::sameAs();
        if($profiles)$organization['sameAs']=$profiles;
        $contact=[];
        if($siteEmail!=='' && filter_var($siteEmail,FILTER_VALIDATE_EMAIL) &&
           !str_ends_with($siteEmail,'@yorumhizmeti.tr'))$contact['email']=$siteEmail;
        if($sitePhone!=='' && !str_contains($sitePhone,'500 000 00 00'))$contact['telephone']=$sitePhone;
        if($contact)$organization['contactPoint']=array_merge([
             '@type'=>'ContactPoint','contactType'=>'customer service',
             'availableLanguage'=>'tr'
        ],$contact);
        $schemaOrg = [
            '@context'=>'https://schema.org',
            '@graph'=>[
                $organization,
                [
                    '@type'=>'WebSite','@id'=>$siteUrl.'/#website',
                    'name'=>$siteName,'url'=>$siteUrl,'inLanguage'=>'tr-TR',
                    'publisher'=>['@id'=>$siteUrl.'/#organization'],
                    'potentialAction'=>[
                        '@type'=>'SearchAction',
                        'target'=>$siteUrl.'/kategoriler?q={search_term_string}',
                        'query-input'=>'required name=search_term_string'
                    ]
                ]
            ]
        ];

        // The global AIO question is published visibly through the existing FAQ
        // section before it is included in JSON-LD; never emit hidden Q&A data.
        $homeQuestion=trim((string)setting('seo_home_question'));
        $homeAnswer=trim((string)setting('seo_home_answer'));
        if($homeQuestion!==''&&$homeAnswer!==''&&!in_array(
            mb_strtolower($homeQuestion,'UTF-8'),
            array_map(static fn($f)=>mb_strtolower((string)($f['question']??''),'UTF-8'),$faqs),
            true
        ))$faqs[]=['question'=>$homeQuestion,'answer'=>$homeAnswer];

        // FAQ Schema
        if (!empty($faqs)) {
            $faqItems = [];
            foreach ($faqs as $faq) {
                $faqItems[] = [
                    '@type' => 'Question',
                    'name' => $faq['question'],
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => strip_tags($faq['answer']),
                    ],
                ];
            }
            $schemaOrg['@graph'][] = [
                '@type' => 'FAQPage',
                'mainEntity' => $faqItems,
            ];
        }

        $this->render('frontend/home', [
            'canonicalUrl'=>url('/'),
            'pageTitle' => setting('default_seo_title', 'NetVera Teknoloji Yazılım | Yazılım, Dijital Ajans ve Sosyal Medya'),
            'ogTitle' => setting('seo_og_title'),
            'ogDescription' => setting('seo_og_description'),
            'nvSeoData' => [
                'robots'=>setting('seo_default_robots'),
                'geo_summary'=>setting('seo_geo_summary'),
                'entity_topics'=>setting('seo_entity_topics'),
                'service_area'=>setting('seo_service_area'),
                'author_name'=>setting('site_name'),
                'author_type'=>setting('seo_org_type')
            ],
            'metaDescription' => setting('default_seo_description', 'NetVera Teknoloji Yazılım: hazır yazılım, dijital ajans, SEO, reklam yönetimi ve sosyal medya hizmetleri.'),
            'categories' => $categories,
            'featuredPackages' => $featuredPackages,
            'featuredPackageGroups' => $featuredPackageGroups,
            'featuredSoftwareGroups' => $featuredSoftwareGroups,
            'featuredChildCategories' => $featuredChildCategories,
            'featuredChildPackageGroups' => $featuredChildPackageGroups,
            'featuredNavGroups' => $featuredNavGroups,
            'initialFeaturedNavGroup' => $initialFeaturedNavGroup,
            'homePromoGroups' => $homePromoGroups,
            'softwareHighlights' => $softwareHighlights,
            'softwareCatalogUrl' => $softwareCatalogUrl,
            'softwarePreviewCategories' => $softwarePreviewCategories,
            'projectReferences' => $projectReferences,
            'homeQuickCategories' => $homeQuickCategories,
            'latestPosts' => $latestPosts,
            'faqs' => $faqs,
            'sections' => $sections,
            'schema' => json_encode($schemaOrg, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ]);
    }
}
