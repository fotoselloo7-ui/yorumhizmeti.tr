<?php
/** Fixed isolated member QA DB + localhost:8019 only. Never submits payment or contacts a provider. */
declare(strict_types=1);
if(PHP_SAPI!=='cli')exit(2);
define('BASE_PATH',dirname(__DIR__));define('PUBLIC_PATH',BASE_PATH.'/public');
$_ENV=array_merge(parse_ini_file(BASE_PATH.'/.env'),['DB_NAME'=>'netvera_member_qa_20261018','APP_URL'=>'http://127.0.0.1:8019','NETVERA_LOCAL_AUTO_SETUP'=>'0']);
spl_autoload_register(static function(string $c):void{if(str_starts_with($c,'App\\'))require_once BASE_PATH.'/app/'.str_replace('\\','/',substr($c,4)).'.php';});require BASE_PATH.'/app/Core/Helpers.php';
$db=App\Core\Database::getInstance();$pdo=$db->getPdo();$n=0;
$ok=static function(bool $v,string $why)use(&$n):void{if(!$v)throw new RuntimeException($why);$n++;};
$ok((bool)$db->fetch('SELECT id FROM users WHERE email=?',['member-owner@example.test']),'Synthetic QA fixtures required');
$ok(!(int)$db->fetch('SELECT COUNT(*) n FROM payment_gateways WHERE is_active=1')['n'],'QA payment gateways disabled');
$ok(!(int)$db->fetch("SELECT COUNT(*) n FROM settings WHERE setting_key='smtp_host' AND setting_value!=''")['n'],'QA mail disabled');
$client=static function(){ $h=curl_init();curl_setopt_array($h,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_COOKIEFILE=>'',CURLOPT_FOLLOWLOCATION=>false,CURLOPT_TIMEOUT=>20]);return $h;};
$request=static function($h,string $path,?array $data=null,bool $multipart=false):array{
 if(!str_starts_with($path,'/')||str_starts_with($path,'//'))throw new RuntimeException('Local paths only');
 curl_setopt_array($h,[CURLOPT_URL=>'http://127.0.0.1:8019'.$path,CURLOPT_POST=>$data!==null]);if($data!==null)curl_setopt($h,CURLOPT_POSTFIELDS,$multipart?$data:http_build_query($data));
 $html=curl_exec($h);if($html===false)throw new RuntimeException('Local HTTP failed');return [(int)curl_getinfo($h,CURLINFO_HTTP_CODE),(string)$html];
};
$dom=static function(string $html):DOMXPath{$doc=new DOMDocument();@$doc->loadHTML('<?xml encoding="UTF-8">'.$html);return new DOMXPath($doc);};
$token=static function(string $html):string{preg_match('/name="_csrf_token" value="([^"]+)"/',$html,$m);return $m[1]??'';};
$form=static function(string $html)use($dom):array{
 $xp=$dom($html);$data=[];
 foreach($xp->query('//form[@enctype="multipart/form-data"]//*[self::input or self::select or self::textarea][@name]')as $el){$name=$el->getAttribute('name');$type=$el->getAttribute('type');if(in_array($type,['file','submit'],true)||($type==='checkbox'&&!$el->hasAttribute('checked')))continue;
 if($el->tagName==='select'){$chosen=$xp->query('.//option[@selected]',$el)->item(0)??$xp->query('.//option',$el)->item(0);$value=$chosen?->getAttribute('value')??'';}
 elseif($el->tagName==='textarea')$value=$el->textContent;else $value=$el->getAttribute('value');
 $data[$name]=$value;}
 // Convert PHP bracket names once; subsequent http_build_query recreates them.
 parse_str(http_build_query($data),$nested);return $nested;
};
$guest=$client();$admin=$client();$category=$db->fetch("SELECT * FROM categories WHERE slug='instagram-takipci'");$package=$db->fetch("SELECT * FROM packages WHERE slug='instagram-turk-takipci-baslangic-paketi-100-takipci'");
$ok($category!==null&&$package!==null,'Canonical social fixtures exist');
$nonSocial=$db->fetch("SELECT * FROM packages WHERE status='active' AND category_id IN (SELECT id FROM categories WHERE slug='google-hizmetleri' OR parent_id IN (SELECT id FROM categories WHERE slug='google-hizmetleri')) LIMIT 1");
$paths=['/kategoriler/grup/social','/kategori/instagram-hizmetleri','/kategori/instagram-takipci','/kategori/instagram-takipci?hizmet=14-instagram-turk-takipci','/kategori/tiktok-hizmetleri','/kategori/youtube-hizmetleri','/paket/'.$package['slug']];
foreach($db->fetchAll('SELECT c.*,p.name parent_category_name,p.slug parent_category_slug FROM categories c LEFT JOIN categories p ON p.id=c.parent_id WHERE c.status=? AND (c.parent_id IS NULL OR p.status=?)',['active','active'])as $cat)if(App\Services\SocialPackageService::isSocialCategory((int)$cat['id']))$paths[]='/kategori/'.rawurlencode($cat['slug']);
foreach(array_unique($paths)as $path){[$code,$html]=$request($guest,$path);$ok($code===200&&!str_contains($html,'Fatal error'),'Social route renders: '.$path);$ok(str_contains($html,'social-storefront.css')&&substr_count($html,'<h1')===1,'One social component and one H1: '.$path);$xp=$dom($html);$ok($xp->query('//main[contains(@class,"social-storefront")]')->length===1,'Social-only main scope');foreach($xp->query('//main//img')as $img)$ok($img->hasAttribute('alt')&&str_starts_with($img->getAttribute('src'),'/'),'Local image with alt');}
foreach(['/','/kategoriler','/kategoriler/grup/agency','/kategoriler/grup/marketing','/kategori/web-site-hizmetleri','/hazir-scriptler','/paket/'.$nonSocial['slug']]as $path){[$code,$html]=$request($guest,$path);$ok($code===200&&!str_contains($html,'social-storefront.css')&&!str_contains($html,'class="social-storefront'),'Agency/software/marketing/home remain outside the new component: '.$path);}
[, $html]=$request($guest,'/kategori/instagram-hizmetleri');$xp=$dom($html);$ok($xp->query('//a[contains(@class,"social-service-card")]')->length>=10,'Platform has compact separate service types');
[, $html]=$request($guest,'/kategori/instagram-takipci?hizmet=14-instagram-turk-takipci');$ok(str_contains($html,'100</strong> takipçi')&&str_contains($html,'500</strong> takipçi'),'Each package has an explicit fixed amount');$ok(str_contains($html,'content="noindex,follow"')&&str_contains($html,'rel="canonical" href="http://127.0.0.1:8019/kategori/instagram-takipci"'),'Filtered service preserves canonical and noindex');
[, $html]=$request($guest,'/paket/'.$package['slug']);$csrf=$token($html);$xp=$dom($html);$ok($xp->query('//form[@id="packageForm"]//input[@name="quantity" and @value="1"]')->length===1,'No social multiplier in actual form');
$post=['_csrf_token'=>$csrf,'package_id'=>$package['id'],'quantity'=>999,'field_'.$package['id'].'_smm_target'=>'@synthetic.social.premium'];
[$code]=$request($guest,'/sepet/ekle',array_diff_key($post,['_csrf_token'=>true]));$ok($code===403,'CSRF guard retained');
[$code]=$request($guest,'/sepet/ekle',$post);[, $cart]=$request($guest,'/sepet');$ok($code===302&&str_contains($cart,'100 takipçi')&&str_contains($cart,'64,90'),'Native cart keeps 100 followers / 64.90 despite tampered amount');
$post['checkout']=1;[$code]=$request($guest,'/sepet/ekle',$post);$ok($code===302,'Buy Now remains native server redirect; no payment submission');
$backups=[];
foreach([['category',$category['id']],['package',$package['id']]]as [$type,$id]){$key=App\Services\SocialStorefrontService::key($type,(int)$id);$backups[$key]=$db->fetch('SELECT * FROM settings WHERE setting_key=?',[$key]);}
$relatedKeys=['category_hero_'.$category['id'],'package_highlights_'.$package['id'],'social_package_'.$package['id']];
foreach($relatedKeys as $key)$backups[$key]=$db->fetch('SELECT * FROM settings WHERE setting_key=?',[$key]);
$seoBackups=[];foreach([['category',$category['id']],['package',$package['id']]]as [$type,$id])$seoBackups[$type]=$db->fetch('SELECT * FROM nv_editorial_seo WHERE entity_type=? AND entity_id=?',[$type,$id]);
try{
 [, $html]=$request($admin,'/admin/giris');[$code]=$request($admin,'/admin/giris',['_csrf_token'=>$token($html),'email'=>'member-admin@example.test','password'=>'synthetic-member-qa-only']);$ok($code===302,'Synthetic admin login');
 foreach([['category',$category,'kategori'],['package',$package,'paket']]as [$type,$entity,$route]){
  [, $html]=$request($admin,'/admin/'.$route.'/'.$entity['id'].'/duzenle');$data=$form($html);$ok(($data['social_content_present']??'')==='1','Actual editor exposes scoped content fields');
  $data['social_page_title']='SENTETİK PREMIUM H1 '.$type;$data['social_content_mode']='custom';$data['social_sections']=[['title'=>'SENTETİK REHBER H2','body'=>'Özgün ve güvenli yanıt <script>alert(1)</script>','subheading'=>'SENTETİK H3','detail'=>'Açıklama','list'=>"Birinci kontrol\nİkinci kontrol",'image'=>'','alt'=>'Sentetik rehber görseli']];$data['social_faqs']=[['question'=>'Sentetik soru?','answer'=>'Sentetik cevap.']];
  [$code]=$request($admin,'/admin/'.$route.'/'.$entity['id'].'/guncelle',array_diff_key($data,['_csrf_token'=>true]));$ok($code===403,'Admin content CSRF enforced');
  [$code]=$request($admin,'/admin/'.$route.'/'.$entity['id'].'/guncelle',$data);$ok($code===302,'Actual admin saves content');
  [, $html]=$request($guest,($type==='category'?'/kategori/':'/paket/').$entity['slug']);$xp=$dom($html);$ok(str_contains($html,'SENTETİK PREMIUM H1 '.$type)&&str_contains($html,'<h2>SENTETİK REHBER H2</h2>')&&str_contains($html,'<h3>SENTETİK H3</h3>')&&str_contains($html,'Sentetik cevap.'),'Saved H1/H2/H3/FAQ reflected in server HTML');$ok(!str_contains($html,'<script>alert(1)</script>'),'Editorial HTML cannot execute scripts');
  $data['social_page_title']=str_repeat('x',181);$request($admin,'/admin/'.$route.'/'.$entity['id'].'/guncelle',$data);$row=$db->fetch('SELECT setting_value FROM settings WHERE setting_key=?',[App\Services\SocialStorefrontService::key($type,(int)$entity['id'])]);$ok(str_contains($row['setting_value'],'SENTETİK PREMIUM H1'),'Invalid content cannot overwrite saved data');
 }
 $ok($db->fetch('SELECT price FROM packages WHERE id=?',[$package['id']])['price']===$package['price'],'Editorial save preserves price');
}finally{
 foreach([['categories',$category],['packages',$package]]as [$table,$row]){$id=$row['id'];unset($row['id']);$db->update($table,$row,'id=?',[$id]);}
 foreach($backups as $key=>$row){if($row)$db->query('INSERT INTO settings(setting_key,setting_value,setting_group) VALUES(?,?,?) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value),setting_group=VALUES(setting_group)',[$key,$row['setting_value'],$row['setting_group']]);else $db->delete('settings','setting_key=?',[$key]);}
 foreach($seoBackups as $type=>$row){$id=$type==='category'?$category['id']:$package['id'];if($row)$db->query('INSERT INTO nv_editorial_seo(entity_type,entity_id,extra_json) VALUES(?,?,?) ON DUPLICATE KEY UPDATE extra_json=VALUES(extra_json)',[$type,$id,$row['extra_json']]);else $db->delete('nv_editorial_seo','entity_type=? AND entity_id=?',[$type,$id]);}
 curl_close($guest);curl_close($admin);
}
echo "PASS $n social HTTP / category hierarchy / SEO / native cart / admin editorial persistence and isolation checks; QA changes restored\n";
