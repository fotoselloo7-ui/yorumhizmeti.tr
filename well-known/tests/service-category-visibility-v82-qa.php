<?php
declare(strict_types=1);
$root=dirname(__DIR__);
require_once $root.'/app/Services/ServiceCategoryVisibility.php';
require_once $root.'/app/Services/IconService.php';
require_once $root.'/app/Services/SocialPlatformIdentity.php';
require_once $root.'/app/Services/CatalogMenuService.php';
use App\Services\ServiceCategoryVisibility as Policy;
use App\Services\CatalogMenuService as Menu;
use App\Services\SocialPlatformIdentity as Platform;

$google=['slug'=>'google-hizmetleri','name'=>'Google Hizmetleri'];
$old=['Google Yorum','Google 5 Yıldız','Google Yorum Beğeni','Google Değerlendirme'];
foreach($old as $name){
 $child=['slug'=>'google-'.strtolower(str_replace(' ','-',$name)),'name'=>$name];
 if(Policy::show($child,$google))throw new RuntimeException('Retired Google review category still publicly promoted: '.$name);
}
foreach(['Google Harita','Google İşletme Profili','Google Ads','Google Harita SEO','Yerel SEO'] as $name){
 if(!Policy::show(['slug'=>'google-'.strtolower(str_replace(' ','-',$name)),'name'=>$name],$google))
   throw new RuntimeException('Valid Google corporate service hidden: '.$name);
}
foreach(['Instagram Yorum','Instagram Takipçi','YouTube Video','Sosyal Medya Ajansı'] as $name){
 if(!Policy::show(['slug'=>'service-'.mb_strtolower(str_replace(' ','-',$name),'UTF-8'),'name'=>$name]))
   throw new RuntimeException('Unrelated service was hidden: '.$name);
}
$legacy=[
 'featured_group_name'=>'Google Hizmetleri','featured_group_slug'=>'google-hizmetleri',
 'category_name'=>'Google Yorum','category_slug'=>'google-yorum',
 'name'=>'Google 5 Yıldız Değerlendirme Paketi'];
if(Policy::showPackage($legacy))throw new RuntimeException('Retired Google star package appears on featured carousel.');
$real=$legacy;
$real['category_name']='Google Harita SEO';
$real['category_slug']='google-harita-seo';
$real['name']='Google Harita Optimizasyonu';
if(!Policy::showPackage($real))throw new RuntimeException('Legitimate Google map package was hidden.');
$rootPackage=$real;$rootPackage['category_name']='Google Hizmetleri';$rootPackage['name']='Google 5 Yıldız';
if(Policy::showPackage($rootPackage))throw new RuntimeException('Old parent-attached review package still advertised.');
if(Menu::style(['slug'=>'seo-hizmetleri','name'=>'SEO Hizmetleri'])!=='seo')
 throw new RuntimeException('SEO service color classification missing.');
foreach(['Dijital Reklam'=>'ads','İtibar Yönetimi'=>'reputation','Yerel İşletme Hizmetleri'=>'local','Web Site Hizmetleri'=>'web'] as $name=>$expected){
 $style=Menu::style(['slug'=>mb_strtolower(str_replace(' ','-',$name),'UTF-8'),'name'=>$name]);
 if($style!==$expected)throw new RuntimeException('Wrong category identity for '.$name.': '.$style);
}
if(Platform::fromCategory($google)!=='google')
 throw new RuntimeException('Platform parent logo lost.');

$load=static fn(string $path):string=>(string)file_get_contents($root.'/'.$path);
$controller=$load('app/Controllers/HomeController.php');
$menu=$load('app/Services/CatalogMenuService.php');
$markup=$load('resources/views/frontend/home.php');
$css=$load('public/assets/css/service-category-identity-v82.css');
$layout=$load('resources/views/layouts/app.php');
foreach([
 ['source'=>$controller,'term'=>'ServiceCategoryVisibility::showPackage'],
 ['source'=>$menu,'term'=>'ServiceCategoryVisibility::show($child,$cat)'],
 ['source'=>$markup,'term'=>'nv-sector-'],
 ['source'=>$markup,'term'=>'$nvChildParentStyle'],
 ['source'=>$markup,'term'=>'data-featured-root-id'],
 ['source'=>$css,'term'=>'--ns-soft'],
 ['source'=>$css,'term'=>'nv-sector-seo'],
 ['source'=>$css,'term'=>'nv-sector-ads'],
 ['source'=>$css,'term'=>'nv-sector-reputation'],
 ['source'=>$css,'term'=>'nv-sector-local'],
 ['source'=>$css,'term'=>'nv26-catalog-tile'],
 ['source'=>$layout,'term'=>'service-category-identity-v82.css']
] as $check){
 if(!str_contains($check['source'],$check['term']))throw new RuntimeException('Missing brand category integration: '.$check['term']);
}
if(strpos($layout,'service-category-identity-v82.css')<strpos($layout,'platform-inheritance-flat-v80.css'))
 throw new RuntimeException('Sector service cards must load after the previously conflicting palette.');
if(str_contains($markup,'Google Yorum Beğeni')||str_contains($markup,'Google 5 Yıldız'))
 throw new RuntimeException('Retired Google labels hardcoded in featured view.');
echo "PASS: DB-backed service group visibility, old Google categories removed from discovery, valid services preserved, sector cards consistent\n";
