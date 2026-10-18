<?php
/** Writes only in a rolled-back transaction on an existing owned synthetic QA DB. */
declare(strict_types=1);
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
define('BASE_PATH',dirname(__DIR__));define('PUBLIC_PATH',BASE_PATH.'/public');
$_ENV=parse_ini_file(BASE_PATH.'/.env');$_ENV['DB_NAME']='netvera_social_sales_qa_20261017';$_ENV['APP_URL']='http://127.0.0.1:8018';
spl_autoload_register(static function(string $class):void{if(str_starts_with($class,'App\\'))require_once BASE_PATH.'/app/'.str_replace('\\','/',substr($class,4)).'.php';});
require BASE_PATH.'/app/Core/Helpers.php';
use App\Services\NetveraSeoBridge as SEO;
use App\Services\SeoMetadataService as Meta;
$db=App\Core\Database::getInstance();$pdo=$db->getPdo();$pdo->beginTransaction();
$n=0;$ok=static function(bool $pass,string $why)use(&$n):void{if(!$pass)throw new RuntimeException($why);$n++;};
try{
 SEO::save('category',14,['nvseo_robots'=>'noindex,follow','nvseo_max_snippet'=>'0','nvseo_max_image_preview'=>'none','nvseo_max_video_preview'=>'-1']);
 $ok($pdo->inTransaction(),'SEO save cannot implicitly commit an editor transaction');
 $stored=SEO::get('category',14);$ok($stored['max_snippet']==='0'&&$stored['max_image_preview']==='none','Preview fields saved in existing JSON metadata');
 SEO::save('category',14,['nvseo_robots'=>'noindex,follow']);$stored=SEO::get('category',14);
 $ok($stored['max_snippet']==='0','Legacy batch submission preserves new optional preview fields');
 $prepared=Meta::prepare(['nvSeoData'=>$stored],'frontend/category-detail','app','/kategori/instagram-takipci');
 $ok(!Meta::indexable($prepared['nvRobots'])&&str_contains($prepared['nvRobots'],'max-snippet:0'),'Saved policy reaches real view metadata');
 $category=$db->fetch('SELECT * FROM categories WHERE id=14');
 $_POST=['nvseo_robots'=>'noindex,follow','nvseo_max_snippet'=>'','nvseo_max_image_preview'=>'','nvseo_max_video_preview'=>''];
 $fill=new ReflectionMethod(App\Controllers\Admin\CategoryController::class,'populateMissingSeo');
 $fill->invokeArgs(new App\Controllers\Admin\CategoryController(),[&$category,14]);
 $ok($_POST['nvseo_max_snippet']===''&&$_POST['nvseo_max_image_preview']==='','Legacy category copy autofill must not restore deliberately cleared preview controls');
 SEO::save('category',14,$_POST);$cleared=SEO::get('category',14);
 $ok($cleared['max_snippet']===''&&$cleared['max_image_preview']===''&&$cleared['max_video_preview']==='','Admin clear/reset persists through the real category preparation chain');
 $baseline=(new App\Services\SitemapService())->generate();
 $ok(!str_contains($baseline,'/kategori/instagram-takipci</loc>'),'Editorial noindex omitted from sitemap');
 $pkg=$db->fetch("SELECT id,slug FROM packages WHERE status='active' ORDER BY id LIMIT 1");
 SEO::save('package',(int)$pkg['id'],['nvseo_robots'=>'noindex,follow']);
 $map=(new App\Services\SitemapService())->generate();
 $ok(!str_contains($map,'/paket/'.$pkg['slug'].'</loc>'),'Package noindex omitted from sitemap');
 $db->query("UPDATE categories SET status='inactive' WHERE id=2");
 $map=(new App\Services\SitemapService())->generate();
 $ok(!str_contains($map,'/kategori/instagram-begeni</loc>'),'Inactive parent cannot leave child URLs in sitemap');
 $ok(!str_contains($map,'1970-01-01'),'No bogus epoch freshness');
 $doc=new DOMDocument();$ok($doc->loadXML($map),'Generated sitemap is valid XML');
 $locs=[];foreach($doc->getElementsByTagName('loc')as $loc)$locs[]=$loc->textContent;
 $ok(count($locs)===count(array_unique($locs)),'Sitemap URLs are unique');
}finally{if($pdo->inTransaction())$pdo->rollBack();}
echo "PASS $n preview persistence / transaction / sitemap integration contracts; QA writes rolled back\n";
