<?php
declare(strict_types=1);
$base=dirname(__DIR__);
require_once $base.'/app/Services/IconService.php';
require_once $base.'/app/Services/SocialPlatformIdentity.php';
require_once $base.'/app/Services/CatalogMenuService.php';

use App\Services\SocialPlatformIdentity as Brand;
use App\Services\CatalogMenuService as Menu;
use App\Services\IconService as Icons;

foreach([
  ['google','Google Hizmetleri','Google Harita Yorum','Google 5 Yıldız Değerlendirme'],
  ['instagram','Instagram Hizmetleri','Instagram Takipçi','Instagram Başlangıç Paketi'],
  ['tiktok','TikTok Hizmetleri','TikTok İzlenme','TikTok Video 1000'],
  ['youtube','YouTube Hizmetleri','YouTube İzlenme','YouTube Video Paketi'],
  ['facebook','Facebook Hizmetleri','Facebook Beğeni','Facebook Etkileşim']
] as [$key,$root,$child,$product]){
    $parent=['name'=>$root,'slug'=>$key.'-hizmetleri'];
    $childCat=['name'=>$child,'slug'=>'alt-kategori'];
    $fromRoot=Brand::fromCategory($parent);
    $fromChild=Brand::fromCategory($childCat,$parent);
    $fromPackage=Brand::fromPackage([
      'featured_group_name'=>$root,
      'featured_group_slug'=>$parent['slug'],
      'category_name'=>$child,
      'category_slug'=>'alt-kategori',
      'name'=>$product
    ]);
    foreach([$fromRoot,$fromChild,$fromPackage] as $actual) {
      if($actual!==$key)throw new RuntimeException('Brand does not inherit '.$root.': '.var_export($actual,true));
    }
    $icon=Brand::iconFor($key);
    if(!Icons::has($icon)||Menu::subcategoryIcon($childCat,$parent)!==$icon)
        throw new RuntimeException('Child icon was not inherited from '.$root);
}
if(Brand::fromPackage(['featured_group_slug'=>'google-hizmetleri',
    'category_slug'=>'instagram-yorum', 'name'=>'Instagram Comment'])!=='google'){
    throw new RuntimeException('Parent platform must win over conflicting child name.');
}
if(Brand::fromPackage(['featured_group_slug'=>'kurumsal-web',
    'category_slug'=>'crm', 'name'=>'Kurumsal Yazılım Paketi'])!==null)
    throw new RuntimeException('Software must remain unbranded.');

$view=file_get_contents($base.'/resources/views/frontend/home.php');
foreach([
 'SocialPlatformIdentity::fromPackage($pkg)',
 'SocialPlatformIdentity::iconFor($parentPlatform)',
 'SocialPlatformIdentity::iconFor($childPlatform',
 'SocialPlatformIdentity::classFor($childPlatform)'
] as $rule){
 if(!str_contains($view,$rule))throw new RuntimeException('Home brand propagation missing '.$rule);
}
$cat=file_get_contents($base.'/resources/views/frontend/category-detail.php');
if(!str_contains($cat,'SocialPlatformIdentity::fromCategory($sub,$category)') ||
   !str_contains($cat,'SocialPlatformIdentity::iconFor($categoryPlatform)'))
 throw new RuntimeException('Nested category presentation is not inherited.');
$pkg=file_get_contents($base.'/resources/views/frontend/package-detail.php');
if(!str_contains($pkg,'SocialPlatformIdentity::fromPackage($package)'))
 throw new RuntimeException('Detail product brand not inherited.');
$css=file_get_contents($base.'/public/assets/css/platform-inheritance-flat-v80.css');
foreach(['.nv-platform-instagram','.nv-platform-tiktok','.nv-platform-youtube',
  '.nv-platform-facebook','.nv-platform-google','--np-soft',
  'box-shadow:none!important','-webkit-background-clip:text',
  '.nv43-subcategory-link','.yh18-featured-card','.yv-category-package-card-v5',
  '.nv26-catalog-tile','.nv26-social-mega-card'] as $rule){
 if(!str_contains($css,$rule))throw new RuntimeException('Missing flat platform CSS '.$rule);
}
$layout=file_get_contents($base.'/resources/views/layouts/app.php');
if(strpos($layout,'platform-inheritance-flat-v80.css')<=strpos($layout,'social-platform-identity-v77.css'))
 throw new RuntimeException('New platform identity must load after older glow CSS.');
echo "PASS: 5 official parent-to-child-to-package brand icons, Google multicolor, TikTok dual-accent and flat card styles\n";
