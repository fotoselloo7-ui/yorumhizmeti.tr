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
$home=(string)file_get_contents($root.'/resources/views/frontend/home.php');
if(!str_contains($home,'yh49-category-link') ||
   !str_contains($home,"SocialPlatformIdentity::fromText((\$yh49Item['style']") ||
   !str_contains($home,"(\$yh49Item['detail']??'')")){
    throw new RuntimeException('Screenshot marquee parents/children still use generic purple.');
}
$layout=(string)file_get_contents($root.'/resources/views/layouts/app.php');
if(!str_contains($layout,'SocialPlatformIdentity::fromCategory($nv26Cat)') ||
   !str_contains($layout,"'brand' => \\App\\Services\\SocialPlatformIdentity::classFor") ||
   !str_contains($layout,"class=\"nv56-quick-category <?= e(\$nv56Link['brand']")){
    throw new RuntimeException('Platform brand missing in mega menu or quick access.');
}
$mustHave=['spotify'=>'#1DB954','facebook'=>'#1877F2',
  'telegram'=>'#229ED9','discord'=>'#5865F2','youtube'=>'#FF0033',
  'tiktok'=>'#111111','instagram'=>'#E1306C','twitter'=>'#111111',
  'threads'=>'#121212','linkedin'=>'#0A66C2'];
foreach($mustHave as $key=>$expected){
    if(!str_contains($css,'.nv-platform-'.$key.'{--np:'.$expected.';'))
        throw new RuntimeException('Missing official platform token '.$key);
}
foreach([
  '.yh49-category-marquee .yh49-category-link[class*="nv-platform-"]',
  '.yh49-category-link.nv-platform-spotify',
  '.yh49-category-link.nv-platform-telegram',
  '.yh49-category-link.nv-platform-discord',
  '.yh49-category-link.nv-platform-twitter',
  '.yh49-category-link.nv-platform-threads',
  '.yh49-category-link.nv-platform-facebook',
  '.yh49-category-link.nv-platform-youtube',
  '.yh49-category-link.nv-platform-tiktok',
  '.yh49-category-link.nv-platform-instagram',
  '.nv26-social-mega-card[class*="nv-platform-"]',
  '.nv56-quick-category[class*="nv-platform-"]'
] as $needle){
 if(!str_contains($css,$needle))throw new RuntimeException('Platform ribbon/menu override absent '.$needle);
}
if(!str_contains($css,'--np-on:#0B2515') ||
   !str_contains($css,'#1ED760,#1DB954') ||
   !str_contains($css,'color:var(--np-on,#fff) !important')){
    throw new RuntimeException('Spotify green must use high-contrast readable dark text.');
}
if(!str_contains($layout,'social-platform-identity-v77.css') ||
   !str_contains($layout,'?v=78.2'))
    throw new RuntimeException('Platform stylesheet stale in browser cache.');
if(Brand::fromText('Hazır Yazılımlar & Scriptler')!==null)
    throw new RuntimeException('Software categories picked up a platform brand.');

echo 'PASS '.count($platforms)." distinctive corporate palettes: catalog, featured home, child service, package and category views; software unaffected\n";
