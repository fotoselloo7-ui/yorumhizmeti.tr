<?php
declare(strict_types=1);
$root=dirname(__DIR__);
$read=static fn(string $path):string=>(string)file_get_contents($root.'/'.$path);
$home=$read('resources/views/frontend/home.php');
if(!preg_match('/<section class="nv29-services".*?<\/section>/s',$home,$match))throw new RuntimeException('Expertise section missing');
$component=$match[0];
foreach(['nv106-','nv29-social-person','nv29-card-glow','nv29-agency-window','nv29-analytics-card','hero-woman-cutout.png'] as $legacy) {
    if(str_contains($component,$legacy))throw new RuntimeException('Split artwork returned: '.$legacy);
}
$css=$read('public/assets/css/home-service-groups-v29.css');
foreach(['object-fit:contain','flex-wrap:wrap','justify-content:center','calc((100% - 32px)/3)','flex-basis:100%','margin-top:auto',':focus-visible','prefers-reduced-motion','container-type:inline-size','.nv29-container','.nv29-card-body','bottom:80px'] as $token) {
    if(!str_contains($css,$token))throw new RuntimeException('Responsive expertise contract missing '.$token);
}
foreach(['!important','overflow:hidden','object-fit:cover','nv106-',':last-child',':nth-child'] as $legacy) {
    if(str_contains($css,$legacy))throw new RuntimeException('Clipped/split layout returned: '.$legacy);
}
// Pure synthetic template fixture: no database, requests or production data.
function e($value):string{return htmlspecialchars((string)$value,ENT_QUOTES,'UTF-8');}
function asset($value):string{return '/assets/'.$value;}
function icon($name,$size):string{return '<span class="icon" aria-hidden="true"></span>';}
$homePromoGroups=[];
foreach(['social','agency','marketing'] as $key) {
    $homePromoGroups[]=['key'=>$key,'title'=>'Sentetik <başlık> '.$key,'eyebrow'=>'Sentetik grup','description'=>'Fixture description',
        'cta'=>'Fixture CTA','url'=>'/kategoriler/grup/'.$key,
        'chips'=>[['name'=>'Sentetik & kategori','url'=>'/kategori/sentetik-'.$key,'icon'=>'layers']]];
    $png=$read('public/assets/img/expertise/'.$key.'.png');
    $size=getimagesizefromstring($png);
    // Preserve high-resolution RGBA assets, not flat screenshots containing UI.
    if(substr($png,0,8)!=="\x89PNG\r\n\x1a\n" || ord($png[25])!==6 || !$size || min($size[0],$size[1])<1000)throw new RuntimeException('Incomplete/opaque artwork: '.$key);
}
ob_start(); eval('?>'.$component); $rendered=(string)ob_get_clean();
if(substr_count($rendered,'class="nv29-artwork"')!==3)throw new RuntimeException('Exactly one scene per service family required');
foreach(['social','agency','marketing'] as $key) {
    foreach(['/assets/img/expertise/'.$key.'.png','/kategoriler/grup/'.$key,'/kategori/sentetik-'.$key,'Sentetik &lt;başlık&gt; '.$key] as $expected) {
        if(!str_contains($rendered,$expected))throw new RuntimeException('Data/escaping/art mapping lost: '.$expected);
    }
}
if(str_contains($rendered,'style=') || str_contains($rendered,'<script'))throw new RuntimeException('Inline behavior/style must not return');
echo "PASS: three RGBA reference artworks, preserved dynamic links/admin copy/escaping, shared pastel responsive layout\n";
