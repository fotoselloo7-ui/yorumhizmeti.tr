<?php
/** Isolated localhost QA. No production records, payments, providers or mail. */
declare(strict_types=1);
if(PHP_SAPI!=='cli')exit(2);
define('BASE_PATH',dirname(__DIR__));define('PUBLIC_PATH',BASE_PATH.'/public');
$_ENV=array_merge(parse_ini_file(BASE_PATH.'/.env'),['DB_NAME'=>'netvera_member_qa_20261018','APP_URL'=>'http://127.0.0.1:8019','NETVERA_LOCAL_AUTO_SETUP'=>'0']);
spl_autoload_register(static function($c){if(str_starts_with($c,'App\\'))require BASE_PATH.'/app/'.str_replace('\\','/',substr($c,4)).'.php';});require BASE_PATH.'/app/Core/Helpers.php';
use App\Services\RegionalLandingService as Landing;
use App\Services\HomeKnowledgeService as Home;
use App\Core\Database;
$db=Database::getInstance();$n=0;$ids=[];$run=bin2hex(random_bytes(3));$keep=in_array('--keep-fixtures',$argv,true);$fixturePaths=[];
$ok=static function($condition,$why)use(&$n){if(!$condition)throw new RuntimeException($why);$n++;};
$ok((bool)$db->fetch('SELECT id FROM users WHERE email=?',['member-owner@example.test']),'Dedicated synthetic QA database required');
$ok(!$db->fetch('SELECT id FROM payment_gateways WHERE is_active=1 LIMIT 1'),'QA payment gateways disabled');
$ok(!$db->fetch("SELECT setting_key FROM settings WHERE setting_key='smtp_host' AND setting_value!=''"),'QA email disabled');
$client=static function(){ $h=curl_init();curl_setopt_array($h,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_COOKIEFILE=>'',CURLOPT_FOLLOWLOCATION=>false,CURLOPT_TIMEOUT=>20]);return $h;};
$request=static function($h,string $path,?array $data=null,bool $multipart=false):array{
 if(!str_starts_with($path,'/')||str_starts_with($path,'//'))throw new RuntimeException('Local paths only');
 curl_setopt_array($h,[CURLOPT_URL=>'http://127.0.0.1:8019'.$path,CURLOPT_POST=>$data!==null]);if($data!==null)curl_setopt($h,CURLOPT_POSTFIELDS,$multipart?$data:http_build_query($data));
 $html=curl_exec($h);if($html===false)throw new RuntimeException('QA HTTP failed');return [(int)curl_getinfo($h,CURLINFO_HTTP_CODE),(string)$html];
};
$token=static function($html){preg_match('/name="_csrf_token" value="([^"]+)"/',$html,$m);return $m[1]??'';};
$dom=static function($html){$doc=new DOMDocument();@$doc->loadHTML('<?xml encoding="UTF-8">'.$html);return new DOMXPath($doc);};
$guest=$client();$admin=$client();
$backup=[];foreach([Home::KEY,Landing::REDIRECTS,'showcase_references_v1']as $key)$backup[$key]=$db->fetch('SELECT * FROM settings WHERE setting_key=?',[$key]);
try{
 [$code]=$request($guest,'/admin/bilgi-alani');$ok($code===302,'Home editor requires admin');[$code]=$request($guest,'/admin/konumsal-sayfalar');$ok($code===302,'Landing editor requires admin');
 [, $html]=$request($guest,'/');$xp=$dom($html);$ok($xp->query('//h1')->length===1&&$xp->query('//article[contains(@class,"knowledge-card")]')->length===3,'Three knowledge cards without a second H1');
 [, $html]=$request($admin,'/admin/giris');[$code]=$request($admin,'/admin/giris',['_csrf_token'=>$token($html),'email'=>'member-admin@example.test','password'=>'synthetic-member-qa-only']);$ok($code===302,'Synthetic admin login');
 [, $html]=$request($admin,'/admin/bilgi-alani');$csrf=$token($html);$xp=$dom($html);$ok($xp->query('//input[@name="cards[0][links][0][label]"]')->length===1,'Actual nested home link field names');$ok($xp->query('//textarea[@name="cards[2][faqs][0][answer]"]')->length===1,'Actual third-card FAQ field names');
 preg_match('/name="revision" value="([^"]+)"/',$html,$m);$home=Home::defaults();$home['revision']=$m[1];$home['_csrf_token']=$csrf;$home['cards'][0]['title']='SENTETİK BİLGİ KARTI';$home['cards'][0]['body']='Güvenli [Instagram hizmeti](/kategori/instagram-hizmetleri) <script>alert(1)</script>';$home['cards'][0]['faqs']=[['question'=>'SENTETİK SORU?','answer'=>'SENTETİK GÖRÜNÜR YANIT.']];
 [$code]=$request($admin,'/admin/bilgi-alani',array_diff_key($home,['_csrf_token'=>true]));$ok($code===403,'Home CSRF enforced');[$code]=$request($admin,'/admin/bilgi-alani',$home);$ok($code===302,'Home editor saves');
 [, $html]=$request($guest,'/');$ok(str_contains($html,'SENTETİK BİLGİ KARTI')&&str_contains($html,'SENTETİK GÖRÜNÜR YANIT.')&&str_contains($html,'href="/kategori/instagram-hizmetleri"'),'Home titles/FAQ/internal links reflected');$ok(!str_contains($html,'<script>alert(1)</script>'),'Home editorial HTML escaped');
 $home['cards'][0]['title']='STALE SHOULD NOT SAVE';$request($admin,'/admin/bilgi-alani',$home);[, $html]=$request($guest,'/');$ok(!str_contains($html,'STALE SHOULD NOT SAVE'),'Stale home revision cannot overwrite');
 [, $html]=$request($admin,'/admin/konumsal-sayfalar/yeni?sablon=reklam');$csrf=$token($html);$ok(str_contains($html,'Alanya Reklam Ajansı')&&str_contains($html,'name="sections[0][title]"')&&str_contains($html,'landing-editor.js'),'Real starter form, structured H2 fields, external admin script');
 $categoryIds=array_map('intval',array_column($db->fetchAll("SELECT id FROM categories WHERE status='active' ORDER BY id LIMIT 3"),'id'));
 $reference=['id'=>'qa_reference_'.$run,'title'=>'Sentetik portföy örneği','status'=>'active','group'=>'agency','service'=>'web-site','media_type'=>'website','image'=>'/assets/img/category-heroes/web.webp','url'=>'https://example.test/project','description'=>'Yerel müşteri iddiası içermeyen sentetik çalışma.'];
 $db->query('INSERT INTO settings(setting_key,setting_value,setting_group) VALUES(?,?,?) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)',['showcase_references_v1',json_encode([$reference],JSON_UNESCAPED_UNICODE),'homepage']);
 foreach(['reklam','sosyal','seo']as $kind){
  $data=Landing::starter($kind);$data['local_context'].=' Sentetik doğrulama çalışması '.$run.'.';$data['slug']='qa-region-'.$run.'-'.$data['slug'];$data['service_ids']=$categoryIds;$data['reference_ids']=[$reference['id']];$data['_csrf_token']=$csrf;$data['id']='0';$data['revision']='';
  [$code]=$request($admin,'/admin/konumsal-sayfalar/kaydet',array_diff_key($data,['_csrf_token'=>true]));$ok($code===403,'Landing save CSRF enforced');
  [$code]=$request($admin,'/admin/konumsal-sayfalar/kaydet',$data);$row=Landing::bySlug($data['slug']);$ok($code===302&&$row!==null,'Actual admin creates draft');$ids[]=$row['id'];$path='/'.rawurlencode($row['slug']);
  [$code]=$request($guest,$path);$ok($code===404,'Draft not public');[$code]=$request($guest,$path.'?onizleme=1');$ok($code===404,'Query cannot grant preview');
  [$code,$html]=$request($admin,$path.'?onizleme=1');$ok($code===200&&str_contains($html,'noindex')&&str_contains($html,'Taslak önizleme'),'Authenticated draft preview remains noindex');
  $data['id']=$row['id'];$data['revision']=Landing::revision($row);$data['status']='active';$data['seo']['robots']='index,follow,max-image-preview:large';
  [$code]=$request($admin,'/admin/konumsal-sayfalar/kaydet',$data);$row=Landing::byId((int)$row['id']);$ok($code===302&&$row['status']==='active','Actual admin publishes');
  [$code,$html]=$request($guest,$path);$xp=$dom($html);$ok($code===200&&$xp->query('//h1')->length===1&&$xp->query('//h2')->length>=3,'Published landing has semantic hierarchy');$ok(str_contains($html,'Service')&&str_contains($html,'areaServed')&&!str_contains($html,'"@type":"LocalBusiness"'),'Truthful service area, no fake local branch schema');$ok(str_contains($html,$reference['title'])&&str_contains($html,'/kategori/'),'Selected active services/reference visible');$ok(str_contains($html,'href="/iletisim"'),'CTA inherits shared contact destination');$ok(str_contains($html,'application/ld+json')&&str_contains($html,'rel="canonical" href="http://127.0.0.1:8019'.$path.'"'),'Canonical and structured data available');
  if($kind==='reklam'){
   // Upload original local artwork through the real native multipart form.
   $data['revision']=Landing::revision($row);$upload=$data;$upload['image_upload']=new CURLFile(PUBLIC_PATH.'/assets/img/category-heroes/ads.webp','image/webp','synthetic-landing.webp');$flat=[];$flatten=static function($values,$prefix='')use(&$flatten,&$flat){foreach($values as $key=>$value){$name=$prefix===''?(string)$key:$prefix.'['.$key.']';if(is_array($value))$flatten($value,$name);else $flat[$name]=$value;}};$flatten($upload);
   [$code]=$request($admin,'/admin/konumsal-sayfalar/kaydet',$flat,true);$row=Landing::byId((int)$row['id']);$saved=Landing::decode($row);$ok($code===302&&str_starts_with($saved['image'],'/uploads/landing-pages/')&&is_file(PUBLIC_PATH.$saved['image']),'Validated actual image upload');
   $data=Landing::decode($row)+['_csrf_token'=>$csrf,'id'=>$row['id'],'revision'=>Landing::revision($row)];$oldPath=$path;$data['slug'].='-yeni';$data['layout']='editorial';$data['theme']='blue';
   $request($admin,'/admin/konumsal-sayfalar/kaydet',$data);$row=Landing::byId((int)$row['id']);$path='/'.rawurlencode($row['slug']);[$code]=$request($guest,$oldPath);$ok($code===301,'Slug change preserves old root URL with 301');
   [$code]=$request($guest,'/sayfa/'.rawurlencode($row['slug']));$ok($code===301,'Generic page alias canonicalizes to root, no duplicate HTML');
   $stale=$data;$stale['title']='STALE LANDING';$request($admin,'/admin/konumsal-sayfalar/kaydet',$stale);$ok(Landing::byId((int)$row['id'])['title']!=='STALE LANDING','Stale landing revision cannot overwrite');
   $same=Landing::decode($row);$same['slug']='qa-region-'.$run.'-duplicate';$same['_csrf_token']=$csrf;$request($admin,'/admin/konumsal-sayfalar/kaydet',$same);$ok(Landing::bySlug($same['slug'])===null,'Copied region body cannot be published');
   [, $sitemap]=$request($guest,'/sitemap.xml');$ok(str_contains($sitemap,$path)&&!str_contains($sitemap,'/sayfa/'.rawurlencode($row['slug']))&&!str_contains($sitemap,$oldPath.'</loc>'),'Sitemap uses current root canonical only');
   $hide=Landing::decode($row)+['_csrf_token'=>$csrf,'id'=>$row['id'],'revision'=>Landing::revision($row)];$hide['seo']['robots']='noindex,follow';$request($admin,'/admin/konumsal-sayfalar/kaydet',$hide);[, $sitemap]=$request($guest,'/sitemap.xml');$ok(!str_contains($sitemap,$path.'</loc>'),'Noindex landing excluded from sitemap');$row=Landing::byId((int)$row['id']);$hide['revision']=Landing::revision($row);$hide['seo']['robots']='index,follow,max-image-preview:large';$request($admin,'/admin/konumsal-sayfalar/kaydet',$hide);
  }
  $fixturePaths[]=$path;
 }
 foreach(['/','/hazir-scriptler','/kategori/instagram-hizmetleri','/kategoriler/grup/agency','/sayfa/gizlilik-politikasi','/hizmet-bolgeleri']as $path){[$code]=$request($guest,$path);$ok($code===200,'Existing/public discovery route preserved: '.$path);}
 [$code]=$request($guest,'/qa-unknown-region-'.$run);$ok($code===404,'Unknown root remains 404');
}finally{
 // Keep only synthetic QA fixture pages on explicit CLI flag for browser QA.
 if(!$keep)foreach($ids as $id)$db->delete('pages','id=?',[(int)$id]);
 foreach($backup as $key=>$row){if($keep&&$key==='showcase_references_v1')continue;if($row)$db->query('INSERT INTO settings(setting_key,setting_value,setting_group) VALUES(?,?,?) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value),setting_group=VALUES(setting_group)',[$key,$row['setting_value'],$row['setting_group']]);else $db->delete('settings','setting_key=?',[$key]);}
 curl_close($guest);curl_close($admin);
}
echo "PASS $n HTTP admin/home/landing/preview/upload/301/SEO/concurrency/isolation checks\n";
if($keep)echo 'QA_FIXTURES='.json_encode($fixturePaths,JSON_UNESCAPED_SLASHES).' IDS='.json_encode($ids)."\n";
