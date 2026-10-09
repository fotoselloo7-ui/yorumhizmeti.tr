<?php
declare(strict_types=1);
$base=dirname(__DIR__);
$get=static fn($path)=>file_get_contents($base.'/'.$path) ?: '';
$must=[
 'app/Core/App.php'=>[
    "TestimonialController@index","MobileDeskController@index",
    "MobileDeskController@feed","MobileDeskController@detail","MobileDeskController@reply",
    "NetveraInboxController@saveAgent"
 ],
 'resources/views/frontend/home.php'=>['data-review-viewport','data-review-track','TestimonialManager::rows'],
 'public/assets/js/reviews-v68.js'=>['nv69-is-sliding','translate3d(-','transitionend','setInterval'],
 'resources/views/admin/testimonials/index.php'=>['name="rating"','name="category_id"','name="package_id"','name="image"'],
 'resources/views/admin/payment-gateways/index.php'=>['nv68-payment-list','gateway-card'], // old gateway-card MUST be absent, checked below
 'resources/views/layouts/mobile.php'=>['nv-desk.webmanifest','nv-desk-alerts.js'],
 'public/assets/js/nv-desk.js'=>['/admin/cep/veri','/admin/cep/kayit/','/admin/cep/yanit/'],
 'app/Controllers/Admin/MobileDeskController.php'=>['Csrf::check','AdminAuth::admin','Cache-Control: no-store'],
 'public/nv-desk-sw.js'=>["if(!url.pathname.startsWith('/assets/'))return","event.request.method!=='GET'"],
 'resources/views/frontend/partials/netvera-live-chat.php'=>['SupportDeskSettings::publicProfile','nv68-chat-person']
];
$count=0;
foreach($must as $path=>$needles){
  $text=$get($path);
  if($text==='')throw new RuntimeException('Missing file '.$path);
  foreach($needles as $phrase){
    if($phrase==='gateway-card')continue;
    if(!str_contains($text,$phrase))throw new RuntimeException($path.' missing '.$phrase);
    $count++;
  }
}
$payment=$get('resources/views/admin/payment-gateways/index.php');
if(str_contains($payment,'gateway-card')||str_contains($payment,'Sistem Ödeme Yolları'))
 throw new RuntimeException('Nested payment modules were not flattened.');
if(!str_contains($payment,'foreach($gateways as $gw)'))throw new RuntimeException('Gateways no longer use live database.');
$home=$get('resources/views/frontend/home.php');
if(str_contains($home,'array_slice($reviews, 0, 4)') || str_contains($home,'data-review-template'))
 throw new RuntimeException('Legacy first-card-only testimonial animation still present.');
$carouselJs=$get('public/assets/js/reviews-v68.js');
$carouselCss=$get('public/assets/css/reviews-v68.css');
if(str_contains($carouselJs,'nv68-fade-out') || str_contains($carouselCss,'nv68-fade-in'))
 throw new RuntimeException('Vertical testimonial fades must not be used.');
if(!str_contains($carouselCss,'display:flex!important') || !str_contains($carouselCss,'flex:0 0 calc((100% - 32px)/3)'))
 throw new RuntimeException('Horizontal fixed-width carousel cards missing.');
$sw=$get('public/nv-desk-sw.js');
if(str_contains($sw,'/admin/cep/veri'))throw new RuntimeException('Private API must never be precached.');
$manifest=json_decode($get('public/nv-desk.webmanifest'),true,512,JSON_THROW_ON_ERROR);
if(($manifest['start_url']??'')!=='/admin/cep'||($manifest['display']??'')!=='standalone')throw new RuntimeException('Invalid PWA manifest.');
foreach([192,512] as $size){
    $file=$base.'/public/assets/img/nv-desk-icon-'.$size.'.png';
    $image=@getimagesize($file);
    if(!$image || $image[0]!==$size || $image[1]!==$size ||
       ($image['mime']??'')!=='image/png')
        throw new RuntimeException('PWA raster icon corrupted: '.$file);
}
echo "PASS: {$count} review/payment/support/mobile checks; PWA private data not cached\n";
