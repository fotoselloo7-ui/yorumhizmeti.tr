<?php
declare(strict_types=1);
$base=dirname(__DIR__);
$read=static fn(string $file):string=>(string)file_get_contents($base.'/'.$file);
$directory=$read('public/assets/css/category-directory-v88.css');
foreach(['.nv26-catalog-controls','grid-template-columns:repeat(4,minmax(0,1fr))','grid-template-columns:1fr','--directory-accent:var(--np-ink,var(--ns-ink,var(--tile)))','min-height:92px',':focus-visible','prefers-reduced-motion'] as $token) {
    if(!str_contains($directory,$token))throw new RuntimeException('Directory regression: '.$token);
}
if(str_contains($directory,'!important'))throw new RuntimeException('Directory override pile returned.');
if(is_file($base.'/public/assets/css/catalog-contrast-v87.css'))throw new RuntimeException('Retired contrast patch returned.');
$layout=$read('resources/views/layouts/app.php');
if(!str_contains($layout,"\$template!=='frontend/home'?'nv-inner-page':''"))throw new RuntimeException('Homepage isolation missing.');
if(!preg_match("/if\\(\\\$template==='frontend\\/categories'\\):.*?category-directory-v88.css/s",$layout))throw new RuntimeException('Directory stylesheet must be route-scoped.');
$styles=$read('public/assets/css/storefront-design-language-v89.css');
foreach(['body.nv-inner-page','--nv-inner-type-11:14px','.yv-content-page .yv-content-card','.yv-faq-list','.yv-contact-form','.panel-welcome','.nv40-product-copy'] as $token) {
    if(!str_contains($styles,$token))throw new RuntimeException('Interior style missing '.$token);
}
// Shared typography must preserve the exact old size outside the interior body.
$type=$read('public/assets/css/typography-readability-v16.css');
if(!str_contains($type,'font-size:var(--nv-inner-type-11,11px)'))throw new RuntimeException('Legacy homepage type fallback changed.');
foreach(['agency-catalog-v26.css','platform-inheritance-flat-v80.css','social-platform-identity-v77.css','catalog-visual-parity-v84.css'] as $file) {
    $css=$read('public/assets/css/'.$file);
    if(preg_match('/\\.nv26-(?:catalog|tile-)[^{}]*\\{[^{}]*!important/s',$css))throw new RuntimeException('Directory still styled by '.$file);
}
$view=$read('resources/views/frontend/categories.php');
foreach(['CatalogMenuService::groups','SocialPlatformIdentity::classFor','/hazir-scriptler','aria-current="page"','name="q"'] as $token) {
    if(!str_contains($view,$token))throw new RuntimeException('Directory data/accessibility regression: '.$token);
}
echo "PASS: isolated interior typography, single directory style owner, data-driven links, accessible responsive cards\n";
