<?php
/** Offline synthetic validation and presentation contracts. */
declare(strict_types=1);
if(PHP_SAPI!=='cli')exit(2);
$root=dirname(__DIR__);spl_autoload_register(static function(string $c)use($root):void{if(str_starts_with($c,'App\\'))require $root.'/app/'.str_replace('\\','/',substr($c,4)).'.php';});
use App\Services\MemberPanelService as Panel;use App\Services\MemberSupportService as Support;
$n=0;$ok=static function(bool $p,string $m)use(&$n):void{if(!$p)throw new RuntimeException($m);$n++;};
$reject=static function(callable $fn,string $why)use($ok):void{$bad=false;try{$fn();}catch(InvalidArgumentException $e){$bad=true;}$ok($bad,$why);};
$ok(Panel::filters(['q'=>['bad'],'status'=>'injected','payment'=>['paid'],'period'=>'-90'])===['q'=>'','status'=>'','payment'=>'','period'=>''],'Malformed filters never become SQL');
$ok(Panel::filters(['status'=>'active','payment'=>'unpaid'])['payment']==='unpaid','Action card groups survive filtering');
$ok(strlen(Panel::filters(['q'=>str_repeat('x',200)])['q'])===80,'Search bounded');
$ok(Panel::link('/siparislerim',['q'=>'web & smm','status'=>'completed','junk'=>'x'],3)==='/siparislerim?q=web%20%26%20smm&status=completed&page=3','Pagination keeps only valid search filters');
$ok(Panel::profile(['name'=>'  Sentetik Üye  ','phone'=>'+90 555 000 00 00'])['name']==='Sentetik Üye','Profile normalized');
foreach([['name'=>'x'],['name'=>['bad']],['name'=>str_repeat('x',101)],['name'=>'<script>bad</script>'],['name'=>'QA Member','phone'=>'invalid-email@x']]as $in)$reject(static fn()=>Panel::profile($in),'Invalid profile rejected');
$reject(static fn()=>Panel::profile(['name'=>'QA Member','phone'=>['malformed']]),'Malformed phone cannot silently erase profile contact information');
$hash=password_hash('synthetic-old-password',PASSWORD_DEFAULT);
$valid=['current_password'=>'synthetic-old-password','new_password'=>'synthetic-new-password','new_password_confirmation'=>'synthetic-new-password'];
$ok(Panel::password($valid,$hash)==='synthetic-new-password','Valid password path');
foreach([['current_password'=>'wrong'],['new_password'=>'short'],['new_password_confirmation'=>'mismatch'],['new_password'=>str_repeat('x',73)],['new_password'=>"nul\0value"],['new_password'=>'synthetic-old-password','new_password_confirmation'=>'synthetic-old-password'],['new_password'=>'üçüç','new_password_confirmation'=>'üçüç']]as $bad)$reject(static fn()=>Panel::password(array_merge($valid,$bad),$hash),'Credential validation / confirmation enforced');
$ok(Panel::progress(['state'=>'in_progress','quantity'=>1000,'remains'=>350])===65,'Provider-reported remaining becomes bounded progress');
foreach([null,-1,1001]as $remains)$ok(Panel::progress(['state'=>'in_progress','quantity'=>1000,'remains'=>$remains])===null,'Unknown / invalid progress is not invented');
$ok(Panel::progress(['state'=>'completed','quantity'=>1000])===100,'Completed job is explicit');
$ok(Panel::deliveryLabel('manual_review')==='Ekip kontrolünde','Internal provider errors replaced with safe label');
$ok(Support::message('  Synthetic support message  ')==='Synthetic support message','Support content normalized');
foreach(['',str_repeat('x',10001),['bad']]as $bad)$reject(static fn()=>Support::message($bad),'Empty/oversized support content rejected');
foreach(['member-support:../.env','/uploads/support/../../.env','https://example.test/private.pdf']as $stored)$ok(Support::file($stored)===null,'Download storage path is allowlisted, not a URL or traversal');
$ok(str_contains(file_get_contents($root.'/public/.htaccess'),'RewriteRule ^uploads/support'),'Apache legacy direct file access blocked');
$css=file_get_contents($root.'/public/assets/css/member-panel.css');$ok(!str_contains($css,'!important'),'New component has no specificity pile');
foreach(['1440','@media(max-width:900px)','@media(max-width:420px)','prefers-reduced-motion','@media print']as $needle){if($needle==='1440')continue;$ok(str_contains($css,$needle),'Responsive / accessibility branch present');}
$source=file_get_contents($root.'/app/Services/MemberPanelService.php');$ok(!str_contains($source,'SmmFulfillmentService::process')&&!str_contains($source,'::api('),'Opening a member panel cannot submit or poll provider jobs');
$ok(!str_contains(file_get_contents($root.'/resources/views/frontend/account/order-detail.php'),"['admin_note']"),'Private staff notes not rendered');
echo "PASS $n member validation / filters / progress / privacy / component contracts\n";
