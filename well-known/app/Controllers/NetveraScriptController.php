<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Services\NetveraBridgeService as Catalog;

class NetveraScriptController extends Controller
{
    /** Netvera'nın eski indeksli kısa adresleri, orijinal canonical ürüne gider. */
    public function legacyNews(): void { $this->legacyRedirect('haber-sitesi-scripti'); }
    public function legacyCleaning(): void { $this->legacyRedirect('netvera-temizlik-firmasi-script-yazilimi-pro'); }
    public function legacyRealEstate(): void { $this->legacyRedirect('netvera-emlak-script-yazilimi-pro'); }

    public function legacyRedirect(string $slug): void
    {
        $product=Catalog::find($slug);
        if (!$product) {
            http_response_code(404);
            $this->render('frontend/404',['pageTitle'=>'Yazılım Bulunamadı']);
            return;
        }
        header('Location: /hazir-scriptler/'.rawurlencode((string)$product['slug']),true,301);
        exit;
    }

    public function index(): void
    {
        $category = trim((string)($_GET['category'] ?? ''));
        if ($category !== '') {
            foreach (Catalog::categories() as $row) {
                if ($row['slug'] === $category) {
                    \App\Services\PublicSeoUrls::redirectLegacyFacet(
                        \App\Services\PublicSeoUrls::path('/hazir-scriptler/kategori', $category), 'category'
                    );
                }
            }
        }
        $this->renderCatalog('/hazir-scriptler');
    }

    public function categoryPage(string $slug): void
    {
        $slug = rawurldecode($slug);
        foreach (Catalog::categories() as $row) {
            if ($row['slug'] === $slug) {
                $_GET['category'] = $slug;
                $this->renderCatalog(\App\Services\PublicSeoUrls::path('/hazir-scriptler/kategori', $slug));
                return;
            }
        }
        \App\Services\PublicSeoUrls::notFound('Yazılım Kategorisi Bulunamadı');
    }

    private function renderCatalog(string $canonicalPath): void
    {
        $q=mb_substr(trim((string)($_GET['q']??'')),0,70);
        $cat=trim((string)($_GET['category']??''));
        $categories=Catalog::categories();$categoryId=null;
        // Expanded ready-script subtypes are an ADDITIONAL facet on the same
        // real imported catalog; original NetVera category/SEO URLs remain.
        $type=trim((string)($_GET['type']??''));
        if(!\App\Services\NetveraScriptTypeService::valid($type))$type='';
        $typeDefinitions=\App\Services\NetveraScriptTypeService::definitions();
        foreach($categories as $row)if($row['slug']===$cat)$categoryId=(int)$row['legacy_id'];

        // Untrusted query parameters never become SQL identifiers or free-form clauses.
        $priceFromQuery=static function(string $key):?float {
            $raw=trim((string)($_GET[$key]??''));
            if($raw==='' || !is_numeric($raw))return null;
            $amount=(float)$raw;
            return is_finite($amount)?min(100000000,max(0,$amount)):null;
        };
        $minPrice=$priceFromQuery('min_price');
        $maxPrice=$priceFromQuery('max_price');
        $minRating=max(0,min(5,(int)($_GET['min_rating']??0)));
        $sort=(string)($_GET['sort']??'recommended');
        if(!in_array($sort,['recommended','price_asc','price_desc','rating','newest'],true))
            $sort='recommended';

        $allProducts=Catalog::all($q,$categoryId,$minPrice,$maxPrice,$minRating,$sort);
        $typeCounts=\App\Services\NetveraScriptTypeService::counts(Catalog::all());
        $visibleProducts=\App\Services\NetveraScriptTypeService::filter($allProducts,$type);
        $this->render('frontend/netvera-scripts',[
            'pageTitle'=>'Hazır Scriptler ve Profesyonel Yazılımlar',
            'metaDescription'=>'Sektörel PHP web yazılımları, otomasyon, CMS ve hazır script ürünleri.',
            'canonicalUrl'=>url($canonicalPath),
            'noindex'=>$q!=='' || $type!=='' || $minPrice!==null || $maxPrice!==null
                || $minRating>0 || $sort!=='recommended',
            'products'=>$visibleProducts, 'softwareTypes'=>$typeDefinitions,
            'filterType'=>$type, 'softwareTypeCounts'=>$typeCounts,
            'categories'=>$categories,'filterCategory'=>$cat,'filterQuery'=>$q,
            'filterMinPrice'=>$minPrice,'filterMaxPrice'=>$maxPrice,
            'filterMinRating'=>$minRating,'filterSort'=>$sort,
        ]);
    }

