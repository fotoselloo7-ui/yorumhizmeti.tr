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
foreach(['object-fit:contain','flex-wrap:wrap','justify-content:center','calc((100% - 40px)/3)','flex-basis:100%','margin-top:auto',':focus-visible','prefers-reduced-motion'] as $token) {
    if(!str_contains($css,$token))throw new RuntimeException('Responsive expertise contract missing '.$token);
}
foreach(['!important','position:absolute','overflow:hidden','64%','nv106-'] as $legacy) {
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
    $svg=$read('public/assets/img/expertise/'.$key.'.svg');
    if(!str_contains($svg,'viewBox="0 0 360 140"') || preg_match('/<script|<image|<foreignObject|https?:\/\//i',str_replace('http://www.w3.org/2000/svg','',$svg)))throw new RuntimeException('Unsafe/incomplete local SVG: '.$key);
}
ob_start(); eval('?>'.$component); $rendered=(string)ob_get_clean();
if(substr_count($rendered,'class="nv29-artwork"')!==3)throw new RuntimeException('Exactly one scene per service family required');
foreach(['social','agency','marketing'] as $key) {
    foreach(['/assets/img/expertise/'.$key.'.svg','/kategoriler/grup/'.$key,'/kategori/sentetik-'.$key,'Sentetik &lt;başlık&gt; '.$key] as $expected) {
        if(!str_contains($rendered,$expected))throw new RuntimeException('Data/escaping/art mapping lost: '.$expected);
    }
}
if(str_contains($rendered,'style=') || str_contains($rendered,'<script'))throw new RuntimeException('Inline behavior/style must not return');
echo "PASS: three single-scene SVG cards, preserved dynamic links/escaping, responsive one-surface layout\n";
