<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Database;

class PackageController extends Controller
{
    public function show(string $slug): void
    {
        // Önceki YorumHizmeti yazılım demo paketi: Netvera'daki esas ürünün
        // tek indekslenebilir URL'sine 301. Hizmet paketleri aynen kalır.
        $softwareAliases=[
            'netvera-haber-sitesi-script-yazilimi'=>'haber-sitesi-scripti',
        ];
        if (isset($softwareAliases[$slug])) {
            $target=$softwareAliases[$slug];
            if (\App\Services\NetveraBridgeService::find($target)) {
                header('Location: /hazir-scriptler/'.rawurlencode($target),true,301);
                exit;
            }
        }
        $db = Database::getInstance();
        $package = $db->fetch("
            SELECT p.*, c.name AS category_name, c.slug AS category_slug,
                   parent.slug AS parent_category_slug, parent.name AS parent_category_name
            FROM packages p
            INNER JOIN categories c ON p.category_id = c.id AND c.status = 'active'
            LEFT JOIN categories parent ON c.parent_id = parent.id
            WHERE p.slug = ? AND p.status = 'active'
              AND (c.parent_id IS NULL OR parent.status = 'active')
        ", [$slug]);
        if (!$package) { $this->render('frontend/404', ['pageTitle' => 'Paket Bulunamadı']); return; }

        $quoteMode=\App\Services\ServiceQuotePolicy::package($package);
        // Fix old YorumHizmeti metadata immediately even before an optional
        // database normalization. Preserve manually authored NetVera metadata.
        $seoSuggestions=\App\Services\PackageSeoLaunchService::recommend($package,$quoteMode);
        foreach(['seo_title','seo_description','og_title','og_description'] as $field){
            if(\App\Services\PackageSeoLaunchService::stale($field,$package[$field]??null)){
                $package[$field]=$seoSuggestions[$field];
            }
        }
        $fields = \App\Services\SmmOrderFields::fields($package);
        $socialSale = \App\Services\SocialPackageService::info($package);
        $socialPage=$socialSale!==null?\App\Services\SocialStorefrontService::page($package+['_sale'=>$socialSale],'package'):null;
        $socialAlternatives=[];
        if($socialSale!==null){
            foreach($db->fetchAll('SELECT * FROM packages WHERE category_id=? AND status=? ORDER BY sort_order,id',[$package['category_id'],'active'])as $candidate){
                $candidateInfo=\App\Services\SocialPackageService::info($candidate);
                if($candidateInfo['key']===$socialSale['key'])$socialAlternatives[]=$candidate;
            }
        }
        $variantPackages = $socialSale !== null ? [] : $db->fetchAll("SELECT id, name, slug, price, discount_price, seo_title, og_title FROM packages WHERE category_id = ? AND status = 'active' ORDER BY sort_order ASC LIMIT 3", [$package['category_id']]);
        $relatedPackages = $db->fetchAll("
            SELECT p.*, c.name AS category_name, c.slug AS category_slug
            FROM packages p
            INNER JOIN categories c ON p.category_id = c.id AND c.status = 'active'
            LEFT JOIN categories parent ON c.parent_id = parent.id
            WHERE p.id != ? AND p.status = 'active'
              AND (c.parent_id IS NULL OR parent.status = 'active')
            ORDER BY (p.category_id = ?) DESC, p.is_featured DESC, p.sort_order ASC LIMIT 4
        ", [$package['id'], $package['category_id']]);
        $faqs = $db->fetchAll("SELECT * FROM faqs WHERE status = 'active' ORDER BY sort_order ASC LIMIT 6");
        $testimonialSection = null;
        try {
            $testimonialSection = $db->fetch("SELECT * FROM home_sections WHERE section_key = 'testimonials' AND status = 'active' LIMIT 1");
            if ($testimonialSection) {
                $testimonialSection['extra'] = !empty($testimonialSection['extra_data']) ? json_decode($testimonialSection['extra_data'], true) : [];
            }
        } catch (\Exception $e) {
            $testimonialSection = null;
        }

        // Draft market listings may be discoverable from category pages, but
        // a tentative supplier/price is NOT an indexable standalone product.
        $nvDraft=\App\Services\MarketProposalGuard::origin((int)$package['id']);
        $nvSeoData=\App\Services\NetveraSeoBridge::get('package',(int)$package['id']);
        if($nvDraft!==null && (($nvDraft['kind']??'')!=='social' || !\App\Services\MarketProposalGuard::ready($package)))
            $nvSeoData['robots']='noindex,follow';
        $schema=$quoteMode ? json_encode([
            '@context'=>'https://schema.org',
            '@type'=>'Service',
            'name'=>package_display_name($package),
            'description'=>strip_tags((string)($package['short_description']??'')),
            'serviceType'=>(string)$package['category_name'],
            'provider'=>['@type'=>'Organization','name'=>'NetVera Teknoloji Yazılım','url'=>url('/')],
            'url'=>url('/paket/'.$package['slug'])
        ],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) : json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => package_display_name($package),
            'description' => strip_tags($package['short_description'] ?? ''),
            'offers' => [
                '@type' => 'Offer',
                'price' => (!empty($package['discount_price'])&&$package['discount_price']<$package['price'])?$package['discount_price']:$package['price'],
                'priceCurrency' => 'TRY',
                'availability' => !empty($socialSale)&&($socialSale['quantity']<1||$socialSale['unit']===''||!$socialSale['mapping_matches'])?'https://schema.org/OutOfStock':'https://schema.org/InStock',
            ],
        ], JSON_UNESCAPED_UNICODE);

        $this->render('frontend/package-detail', [
            'pageTitle' => $package['seo_title'] ?: package_display_name($package) . ' - ' . setting('site_name'),
            'metaDescription' => $package['seo_description'] ?: excerpt(strip_tags($package['short_description'] ?? ''), 160),
            'canonicalUrl' => url('/paket/' . $package['slug']),
            'ogType'=>'product',
            'ogTitle'=>$package['og_title']?:($package['seo_title']?:package_display_name($package)),
            'ogDescription'=>$package['og_description']?:($package['seo_description']?:excerpt(strip_tags($package['short_description']??''),160)),
            'ogImage'=>!empty($package['og_image'])?upload_url($package['og_image']):($socialPage?url($socialPage['image']):(!empty($package['image'])?upload_url($package['image']):null)),
            'schema' => $schema,
            'nvSeoData' => $nvSeoData,
            'package' => $package,
            'quoteMode' => $quoteMode,
            'socialSale' => $socialSale,
            'socialPage' => $socialPage,
            'socialAlternatives' => $socialAlternatives,
            'fields' => $fields,
            'variantPackages' => $variantPackages,
            'relatedPackages' => $relatedPackages,
            'faqs' => $faqs,
            'testimonialSection' => $testimonialSection,
        ]);
    }
}
