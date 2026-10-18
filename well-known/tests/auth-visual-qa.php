<?php
/** Offline render contracts; optional read-only HTTP checks against local UI QA. */
declare(strict_types=1);
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
$root=dirname(__DIR__);define('PUBLIC_PATH',$root.'/public');
$settings=[];
function setting(string $key,string $default=''):string{return (string)($GLOBALS['settings'][$key]??$default);}
function asset(string $path):string{return '/assets/'.ltrim($path,'/');}
function upload_url(?string $path,string $fallbackType='default'):string{return (string)$path;}
function e(?string $value):string{return htmlspecialchars($value??'',ENT_QUOTES,'UTF-8');}
function icon(string $key,int $size=16):string{return '<svg aria-hidden="true"></svg>';}
function csrfField():string{return '<input type="hidden" name="_csrf_token" value="synthetic-visual-token">';}
require $root.'/app/Services/SiteLogoService.php';
$count=0;$assert=static function(bool $ok,string $why)use(&$count):void{if(!$ok)throw new RuntimeException($why);$count++;};
$render=static function(string $file)use($root):string{$flash=[];ob_start();require $root.'/resources/views/frontend/auth/'.$file.'.php';return ob_get_clean();};
$xpath=static function(string $html):DOMXPath{$doc=new DOMDocument();libxml_use_internal_errors(true);$doc->loadHTML('<?xml encoding="UTF-8">'.$html);libxml_clear_errors();return new DOMXPath($doc);};
foreach(['login'=>'/giris','register'=>'/kayit']as $file=>$action){
 $html=$render($file);$x=$xpath($html);
 $assert($x->query('//form[@method="POST" and @action="'.$action.'"]')->length===1,'Original form endpoint / method: '.$file);
 $assert($x->query('//input[@name="_csrf_token"]')->length===1,'CSRF field retained: '.$file);
 $assert($x->query('//input[@name="email" and @type="email" and @required]')->length===1,'Email constraints retained: '.$file);
 $assert($x->query('//input[@name="password" and @type="password" and @required]')->length===1,'Password field retained: '.$file);
 $assert($x->query('//button[@type="button" and @data-auth-toggle="password"]')->length===1,'Password reveal stays non-submit: '.$file);
 $assert($x->query('//span[@class="auth25-logo-surface"]/img[@src="/assets/img/netvera-logo-current.svg"]')->length===2,'Desktop and mobile use real admin-managed logo: '.$file);
 $assert($x->query('//div[@class="auth25-art" and @aria-hidden="true"]/img[@src="/assets/img/auth-woman-fuchsia.webp" and @width="1024" and @height="1536" and @alt=""]')->length===1,'Decorative portrait / intrinsic geometry: '.$file);
 $assert(!str_contains($html,'hero-woman-cutout')&&!str_contains($html,'auth25-brandmark'),'Old woman / Y placeholder retired: '.$file);
}
$settings=['site_logo_mode'=>'custom','site_logo'=>'https://example.test/synthetic-brand.svg','site_name'=>'NetVera & <Sentetik>'];
$html=$render('login');$x=$xpath($html);
$assert($x->query('//img[@src="https://example.test/synthetic-brand.svg"]')->length===2,'Admin custom logo remains supported');
$assert(str_contains($html,'NetVera &amp; &lt;Sentetik&gt;'),'Admin identity escaped');
$css=file_get_contents($root.'/public/assets/css/auth-premium-v25.css');
$assert(str_contains($css,'.auth25-logo-surface{')&&str_contains($css,'background:#fff'),'High contrast logo surface has one style owner');
$assert(str_contains($css,'object-fit:contain;filter:none'),'Original logo colors are not filtered');
$assert(!str_contains(file_get_contents($root.'/resources/views/layouts/app.php'),'auth-brand-logo-v94.css'),'Unused conflicting logo override no longer loaded');
$size=getimagesize(PUBLIC_PATH.'/assets/img/auth-woman-fuchsia.webp');
$assert($size[0]===1024&&$size[1]===1536,'Portrait native resolution retained');

if(in_array('--http',$argv,true)){
 foreach(['/giris','/kayit']as $route){
  $ch=curl_init('http://127.0.0.1:8017'.$route);curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_TIMEOUT=>10]);$html=curl_exec($ch);$code=curl_getinfo($ch,CURLINFO_HTTP_CODE);curl_close($ch);
  $assert($html!==false&&$code===200,'Real local page renders: '.$route);$x=$xpath($html);
  $assert($x->query('//input[@name="_csrf_token" and @value!=""]')->length===1,'Real CSRF token rendered: '.$route);
  $assert($x->query('//img[@src="/assets/img/auth-woman-fuchsia.webp"]')->length===1,'Real local portrait rendered: '.$route);
  $assert($x->query('//span[@class="auth25-logo-surface"]/img')->length===2,'Real shared logo rendered: '.$route);
  $assert($x->query('//meta[@name="robots" and @content="noindex, nofollow"]')->length===1,'Private account page robots preserved: '.$route);
 }
}
echo "PASS $count auth portrait, brand contrast, admin logo, escaping, form and CSRF contracts\n";