    public function subCategoryPage(string $mainSlug,string $subSlug): void
    {
        // Preserve legacy 3-segment route. Missing category content receives
        // no synthetic products or redirect that would break indexed links.
        $parent=null;
        $child=null;
        foreach (Catalog::categories() as $row) {
            if ($row['slug']===$mainSlug) $parent=$row;
            if ($row['slug']===$subSlug) $child=$row;
        }
        if (!$parent || !$child ||
            (int)$child['parent_legacy_id']!==(int)$parent['legacy_id']) {
            http_response_code(404);
            $this->render('frontend/404',['pageTitle'=>'Yazılım Kategorisi Bulunamadı']);
            return;
        }
        $_GET['category']=$subSlug;
        $this->renderCatalog('/hazir-scriptler/'.rawurlencode($mainSlug).'/'.rawurlencode($subSlug));
    }

    public function detail(string $slug): void
    {
        $p=Catalog::find($slug);
        if(!$p){
            http_response_code(404);
            $this->render('frontend/404',['pageTitle'=>'Yazılım Bulunamadı']);
            return;
        }
        $data=Catalog::jsonFields($p);
        $reviews=Catalog::reviews((int)$p['legacy_id']);
        $ogImage=Catalog::publicUrl($data['og_image']??null);
        if(!$ogImage && str_starts_with((string)($data['og_image']??''),'/uploads/')){
            $ogImage=url((string)$data['og_image']);
        }
        if(!$ogImage && !empty($p['cover_image']))$ogImage=url($p['cover_image']);
        $schema=[
            '@context'=>'https://schema.org','@type'=>'SoftwareApplication',
            'name'=>$p['name'],
            'url'=>url('/hazir-scriptler/'.$p['slug']),
            'description'=>trim(strip_tags((string)($p['short_desc']??''))),
            'offers'=>[
                '@type'=>'Offer','price'=>(float)$p['price'],
                'priceCurrency'=>'TRY',
                'availability'=>'https://schema.org/InStock',
            ],
            'applicationCategory'=>'BusinessApplication',
        ];
        if(!empty($p['cover_image']))$schema['image']=url($p['cover_image']);
        if(!empty($data['current_version']))$schema['softwareVersion']=(string)$data['current_version'];
        if($reviews){
            $schema['aggregateRating']=[
                '@type'=>'AggregateRating',
                'ratingValue'=>round(array_sum(array_map(static fn($r)=>(int)$r['rating'],$reviews))/count($reviews),2),
                'reviewCount'=>count($reviews),
            ];
        }
        $this->render('frontend/netvera-script-detail',[
            'pageTitle'=>$p['meta_title'] ?: $p['name'],
            'metaDescription'=>$p['meta_description'] ?: strip_tags((string)$p['short_desc']),
            'canonicalUrl'=>url('/hazir-scriptler/'.$p['slug']),
            'ogType'=>'product',
            'ogTitle'=>trim((string)($data['og_title']??'')) ?: ($p['meta_title'] ?: $p['name']),
            'ogDescription'=>trim((string)($data['og_description']??'')) ?: ($p['meta_description'] ?: strip_tags((string)$p['short_desc'])),
            'ogImage'=>$ogImage,
            'schema'=>json_encode($schema,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|
                JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT),
            'product'=>$p,'productData'=>$data,
            'gallery'=>Catalog::gallery((int)$p['legacy_id']),
            'reviews'=>$reviews,
            'modules'=>Catalog::arrayField($data,'modules_json'),
            'technical'=>Catalog::arrayField($data,'specs_json'),
            'license'=>Catalog::arrayField($data,'license_json'),
            'faqs'=>Catalog::arrayField($data,'faq_json'),
            'demoUrl'=>Catalog::publicUrl($data['demo_url']??null),
        ]);
    }
}
