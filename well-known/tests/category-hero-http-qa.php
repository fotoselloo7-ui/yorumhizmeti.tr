<?php
/** Read-only public rendering audit against the isolated UI QA server/database. */
declare(strict_types=1);
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
define('BASE_PATH',dirname(__DIR__));define('PUBLIC_PATH',BASE_PATH.'/public');
$_ENV=parse_ini_file(BASE_PATH.'/.env');$_ENV['DB_NAME']='netvera_ui_audit_20261013';
$_ENV['APP_URL']='http://127.0.0.1:8017';
spl_autoload_register(static function(string $class):void{if(str_starts_with($class,'App\\'))require_once BASE_PATH.'/app/'.str_replace('\\','/',substr($class,4)).'.php';});
require BASE_PATH.'/app/Core/Helpers.php';
use App\Services\CategoryHeroService as Hero;
use App\Services\NetveraBridgeService as Catalog;
use App\Services\PublicSeoUrls as URLs;
$count=0;$pages=0;$titles=[];
$assert=static function(bool $ok,string $why)use(&$count):void{if(!$ok)throw new RuntimeException($why);$count++;};
$ch=curl_init();curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_TIMEOUT=>15,CURLOPT_FOLLOWLOCATION=>false]);
$check=static function(string $path,string $name,?array $seo=null)use($ch,$assert,&$pages,&$titles):DOMXPath{
 curl_setopt($ch,CURLOPT_URL,'http://127.0.0.1:8017'.$path);$html=curl_exec($ch);
 $assert($html!==false&&curl_getinfo($ch,CURLINFO_HTTP_CODE)===200,'200 without redirect: '.$path);
 $doc=new DOMDocument();libxml_use_internal_errors(true);$doc->loadHTML('<?xml encoding="UTF-8">'.$html);libxml_clear_errors();$x=new DOMXPath($doc);
 $text=static fn(string $query):string=>trim($x->evaluate('string('.$query.')'));
 $assert($x->query('//h1')->length===1,'Single H1: '.$path);
 $assert(str_contains($text('//h1'),$name),'Actual category H1: '.$path);
 $title=$text('//title');$assert($title!==''&&!isset($titles[$title]),'Unique rendered title: '.$path);$titles[$title]=true;
 $assert($text('//link[@rel="canonical"]/@href')==='http://127.0.0.1:8017'.$path,'Canonical preserved: '.$path);
 $assert($text('//meta[@name="description"]/@content')!=='','Description exists: '.$path);
 $img=$x->query('//div[contains(concat(" ",normalize-space(@class)," ")," nv-category-hero-media ")]/img | //div[contains(concat(" ",normalize-space(@class)," ")," social-hero-art ")]/picture/img')->item(0);
 $assert($img instanceof DOMElement&&$img->getAttribute('alt')!==''&&$img->getAttribute('width')==='1536','Honest local banner / intrinsic image: '.$path);
 $src=$img->getAttribute('src');$assert((str_starts_with($src,'/assets/img/category-heroes/')||str_starts_with($src,'/assets/img/social-storefront/'))&&is_file(PUBLIC_PATH.$src),'Final category image resolves: '.$path);
 $assert($text('//meta[@property="og:image"]/@content')==='http://127.0.0.1:8017'.$src,'OG follows banner: '.$path);
 $assert(!str_contains($html,'hero-woman-cutout'),'Old shared portrait absent: '.$path);
 if($seo){foreach(['meta_title'=>'//title','meta_description'=>'//meta[@name="description"]/@content']as $key=>$query)if(!empty($seo[$key]))$assert($text($query)===$seo[$key],'Authored catalogue SEO preserved: '.$path);}
 $pages++;return $x;
};
$db=App\Core\Database::getInstance();
$services=$db->fetchAll('SELECT c.*,p.name AS parent_category_name,p.slug AS parent_category_slug FROM categories c LEFT JOIN categories p ON p.id=c.parent_id WHERE c.status="active" AND (c.parent_id IS NULL OR p.status="active")');
foreach($services as $c){
 if(App\Services\ServiceCategoryVisibility::isLegacySoftware($c))continue;
 $path=URLs::path('/kategori',$c['slug']);$x=$check($path,$c['name']);
 if(!empty($c['parent_category_slug'])){
  $assert($x->query('//nav[@aria-label="İçerik yolu"]/a[@href="'.URLs::path('/kategori',$c['parent_category_slug']).'"]')->length===1,'Parent breadcrumb: '.$path);
  foreach($x->query('//script[@type="application/ld+json"]')as $node)foreach(json_decode($node->textContent,true)['@graph']??[]as $schema){
   if(($schema['@type']??'')==='BreadcrumbList')$assert(count($schema['itemListElement'])===4&&$schema['itemListElement'][2]['name']===$c['parent_category_name'],'Structured breadcrumb matches visible hierarchy: '.$path);
  }
 }
}
$serviceCount=$pages;
$check('/hazir-scriptler','Hazır Yazılımlar & Scriptler');$categories=Catalog::categories();
foreach($categories as $c)$check(URLs::scriptCategory($c,$categories),$c['name'],$c);
foreach(App\Services\NetveraScriptTypeService::definitions()as [$name,$slug])$check(URLs::path('/hazir-scriptler/tur',$slug),$name);
curl_close($ch);
echo "PASS $count HTTP contracts on $pages pages ($serviceCount service categories, ".count($categories)." software categories, 34 software types + root); no database writes\n";
