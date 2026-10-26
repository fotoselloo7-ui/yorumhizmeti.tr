<?php
declare(strict_types=1);
if(PHP_SAPI!=='cli')exit(2);
define('BASE_PATH', dirname(__DIR__));
define('PUBLIC_PATH', BASE_PATH.'/public');
spl_autoload_register(static function(string $class):void{
    if(str_starts_with($class,'App\\'))require BASE_PATH.'/app/'.str_replace('\\','/',substr($class,4)).'.php';
});
require BASE_PATH.'/app/Core/Helpers.php';
use App\Services\RegionalLandingService as Regional;
use App\Services\LandingThemeService as Themes;

$count=0;
$ok=static function(bool $condition,string $msg) use (&$count):void{
    if(!$condition)throw new RuntimeException($msg);
    $count++;
};
$base=Regional::starter('reklam');
$base['editorial_title']='Alanya işletmeleri için dijital hizmet rehberi';
$base['editorial_lead']='Alanya işletmeleri için somut planlı yazılım ve reklam hizmetleri.';
$base['editorial_body']="Hizmet alanımız proje bazlı belirlenir. [Hizmetler](/kategoriler) sayfamızı inceleyin.\n\nHer proje için özgün kapsama ve raporlamaya odaklanıyoruz.";
$base['editorial_qa']=[
    ['question'=>'Çalışmanın kapsamı nedir?','answer'=>'Uzaktan hizmet kapsamı teklif aşamasında belirlenir.'],
    ['question'=>'Ödeme ve proje ne zaman başlar?','answer'=>'İş planı ve teslimler anlaşmadan sonra netleşir.']
];
$clean=Regional::validate($base);
$ok($clean['editorial_title']===$base['editorial_title']&&$clean['editorial_body']===$base['editorial_body'],'Per-page guide is preserved in JSON validation');
$ok(count($clean['editorial_qa'])===2,'Guide Q&A preserved');
$ok(Regional::validate(array_replace(Regional::defaults(),$base))['editorial_title']===$base['editorial_title'],'Existing defaults accept authored guide');
$recovered=Regional::editorInput([
    'editorial_title'=>'Test taslak','editorial_body'=>'Kaydedilmemiş uzun metin',
    'editorial_qa'=>[['question'=>'Düzenlenecek?','answer'=>'Evet.']]
],$base);
$ok($recovered['editorial_body']==='Kaydedilmemiş uzun metin'&&count($recovered['editorial_qa'])===1,'Failed form safely retains guide data');
foreach(['<script>alert(1)</script>'.str_repeat('a',16000),str_repeat('x',16001)] as $badBody){
    $bad=$base;$bad['editorial_body']=$badBody;
    try {Regional::validate($bad);$ok(false,'Oversized guide should be rejected');}
    catch(InvalidArgumentException $e){$ok(true,'Oversized guide rejected');}
}
$bad=$base;$bad['editorial_qa']=[['question'=>'Soru var','answer'=>'']];
try{Regional::validate($bad);$ok(false,'Incomplete long-form Q&A must fail');}
catch(InvalidArgumentException $e){$ok(true,'Incomplete long-form Q&A rejected');}

$sources=['services'=>[],'scripts'=>[],'blog'=>[],'references'=>[]];
$sources['scripts']=array_map(static fn($n)=>[
    'id'=>$n,'title'=>'Sentetik yazılım '.$n,'url'=>'/hazir-scriptler/sentetik-yazilim-'.$n,
    'category'=>'Yazılım','image'=>'','description'=>'', 'demo_url'=>null
],range(1,7));
$sources['blog']=array_map(static fn($n)=>[
    'id'=>$n,'title'=>'Sentetik rehber '.$n,'url'=>'/blog/sentetik-rehber-'.$n,
    'category'=>'Blog','image'=>'','description'=>''
],range(1,7));
$sources['references']=array_map(static fn($n)=>[
    'id'=>'r'.$n,'title'=>'Portföy örneği '.$n,'url'=>'https://example.com',
    'image'=>'','description'=>'', 'status'=>'active',
    'placements'=>[['group'=>$n%2?'agency':'marketing','service'=>$n%2?'web-site':'seo']]
],range(1,7));
$clean['script_ids']=array_column($sources['scripts'],'id');
$clean['blog_ids']=array_column($sources['blog'],'id');
$clean['reference_ids']=array_column($sources['references'],'id');

foreach(Themes::all() as $themeKey=>$profile){
    $d=$clean;
    $seo=$d['seo'];
    $mode='Uzaktan / çevrim içi';
    $blocks=Themes::blocks($d,$sources);
    $landingPreview=false;
    $landingServices=$sources['services'];
    $landingScripts=$sources['scripts'];
    $landingBlogs=$sources['blog'];
    $landingReferences=$sources['references'];
    $landingContacts=[['label'=>'İletişim','url'=>'/iletisim','icon'=>'message-circle']];
    ob_start();require BASE_PATH.'/resources/views/frontend/landing/themes/page.php';$html=(string)ob_get_clean();
    $ok(str_contains($html,'data-nvl-reference-filters')&&str_contains($html,'data-nvl-reference-filter="marketing"'),'Reference filter rendered: '.$themeKey);
    $ok(str_contains($html,'data-nvl-groups=')&&str_contains($html,'data-nvl-rail-viewport'),'Carousel DOM and portfolio groups rendered: '.$themeKey);
    $ok(str_contains($html,'nvl-deep-guide')&&str_contains($html,$clean['editorial_title']),'Individual long editorial content appears: '.$themeKey);
    $ok(str_contains($html,'Merak edilenler')&&str_contains($html,'Ödeme ve proje ne zaman başlar?'),'Q&A visible: '.$themeKey);
    $ok(str_contains($html,'/hazir-scriptler/sentetik-yazilim-1')&&str_contains($html,'/blog/sentetik-rehber-1'),'Original product/blog URLs preserved: '.$themeKey);
    $ok(str_contains($html,'id="landing-local-guide"')&&str_contains($html,'[Hizmetler]')===false,'Public guide renders semantic HTML with safe links: '.$themeKey);
}
$css=file_get_contents(PUBLIC_PATH.'/assets/css/regional-showcase-v1.css');
$js=file_get_contents(PUBLIC_PATH.'/assets/js/regional-showcase-v1.js');
$editor=file_get_contents(BASE_PATH.'/resources/views/admin/landing/form.php');
$ok(str_contains($css,'grid-template-rows:repeat(2')&&str_contains($css,'grid-auto-flow:column'),'CSS uses exactly two horizontal rows');
$ok(str_contains($css,'nvl-section.is-nvl-rail .nvl-section-heading')&&str_contains($css,'align-items:center'),'Side heading is vertically aligned to both rows');
$ok(str_contains($js,'landing:themechange')&&str_contains($js,'data-nvl-reference-filter'),'Theme updates and portfolio filters connected');
$ok(str_contains($js,'prefers-reduced-motion')&&str_contains($js,"'mouseenter'"),'Auto-scrolling respects reduced motion and interaction pause');
$ok(str_contains($editor,'name="editorial_qa[<?= $i ?>][question]"')&&str_contains($editor,'editorial_body'),'Admin has per-page long story and editable Q&A');
$ok(str_contains($editor,'data-builder-panel="editorial"'),'Separate regional editorial admin tab exists');
$ok(!str_contains($js,'/hazir-scriptler/')&&!str_contains($js,'/blog/'),'Carousel JS never rewrites product or blog links');
echo "PASS $count regional two-row filter/marquee and long editorial contracts across 10 themes\n";
