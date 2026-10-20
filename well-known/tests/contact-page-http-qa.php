<?php
/** Writes exclusively to the isolated member QA database, never sends email. */
declare(strict_types=1);
if(PHP_SAPI!=='cli')exit(2);
define('BASE_PATH',dirname(__DIR__));define('PUBLIC_PATH',BASE_PATH.'/public');
$_ENV=array_merge(parse_ini_file(BASE_PATH.'/.env'),['DB_NAME'=>'netvera_member_qa_20261018','APP_URL'=>'http://127.0.0.1:8019','NETVERA_LOCAL_AUTO_SETUP'=>'0']);
spl_autoload_register(static function($c){if(str_starts_with($c,'App\\'))require BASE_PATH.'/app/'.str_replace('\\','/',substr($c,4)).'.php';});require BASE_PATH.'/app/Core/Helpers.php';
use App\Core\Database;
use App\Services\ContactPageService as Contact;
$db=Database::getInstance();$n=0;$passed=false;$keep=in_array('--keep-fixtures',$argv,true);$messages=[];
$ok=static function($v,$why)use(&$n){if(!$v)throw new RuntimeException($why);$n++;};
$ok((bool)$db->fetch('SELECT id FROM users WHERE email=?',['member-owner@example.test']),'Synthetic QA database required');
$ok(!$db->fetch('SELECT id FROM payment_gateways WHERE is_active=1 LIMIT 1'),'QA gateways disabled');
$ok(!$db->fetch("SELECT setting_key FROM settings WHERE setting_key='smtp_host' AND setting_value!=''"),'QA mail disabled');
$keys=array_merge(array_keys(Contact::defaults()),['site_phone','site_email','site_address','whatsapp_number']);$backup=[];
foreach($keys as $key)$backup[$key]=$db->fetch('SELECT * FROM settings WHERE setting_key=?',[$key]);
$client=static function(){$h=curl_init();curl_setopt_array($h,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_COOKIEFILE=>'',CURLOPT_FOLLOWLOCATION=>false,CURLOPT_TIMEOUT=>20]);return $h;};
$request=static function($h,$path,$data=null){curl_setopt_array($h,[CURLOPT_URL=>'http://127.0.0.1:8019'.$path,CURLOPT_POST=>$data!==null]);if($data!==null)curl_setopt($h,CURLOPT_POSTFIELDS,http_build_query($data));$html=curl_exec($h);if($html===false)throw new RuntimeException('QA request failed');return [(int)curl_getinfo($h,CURLINFO_HTTP_CODE),(string)$html];};
$token=static function($html){preg_match('/name="_csrf_token" value="([^"]+)"/',$html,$m);return $m[1]??'';};
$guest=$client();$admin=$client();
try{
    [$code]=$request($guest,'/admin/iletisim-ayarlari');$ok($code===302,'Editor admin guard');
    [$code]=$request($guest,'/admin/iletisim-ayarlari/kaydet',[]);$ok($code===302,'Save admin guard');
    [,$html]=$request($admin,'/admin/giris');[$code]=$request($admin,'/admin/giris',['_csrf_token'=>$token($html),'email'=>'member-admin@example.test','password'=>'synthetic-member-qa-only']);$ok($code===302,'Synthetic admin login');
    [,$html]=$request($admin,'/admin/iletisim-ayarlari');$csrf=$token($html);$ok($csrf!==''&&str_contains($html,'name="contact_map_embed"'),'Native map editor');
    $data=Contact::defaults()+['site_email'=>'contact-qa@example.test','site_phone'=>'+90 555 111 22 33','whatsapp_number'=>'905551112233','site_address'=>'Sentetik QA adresi — gerçek kurum adresi değildir.'];
    $url='https://maps.google.com/maps?q=Alanya&output=embed';$data['contact_map_enabled']='1';$data['contact_map_embed']='<iframe src="https://maps.google.com/maps?q=Alanya&amp;output=embed" width="600" style="border:0;" onload="alert(1)"></iframe>';
    [$code]=$request($admin,'/admin/iletisim-ayarlari/kaydet',$data);$ok($code===403,'CSRF enforced');
    $data['_csrf_token']=$csrf;[$code]=$request($admin,'/admin/iletisim-ayarlari/kaydet',$data);$ok($code===302,'Native settings save');
    $stored=$db->fetch('SELECT setting_value FROM settings WHERE setting_key=?',['contact_map_embed'])['setting_value'];$ok($stored===$url,'Only normalized Google src persisted');
    [,$html]=$request($guest,'/iletisim');$ok(str_contains($html,'data-contact-map')&&str_contains($html,'contact-map.js'),'Map component and scoped behavior');
    $ok(!str_contains($html,'onload="alert(1)"'),'Iframe event never rendered');
    $ok(!preg_match('~<iframe[^>]+\ssrc=~i',$html),'No remote map loaded before explicit action');
    $ok(str_contains($html,'Sentetik QA adresi')&&str_contains($html,'contact-qa@example.test'),'Shared address/email parity');
    $ok(substr_count($html,'<h1 ')===1,'Single H1');
    $ok(str_contains($html,'name="privacy_consent"')&&str_contains($html,'name="website_url"'),'Contact protections preserved');
    $request($admin,'/admin/iletisim-ayarlari/kaydet',array_replace($data,['contact_map_embed'=>'https://evil.test/maps/embed?pb=x','site_email'=>'must-not-save@example.test']));
    $ok($db->fetch('SELECT setting_value FROM settings WHERE setting_key=?',['site_email'])['setting_value']==='contact-qa@example.test','Invalid map prevents all field writes');
    $request($admin,'/admin/site-ayarlari/kaydet',['_csrf_token'=>$csrf,'contact_map_embed'=>'javascript:alert(1)']);
    $ok($db->fetch('SELECT setting_value FROM settings WHERE setting_key=?',['contact_map_embed'])['setting_value']===$url,'Generic settings cannot bypass validation');
    $request($admin,'/admin/iletisim-ayarlari/kaydet',array_replace($data,['contact_map_enabled'=>'0']));
    [,$html]=$request($guest,'/iletisim');$ok(!str_contains($html,'data-contact-map'),'Disabled map entirely omitted');
    $request($admin,'/admin/iletisim-ayarlari/kaydet',array_replace($data,['contact_map_embed'=>'','contact_map_enabled'=>'0']));
    $ok($db->fetch('SELECT setting_value FROM settings WHERE setting_key=?',['contact_map_embed'])['setting_value']==='','Map can be cleared');
    $request($admin,'/admin/iletisim-ayarlari/kaydet',$data);
    [,$html]=$request($guest,'/iletisim');$formToken=$token($html);$email='contact-'.bin2hex(random_bytes(5)).'@example.test';
    $message=['_csrf_token'=>$formToken,'name'=>'Sentetik QA','email'=>$email,'subject'=>'Sentetik iletişim testi','message'=>'Yalnızca yerel iletişim formu doğrulaması.','privacy_consent'=>'1','website_url'=>''];
    [$code]=$request($guest,'/iletisim',array_diff_key($message,['_csrf_token'=>true]));$ok($code===403,'Contact form CSRF');
    $request($guest,'/iletisim',array_replace($message,['website_url'=>'bot']));
    $ok(!$db->fetch('SELECT id FROM contact_messages WHERE email=?',[$email]),'Honeypot blocks insert');
    $request($guest,'/iletisim',array_replace($message,['privacy_consent'=>'0']));
    $ok(!$db->fetch('SELECT id FROM contact_messages WHERE email=?',[$email]),'Consent required');
    $request($guest,'/iletisim',$message);$row=$db->fetch('SELECT id FROM contact_messages WHERE email=?',[$email]);if($row)$messages[]=(int)$row['id'];$ok((bool)$row,'Contact message persisted once');
    [,$html]=$request($guest,'/iletisim');$ok(str_contains($html,'Mesajınız kaydedildi.'),'Contact success feedback preserved');
    $request($guest,'/iletisim',$message);$ok($db->count('contact_messages','email=?',[$email])===1,'Contact rate limit');
    $passed=true;echo "PASS $n HTTP contact/admin/CSRF/embed/toggle/form/shared-settings checks".($keep?" (synthetic QA map retained for browser)":"")."\n";
}finally{
    foreach($messages as $id)$db->delete('contact_messages','id=?',[$id]);
    if(!$keep||!$passed)foreach($backup as $key=>$row){if($row)$db->update('settings',['setting_value'=>$row['setting_value'],'setting_group'=>$row['setting_group']],'setting_key=?',[$key]);else $db->delete('settings','setting_key=?',[$key]);}
    curl_close($guest);curl_close($admin);
}
