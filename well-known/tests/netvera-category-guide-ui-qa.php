<?php
declare(strict_types=1);
// Regression for the three screenshot issues, no database connection required.
$root=dirname(__DIR__);
$get=static fn(string $file):string=>(string)file_get_contents($root.'/'.$file);
$assert=static function(bool $condition,string $label):void{
    if(!$condition){fwrite(STDERR,"FAIL: ".$label.PHP_EOL);exit(1);}
};
$social=$get('resources/views/frontend/social/category.php');
$editorial=$get('resources/views/frontend/social/editorial.php');
$layouts=$get('resources/views/layouts/app.php');
$category=$get('resources/views/frontend/category-detail.php');
$style=$get('public/assets/css/service-editorial-guide-v1.css');
$base=$get('public/assets/css/storefront-v4.css');
$phone=$get('public/assets/css/premium-section-footer-v23.css');
$slugTest=$get('tests/public-slug-url-qa.php');

$assert(!str_contains($social,"require __DIR__.'/service-guide.php'"),'Duplicate SEO guide must not render on social subservices');
$assert(!is_file($root.'/resources/views/frontend/social/service-guide.php'),'Delete obsolete hardcoded duplicate guide');
$assert(str_contains($social,"require __DIR__.'/editorial.php'"),'Do not remove admin-controlled social editorial');
$assert(str_contains($editorial,'socialPage[\'faqs\']') || str_contains($editorial,'$socialFaqs'), 'Keep original admin-controlled questions');
$assert(str_contains($slugTest,'Duplicated social product SEO guide'),'Regression must cover duplicate removal');

$assert(str_contains($category,"partials/service-seo-guide.php"),'Long category explanation missing');
$assert(str_contains($layouts,"'frontend/category-detail','frontend/package-detail'"),'Category detail needs the actual SEO guide stylesheet');
$assert(str_contains($style,'.nv-service-guide-layout') &&
    str_contains($style,'grid-template-columns:minmax(0,1.28fr)') &&
    str_contains($style,'.nv-service-guide-faq summary') &&
    str_contains($style,'@media(max-width:600px)'),'Desktop/mobile guide must be legible, styled and responsive');

foreach(['.yv-category-phone-art-v5 .screen i{',
         '.yv-category-phone-art-v5 .screen i:nth-of-type(1){',
         '.yv-category-phone-art-v5 .screen i:nth-of-type(2){'] as $selector){
    $assert(!str_contains($base,$selector),'Phone icon inherits column graph bar sizing: '.$selector);
}
$assert(str_contains($base,'.screen .yh23-mini-graph > i'),'Graph bar CSS must be isolated');
$assert(str_contains($phone,'.screen > span > .icon') &&
    str_contains($phone,'place-items:center!important'),'Phone icon must be centered in all category mockups');
echo "PASS: no duplicate social SEO/FAQ module, long category guide styled, phone symbols isolated and centered.\n";
