<?php
/** Isolated synthetic QA only. --keep-fixtures leaves only this run's pages for browser checks. */
declare(strict_types=1);
if(PHP_SAPI!=='cli')exit(2);
define('BASE_PATH',dirname(__DIR__));define('PUBLIC_PATH',BASE_PATH.'/public');
$_ENV=array_merge(parse_ini_file(BASE_PATH.'/.env'),['DB_NAME'=>'netvera_member_qa_20261018','APP_URL'=>'http://127.0.0.1:8019','NETVERA_LOCAL_AUTO_SETUP'=>'0']);
spl_autoload_register(static function($c){if(str_starts_with($c,'App\\'))require BASE_PATH.'/app/'.str_replace('\\','/',substr($c,4)).'.php';});require BASE_PATH.'/app/Core/Helpers.php';
use App\Core\Database;use App\Services\RegionalLandingService as Page;use App\Services\LandingThemeService as Theme;use App\Services\LandingCatalogService as Catalog;
$db=Database::getInstance();$n=0;$ids=[];$pass=false;$keep=in_array('--keep-fixtures',$argv,true);$run=bin2hex(random_bytes(4));
$ok=static function($v,$why)use(&$n){if(!$v)throw new RuntimeException($why);$n++;};
$ok((bool)$db->fetch('SELECT id FROM users WHERE email=?',['member-owner@example.test']),'Synthetic QA database required');
$ok(!$db->fetch('SELECT id FROM payment_gateways WHERE is_active=1 LIMIT 1'),'Payments disabled');
$ok(!$db->fetch("SELECT setting_key FROM settings WHERE setting_key='smtp_host' AND setting_value!=''"),'Mail disabled');
$client=static function(){$h=curl_init();curl_setopt_array($h,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_COOKIEFILE=>'',CURLOPT_FOLLOWLOCATION=>false,CURLOPT_TIMEOUT=>25]);return $h;};
$request=static function($h,$path,$post=null){curl_setopt_array($h,[CURLOPT_URL=>'http://127.0.0.1:8019'.$path,CURLOPT_POST=>$post!==null]);if($post!==null)curl_setopt($h,CURLOPT_POSTFIELDS,http_build_query($post));$html=curl_exec($h);if($html===false)throw new RuntimeException('QA HTTP unavailable');return [(int)curl_getinfo($h,CURLINFO_HTTP_CODE),(string)$html];};
$token=static function($html){preg_match('/name="_csrf_token" value="([^"]+)"/',$html,$m);return $m[1]??'';};
$dom=static function($html){$d=new DOMDocument();@$d->loadHTML('<?xml encoding="UTF-8">'.$html);$xp=new DOMXPath($d);foreach($xp->query('//template')as $inert)$inert->parentNode->removeChild($inert);return new DOMXPath($d);};
$guest=$client();$admin=$client();
try{
    [,$html]=$request($admin,'/admin/giris');[$code]=$request($admin,'/admin/giris',['_csrf_token'=>$token($html),'email'=>'member-admin@example.test','password'=>'synthetic-member-qa-only']);$ok($code===302,'Admin login');
    [,$editor]=$request($admin,'/admin/konumsal-sayfalar/yeni?sablon=reklam');$csrf=$token($editor);$xp=$dom($editor);
    $ok($xp->query('//input[@name="theme_key" and @checked and @value="landing-01"]')->length===1,'New page uses safe theme 01');
    $ok($xp->query('//input[@name="theme_key"]')->length===11&&$xp->query('//*[@data-theme-preview]')->length===20,'Ten preview cards plus legacy fallback');
    $base=Page::starter('reklam');$base['status']='active';$base['theme_switcher']='1';$base['seo']['robots']='noindex,follow';$base['_csrf_token']=$csrf;
    $base['service_ids']=array_slice(array_column(Catalog::services(),'id'),0,2);$base['script_ids']=array_slice(array_column(Catalog::scripts(),'id'),0,2);$base['blog_ids']=array_slice(array_column(Catalog::blogs(),'id'),0,2);
    foreach(['a'=>'landing-02','b'=>'landing-08']as $suffix=>$key){$d=$base;$d['slug']='qa-themes-'.$run.'-'.$suffix;$d['theme_key']=$key;$d['local_context'].=' Bağımsız tema kontrolü '.$run.'-'.$suffix.'.';[$code]=$request($admin,'/admin/konumsal-sayfalar/kaydet',$d);$row=Page::bySlug($d['slug']);$ok($code===302&&$row!==null,'Native page theme save '.$suffix);$ids[$suffix]=(int)$row['id'];}
    $a=Page::byId($ids['a']);$b=Page::byId($ids['b']);$unchangedB=$b['content'];$path='/'.$a['slug'];$baseline=null;$variants=[];
    foreach(Theme::all()as $key=>$profile){
        $data=Page::decode($a);$data['theme_key']=$key;$data['revision']=Page::revision($a);$data['id']=$a['id'];$data['_csrf_token']=$csrf;
        [$code]=$request($admin,'/admin/konumsal-sayfalar/kaydet',$data);$ok($code===302,'Theme saved '.$key);$a=Page::byId($ids['a']);$ok(Page::decode($a)['theme_key']===$key,'Theme persisted '.$key);
        [$code,$html]=$request($guest,$path);$xp=$dom($html);$ok($code===200&&$xp->query('//main[@data-theme="'.$key.'"]')->length===1,'Real SSR renderer '.$key);
        $ok($xp->query('//h1')->length===1,'One actual H1 '.$key);
        foreach(['overview','services','scripts','content','answer','blog','faq']as $type)$ok($xp->query('//main//*[@id="landing-block-'.$type.'"]')->length===1,'Visible block survives '.$key.' '.$type);
        $snapshot=[];foreach(['//title','//meta[@name="description"]/@content','//link[@rel="canonical"]/@href','//meta[@property="og:title"]/@content','//meta[@name="robots"]/@content','//h1','//main//h2','//main//h3','//script[@type="application/ld+json"]']as $query){$values=[];foreach($xp->query($query)as $node){$value=$node->textContent;if($query==='//script[@type="application/ld+json"]'){$graph=json_decode($value,true);$values[]=$graph;}else $values[]=$value;}$snapshot[$query]=$values;}
        // dateModified is expected to advance on admin saves; all schema identities stay stable.
        $schema=$snapshot['//script[@type="application/ld+json"]'];array_walk_recursive($schema,static function(&$value,$key){if($key==='dateModified')$value='SAVE_TIME';});$snapshot['//script[@type="application/ld+json"]']=$schema;
        if($baseline===null)$baseline=$snapshot;else $ok($snapshot===$baseline,'SEO and heading data invariant '.$key);
        $ok(Page::byId($ids['b'])['content']===$unchangedB,'Other page untouched '.$key);
        $variants[]=$profile['hero'];$ok(str_contains($html,'nvl-stage'.($key==='landing-08'?'-saas':' ')),'Theme owns real hero structure '.$key);
    }
    $ok(count(array_unique($variants))===10,'Ten distinct hero compositions');
    $data=Page::decode($a);$data['theme_key']='landing-02';$aId=Page::save($data,$ids['a'],Page::revision($a));$a=Page::byId($aId);
    [,$html]=$request($guest,$path.'?onizleme=1&tema=landing-04');$xp=$dom($html);$ok($xp->query('//main[@data-theme="landing-02"]')->length===1,'Guest query cannot override published theme');
    [,$html]=$request($admin,$path.'?onizleme=1&tema=landing-04');$xp=$dom($html);$ok($xp->query('//main[@data-theme="landing-04"]')->length===1&&str_contains($html,'noindex'),'Authorized preview is private/noindex');
    $ok(Page::decode(Page::byId($ids['a']))['theme_key']==='landing-02','Preview never persists');
    $a=Page::byId($ids['a']);$hidden=Page::decode($a);$hidden['theme_switcher']=false;Page::save($hidden,$ids['a'],Page::revision($a));
    [,$html]=$request($guest,$path);$ok(!str_contains($html,'data-theme-choice')&&!str_contains($html,'data-landing-hero=')&&str_contains($html,'data-theme="landing-02"'),'Disabled picker omits controls/inert templates but retains SSR theme');
    $a=Page::byId($ids['a']);$hidden['theme_switcher']=true;Page::save($hidden,$ids['a'],Page::revision($a));$a=Page::byId($ids['a']);
    $data=Page::decode($a);$data['theme_key']='invalid-theme';$data['slug']='qa-themes-'.$run.'-fallback';$data['local_context'].=' Fallback '.$run;unset($data['id']);$ids['fallback']=Page::save($data);
    [,$html]=$request($guest,'/'.$data['slug']);$ok(str_contains($html,'class="regional-landing'),'Unknown key preserves legacy instead of 500');
    foreach(['/','/kategoriler','/blog','/hazir-scriptler','/iletisim','/admin/bilgi-alani']as $normal){[,$html]=$request(str_starts_with($normal,'/admin')?$admin:$guest,$normal);$ok(!str_contains($html,'data-landing-theme-host')&&!str_contains($html,'landing-themes.css'),'Other route isolated '.$normal);}
    $pass=true;echo "PASS $n ten-theme SSR/admin/SEO/preview/isolation checks\n";if($keep)echo 'THEME_A='.$path.' EDITOR=/admin/konumsal-sayfalar/'.$ids['a'].'/duzenle THEME_B=/'.$b['slug'].' FALLBACK=/'.$data['slug']."\n";
}finally{if(!$pass||!$keep)foreach($ids as $id)$db->delete('pages','id=?',[$id]);curl_close($guest);curl_close($admin);}
