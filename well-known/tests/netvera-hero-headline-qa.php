<?php
declare(strict_types=1);

require_once dirname(__DIR__).'/app/Services/HeroHeadline.php';
use App\Services\HeroHeadline;

function check(bool $ok,string $message):void{
    if(!$ok){fwrite(STDERR,'FAIL: '.$message.PHP_EOL);exit(1);}
}

$cases=[
    ['Landing','Page',1,'Landing Page'],
    ['Web Site','Hizmetleri',1,'Web Site Hizmetleri'],
    ['Kurumsal Web Site','Tasarım Paketi',2,'Kurumsal Web Site Tasarım Paketi'],
    ['SEO ve Dijital','Pazarlama Hizmetleri',2,'SEO ve Dijital Pazarlama Hizmetleri'],
    ['TikTok Takipçi','',1,'TikTok Takipçi'],
    ['Özel Yazılım','',1,'Özel Yazılım'],
    ['Sosyal Medya','Yönetimi',1,'Sosyal Medya Yönetimi'],
];
foreach($cases as [$lead,$accent,$expectedLines,$plain]){
    $html=HeroHeadline::render($lead,$accent);
    check(substr_count($html,'class="nv-headline-line"')===$expectedLines,'Line count: '.$plain);
    check(trim(strip_tags($html))===$plain,'H1 text must be preserved: '.$plain);
    if($accent!=='')check(str_contains($html,'nv-category-hero-accent'),'Missing styled accent: '.$plain);
    if($expectedLines===2){
        check(str_contains($html,'Kurumsal Web Site')||str_contains($html,'SEO ve Dijital'),
              'Three-word breakpoint missing');
    }
}
check(str_contains(HeroHeadline::render('<script>','Page'),'&lt;script&gt;'),'H1 rendering must escape text');

$base=dirname(__DIR__);
$category=file_get_contents($base.'/resources/views/frontend/partials/category-hero.php');
$social=file_get_contents($base.'/resources/views/frontend/social/hero.php');
$layout=file_get_contents($base.'/resources/views/layouts/app.php');
$css=file_get_contents($base.'/public/assets/css/service-hero-headlines-v1.css');
check(str_contains($category,'HeroHeadline::render'),'Service category H1 missing shared formatter');
check(str_contains($social,'HeroHeadline::render'),'Social category H1 missing shared formatter');
check(str_contains($layout,'service-hero-headlines-v1.css'),'Last-loaded public headline rules missing');
check(str_contains($css,'white-space:nowrap!important'),'Short headline row may still wrap');
check(str_contains($css,'.nv-category-hero-accent'),'Highlighted last word styling lost');
check(str_contains($css,'@media(max-width:520px)'),'Missing responsive text sizing');
echo "PASS: one-line up to three words, controlled fourth-word break, Turkish accents, CSS on desktop/mobile, SEO text preserved.\n";
