<?php
/** Isolated localhost + canonical member QA DB only. No production credentials/payment/email. */
declare(strict_types=1);
if(PHP_SAPI!=='cli')exit(2);
$root=dirname(__DIR__);$config=parse_ini_file($root.'/.env');
$db=new PDO('mysql:host='.$config['DB_HOST'].';dbname=netvera_member_qa_20261018;charset=utf8mb4',$config['DB_USER'],$config['DB_PASS']??'',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
$n=0;$ok=static function(bool $v,string $m)use(&$n):void{if(!$v)throw new RuntimeException($m);$n++;};
$scalar=static function(string $sql,array $args=[])use($db){$s=$db->prepare($sql);$s->execute($args);return $s->fetchColumn();};
$owner=(int)$scalar("SELECT id FROM users WHERE email='member-owner@example.test'");$other=(int)$scalar("SELECT id FROM users WHERE email='member-other@example.test'");
$ok($owner>0&&$other>0,'Dedicated member fixtures required');
$ok(!(int)$scalar('SELECT COUNT(*) FROM payment_gateways WHERE is_active=1'),'QA payment gateways disabled');
$ok(!(int)$scalar("SELECT COUNT(*) FROM settings WHERE setting_key='smtp_host' AND setting_value!=''"),'QA SMTP disabled');
$order=(int)$scalar("SELECT id FROM orders WHERE order_number='MEMBER-QA-001'");$foreign=(int)$scalar("SELECT id FROM orders WHERE order_number='MEMBER-FOREIGN-001'");
$ticket=(int)$scalar("SELECT id FROM support_tickets WHERE ticket_number='MEMBER-TICKET-001'");
$client=static function(){ $h=curl_init();curl_setopt_array($h,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_FOLLOWLOCATION=>false,CURLOPT_TIMEOUT=>15,CURLOPT_COOKIEFILE=>'']);return $h;};
$request=static function($h,string $path,?array $post=null,bool $upload=false):array{
 if(!str_starts_with($path,'/')||str_starts_with($path,'//'))throw new RuntimeException('Only local QA paths permitted');
 $headers=[];curl_setopt_array($h,[CURLOPT_URL=>'http://127.0.0.1:8019'.$path,CURLOPT_POST=>$post!==null,CURLOPT_HEADERFUNCTION=>static function($h,$line)use(&$headers){$headers[]=trim($line);return strlen($line);}]);
 if($post!==null)curl_setopt($h,CURLOPT_POSTFIELDS,$upload?$post:http_build_query($post));
 $body=curl_exec($h);if($body===false)throw new RuntimeException('Local member QA request failed');return [(int)curl_getinfo($h,CURLINFO_HTTP_CODE),(string)$body,$headers];
};
$token=static function(string $html):string{preg_match('/name="_csrf_token" value="([^"]+)"/',$html,$m);return $m[1]??'';};
$login=static function($h,string $email)use($request,$token,$ok):void{[, $html]=$request($h,'/giris');[$code]=$request($h,'/giris',['_csrf_token'=>$token($html),'email'=>$email,'password'=>'synthetic-member-qa-only']);$ok($code===302,'Synthetic member login');};
$guest=$client();
foreach(['/hesabim','/hesabim/profil','/hesabim/guvenlik','/hesabim/yazilimlar','/hesabim/odemeler','/hesabim/hareketler','/siparislerim','/siparis/'.$order,'/destek','/destek/yeni','/destek/dosya/1']as $path){[$code,,$headers]=$request($guest,$path);$ok($code===302&&(bool)array_filter($headers,static fn($h)=>$h==='Location: /giris'),'Guest cannot access member route: '.$path);}
curl_close($guest);
$member=$client();$second=$client();$admin=$client();$login($member,'member-owner@example.test');$login($second,'member-other@example.test');
$hash=$scalar('SELECT password FROM users WHERE id=?',[$owner]);$priorName=$scalar('SELECT name FROM users WHERE id=?',[$owner]);$priorPhone=$scalar('SELECT phone FROM users WHERE id=?',[$owner]);
try{
 foreach(['/hesabim','/hesabim/profil','/hesabim/guvenlik','/hesabim/yazilimlar','/hesabim/odemeler','/hesabim/hareketler','/siparislerim','/bayilik','/siparis/'.$order,'/destek','/destek/yeni','/destek/'.$ticket]as $path){
  [$code,$html,$headers]=$request($member,$path);$ok($code===200&&!str_contains($html,'Fatal error'),'Member route renders: '.$path);
  $ok(str_contains($html,'member-panel.css')&&str_contains($html,'member-panel.js'),'One new member component: '.$path);
  $ok((bool)array_filter($headers,static fn($h)=>stripos($h,'X-Robots-Tag: noindex')===0),'Private robots HTTP policy: '.$path);
  $ok((bool)array_filter($headers,static fn($h)=>stripos($h,'Cache-Control: private, no-store')===0),'Private pages are not publicly cached: '.$path);
  $ok(!str_contains($html,'MEMBER-FOREIGN-001')&&!str_contains($html,'SYNTHETIC-STAFF-NOTE')&&!str_contains($html,'SYNTHETIC-INTERNAL-LOG')&&!str_contains($html,'SYNTHETIC-PROVIDER-ERROR'),'No cross-member or provider/staff information: '.$path);
 }
 [$code,$html]=$request($member,'/siparis/'.$order);$ok(str_contains($html,'value="65"')&&str_contains($html,'350'),'Real persisted SMM remaining / 65% progress visible');
 $ok(str_contains($html,'/destek/yeni?order_id='.$order),'Order-bound support entry');
 [$code,$html]=$request($member,'/hesabim/yazilimlar');$ok(str_contains($html,'NetVera Sentetik Yazılım'),'Verified paid legacy software remains visible');
 foreach(['/siparis/'.$foreign,'/destek/999999999']as $path){[$code]=$request($member,$path);$ok($code===404,'Foreign/missing resource is 404');}
 [, $html]=$request($member,'/siparislerim?q=MEMBER-QA-001');$ok(str_contains($html,'1 sipariş bulundu')&&!str_contains($html,'#MEMBER-QA-002'),'Owner-only order number search');
 [, $html]=$request($member,'/siparislerim?q=takip%C3%A7i');$ok(str_contains($html,'1 sipariş bulundu'),'Package name search');
 [, $html]=$request($member,'/siparislerim?q=%25');$ok(str_contains($html,'0 sipariş bulundu'),'Literal percent does not act as a search wildcard');
 [, $html]=$request($member,'/siparislerim?status=active');$ok(str_contains($html,'Devam eden siparişler')&&!str_contains($html,'#MEMBER-QA-002'),'Active card matches active filter');
 [, $html]=$request($member,'/siparislerim?payment=unpaid');$ok(!str_contains($html,'#MEMBER-QA-004'),'Unpaid action does not include paid orders');
 [, $html]=$request($member,'/siparislerim?status=completed&page=999999');$ok(!str_contains($html,'Fatal error')&&str_contains($html,'MEMBER-QA-'),'Out-of-range page clamps to valid results');
 [, $html]=$request($member,'/siparislerim?page=2');$ok(str_contains($html,'aria-current="page">2</a>'),'Order pagination is functional');
 [, $html]=$request($member,'/hesabim/profil');$csrf=$token($html);
 [$code]=$request($member,'/hesabim/guncelle',['name'=>'Wrong no CSRF']);$ok($code===403,'Profile CSRF enforced');
 $request($member,'/hesabim/guncelle',['_csrf_token'=>$csrf,'name'=>'<svg/onload=alert(1)>','phone'=>'bad']);$ok($scalar('SELECT name FROM users WHERE id=?',[$owner])===$priorName,'Invalid profile does not persist');
 $request($member,'/hesabim/guncelle',['_csrf_token'=>$csrf,'name'=>'Elif QA Yılmaz','phone'=>'+90 555 000 00 00','email'=>'attacker@example.test','status'=>'banned']);
 $ok($scalar('SELECT name FROM users WHERE id=?',[$owner])==='Elif QA Yılmaz'&&$scalar('SELECT email FROM users WHERE id=?',[$owner])==='member-owner@example.test','Allowed profile fields save, email/status tampering ignored');
 [, $html]=$request($member,'/hesabim/profil');$ok(str_contains($html,'Elif QA Yılmaz'),'Profile and navigation use updated persisted name');
 $request($member,'/hesabim/sifre-degistir',['_csrf_token'=>$csrf,'current_password'=>'synthetic-member-qa-only','new_password'=>'synthetic-member-updated','new_password_confirmation'=>'mismatch']);
 $ok($scalar('SELECT password FROM users WHERE id=?',[$owner])===$hash,'Server rejects password confirmation mismatch');
 $request($member,'/hesabim/sifre-degistir',['_csrf_token'=>$csrf,'current_password'=>'synthetic-member-qa-only','new_password'=>'synthetic-member-updated','new_password_confirmation'=>'synthetic-member-updated']);
 $ok(password_verify('synthetic-member-updated',(string)$scalar('SELECT password FROM users WHERE id=?',[$owner])),'Valid password update uses password_hash');
 $before=(int)$scalar('SELECT COUNT(*) FROM support_tickets WHERE user_id=?',[$owner]);
 $request($member,'/destek/olustur',['_csrf_token'=>$csrf,'order_id'=>$foreign,'subject'=>'Synthetic unauthorized link','priority'=>'medium','message'=>'Synthetic message']);
 $ok((int)$scalar('SELECT COUNT(*) FROM support_tickets WHERE user_id=?',[$owner])===$before,'Foreign order cannot be bound to a support ticket');
 [, $html]=$request($member,'/destek/yeni?order_id='.$order);$ok(str_contains($html,'value="'.$order.'" selected'),'Order-bound support preselects only owned order');
 $request($member,'/destek/olustur',['_csrf_token'=>$csrf,'order_id'=>$order,'subject'=>'x','priority'=>'urgent','message'=>'']);
 $ok((int)$scalar('SELECT COUNT(*) FROM support_tickets WHERE user_id=?',[$owner])===$before,'Invalid ticket creates no orphan row');
 [$code,,$headers]=$request($member,'/destek/olustur',['_csrf_token'=>$csrf,'order_id'=>$order,'subject'=>'Synthetic secure attachment '.bin2hex(random_bytes(2)),'priority'=>'medium','message'=>'<script>SYNTHETIC-XSS</script>','attachment'=>new CURLFile($root.'/tests/fixtures/member-support-synthetic.pdf','application/pdf','example.pdf')],true);
 $location='';foreach($headers as $header)if(str_starts_with($header,'Location: '))$location=substr($header,10);
 $ok($code===302&&preg_match('~^/destek/\d+$~',$location)===1,'Atomic ticket/message with a private attachment');$created=(int)basename($location);
 $messageId=(int)$scalar('SELECT id FROM support_messages WHERE ticket_id=? ORDER BY id DESC LIMIT 1',[$created]);
 $stored=(string)$scalar('SELECT attachment FROM support_messages WHERE id=?',[$messageId]);$ok(str_starts_with($stored,'member-support:')&&!is_file($root.'/public/uploads/support/'.substr($stored,15)),'New support attachment lives outside public uploads');
 [, $html]=$request($member,$location);$ok(str_contains($html,'&lt;script&gt;SYNTHETIC-XSS&lt;/script&gt;')&&!str_contains($html,'<script>SYNTHETIC-XSS'),'Support message output escaped');
 [$code,$file,$headers]=$request($member,'/destek/dosya/'.$messageId);$ok($code===200&&str_contains($file,'NETVERA-SYNTHETIC-MEMBER-ATTACHMENT'),'Owner can download private attachment');
 $ok((bool)array_filter($headers,static fn($h)=>stripos($h,'Cache-Control: private, no-store')===0),'Private download never publicly cached');
 [$code]=$request($second,'/destek/dosya/'.$messageId);$ok($code===404,'Another member cannot download attachment');
 [$code]=$request($second,$location);$ok($code===404,'Another member cannot read conversation');
 $messages=(int)$scalar('SELECT COUNT(*) FROM support_messages WHERE ticket_id=?',[$created]);
 $request($second,$location.'/yanit',['_csrf_token'=>$token($request($second,'/destek/yeni')[1]),'message'=>'Cross member injection']);
 $ok((int)$scalar('SELECT COUNT(*) FROM support_messages WHERE ticket_id=?',[$created])===$messages,'Cross-member reply rejected');
 $request($member,$location.'/kapat',['_csrf_token'=>$csrf]);$ok($scalar('SELECT status FROM support_tickets WHERE id=?',[$created])==='closed','Member may close own support request');
 $request($member,$location.'/yanit',['_csrf_token'=>$csrf,'message'=>'Closed ticket bypass attempt']);$ok((int)$scalar('SELECT COUNT(*) FROM support_messages WHERE ticket_id=?',[$created])===$messages,'Closed ticket reply blocked server-side');
 [, $html]=$request($admin,'/admin/giris');[$code]=$request($admin,'/admin/giris',['_csrf_token'=>$token($html),'email'=>'member-admin@example.test','password'=>'synthetic-member-qa-only']);$ok($code===302,'Synthetic admin login');
 [, $html]=$request($admin,'/admin/destek/'.$ticket);$adminCsrf=$token($html);$ok(str_contains($html,'value="answered"'),'Admin uses canonical answered enum');
 [$code]=$request($admin,'/admin/destek/'.$ticket.'/yanit',['_csrf_token'=>$adminCsrf,'message'=>'Sentetik admin yanıtı','status'=>'answered']);$ok($code===302&&$scalar('SELECT status FROM support_tickets WHERE id=?',[$ticket])==='answered','Admin reply commits a valid canonical status');
 [$code,$file]=$request($admin,'/destek/dosya/'.$messageId);$ok($code===200&&str_contains($file,'NETVERA-SYNTHETIC-MEMBER-ATTACHMENT'),'Active support admin can retrieve private attachment');
 [$code]=$request($member,'/uploads/support/example.pdf');$ok($code===403,'QA server mirrors the Apache direct legacy support file deny rule');
 [$code]=$request($member,'/storage/member-support/'.substr($stored,15));$ok($code===404,'New private attachment has no public document-root path');
 $db->prepare("UPDATE users SET status='inactive' WHERE id=?")->execute([$other]);
 [$code,,$headers]=$request($second,'/hesabim');$ok($code===302&&(bool)array_filter($headers,static fn($h)=>$h==='Location: /giris'),'Stale inactive member session cannot access protected pages');
}finally{
 $db->prepare('UPDATE users SET password=?,name=?,phone=? WHERE id=?')->execute([$hash,$priorName,$priorPhone,$owner]);
 $db->prepare("UPDATE users SET status='active' WHERE id=?")->execute([$other]);
 foreach([$member,$second,$admin]as $h)curl_close($h);
}
echo "PASS $n member HTTP / access isolation / search / profile / password / support / private attachment / admin parity contracts; synthetic QA only\n";
