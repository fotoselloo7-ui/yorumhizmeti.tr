<?php
declare(strict_types=1);
$root=dirname(__DIR__);
$read=static fn(string $p):string=>(string)file_get_contents($root.'/'.$p);
require_once $root.'/app/Services/NetveraBrandSettings.php';
require_once $root.'/app/Services/NetveraThemeService.php';
require_once $root.'/app/Services/SoftwareCatalogService.php';
require_once $root.'/app/Services/NetveraScriptTypeService.php';
$brand=\App\Services\NetveraBrandSettings::class;
if(!$brand::upgradeable('site_slogan','Google Yorum, Sosyal Medya ve Dijital Güven Hizmetleri'))
 throw new RuntimeException('Screenshot legacy slogan was not recognized.');
if($brand::display('site_slogan','Google Yorum, Sosyal Medya ve Dijital Güven Hizmetleri')
 !=='Yazılım, Dijital Ajans ve Sosyal Medya Hizmetleri')
 throw new RuntimeException('Legacy slogan must display updated NetVera positioning.');
if($brand::upgradeable('site_slogan','Yapay Zekâ Destekli Özel Ajansım'))
 throw new RuntimeException('Must not override customer-authored slogans.');
foreach([
    ['#FFFFFF','#17264E'],['#000000','#FFFFFF'],
    ['#1d1d33','#FFFFFF'],['#F7D800','#17264E']
] as [$hex,$expect]){
    if(\App\Services\NetveraThemeService::onColor($hex)!==$expect)
        throw new RuntimeException('Theme contrast failed: '.$hex);
}
if(\App\Services\NetveraThemeService::onGradient('#ffffff','#eeeeee')!=='#17264E')
    throw new RuntimeException('Light brand gradients require dark text.');
$settings=$read('resources/views/admin/settings/site.php');
if(!str_contains($settings,'id="nvThemeEnabled"') ||
   !str_contains($settings,'function nvThemeActivate()'))
 throw new RuntimeException('Palette activation is not user-driven.');
if(str_contains($settings,'name="theme_preset_enabled" value="1"'))
 throw new RuntimeException('Unrelated Site Settings save may accidentally activate theme again.');
$ctrl=$read('app/Controllers/Admin/SettingsController.php');
foreach([
 "if(($"."_"."POST['_action']??'')==='reset_theme')",
 "set('theme_preset_enabled','0','theme')",
 "redirect('/admin/site-ayarlari')"
] as $marker)if(!str_contains($ctrl,$marker))
 throw new RuntimeException('Theme reset not saved server-side: '.$marker);
$layout=$read('resources/views/layouts/app.php');
if(!str_contains($layout,'netvera-premium-v76.css') ||
   !str_contains($layout,"nvThemeEnabled?' class=\"nv-theme-enabled\""))
 throw new RuntimeException('CSS reset mode is not scoped.');
$heroStyles=$read('public/assets/css/netvera-premium-v76.css');
if(!str_contains($heroStyles,'/* v77: fix legacy responsive') ||
   !str_contains($heroStyles,'font-size:clamp(37px,3.35vw,52px)!important') ||
   !str_contains($heroStyles,'min-height:0!important'))
    throw new RuntimeException('Oversized legacy hero font and empty space could return.');
$home=$read('resources/views/frontend/home.php');
foreach(['class="nv75-hero-heading"','nv75-hero-middle','nv75-hero-highlight',
         'nv75-benefits','nv75-ecosystem','nv75-platform-services'] as $marker)
 if(!str_contains($home,$marker))throw new RuntimeException('Homepage visual upgrade missing '.$marker);
$a=strpos($home,'<section class="yh6-why nv75-why"');
$b=strpos($home,'</section>',$a);
if($a===false||$b===false)throw new RuntimeException('No why-section');
if(str_contains(substr($home,$a,$b-$a),"icon('check-circle'"))
 throw new RuntimeException('Old weak green ticks still present in why-section.');
if(!str_contains($home,'/hazir-scriptler?type='))
 throw new RuntimeException('Homepage still links new software categories to wrong catalogue.');
$defs=\App\Services\NetveraScriptTypeService::definitions();
if(count($defs)<28)throw new RuntimeException('Missing software type facets.');
$sample=[
 ['name'=>'NetVera Blog Scripti Pro','slug'=>'netvera-blog-scripti','category_name'=>'Blog Sitesi','price'=>2000],
 ['name'=>'NetVera Emlak Yazılımı','slug'=>'netvera-emlak-pro','category_name'=>'Emlak','price'=>3000],
 ['name'=>'NetVera Nakliye Scripti','slug'=>'netvera-nakliye-pro','category_name'=>'Nakliye','price'=>4000],
];
$expect=[
 'blog-cms-scripti'=>'NetVera Blog Scripti Pro',
 'emlak-sitesi-scripti'=>'NetVera Emlak Yazılımı',
 'nakliye-lojistik-scripti'=>'NetVera Nakliye Scripti'
];
foreach($expect as $slug=>$name){
 $found=\App\Services\NetveraScriptTypeService::filter($sample,$slug);
 if(count($found)!==1||$found[0]['name']!==$name)
  throw new RuntimeException('Software type facet failed: '.$slug);
}
if(\App\Services\NetveraScriptTypeService::filter($sample,'untrusted')!==$sample)
 throw new RuntimeException('Unknown filter must not hide original migrated catalogue.');
$catalog=$read('resources/views/frontend/netvera-scripts.php');
foreach(['Yazılım Türleri','softwareTypeCounts','filterType','nv75-type-link'] as $mark)
 if(!str_contains($catalog,$mark))throw new RuntimeException('New software facet missing: '.$mark);
$scriptController=$read('app/Controllers/NetveraScriptController.php');
if(!str_contains($scriptController,'NetveraScriptTypeService::counts') ||
   !str_contains($scriptController,'NetveraScriptTypeService::filter'))
 throw new RuntimeException('Original NetVera products are not connected to type filters.');
echo "PASS: legacy slogan migration, saved default palette, responsive 3-line hero, premium NetVera capabilities and ".count($defs)." software type filters\n";
