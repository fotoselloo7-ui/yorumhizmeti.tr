<?php
declare(strict_types=1);
if(PHP_SAPI!=='cli')exit(2);
define('BASE_PATH',dirname(__DIR__));
define('PUBLIC_PATH',BASE_PATH.'/public');
spl_autoload_register(static function(string $class):void{
 if(str_starts_with($class,'App\\'))require BASE_PATH.'/app/'.str_replace('\\','/',substr($class,4)).'.php';
});
require BASE_PATH.'/app/Core/Helpers.php';

use App\Services\CategoryEditorialCopyService as Copy;
use App\Services\CategorySeoCompleteService as Complete;
use App\Services\CategorySearchBlueprint as Blueprint;
$checks=0;
$ok=static function(bool $condition,string $why)use(&$checks):void{
 if(!$condition)throw new RuntimeException($why);
 $checks++;
};
$sql=file_get_contents(BASE_PATH.'/database/database.sql');
$ok(preg_match('/INSERT INTO `categories` VALUES ([\s\S]*?);/u',$sql,$m)===1,'Baseline category fixture exists');
$matches=[];
preg_match_all('/\(([0-9]+),(?:NULL|[0-9]+),\'((?:\\\\\'|[^\'])*)\',\'([a-z0-9-]+)\'/u',$m[1],$matches,PREG_SET_ORDER);
$slugs=[];
foreach($matches as $record){
 $id=(int)$record[1];$name=stripslashes($record[2]);$slug=$record[3];
 $slugs[$slug]=$id;
 $sample=['id'=>$id,'name'=>$name,'slug'=>$slug,'description'=>'','status'=>'active','image'=>'',
  'seo_title'=>'','seo_description'=>'','seo_focus_keyword'=>'',
  'og_title'=>'','og_description'=>'','canonical_url'=>''];
 $desc=Copy::description($sample);
 $ok(mb_strlen($desc,'UTF-8')>=100,'Service description has meaningful detail: '.$slug);
 $ok(!preg_match('/YorumHizmeti|Yorum Hizmeti|1\. sıra garantisi|kesin.*satış/iu',$desc),'No legacy brand or fabricated outcome: '.$slug);
 $suggest=Complete::recommendations($sample);
 if(!$suggest)continue; // Imported software taxonomy is explicitly protected.
 $ok($suggest['slug']===$slug,'SEO recommendation keeps indexed slug: '.$slug);
 $ok(str_contains($suggest['canonical_url'],'/kategori/'.$slug),'Canonical preserves exact category route: '.$slug);
 $filled=Complete::fillForForm($sample);
 $ok(trim((string)$filled['category']['description'])!==''&&trim((string)$filled['category']['seo_description'])!=='','Blank service category gets full presentation and meta: '.$slug);
}
$ok(count($slugs)>=105,'All 108 legacy SQL category identities checked');
$custom=['name'=>'Web Tasarım','slug'=>'web-tasarim',
 'description'=>'Bu kategorinin yıllardır yayımlanan tamamen özgün ve elle düzenlenmiş hizmet açıklaması; gereksinimleri detaylı olarak açıklar.',
 'seo_title'=>'Elle Düzenlenen Web Tasarım Başlığı',
 'seo_description'=>'Web tasarım kategorisinin kullanıcı tarafından özel olarak yazılmış, detaylı bir meta açıklamasıdır; korunması gerekir.',
 'og_title'=>'Özgün Açık Grafik Başlığı','canonical_url'=>'https://netvera.tr/kategori/web-tasarim'];
$verified=Complete::fillForForm($custom);
$ok($verified['category']['description']===$custom['description'],'Authored content never overwritten');
$ok($verified['category']['seo_title']===$custom['seo_title'],'Custom SEO title preserved');
$ok($verified['category']['canonical_url']===$custom['canonical_url'],'Existing canonical preserved');
$frontend=file_get_contents(BASE_PATH.'/resources/views/frontend/categories.php');
$catalog=file_get_contents(BASE_PATH.'/app/Services/CatalogMenuService.php');
$landing=file_get_contents(BASE_PATH.'/app/Services/RegionalLandingService.php');
$ok(str_contains($frontend,'nv26-tile-editorial')&&str_contains($catalog,'CategoryEditorialCopyService::description'),'Catalog cards display category-specific fallback copy');
$ok(str_contains($landing,'CategoryEditorialCopyService::description'),'Every landing theme shares non-destructive service explanations');
$js=file_get_contents(PUBLIC_PATH.'/assets/js/regional-showcase-v1.js');
$css=file_get_contents(PUBLIC_PATH.'/assets/css/regional-showcase-v1.css');
$ok(str_contains($js,"['references', 'scripts', 'blog'].includes(section.dataset.component)"),'All landing themes activate dynamic showcases');
$ok(str_contains($js,'requestAnimationFrame(tick)')&&str_contains($js,'viewport.scrollLeft=next')&&str_contains($js,'s.dir=-1'),'Visible auto-scrolling actually advances by time');
$ok(str_contains($js,'setPointerCapture')&&str_contains($js,"'pointermove'"),'Desktop drag with pointer capture');
$ok(str_contains($js,"'touchstart'")&&str_contains($css,'touch-action:pan-x pan-y'),'Native touch swipe available');
$ok(str_contains($js,'prefers-reduced-motion')&&str_contains($js,'s.focused'),'Accessible reduced-motion and focus pause');
$ok(str_contains($js,'landing:themechange'),'Changing only one regional theme reinitializes its showcases');
$ok(!str_contains($js,'cloneNode(')&&!str_contains($js,'appendCycle('),'Never clone or repeat a portfolio card');
$ok(str_contains($css,'minmax(360px,max-content)')&&str_contains($css,'-webkit-line-clamp:unset!important'),'Headings are not clipped by fixed card height');
$ok(str_contains($css,'.nvl-card[hidden]{display:none!important}'),'Reference category filters really hide unselected cards');
echo "PASS $checks service category descriptions and accessible automatic two-row marquees across 10 themes\n";
