<?php
declare(strict_types=1);
$root=dirname(__DIR__);
$get=static fn(string $p): string => (string)file_get_contents($root.'/'.$p);
$check=static function(bool $ok,string $msg):void{if(!$ok)throw new RuntimeException($msg);};

$service=$get('app/Services/HeroSliderService.php');
$controller=$get('app/Controllers/Admin/HeroSliderController.php');
$routes=$get('app/Core/App.php');
$homeController=$get('app/Controllers/HomeController.php');
$home=$get('resources/views/frontend/home.php');
$admin=$get('resources/views/admin/hero-slider/form.php');
$index=$get('resources/views/admin/hero-slider/index.php');
$layout=$get('resources/views/layouts/app.php');
$adminLayout=$get('resources/views/layouts/admin.php');
$login=$get('resources/views/frontend/auth/login.php');
$css=$get('public/assets/css/home-hero-slider-v94.css');
$js=$get('public/assets/js/home-hero-slider-v94.js');
$sql=$get('database/migrations/home-hero-slider-v94.sql');

$check(str_contains($service,'CREATE TABLE IF NOT EXISTS hero_slides'),'Runtime slider schema missing');
$check(str_contains($service,'quick_links JSON'),'Quick-link JSON storage missing');
$check(str_contains($service,'autoplay_delay'),'Per-slide autoplay missing');
$check(str_contains($service,'starts_at')&&str_contains($service,'ends_at'),'Publishing window missing');
$artworks=[
    ['public/assets/img/slider/social-media-v107.webp',2400,840,60000],
    ['public/assets/img/slider/agency-software-v107.webp',2400,840,60000],
    ['public/assets/img/slider/seo-digital-marketing-v107.webp',2400,840,60000],
    ['public/assets/img/slider/social-media-mobile-v107.webp',1080,1920,40000],
    ['public/assets/img/slider/agency-software-mobile-v107.webp',1080,1920,40000],
    ['public/assets/img/slider/seo-digital-marketing-mobile-v107.webp',1080,1920,40000],
];
foreach($artworks as [$artwork,$expectedWidth,$expectedHeight,$minBytes]){
    $full=$root.'/'.$artwork;
    $check(is_file($full),'Bundled hero artwork missing: '.$artwork);
    $check(filesize($full)>=$minBytes,'Bundled hero artwork looks over-compressed: '.$artwork);
    $size=getimagesize($full);
    $check(is_array($size)&&$size[0]===$expectedWidth&&$size[1]===$expectedHeight,
        'Bundled hero artwork has wrong geometry: '.$artwork);
}
$check(str_contains($service,'social-media-v107.webp')&&str_contains($service,'social-media-mobile-v107.webp'),'Social Media HD hero artwork not mapped');
$check(str_contains($service,'agency-software-v107.webp')&&str_contains($service,'agency-software-mobile-v107.webp'),'Agency & Software HD hero artwork not mapped');
$check(str_contains($service,'seo-digital-marketing-v107.webp')&&str_contains($service,'seo-digital-marketing-mobile-v107.webp'),'SEO HD hero artwork not mapped');
$check(str_contains($service,'SEO, GEO, AIO'),'SEO/GEO/AIO category copy missing');

$check(str_contains($controller,'Upload::image'),'Slider image uploader missing');
$check(str_contains($controller,'primary_button_bg')&&str_contains($controller,'primary_button_icon'),'Primary button visual controls missing');
$check(str_contains($controller,'secondary_button_bg')&&str_contains($controller,'secondary_button_icon'),'Secondary button visual controls missing');
$check(str_contains($controller,'quickLinks()'),'Admin quick-link repeater persistence missing');
$check(str_contains($routes,"'/admin/sliderlar/yeni'")&&str_contains($routes,'HeroSliderController@store'),'Slider CRUD routes missing');
$check(str_contains($adminLayout,'Slider / Banner Yönetimi'),'Admin sidebar slider link missing');
$check(str_contains($admin,'desktop_image')&&str_contains($admin,'mobile_image'),'Desktop/mobile per-slide images missing');
$check(str_contains($admin,'2400 × 840 px')&&str_contains($admin,'1080 × 1920 px')&&str_contains($admin,'20:7'),'Admin image-size guidance does not match the real slider geometry');
$check(str_contains($admin,'quick_label[]')&&str_contains($admin,'quick_icon[]')&&str_contains($admin,'quick_color[]'),'Icon/color/link repeater missing');
$check(str_contains($index,'Kopyala')&&str_contains($index,'Yeni Slider'),'Unlimited admin management controls missing');
$check(str_contains($homeController,'HeroSliderService::all(true)'),'Homepage does not load active slides');
$check(str_contains($home,'data-nv94-slider')&&str_contains($home,'data-nv94-slide'),'Homepage slider markup missing');
$check(str_contains($home,'$nv94Mobile')&&str_contains($home,'<picture>'),'Mobile-specific banner rendering missing');
$check(str_contains($home,'$nv94IsComposed')&&str_contains($home,'is-composed-banner'),'HD composed banner rendering marker missing');
$check(str_contains($layout,'home-hero-slider-v94.css')&&str_contains($layout,'home-hero-slider-v94.js'),'Slider assets not loaded globally');
$check(str_contains($css,'.nv94-hero-controls')&&str_contains($css,'@media(max-width:600px)'),'Responsive Bayigram-like controls missing');
$check(str_contains($css,'V107 — exact-fit HD artwork')&&str_contains($css,'.nv94-hero-slide.is-composed-banner'),'V107 exact-fit hero rules missing');
$check(str_contains($css,'object-fit:cover')&&str_contains($css,'position:absolute;inset:0'),'Full-bleed 21:9 hero artwork layout missing');
$check(str_contains($js,'touchstart')&&str_contains($js,'setTimeout'),'Swipe/autoplay behavior missing');
$check(str_contains($login,'SiteLogoService::url()')&&!str_contains($login,'auth25-brandmark">Y'),'Login still uses placeholder brand mark');
$check(str_contains($sql,'CREATE TABLE IF NOT EXISTS')&&str_contains($sql,'hero_slides'),'Deploy migration missing');
echo "PASS: admin-managed unlimited Bayigram-style hero slider, CTA/icon/color/link controls, per-slide images, responsive swipe and login logo\n";
