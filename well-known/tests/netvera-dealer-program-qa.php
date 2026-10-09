<?php
/**
 * Runs only against isolated synthetic CI fixtures. No source or production data.
 */
declare(strict_types=1);
if (PHP_SAPI !== 'cli' || getenv('NETVERA_DEALER_QA_ONLY') !== '1') exit(2);
define('BASE_PATH',dirname(__DIR__));
foreach(file(BASE_PATH.'/.env',FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES) as $line){
    $line=trim($line);
    if($line===''||$line[0]==='#'||!str_contains($line,'='))continue;
    [$key,$value]=explode('=',$line,2);
    $_ENV[trim($key)]=trim($value," \t\r\n\"'");
}
if(($_ENV['APP_ENV']??'')!=='staging'||($_ENV['DB_NAME']??'')!=='netvera_private_target_qa')
    throw new RuntimeException('Refusing non-synthetic dealer QA database');
spl_autoload_register(static function(string $class){
    if(!str_starts_with($class,'App\\'))return;
    $path=BASE_PATH.'/app/'.str_replace('\\','/',substr($class,4)).'.php';
    if(is_file($path))require $path;
});
session_id('netvera-dealer-synthetic-fixture');session_start();
use App\Services\DealerProgramService as Dealer;
use App\Services\LegacyCustomerService as History;
use App\Core\Database;
$db=Database::getInstance();
$user=$db->fetch('SELECT id FROM users WHERE email=?',['qa-buyer@example.test']);
$admin=$db->fetch('SELECT id FROM admins WHERE email=?',['qa-admin@example.test']);
if(!$user||!$admin||!Dealer::ready())throw new RuntimeException('Synthetic QA accounts or schema missing');
$id=(int)$user['id'];
$prior=Dealer::account($id);
if(!$prior||$prior['status']!=='pending'||(float)$prior['commission_rate']!==0.0)
    throw new RuntimeException('Old approved partner was not safely staged for review');
$history=History::overview($id);
if(count($history['software'])!==1||(string)$history['software'][0]['item_name']!=='QA Licensed Script')
    throw new RuntimeException('Only verified paid scripts may be displayed as owned');
if(count($history['orders'])!==2)throw new RuntimeException('Historical customer orders disappeared');
if(Dealer::track($prior['referral_code']))throw new RuntimeException('Pending dealer referral must not count visits');
$already=Dealer::apply($id);
if(!str_contains($already,'bulunmaktadır')&&!str_contains($already,'değerlendirmede'))
    throw new RuntimeException('Repeat application must not create a second record');
$invalid=false;
try{Dealer::review((int)$prior['id'],(int)$admin['id'],'approved','pro',31);}catch(RuntimeException $e){$invalid=true;}
if(!$invalid)throw new RuntimeException('Invalid commission tier must be rejected');
Dealer::review((int)$prior['id'],(int)$admin['id'],'approved','pro',7.5);
$approved=Dealer::account($id);
if($approved['status']!=='approved'||(float)$approved['commission_rate']!==7.5)
    throw new RuntimeException('Admin-approved tier and rate not saved');
if(!Dealer::track($prior['referral_code'])||!Dealer::track($prior['referral_code']))
    throw new RuntimeException('Approved dealer link failed');
if(Dealer::referrals((int)$prior['id'])!==1)
    throw new RuntimeException('Daily visitor deduplication broken');
Dealer::review((int)$prior['id'],(int)$admin['id'],'suspended','pro',7.5);
if(Dealer::track($prior['referral_code']))throw new RuntimeException('Suspended dealer must not record clicks');
if((float)Dealer::account($id)['commission_rate']!==0.0)
    throw new RuntimeException('Suspension must freeze commission rate');
$counts=$db->fetch('SELECT (SELECT COUNT(*) FROM nv_dealer_accounts) AS accounts,
    (SELECT COUNT(*) FROM nv_dealer_referral_events) AS visits,
    (SELECT COUNT(*) FROM nv_private_affiliate_commissions) AS legacy_commissions');
if((int)$counts['accounts']!==1||(int)$counts['visits']!==1||(int)$counts['legacy_commissions']!==1)
    throw new RuntimeException('Partner counts changed unexpectedly');
echo "PASS dealer application dedup, private purchased software ownership, approval, tier/rate, suspended access, anonymous visits and legacy commission preservation\n";
