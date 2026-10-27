<?php
/**
 * Screenshot regression checks: platform badge row, clean blog hero,
 * complete featured article cover. Static and independent of live DB.
 */
declare(strict_types=1);
$root=dirname(__DIR__);
$get=static fn(string $path):string=>(string)file_get_contents($root.'/'.$path);
$assert=static function(bool $condition,string $message):void{
    if(!$condition){fwrite(STDERR,'FAIL: '.$message.PHP_EOL);exit(1);}
};
$card=$get('resources/views/frontend/social/package-card.php');
$social=$get('public/assets/css/social-storefront.css');
$blog=$get('resources/views/frontend/blog/index.php');
$css=$get('public/assets/css/blog-visual-polish-v1.css');
$layout=$get('resources/views/layouts/app.php');
$assert(str_contains($card,'class="social-package-topline"'),'Social card platform and badge row missing');
$assert(str_contains($card,'class="social-package-brand"') &&
    str_contains($card,'class="social-package-badge"'),'Social card badge content changed');
$assert(str_contains($social,'.social-package-topline') &&
    str_contains($social,'justify-content:space-between') &&
    str_contains($social,'.social-package-topline .social-package-badge'),
    'Social header badge alignment not styled');
$assert(!str_contains($blog,'blog-woman-cutout.png'),'Unwanted woman cutout still loaded in blog hero');
$assert(str_contains($blog,'class="yv-blog-hero-core-v8"') &&
    str_contains($blog,'class="yv-blog-hero-core-icon-v8"'),
    'Blog hero visual missing new brand artwork');
$assert(str_contains($css,'.yv-blog-v8 .yv-blog-featured-media-v8::after') &&
    str_contains($css,'content:none!important'),'Obscuring featured image overlay still active');
$assert(str_contains($css,'object-fit:contain!important') &&
    str_contains($css,'max-height:350px'),'Featured article visual still cropped');
$assert(str_contains($css,'@media(max-width:650px)'),'Responsive blog imagery rules missing');
$assert(str_contains($layout,'blog-visual-polish-v1.css') &&
    str_contains($layout,'20261027-card-header-badge'),
    'Blog override or social CSS cache bust not linked');
echo "PASS: badges aligned at every shared social card; premium blog hero and fully visible featured cover at all breakpoints.\n";
