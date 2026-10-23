<?php
/** All writes are scoped to synthetic QA fixtures. No payments/mail/provider calls. */
declare(strict_types=1);
if(PHP_SAPI!=='cli')exit(2);
define('BASE_PATH',dirname(__DIR__));define('PUBLIC_PATH',BASE_PATH.'/public');
$_ENV=array_merge(parse_ini_file(BASE_PATH.'/.env'),['DB_NAME'=>'netvera_member_qa_20261018','APP_URL'=>'http://127.0.0.1:8019','NETVERA_LOCAL_AUTO_SETUP'=>'0']);
spl_autoload_register(static function($c){if(str_starts_with($c,'App\\'))require BASE_PATH.'/app/'.str_replace('\\','/',substr($c,4)).'.php';});require BASE_PATH.'/app/Core/Helpers.php';
use App\Core\Database;
use App\Services\RegionalLandingService as Landing;
use App\Services\LandingCatalogService as Catalog;
$db=Database::getInstance();$run=bin2hex(random_bytes(4));$n=0;$pages=[];$scripts=[];$blogs=[];$passed=false;$keep=in_array('--keep-fixtures',$argv,true);
$ok=static function($value,$why)use(&$n){if(!$value)throw new RuntimeException($why);$n++;};
$ok((bool)$db->fetch('SELECT id FROM users WHERE email=?',['member-owner@example.test']),'Dedicated synthetic QA database');
$ok(!$db->fetch('SELECT id FROM payment_gateways WHERE is_active=1 LIMIT 1'),'Gateways disabled');
$ok(!$db->fetch("SELECT setting_key FROM settings WHERE setting_key='smtp_host' AND setting_value!=''"),'Mail disabled');
$client=static function(){$h=curl_init();curl_setopt_array($h,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_COOKIEFILE=>'',CURLOPT_FOLLOWLOCATION=>false,CURLOPT_TIMEOUT=>20]);return $h;};
$request=static function($h,$path,$data=null){curl_setopt_array($h,[CURLOPT_URL=>'http://127.0.0.1:8019'.$path,CURLOPT_POST=>$data!==null]);if($data!==null)curl_setopt($h,CURLOPT_POSTFIELDS,http_build_query($data));$html=curl_exec($h);if($html===false)throw new RuntimeException('Local QA HTTP failed');return [(int)curl_getinfo($h,CURLINFO_HTTP_CODE),(string)$html];};
$token=static function($html){preg_match('/name="_csrf_token" value="([^"]+)"/',$html,$m);return $m[1]??'';};
$xpath=static function($html){$d=new DOMDocument();@$d->loadHTML('<?xml encoding="UTF-8">'.$html);return new DOMXPath($d);};
$guest=$client();$admin=$client();$marker='SYNTHETIC_DO_NOT_RENDER_'.$run;
try{
    foreach([1,2,3]as $i)$scripts[]=$db->insert('nv_legacy_script_products',['category_legacy_id'=>0,'slug'=>'qa-builder-script-'.$run.'-'.$i,'name'=>'QA Yazılım '.$i,'short_desc'=>'Sentetik yazılım seçkisi.','cover_image'=>'/assets/img/category-heroes/web.webp','price'=>100,'active'=>$i===3?0:1,'public_json'=>json_encode(['demo_admin_password'=>$marker,'demo_credentials_public'=>0])]);
    foreach([1,2,3]as $i)$blogs[]=$db->insert('blog_posts',['slug'=>'qa-builder-blog-'.$run.'-'.$i,'title'=>'QA Rehber '.$i,'excerpt'=>'<script>'.$marker.'</script> Sentetik rehber metni.','content'=>'Sentetik doğrulama yazısı.','image'=>'/assets/img/category-heroes/search.webp','image_alt'=>'Sentetik rehber görseli','status'=>$i===3?'draft':'active','published_at'=>date('Y-m-d H:i:s'),'faqs'=>'[]','toc'=>'[]']);
    [$code]=$request($guest,'/admin/konumsal-sayfalar/yeni');$ok($code===302,'Editor admin guard');
    [,$html]=$request($admin,'/admin/giris');[$code]=$request($admin,'/admin/giris',['_csrf_token'=>$token($html),'email'=>'member-admin@example.test','password'=>'synthetic-member-qa-only']);$ok($code===302,'Synthetic admin login');
    [,$html]=$request($admin,'/admin/konumsal-sayfalar/yeni?sablon=reklam');$csrf=$token($html);$xp=$xpath($html);
    foreach(['services','scripts','blog','references']as $type)$ok($xp->query('//*[@data-content-picker="'.$type.'"]')->length===1,'Independent native picker '.$type);
    foreach(['script_ids','blog_ids','service_ids','reference_ids']as $field)$ok(str_contains($html,$field),'Selection field '.$field);
    $ok(!str_contains($html,$marker),'Product private JSON not present in admin choices');
    $ok($xp->query('//input[@name="script_ids[]" and @value="'.$scripts[2].'"]')->length===0,'Inactive script not offered');
    $ok($xp->query('//input[@name="blog_ids[]" and @value="'.$blogs[2].'"]')->length===0,'Draft blog not offered');
    $data=Landing::starter('reklam');$data['slug']='qa-builder-'.$run;$data['local_context'].=' Sentetik geliştirme kontrolü '.$run.'.';$data['status']='active';$data['seo']['robots']='index,follow';$data['script_ids']=[$scripts[1],$scripts[0],$scripts[2]];$data['blog_ids']=[$blogs[1],$blogs[0],$blogs[2]];
    $data['scripts_title']='Bu sayfanın yazılımları';$data['blog_title']='Bu sayfanın rehberleri';$data['section_order']=['blog','scripts','services','overview','references','content','answer','faq'];$data['_csrf_token']=$csrf;$data['id']=0;$data['revision']='';
    $category=$db->fetch("SELECT id FROM categories WHERE status='active' ORDER BY id LIMIT 1");$data['service_ids']=$category?[(int)$category['id']]:[];
    [$code]=$request($admin,'/admin/konumsal-sayfalar/kaydet',array_diff_key($data,['_csrf_token'=>true]));$ok($code===403,'Native builder CSRF guard');
    [$code]=$request($admin,'/admin/konumsal-sayfalar/kaydet',$data);$row=Landing::bySlug($data['slug']);$ok($code===302&&$row!==null,'Selections saved with existing page endpoint');$pages[]=(int)$row['id'];$path='/'.$data['slug'];
    $saved=Landing::decode($row);$ok($saved['script_ids']===$data['script_ids']&&$saved['blog_ids']===$data['blog_ids'],'Independent IDs persist in exact chosen order');
    [$code,$html]=$request($guest,$path);$xp=$xpath($html);$ok($code===200&&$xp->query('//h1')->length===1,'Public landing remains semantic and routable');
    $ok($xp->query('//*[@id="landing-block-scripts"]//article')->length===2,'Only active selected scripts render');
    $ok($xp->query('//*[@id="landing-block-blog"]//article')->length===2,'Only published selected blogs render');
    $ok(strpos($html,'id="landing-block-blog"')<strpos($html,'id="landing-block-scripts"'),'Chosen section order renders');
    $ok(strpos($html,'QA Yazılım 2')<strpos($html,'QA Yazılım 1')&&strpos($html,'QA Rehber 2')<strpos($html,'QA Rehber 1'),'Each card selection order renders');
    $ok(str_contains($html,'Bu sayfanın yazılımları')&&str_contains($html,'Bu sayfanın rehberleri'),'Independent section titles render');
    $ok(str_contains($html,'/hazir-scriptler/qa-builder-script-'.$run.'-2')&&str_contains($html,'/blog/qa-builder-blog-'.$run.'-2'),'Canonical product/blog destinations');
    $ok(!str_contains($html,'"demo_admin_password"')&&!str_contains($html,'<script>'.$marker.'</script>'),'No private product data or executable excerpt HTML');
    foreach(['scripts','blog']as $type)foreach($xp->query('//*[@id="landing-block-'.$type.'"]//img')as $img){[$code]=$request($guest,$img->getAttribute('src'));$ok($code===200,'Real showcase image response');}
    [,$editor]=$request($admin,'/admin/konumsal-sayfalar/'.$row['id'].'/duzenle');$xp=$xpath($editor);
    $ok($xp->query('//*[@data-content-picker="scripts"]//*[@data-picker-selected]//input[@checked]')->length===3,'Editor keeps unavailable selection instead of silently deleting it');
    $ok(str_contains($editor,'Yayında olmayan / bulunamayan kayıt #'.$scripts[2]),'Unavailable selection explicitly explained');
    $data['id']=$row['id'];$data['revision']=Landing::revision($row);$data['enabled_sections']=['overview','content','faq'];
    $request($admin,'/admin/konumsal-sayfalar/kaydet',$data);$row=Landing::byId((int)$row['id']);[,$html]=$request($guest,$path);
    $ok(!str_contains($html,'id="landing-block-scripts"')&&!str_contains($html,'id="landing-block-blog"'),'Disabled sections do not render');
    $ok(!str_contains($html,'href="#landing-block-scripts"')&&Landing::decode($row)['script_ids']===$data['script_ids'],'Disabled navigation omitted without losing selections');
    $data['revision']=Landing::revision($row);$data['enabled_sections']=array_keys(Landing::SECTIONS);$data['layout']='editorial';$data['theme']='rose';
    $request($admin,'/admin/konumsal-sayfalar/kaydet',$data);$row=Landing::byId((int)$row['id']);[,$html]=$request($guest,$path);$ok(str_contains($html,'landing-layout-editorial')&&str_contains($html,'landing-theme-rose'),'Alternate design remains selectable');
    $bad=array_replace($data,['revision'=>Landing::revision($row),'title'=>'KAYBOLMAYACAK BAŞLIK','image'=>'https://evil.test/tracker.png','script_ids'=>[['bad']]]);
    [$code,$html]=$request($admin,'/admin/konumsal-sayfalar/kaydet',$bad);$ok($code===422&&str_contains($html,'KAYBOLMAYACAK BAŞLIK'),'Validation error preserves submitted text, no redirect loss');
    $ok(!preg_match('~<img[^>]+src="https://evil.test~',$html),'Invalid image is never loaded in recovered admin preview');
    $ok(Landing::byId((int)$row['id'])['title']!==$bad['title'],'Invalid data never overwrites persisted page');
    $stale=array_replace($data,['revision'=>str_repeat('a',64),'title'=>'STALE TITLE']);
    [$code]=$request($admin,'/admin/konumsal-sayfalar/kaydet',$stale);$ok($code===422&&Landing::byId((int)$row['id'])['title']!=='STALE TITLE','Stale editor cannot overwrite');
    $data['revision']=Landing::revision($row);$data['layout']='studio';$data['theme']='violet';$data['slug'].='-guncel';
    $request($admin,'/admin/konumsal-sayfalar/kaydet',$data);$row=Landing::byId((int)$row['id']);$alias=$db->fetch('SELECT id FROM pages WHERE slug=?',[ltrim($path,'/')]);if($alias)$pages[]=(int)$alias['id'];
    [$code]=$request($guest,$path);$ok($code===301,'URL rename preserves old indexed route');$path='/'.$data['slug'];
    [,$html]=$request($guest,$path);$ok(str_contains($html,'landing-layout-studio')&&str_contains($html,'rel="canonical" href="http://127.0.0.1:8019'.$path.'"'),'Studio design and canonical survive rename');
    $request($admin,'/admin/konumsal-sayfalar/'.$row['id'].'/kopyala',['_csrf_token'=>$csrf]);$clone=$db->fetch('SELECT * FROM pages WHERE slug LIKE ? ORDER BY id DESC LIMIT 1',[$data['slug'].'-taslak-%']);
    $ok($clone!==null&&$clone['status']==='inactive','Clone remains unpublished');if($clone)$pages[]=(int)$clone['id'];
    $ok(Landing::decode($clone)['script_ids']===$data['script_ids']&&Landing::decode($clone)['blog_ids']===$data['blog_ids'],'Clone preserves independent selections');
    // Keep source fixture content intentionally professional for visual QA, without unsafe test marker text.
    if($keep)foreach(array_slice($blogs,0,2)as $blogId)$db->update('blog_posts',['excerpt'=>'Dijital projeler için kapsam, içerik ve uygulama yaklaşımı.'],'id=?',[$blogId]);
    $passed=true;echo "PASS $n HTTP builder/native selections/public order/visibility/SEO/security/isolation checks\n";if($keep)echo 'QA_BUILDER='.$path.' EDITOR=/admin/konumsal-sayfalar/'.$row['id'].'/duzenle'."\n";
}finally{
    if(!$keep||!$passed){foreach($pages as $id)$db->delete('pages','id=?',[$id]);foreach($scripts as $id)$db->delete('nv_legacy_script_products','legacy_id=?',[$id]);foreach($blogs as $id)$db->delete('blog_posts','id=?',[$id]);}
    curl_close($guest);curl_close($admin);
}
