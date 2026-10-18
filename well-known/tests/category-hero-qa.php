<?php
/** Pure contracts by default; --integration writes only inside a rolled-back QA transaction. */
declare(strict_types=1);
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
define('BASE_PATH',dirname(__DIR__));define('PUBLIC_PATH',BASE_PATH.'/public');
spl_autoload_register(static function(string $class):void{if(str_starts_with($class,'App\\'))require_once BASE_PATH.'/app/'.str_replace('\\','/',substr($class,4)).'.php';});
require BASE_PATH.'/app/Core/Helpers.php';
use App\Services\CategoryHeroService as Hero;
$count=0;
$assert=static function(bool $ok,string $why)use(&$count):void{if(!$ok)throw new RuntimeException($why);$count++;};
foreach([
 ['Instagram Takipçi','instagram-takipci','audience'],['Instagram Beğeni','instagram-begeni','engagement'],
 ['TikTok Yorum','tiktok-yorum','engagement'],['TikTok Canlı Yayın','tiktok-canli-yayin','video'],
 ['YouTube Beğeni','youtube-begeni','engagement'],['YouTube Abone','youtube-abone','audience'],
 ['Spotify Dinlenme','spotify-dinlenme','audio'],['Discord Sunucu Boost','discord-sunucu-boost','community'],
 ['Web Site Hizmetleri','web-site-hizmetleri','web'],['WordPress Site','wordpress-site','web'],
 ['E-Ticaret Site','eticaret-site','ecommerce'],['App Store Yayınlama','app-store-yayinlama','mobile'],
 ['Logo Tasarım','logo-tasarim','design'],['Reklam Banner','reklam-banner','design'],
 ['Blog Makale','blog-makale','content'],['İçerik SEO','icerik-seo','search'],
 ['Yerel SEO','yerel-seo','local'],['Instagram Reklam','instagram-reklam','ads'],
 ['Şikayet Yönetimi','sikayet-yonetimi','reputation'],['Google İşletme Profili','google-isletme-profili','local'],
] as [$name,$slug,$family]){
 $c=['name'=>$name,'slug'=>$slug,'parent_id'=>1];$d=Hero::defaults($c);$seo=Hero::seoProfile($c);
 $assert($d['visual']===$family,'Service-specific asset: '.$slug);
 $assert(str_contains($seo['title'],$name),'Category-specific search title: '.$slug);
 $assert(str_contains($d['alt'],$name)&&str_contains($d['alt'],'illüstrasyonu'),'Descriptive honest alt: '.$slug);
 $assert(!preg_match('/1\.5K|organik büyüme|dakikalar içinde|%100|birinci sayfa|ilk sırada/iu',json_encode($d,JSON_UNESCAPED_UNICODE)),'No fabricated proof or ranking promise');
}
$c=['name'=>'Instagram Takipçi','slug'=>'instagram-takipci','parent_id'=>2];
$custom=Hero::resolve($c,['lead'=>'Özel başlık','accent'=>'İkinci satır','description'=>'Özgün açıklama','visual'=>'ads']);
$assert($custom['lead']==='Özel başlık'&&$custom['description']==='Özgün açıklama','Admin overrides respected');
$assert(str_contains($custom['src'],'/ads.webp')&&str_contains($custom['alt'],'megafon'),'Alt follows selected illustration');
$fallback=Hero::resolve($c,['visual'=>['invalid'],'lead'=>['invalid']]);
$assert($fallback['lead']==='Instagram'&&$fallback['visual']==='audience','Malformed optional stored metadata safely falls back');
$assert(!Hero::resolve($c+['image'=>'/uploads/not-found.png'],['visual'=>'upload'])['custom'],'Missing custom image safely falls back');
foreach([['hero_visual'=>'../../x'],['hero_lead'=>['bad']],['hero_description'=>str_repeat('x',501)]]as $bad){
 try{Hero::validate($bad);$rejected=false;}catch(RuntimeException $e){$rejected=true;}$assert($rejected,'Invalid admin banner payload rejected');
}
$clean=Hero::validate(['hero_lead'=>'<b>Başlık</b>','hero_visual'=>'audience']);
$assert($clean['lead']==='Başlık','Markup stripped before storing banner text');
$protected=Hero::decorateSeo($c+['seo_title'=>'Özgün editör başlığı','seo_description'=>str_repeat('Özgün ve ayrıntılı hizmet açıklaması. ',4)]);
$assert($protected['seo_title']==='Özgün editör başlığı','Authored SEO title preserved');
$legacy=Hero::decorateSeo($c+['seo_title'=>'Instagram - Yorum Hizmeti','seo_description'=>'']);
$assert($legacy['seo_title']==='Instagram Takipçi Paketleri ve Fiyatları | NetVera','Legacy title replaced without URL mutation');
$assert($legacy['slug']===$c['slug'],'Indexed slug unchanged');
$view=file_get_contents(BASE_PATH.'/resources/views/frontend/partials/category-hero.php');
$assert(str_contains($view,'fetchpriority="high"')&&str_contains($view,'width="1536" height="1024"'),'Hero reserves intrinsic size and priority');
$assert(!str_contains(file_get_contents(BASE_PATH.'/resources/views/frontend/category-detail.php'),'hero-woman-cutout'),'Shared woman removed from category banner runtime');
foreach(array_keys(Hero::families())as $key)$assert(is_file(PUBLIC_PATH.'/assets/img/category-heroes/'.$key.'.webp'),'Final local asset exists: '.$key);
foreach([
 ['name'=>'E-Ticaret Yazılımı','slug'=>'e-ticaret-yazilimi','description'=>'Yönetilebilir mağaza ve ürün kataloğu.'],
 ['name'=>'Haber Sitesi Yazılımı','slug'=>'haber-sitesi-scripti','description'=>'Editoryal CMS ve haber yayınlama.'],
]as $catalog){
 $banner=Hero::catalog($catalog);
 $assert(trim($banner['lead'].' '.$banner['accent'])===$catalog['name'],'Software category keeps its real H1');
 $assert($banner['description']===$catalog['description']&&$banner['cta']==='Yazılımları İncele','Software category uses its authored body / correct CTA');
}

