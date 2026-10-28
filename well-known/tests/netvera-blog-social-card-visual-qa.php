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
$assert(!str_contains($blog,'yv-blog-hero-core-v8') &&
    !str_contains($blog,'yv-blog-float-v8'),
    'Old tilted billboard or floating logo clutter is still in the blog hero');
$assert(str_contains($blog,'class="nv-blog-preview-v2"') &&
    str_contains($blog,'class="nv-blog-preview-articles-v2"'),
    'Clean neutral editorial preview missing');
$assert(str_contains($css,'.yv-blog-v8 .yv-blog-hero-art-v8::after') &&
    str_contains($css,'content:none!important') &&
    str_contains($css,'transform:none!important'),
    'Old blob, overlay or rotated hero still active');
$assert(str_contains($blog,'class="yv-blog-featured-backdrop-v2"') &&
    str_contains($blog,'class="yv-blog-featured-photo-v12"'),
    'Real featured article image must render once clearly with blurred same-image backdrop');
$assert(str_contains($css,'img.yv-blog-featured-backdrop-v2') &&
    str_contains($css,'filter:blur(19px)') &&
    str_contains($css,'background:#f0effa!important'),
    'Featured image background still leaves empty blue panel');
$assert(str_contains($css,'object-fit:contain!important') &&
    str_contains($css,'align-self:center!important'),
    'Featured article cover must remain fully visible and centered');
$assert(str_contains($css,'@media(max-width:650px)'),'Responsive blog imagery rules missing');
$assert(str_contains($layout,'blog-visual-polish-v1.css') &&
    str_contains($layout,'20261027-card-header-badge'),
    'Blog override or social CSS cache bust not linked');
echo "PASS: aligned package badges, clean unrotated blog hero, real centered cover with soft photographic background.\n";
