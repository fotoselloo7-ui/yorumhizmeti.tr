<?php
declare(strict_types=1);
if(PHP_SAPI!=='cli')exit(2);
define('BASE_PATH',dirname(__DIR__));define('PUBLIC_PATH',BASE_PATH.'/public');
spl_autoload_register(static function($c){if(str_starts_with($c,'App\\'))require BASE_PATH.'/app/'.str_replace('\\','/',substr($c,4)).'.php';});require BASE_PATH.'/app/Core/Helpers.php';
use App\Services\LandingThemeService as Theme;use App\Services\RegionalLandingService as Page;
$checks=0;$ok=static function($v,$why)use(&$checks){if(!$v)throw new RuntimeException($why);$checks++;};
$ok(count(Theme::all())===10,'Ten registry keys');$ok(count(array_unique(array_column(Theme::all(),'hero')))===10,'Ten hero structures');
$d=Page::starter('reklam');$ok(Page::validate($d)['theme_key']==='','Legacy payload preserves presentation');
foreach([null,[],false,'../../etc','landing-11','<script>']as $bad)$ok(Theme::key($bad)==='','Unsafe/unknown theme safely falls back');
$d['theme_key']='landing-04';$d['theme_switcher']='0';$clean=Page::validate($d);$ok($clean['theme_key']==='landing-04'&&$clean['theme_switcher']===false,'Per-page theme and switcher validation');
$sources=['services'=>[['id'=>1]],'scripts'=>[['id'=>2]],'blog'=>[['id'=>3]],'references'=>[['id'=>'ref']]];
$blocks=Theme::blocks($d,$sources);$ok(count($blocks)===8,'Every existing nonempty enabled block normalized');
$d['enabled_sections']=['scripts','content'];$ok(array_column(Theme::blocks($d,$sources),'type')===['scripts','content'],'Visibility/order preserved');$d['enabled_sections']=array_keys(Page::SECTIONS);
$ok(Theme::component('future-block')==='generic.php'&&Theme::variant([],'future-block')==='generic','Unknown component fallback');
$seo=$d['seo'];$mode='Uzaktan / çevrim içi';$landingPreview=false;$landingContacts=[['url'=>'/iletisim','label'=>'İletişim','icon'=>'message-circle'],['url'=>'tel:+905551111111','label'=>'+90 555 111 11 11','icon'=>'phone'],['url'=>'https://wa.me/905551111111','label'=>'WhatsApp','icon'=>'whatsapp'],['url'=>'mailto:contact-qa@example.test','label'=>'contact-qa@example.test','icon'=>'mail']];
$landingServices=[['name'=>'Sentetik hizmet','slug'=>'sentetik-hizmet','description'=>'Açıklama']];
$landingScripts=[['title'=>'Sentetik yazılım','url'=>'/hazir-scriptler/sentetik-yazilim','description'=>'Yazılım bilgisi','image'=>'/assets/img/category-heroes/web.webp','category'=>'Yazılım']];
$landingBlogs=[['title'=>'Sentetik rehber','url'=>'/blog/sentetik-rehber','description'=>'Rehber bilgisi','image'=>'','category'=>'Rehber','image_alt'=>'']];
$landingReferences=[['title'=>'Sentetik portföy','description'=>'Portföy bilgisi','image'=>'/assets/img/category-heroes/web.webp','url'=>'https://example.test/']];
foreach(Theme::all()as $themeKey=>$profile){
    ob_start();require BASE_PATH.'/resources/views/frontend/landing/themes/page.php';$html=ob_get_clean();
    $doc=new DOMDocument();@$doc->loadHTML('<?xml encoding="UTF-8">'.$html);$xp=new DOMXPath($doc);
    $ok($xp->query('//h1')->length===1&&$xp->query('//main[@data-theme="'.$themeKey.'"]')->length===1,'Real semantic theme '.$themeKey);
    foreach(Page::SECTIONS as $type=>$label)$ok($xp->query('//*[@data-component="'.$type.'"]')->length===1,'All eight block types '.$themeKey.' '.$type);
    foreach(['/kategori/sentetik-hizmet','/hazir-scriptler/sentetik-yazilim','/blog/sentetik-rehber']as $link)$ok(str_contains($html,'href="'.$link.'"'),'Real link survives '.$themeKey);
    foreach($landingContacts as $contact)$ok($xp->query('//section[contains(@class,"nvl-contact")]//nav/a[@href="'.$contact['url'].'"]')->length===1,'Site contact option renders once '.$themeKey.' '.$contact['icon']);
    $ok(!str_contains($html,'Google Partner')&&!str_contains($html,'%230'),'No fabricated proof '.$themeKey);
    $ok(is_file(PUBLIC_PATH.'/assets/img/landing-themes/'.$themeKey.'.svg'),'Local preview '.$themeKey);
}
$block=['title'=>'Yeni güvenli blok','body'=>'<script>not executable</script> [İletişim](/iletisim)'];ob_start();require BASE_PATH.'/resources/views/frontend/landing/themes/'.Theme::component('future');$html=ob_get_clean();$ok(str_contains($html,'&lt;script&gt;')&&!str_contains($html,'<script>')&&str_contains($html,'href="/iletisim"'),'Generic fallback escapes and keeps safe links');
$css=file_get_contents(PUBLIC_PATH.'/assets/css/landing-themes.css');$ok(!str_contains($css,'!important')&&str_contains($css,'prefers-reduced-motion'),'Scoped, reduced-motion styles without overrides');
$ok(str_contains($css,'.nv-landing .nvl-contact-link{padding:')&&str_contains($css,'.nv-landing .nvl-contact nav a{width:100%'),'Every contact option has a full-width button style');
$js=file_get_contents(PUBLIC_PATH.'/assets/js/landing-themes.js');$ok(str_contains($js,"'landing_theme_' + host.dataset.pageId")&&!str_contains($js,'fetch(')&&!str_contains($js,'history.'),'Browser-only page-keyed preference; no write endpoint or URLs');
$effect=file_get_contents(PUBLIC_PATH.'/assets/js/landing-effects.js');$ok(str_contains($effect,'prefers-reduced-motion: no-preference')&&str_contains($effect,'landing:themechange')&&!str_contains($effect,'fetch('),'Pointer effect honors motion preference, theme switches and local scope');
echo "PASS $checks ten-theme registry/all-block-render/fallback/escaping/isolation checks\n";
