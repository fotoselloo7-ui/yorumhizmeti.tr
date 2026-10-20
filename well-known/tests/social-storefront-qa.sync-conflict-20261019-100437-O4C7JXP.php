<?php
/** Offline contracts. No DB, customer data, network, provider or payment call. */
declare(strict_types=1);
if(PHP_SAPI!=='cli')exit(2);
define('BASE_PATH',dirname(__DIR__));define('PUBLIC_PATH',BASE_PATH.'/public');
spl_autoload_register(static function(string $c):void{if(str_starts_with($c,'App\\'))require_once BASE_PATH.'/app/'.str_replace('\\','/',substr($c,4)).'.php';});
require BASE_PATH.'/app/Core/Helpers.php';
use App\Services\SocialStorefrontService as Ui;
$n=0;$ok=static function(bool $v,string $why)use(&$n):void{if(!$v)throw new RuntimeException($why);$n++;};
$reject=static function(callable $f)use($ok):void{try{$f();$bad=false;}catch(InvalidArgumentException $e){$bad=true;}$ok($bad,'Malformed editorial input rejected');};
$ok(Ui::key('category',14)!==Ui::key('package',14),'Category/package identities isolated');
foreach([['Instagram Beğeni','engagement'],['Instagram Yorum Beğeni','comment_likes'],['Instagram Hikaye İzlenme','views'],['Türk Takipçi','audience'],['Spotify Dinlenme','views']]as [$text,$intent])$ok(Ui::intent($text)['key']===$intent,'Unicode intent and distinct service groups: '.$text);
foreach([['Instagram Hizmetleri','social-creator'],['TikTok Hizmetleri','video-creators'],['YouTube Abone','video-creators'],['Telegram Üye','social-community']]as [$name,$asset]){
 $p=Ui::defaults(['name'=>$name,'slug'=>slugify($name)]);$ok(str_contains($p['image'],$asset.'.webp'),'Relevant platform hero');
 $ok(count($p['sections'])===3&&count($p['faqs'])===4,'Visible useful defaults');
 $ok(!preg_match('/%100 organik|ilk sıra|birinci sayfa|4\.9|1\.324|YorumHizmeti/iu',json_encode($p,JSON_UNESCAPED_UNICODE)),'No fabricated rating or ranking guarantee');
}
$package=['id'=>7,'category_id'=>14,'name'=>'','category_name'=>'Instagram Takipçi','seo_title'=>'Instagram Türk Takipçi Başlangıç Paketi | Yorum Hizmeti','slug'=>'instagram-turk-takipci-100','short_description'=>'100 takipçi için hizmet.'];
$p=Ui::defaults($package,'package');$ok($p['title']==='Instagram Türk Takipçi — 100 takipçi'&&!str_contains($p['summary'],'Yorum Hizmeti'),'Compact fixed amount title, no legacy brand in new copy');
$clean=Ui::validate(['social_content_mode'=>'custom','social_page_title'=>'<h1>Özgün başlık</h1>','social_sections'=>[['title'=>'<h2>Nasıl alınır?</h2>','body'=>'Açık açıklama.','subheading'=>'Koşullar','list'=>"Bir\nİki"]],'social_faqs'=>[['question'=>'Soru?','answer'=>'Yanıt.']]]);
$ok($clean['title']==='Özgün başlık'&&$clean['sections'][0]['title']==='Nasıl alınır?','Headings are structured text, not executable HTML');
$custom=Ui::page(['id'=>1,'name'=>'Instagram Takipçi'],'category',$clean);$ok($custom['title']==='Özgün başlık'&&count($custom['sections'])===1,'Admin page content overrides defaults');
$hidden=Ui::page(['id'=>1,'name'=>'Instagram Takipçi'],'category',['mode'=>'hidden']);$ok(!$hidden['sections']&&!$hidden['faqs'],'Admin can hide lower content');
$bad=Ui::page(['id'=>1,'name'=>'Instagram Takipçi'],'category',['sections'=>'bad','mode'=>'custom']);$ok(count($bad['sections'])===3,'Corrupt stored JSON falls back safely');
foreach([['social_content_mode'=>['bad']],['social_page_title'=>['bad']],['social_page_image'=>'https://third-party.example/image.png'],['social_page_image'=>'/uploads/../.env'],['social_page_image'=>'/uploads/image.svg'],['social_sections'=>[['title'=>'Title without body']]],['social_faqs'=>[['question'=>'No answer']]],['social_sections'=>array_fill(0,13,[])],['social_page_summary'=>str_repeat('x',601)],['social_sections'=>[['title'=>'Title','body'=>'Body','image'=>'/uploads/test.webp']]]]as $input)$reject(static fn()=>Ui::validate($input));
$ok(Ui::prepare([],[])===null,'Legacy admin submission does not erase content');
$ok(Ui::imagePath('/assets/img/social-storefront/social-creator.webp')!=='','Local image path accepted');
$css=file_get_contents(BASE_PATH.'/public/assets/css/social-storefront.css');$ok(!str_contains($css,'!important')&&str_contains($css,'prefers-reduced-motion'),'One component, no override pile, reduced motion');
$view=file_get_contents(BASE_PATH.'/resources/views/frontend/social/package.php');$ok(str_contains($view,'name="quantity" value="1"')&&str_contains($view,'type="submit" name="checkout" value="1"'),'Native CSRF checkout with fixed server-owned package quantity');
$ok(!str_contains($view,'stepProductQty')&&!str_contains($view,'yv-product-variants'),'No secondary quantity multiplier or fake selector');
$ok(str_contains(file_get_contents(BASE_PATH.'/resources/views/frontend/social/editorial.php'),'<h2>')&&!str_contains(file_get_contents(BASE_PATH.'/resources/views/frontend/social/editorial.php'),'Article'),'Editorial landing sections are not fake blog articles');
foreach(['social-creator','video-creators','social-community']as $asset){$file=PUBLIC_PATH.'/assets/img/social-storefront/'.$asset.'.webp';$size=getimagesize($file);$ok($size[0]===1536&&$size[1]===1024&&filesize($file)<400000,'High resolution efficient local artwork');}
foreach(['users','heart','thumbs-up','play','bookmark','share-2','bar-chart','clipboard','arrow-up-right']as $icon)$ok(App\Services\IconService::has($icon),'Registered icon, not fallback box: '.$icon);
$ok(str_contains($css,'@media (hover:hover) and (pointer:fine)')&&str_contains($css,'--social-card-ink: #fff;'),'Pointer-only color inversion with shared state tokens');
$ok(str_contains($css,'.social-service-card,.social-directory-main')&&str_contains($css,'):focus-visible {'),'Keyboard parity across social hierarchy');
$ctaCss=file_get_contents(BASE_PATH.'/public/assets/css/home-faq-final-v47.css');
$ok(str_contains($ctaCss,'.home-service-cta>.container')&&!str_contains(substr($ctaCss,strpos($ctaCss,'/* One authoritative CTA')), '!important;\n  border-top'),'Compact CTA isolated from legacy overrides');
echo "PASS $n social storefront / structured editor / image / quantity / safety contracts\n";
