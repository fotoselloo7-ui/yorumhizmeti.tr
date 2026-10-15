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
if(!str_contains($css,'.nv29-section-intro h2 span{display:block;'))throw new RuntimeException('Purple heading must start below the black heading');
if(!str_contains($component,'class="nv29-card-heading"') || str_contains($component,'nv29-eyebrow') || str_contains($component,'<img'))throw new RuntimeException('Text-only minimal cards required');
foreach(['flex-wrap:wrap','justify-content:center','calc((100% - 32px)/3)','flex-basis:100%','margin-top:auto',':focus-visible','prefers-reduced-motion','max-width:none','.nv29-card-body'] as $token) {
    if(!str_contains($css,$token))throw new RuntimeException('Responsive expertise contract missing '.$token);
}
foreach(['!important','overflow:hidden','object-fit:cover','nv106-',':last-child',':nth-child','position:absolute','min-height:336px','max-width:58%','max-width:46%','nv29-eyebrow','::before','::after'] as $legacy) {
    if(str_contains($css,$legacy))throw new RuntimeException('Clipped/split layout returned: '.$legacy);
}
// Pure synthetic template fixture: no database, requests or production data.
$controller=$read('app/Controllers/HomeController.php');
if(!preg_match('/\$socialTitle = setting\(.*?\n        \}/s',$controller,$titleBlock))throw new RuntimeException('Admin title resolution missing');
function setting($key,$default=''){global $brandFixture;return $brandFixture ?? $default;}
foreach([[null,'Sosyal Medya Hizmetleri'],['Instagram, TikTok ve YouTube Hizmetleri','Sosyal Medya Hizmetleri'],['Özel Admin <Başlığı>','Özel Admin <Başlığı>']] as [$brandFixture,$expectedTitle]) {
    eval($titleBlock[0]);
    if($socialTitle!==$expectedTitle)throw new RuntimeException('Admin custom title/default contract changed');
}
function e($value):string{return htmlspecialchars((string)$value,ENT_QUOTES,'UTF-8');}
function asset($value):string{return '/assets/'.$value;}
function icon($name,$size):string{return '<span class="icon" aria-hidden="true"></span>';}
$homePromoGroups=[];
foreach(['social','agency','marketing'] as $key) {
    $homePromoGroups[]=['key'=>$key,'title'=>'Sentetik <başlık> '.$key,'eyebrow'=>'Sentetik grup','description'=>'Fixture description',
        'cta'=>'Fixture CTA','url'=>'/kategoriler/grup/'.$key,
        'chips'=>[['name'=>'Sentetik & kategori','url'=>'/kategori/sentetik-'.$key,'icon'=>'layers']]];
}
ob_start(); eval('?>'.$component); $rendered=(string)ob_get_clean();
if(substr_count($rendered,'class="nv29-card-title"')!==3 || str_contains($rendered,'<img'))throw new RuntimeException('Three image-free service cards required');
foreach(['social','agency','marketing'] as $key) {
    foreach(['/kategoriler/grup/'.$key,'/kategori/sentetik-'.$key,'Sentetik &lt;başlık&gt; '.$key] as $expected) {
        if(!str_contains($rendered,$expected))throw new RuntimeException('Data/escaping/art mapping lost: '.$expected);
    }
}
if(str_contains($rendered,'style=') || str_contains($rendered,'<script'))throw new RuntimeException('Inline behavior/style must not return');
echo "PASS: three image-free cards, preserved dynamic links/admin copy/escaping, minimal responsive layout\n";