if(in_array('--integration',$argv,true)){
 $_ENV=parse_ini_file(BASE_PATH.'/.env');$_ENV['DB_NAME']='netvera_social_sales_qa_20261017';
 $db=App\Core\Database::getInstance();$pdo=$db->getPdo();$pdo->beginTransaction();
 try{
  Hero::save(14,['hero_lead'=>'Sentetik QA','hero_visual'=>'audience']);
  Hero::save(14,['hero_lead'=>'Yazılım QA','hero_visual'=>'web'],'script');
  $assert(Hero::options(14)['lead']==='Sentetik QA'&&Hero::options(14,'script')['lead']==='Yazılım QA','Service / imported category IDs have isolated namespaces');
  $assert(Hero::get($c+['id'=>14])['lead']==='Sentetik QA','Admin setting resolves into actual banner');
  Hero::save(14,['hero_description'=>'Sentetik yazılım banner açıklaması','hero_visual'=>'web'],'script');
  $assert(Hero::catalog(['legacy_id'=>14,'name'=>'Yazılım QA','slug'=>'yazilim-qa'])['description']==='Sentetik yazılım banner açıklaması','Explicit software banner copy survives empty category prose');
  $rows=$db->fetchAll('SELECT c.*,p.name AS parent_category_name,p.slug AS parent_category_slug FROM categories c LEFT JOIN categories p ON p.id=c.parent_id WHERE c.status="active" AND (c.parent_id IS NULL OR p.status="active")');
  $titles=[];$families=[];$covered=0;
  foreach($rows as $row){
   if(App\Services\ServiceCategoryVisibility::isLegacySoftware($row))continue;
   $d=Hero::defaults($row);$seo=Hero::seoProfile($row);
   $assert(trim($d['lead'].' '.$d['accent'])!==''&&$d['description']!==''&&$d['alt']!=='','Complete banner: '.$row['slug']);
   $assert(str_contains($seo['title'],$row['name']),'No parent-title duplication: '.$row['slug']);
   $assert(!isset($titles[$seo['title']]),'Unique title: '.$row['slug']);
   $titles[$seo['title']]=true;$families[$d['visual']]=true;$covered++;
  }
  $assert($covered>=108&&count($families)===14,'Full service category/subcategory and visual-family coverage');
  echo "DB coverage: $covered categories, ".count($families)." visual families; settings writes rolled back.\n";
 }finally{$pdo->rollBack();}
}
echo "PASS $count category hero, intent, image, admin, safety and SEO contracts\n";
