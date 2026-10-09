<?php
declare(strict_types=1);

$root=dirname(__DIR__);
require_once $root.'/app/Services/CategorySearchBlueprint.php';
require_once $root.'/app/Services/CategorySeoCompleteService.php';

use App\Services\CategorySearchBlueprint as Blueprint;
use App\Services\CategorySeoCompleteService as Complete;

$profiles=Blueprint::profiles();
if(count($profiles)<30)throw new RuntimeException('Category intent coverage unexpectedly small.');
$required=[
 'secondary_keywords','robots','geo_summary','entity_topics','service_area',
 'author_name','author_type','author_url','same_as_urls','content_intent',
 'main_question','direct_answer','sources','reviewer_name','last_reviewed','image_title'
];
$tested=0;
foreach($profiles as $slug=>$profile){
    $sample=[
      'slug'=>$slug,'name'=>str_replace('-',' ',ucwords($slug,'-')),
      'status'=>'active','description'=>'','seo_title'=>'','seo_description'=>'',
      'seo_focus_keyword'=>'','canonical_url'=>'','og_title'=>'','og_description'=>''
    ];
    $suggestion=Complete::recommendations($sample);
    if(!$suggestion)throw new RuntimeException('Missing SEO data: '.$slug);
    if($suggestion['slug']!==$slug)throw new RuntimeException('Never rewrite indexed category slugs.');
    if($suggestion['canonical_url']!=='https://netvera.tr/kategori/'.$slug)
       throw new RuntimeException('Noncanonical service URL '.$slug);
    foreach(['description','seo_title','seo_description','seo_focus_keyword',
             'canonical_url','og_title','og_description'] as $field){
      if(trim((string)$suggestion[$field])==='')throw new RuntimeException($slug.' missing '.$field);
    }
    foreach($required as $key){
      if(!array_key_exists($key,$suggestion['extra']))throw new RuntimeException('Missing SEO module '.$key);
    }
    foreach(['geo_summary','author_name','author_type','author_url',
             'main_question','direct_answer','entity_topics','service_area','robots','content_intent'] as $key){
      if(trim($suggestion['extra'][$key])==='')throw new RuntimeException($slug.' missing extra '.$key);
    }
    // The editorial reviewer, date, official brand profiles and fabricated sources are NEVER invented.
    foreach(['reviewer_name','last_reviewed','same_as_urls'] as $key){
      if($suggestion['extra'][$key]!=='')throw new RuntimeException('Unverified editorial metadata inserted.');
    }
    $filled=Complete::fillForForm($sample);
    if(($filled['category']['seo_title']??'')==='')throw new RuntimeException('Admin category form not prefilled.');
    $tested++;
}
foreach([
 ['name'=>'Hazır Yazılımlar','slug'=>'hazir-yazilimlar'],
 ['name'=>'Web Sitesi Scriptleri','slug'=>'web-sitesi-scriptleri'],
 ['name'=>'NetVera Yazılım','slug'=>'netvera-yazilim'],
 ['name'=>'Sektörel Yönetim Yazılımları','slug'=>'sektorel-yonetim-yazilimlari']
] as $software){
 if(Complete::recommendations($software))throw new RuntimeException('Software SEO must not be modified.');
}
$custom=[
 'slug'=>'instagram-takipci','name'=>'Instagram Takipçi',
 'seo_title'=>'Özgün SEO Başlığı','seo_description'=>'Benzersiz kategori açıklaması müşterinin özel hizmetini anlatır ve ayrıntılı olarak sipariş koşullarını açıklar.',
 'seo_focus_keyword'=>'instagram takipçi al','description'=>'Müşteri tarafından eklenen özgün açıklama.',
 'og_title'=>'Özgün OG Başlığı','canonical_url'=>'https://netvera.tr/kategori/instagram-takipci',
];
$preserved=Complete::fillForForm($custom);
if($preserved['category']['seo_title']!=='Özgün SEO Başlığı')throw new RuntimeException('Hand-authored SEO title lost.');
if($preserved['category']['og_title']!=='Özgün OG Başlığı')throw new RuntimeException('Hand-authored OG lost.');
if($preserved['category']['slug']!==$custom['slug'])throw new RuntimeException('Slug mutation.');
$view=file_get_contents($root.'/resources/views/admin/categories/form.php');
foreach(['name="seo_title"','name="seo_description"','name="seo_focus_keyword"',
         'name="canonical_url"','name="og_title"','name="og_description"',
         'name="image_alt"','id="nvSeoFillCategory"'] as $item) {
  if(!str_contains($view,$item))throw new RuntimeException('Missing category editor field '.$item);
}
$advanced=file_get_contents($root.'/resources/views/admin/partials/netvera-seo.php');
foreach($required as $key){
  if(!str_contains($advanced,'nvseo_'.$key))throw new RuntimeException('Missing GEO/AIO module '.$key);
}
$home=file_get_contents($root.'/resources/views/frontend/home.php');
if(!str_contains($home,'Yazılım, Dijital Ajans ve <span>Sosyal Medya Hizmetleri</span>'))
  throw new RuntimeException('Corporate homepage SEO heading missing.');
foreach(['Yeni Yorum Geldi!','50.000+','1.248','4.9/5'] as $fake){
  if(str_contains($home,$fake))throw new RuntimeException('Unverified old social proof visible: '.$fake);
}
$routes=file_get_contents($root.'/app/Core/App.php');
foreach(['CategoryController@seoSuggestion','CategoryController@optimizeCategorySeo'] as $route)
  if(!str_contains($routes,$route))throw new RuntimeException('SEO form route missing '.$route);
echo "PASS {$tested} targeted non-software category profiles; complete SEO/GEO/AIO forms; unique canonical URLs; human editorial metadata guarded\n";
