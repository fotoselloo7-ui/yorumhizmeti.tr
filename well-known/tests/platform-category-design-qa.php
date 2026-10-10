<?php
declare(strict_types=1);

$root=dirname(__DIR__);
require_once $root.'/app/Services/SocialPlatformIdentity.php';
use App\Services\SocialPlatformIdentity as Identity;

$styles=file_get_contents($root.'/public/assets/css/platform-inheritance-flat-v80.css');
$tokens=file_get_contents($root.'/public/assets/css/social-platform-identity-v77.css');
$markup=file_get_contents($root.'/resources/views/frontend/home.php');
$js=file_get_contents($root.'/public/assets/js/home-featured-tabs-v18.js');
$layout=file_get_contents($root.'/resources/views/layouts/app.php');

foreach (array_keys(Identity::all()) as $platform){
    $name='nv-platform-'.$platform;
    // v77 is the single source for ALL supported brands.
    // v80 overrides only the few colors that need accessibility adjustments.
    if(!str_contains($tokens,'.'.$name.'{'))
        throw new RuntimeException('Missing official platform palette: '.$name);
    if(Identity::classFor($platform)!==$name)
        throw new RuntimeException('Identity class mismatch: '.$platform);
}
$parents=[
    ['root'=>['name'=>'Instagram Hizmetleri','slug'=>'instagram-hizmetleri'],'expected'=>'instagram'],
    ['root'=>['name'=>'TikTok Hizmetleri','slug'=>'tiktok-hizmetleri'],'expected'=>'tiktok'],
    ['root'=>['name'=>'YouTube Hizmetleri','slug'=>'youtube-hizmetleri'],'expected'=>'youtube'],
    ['root'=>['name'=>'Google Hizmetleri','slug'=>'google-hizmetleri'],'expected'=>'google'],
    ['root'=>['name'=>'Facebook Hizmetleri','slug'=>'facebook-hizmetleri'],'expected'=>'facebook'],
    ['root'=>['name'=>'X / Twitter Hizmetleri','slug'=>'x-twitter-hizmetleri'],'expected'=>'twitter']
];
foreach($parents as $one) {
    $brand=Identity::fromCategory($one['root']);
    if($brand!==$one['expected'])
        throw new RuntimeException('Root platform not recognized: '.$one['expected']);
    $child=['name'=>'Instagram Yorum Beğeni','slug'=>'generic-child'];
    if(Identity::fromCategory($child,$one['root'])!==$brand)
        throw new RuntimeException('Child does not inherit parent palette: '.$brand);
    $package=[
        'name'=>'Google Yorum Örnek Paket',
        'featured_group_slug'=>$one['root']['slug'],
        'featured_group_name'=>$one['root']['name']
    ];
    if(Identity::fromPackage($package)!==$brand)
        throw new RuntimeException('Product icon/color must use parent category: '.$brand);
}
$mustStyle=[
    '--np-on:#fff','border-left:3px solid var(--np)',
    'border:1px solid #DCE3EF',
    'background:#FFFFFF!important',
    'background:linear-gradient(118deg,var(--np),var(--np-2))',
    'scroll-snap-type:none!important',
    '.nv-platform-google',
    '.nv-platform-tiktok',
    '.nv43-subcategory-link:is(.active,[aria-selected="true"])',
];
foreach($mustStyle as $selector){
    if(!str_contains($styles,$selector))
        throw new RuntimeException('Platform design token missing: '.$selector);
}
if(str_contains($styles,'Flat outlines across all major site elements'))
    throw new RuntimeException('Outdated blanket shadow-removal CSS still overrides all site cards.');
if(!str_contains($js,"rail.dataset.featuredRailKind === 'root'") ||
   !str_contains($js,'if (rootChanged)'))
    throw new RuntimeException('Auto-scrolling child chips may clip first category.');
foreach(['data-featured-root-id','nv43-subcategory-link','SocialPlatformIdentity::classFor','data-featured-rail-kind="child"'] as $piece){
    if(!str_contains($markup,$piece))
        throw new RuntimeException('Parent-to-child palette inheritance is not present in the actual homepage: '.$piece);
}
if(strpos($layout,'social-platform-identity-v77.css')>strpos($layout,'platform-inheritance-flat-v80.css'))
    throw new RuntimeException('Final platform palette must load AFTER legacy CSS.');
echo 'PASS: '.count(Identity::all()).' platform palettes; real root-child-product inheritance, clear selected states, accessible neutral buttons, no child auto-scroll or blanket flat reset'.PHP_EOL;
