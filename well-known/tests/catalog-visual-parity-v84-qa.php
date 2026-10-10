<?php
declare(strict_types=1);
$base=dirname(__DIR__);
$load=static function(string $file)use($base):string{
  $path=$base.'/'.$file;
  if(!is_file($path))throw new RuntimeException('Missing '.$file);
  return (string)file_get_contents($path);
};
$layout=$load('resources/views/layouts/app.php');
$stages=['social-platform-identity-v77.css','platform-inheritance-flat-v80.css','service-category-identity-v82.css','header-reputation-parity-v83.css','catalog-visual-parity-v84.css'];
$last=-1;
foreach($stages as $file){
  $index=strpos($layout,$file);
  if($index===false||$index<=$last)throw new RuntimeException('Stylesheets are not ordered for visual parity: '.$file);
  $last=$index;
}
$baseColors=$load('public/assets/css/platform-inheritance-flat-v80.css');
foreach(['nv-platform-instagram','nv-platform-tiktok','nv-platform-youtube','nv-platform-facebook','nv-platform-spotify'] as $platform)
 if(!str_contains($baseColors,$platform))throw new RuntimeException('Missing platform color '.$platform);
$styles=$load('public/assets/css/catalog-visual-parity-v84.css');
foreach(['nv26-social-mega-card','nv43-category-card','nv26-catalog-tile','nv43-subcategory-link','yh18-featured-card','--np-soft','--np-ink','--ns-soft','background:#fff!important','@media(max-width:800px)'] as $token)
 if(!str_contains($styles,$token))throw new RuntimeException('Visual parity missing: '.$token);
if(!str_contains($styles,'#featured .nv43-category-card.yh18-featured-tab:is(.active,.is-parent-selected)') &&
   !str_contains($styles,'#featured .nv43-category-card.yh18-featured-tab[class*="nv-platform-"]:is(.active,.is-parent-selected)'))
 throw new RuntimeException('Selected homepage category still uses white or saturated v80 card styles.');
$home=$load('resources/views/frontend/home.php');
foreach(['SocialPlatformIdentity::classFor','nv43-category-card','nv43-subcategory-link','yh18-featured-card'] as $token)
 if(!str_contains($home,$token))throw new RuntimeException('Homepage platform class not reused: '.$token);
if(!str_contains($layout,'nv26-social-mega-card'))throw new RuntimeException('Mega-menu platform tiles not present.');
$category=$load('resources/views/frontend/categories.php');
if(!str_contains($category,'nv26-catalog-tile'))throw new RuntimeException('Public category index not covered.');
echo "PASS: shared identity in header, homepage category slider, subcategory filters, package cards and catalogue; final cascade is ordered\n";
