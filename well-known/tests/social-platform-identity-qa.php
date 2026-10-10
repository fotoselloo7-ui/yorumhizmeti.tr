<?php
declare(strict_types=1);
$root=dirname(__DIR__);
require_once $root.'/app/Services/SocialPlatformIdentity.php';
use App\Services\SocialPlatformIdentity as Brand;

$platforms=Brand::all();
$css=(string)file_get_contents($root.'/public/assets/css/social-platform-identity-v77.css');
if(count($platforms)<15)throw new RuntimeException('Missing social platform identities.');
foreach($platforms as $key=>[$label,$icon,$a,$b,$soft]){
    if(Brand::fromCategory(['name'=>$label,'slug'=>$key.'-hizmetleri'])!==$key)
       throw new RuntimeException('Wrong brand for '.$key);
    if(Brand::classFor($key)!=='nv-platform-'.$key)
       throw new RuntimeException('Invalid class '.$key);
    if(!str_contains($css,'.nv-platform-'.$key.'{')||
       !str_contains($css,'--np:'.$a.';')||
       !str_contains($css,'--np-2:'.$b.';')){
       throw new RuntimeException('Palette mismatch: '.$key);
    }
    if(!preg_match('/^#[a-f0-9]{6}$/i',$soft))
       throw new RuntimeException('Invalid background '.$key);
}
if(Brand::fromText('Hazır Yazılımlar')!==null ||
   Brand::fromText('Kurumsal Web Sitesi Scripti')!==null)
   throw new RuntimeException('Software must not inherit any social platform style.');
if(Brand::fromText('Instagram Takipçi')!=='instagram' ||
   Brand::fromText('TikTok Video İzlenme')!=='tiktok' ||
   Brand::fromText('YouTube Abone')!=='youtube' ||
   Brand::fromText('Facebook Sayfa')!=='facebook' ||
   Brand::fromText('Google İşletme')!=='google' ||
   Brand::fromText('X Takipçi')!=='twitter')
   throw new RuntimeException('Social brand aliases do not resolve.');
foreach([
 'resources/views/frontend/categories.php'=>'SocialPlatformIdentity::fromCategory',
 'resources/views/frontend/category-detail.php'=>'$nvPlatformClass',
 'resources/views/frontend/package-detail.php'=>'$nvPlatformClass',
 'resources/views/frontend/home.php'=>'$nvChildParentPlatform'
] as $path=>$needle){
    $text=(string)file_get_contents($root.'/'.$path);
    if(!str_contains($text,$needle))throw new RuntimeException('Brand class not rendered in '.$path);
}
$layout=(string)file_get_contents($root.'/resources/views/layouts/app.php');
$theme=strpos($layout,'theme-runtime-v71.css');
$palette=strpos($layout,'social-platform-identity-v77.css');
if($theme===false||$palette===false||$palette<=$theme)
    throw new RuntimeException('Brand CSS must override the generic NetVera theme.');
foreach(['#featured .nv43-category-card','nv26-catalog-tiles',
          '.yv-category-v5[class*="nv-platform-"]',
          '.yv-product-v5[class*="nv-platform-"]'] as $needle){
    if(!str_contains($css,$needle))
        throw new RuntimeException('Platform colors missing from '. $needle);
}
echo 'PASS '.count($platforms)." distinctive corporate palettes: catalog, featured home, child service, package and category views; software unaffected\n";
