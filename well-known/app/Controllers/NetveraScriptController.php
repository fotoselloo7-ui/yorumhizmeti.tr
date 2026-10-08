<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Services\NetveraBridgeService as Catalog;

class NetveraScriptController extends Controller
{
    public function index(): void
    {
        $q=mb_substr(trim((string)($_GET['q']??'')),0,70);
        $cat=trim((string)($_GET['category']??''));
        $categories=Catalog::categories();$categoryId=null;
        foreach($categories as $row)if($row['slug']===$cat)$categoryId=(int)$row['legacy_id'];
        $this->render('frontend/netvera-scripts',[
            'pageTitle'=>'Hazır Scriptler ve Profesyonel Yazılımlar',
            'metaDescription'=>'Sektörel PHP web yazılımları, otomasyon, CMS ve hazır script ürünleri.',
            'canonicalUrl'=>url('/hazir-scriptler'),
            'products'=>Catalog::all($q,$categoryId),
            'categories'=>$categories,'filterCategory'=>$cat,'filterQuery'=>$q,
        ]);
    }

    public function subCategoryPage(string $mainSlug,string $subSlug): void
    {
        // Preserve legacy 3-segment route. Missing category content receives
        // no synthetic products or redirect that would break indexed links.
        $_GET['category']=$subSlug;
        $this->index();
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
        $schema=[
            '@context'=>'https://schema.org','@type'=>'SoftwareApplication',
            'name'=>$p['name'],
            'description'=>trim(strip_tags((string)($p['short_desc']??''))),
            'offers'=>[
                '@type'=>'Offer','price'=>(float)$p['price'],
                'priceCurrency'=>'TRY',
                'availability'=>'https://schema.org/InStock',
            ],
            'applicationCategory'=>'BusinessApplication',
        ];
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
            'ogTitle'=>$p['meta_title'] ?: $p['name'],
            'ogDescription'=>$p['meta_description'] ?: strip_tags((string)$p['short_desc']),
            'ogImage'=>!empty($p['cover_image'])?url($p['cover_image']):null,
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
