<?php
/** Disposable fixed QA schema only. No external API requests or live payment. */
declare(strict_types=1);
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
$root=dirname(__DIR__);
$config=parse_ini_file($root.'/.env');
$schema='netvera_social_sales_qa_20261017';
$pdo=new PDO('mysql:host='.$config['DB_HOST'].';charset=utf8mb4',$config['DB_USER'],$config['DB_PASS']??'',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
if(in_array('--setup',$argv,true)){
 $check=$pdo->prepare('SELECT SCHEMA_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME=?');$check->execute([$schema]);
 if($check->fetchColumn())throw new RuntimeException('Disposable schema exists; refusing to overwrite. Run without --setup.');
 $pdo->exec('CREATE DATABASE '.$schema.' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
 $pdo->exec('USE '.$schema);
 $pdo->exec(file_get_contents($root.'/database/database.sql'));
 $pdo->exec('UPDATE payment_gateways SET is_active=0');
 $pdo->prepare('INSERT INTO users(name,email,password,status) VALUES(?,?,?,?)')->execute(['Sosyal Satış QA','social-sales@example.test',password_hash('synthetic-social-sales-only',PASSWORD_DEFAULT),'active']);
 echo "Created dedicated social-sales QA database from canonical database.sql; existing databases untouched.\n";
 exit;
}
define('BASE_PATH',$root);define('PUBLIC_PATH',$root.'/public');
$_ENV=$config;$_ENV['DB_NAME']=$schema;
$_ENV['SMM_ENCRYPTION_KEY']='base64:'.base64_encode(random_bytes(32));
spl_autoload_register(static function(string $class):void {if(str_starts_with($class,'App\\'))require BASE_PATH.'/app/'.str_replace('\\','/',substr($class,4)).'.php';});
require BASE_PATH.'/app/Core/Helpers.php';
$db=\App\Core\Database::getInstance();
$assert=static function(bool $ok,string $why):void {if(!$ok)throw new RuntimeException($why);};
$source=$db->fetch("SELECT p.* FROM packages p JOIN categories c ON c.id=p.category_id WHERE c.slug='instagram-takipci' ORDER BY p.sort_order,p.id LIMIT 1");
$assert($source!==null,'Canonical social package exists');
$info=\App\Services\SocialPackageService::info($source);
$assert($info['quantity']===100,'Canonical source package is 100 followers');
$assert(\App\Services\SocialPackageService::cartQuantity($source,999999)===1,'POST quantity cannot multiply social package');
$ad=$db->fetch("SELECT p.* FROM packages p JOIN categories c ON c.id=p.category_id WHERE c.slug='instagram-reklam' LIMIT 1");
$assert($ad!==null && \App\Services\SocialPackageService::info($ad)===null,'Instagram Ads remains marketing, not social sales');
$assert(\App\Services\SocialPackageService::cartQuantity($ad,3)===3,'Non-social quantity unchanged');
$user=$db->fetch("SELECT id FROM users WHERE email='social-sales@example.test'");
$source['quantity']=9999;
$source['fields']=[['field_key'=>'smm_target','field_label'=>'Profil','value'=>'@synthetic.qa'],['field_key'=>'nv_social_quantity','field_label'=>'Forged amount','value'=>'999999 takipçi']];
$order=(new \App\Services\OrderService())->createOrder((int)$user['id'],[$source],'bank_transfer','bank_transfer');
$item=$db->fetch('SELECT * FROM order_items WHERE order_id=?',[$order]);
$price=!empty($source['discount_price']) && $source['discount_price']<$source['price'] ? (float)$source['discount_price'] : (float)$source['price'];
$assert((int)$item['quantity']===1 && abs((float)$item['total']-$price)<0.001,'One fixed package charged once');
$snapshot=$db->fetch("SELECT field_value FROM order_fields WHERE order_item_id=? AND field_key='nv_social_quantity'",[$item['id']]);
$assert($snapshot['field_value']==='100 takipçi','Server-owned delivery snapshot ignores forged customer value');
\App\Services\SocialPackageService::save((int)$source['id'],['social_quantity'=>500,'social_unit'=>'takipçi','social_service'=>'Instagram Türk Takipçi QA']);
$assert(\App\Services\SocialPackageService::info($source)['quantity']===500,'Admin metadata stored/read through actual settings');
$old=$db->fetch("SELECT field_value FROM order_fields WHERE order_item_id=? AND field_key='nv_social_quantity'",[$item['id']]);
$assert($old['field_value']==='100 takipçi','Historical delivery snapshot immutable after admin change');
\App\Services\SocialPackageService::save((int)$source['id'],['social_quantity'=>100,'social_unit'=>'takipçi','social_service'=>'Instagram Türk Takipçi']);
// A new synthetic mapped package verifies the queue without ever dispatching it.
$suffix=bin2hex(random_bytes(5));
$provider=$db->insert('smm_providers',['name'=>'Synthetic social QA '.$suffix,'endpoint'=>'https://provider.example/api/v2','currency'=>'TRY','is_active'=>1,'api_key_enc'=>\App\Services\SmmApiClient::encrypt('synthetic-qa-no-real-provider')]);
$service=$db->insert('smm_services',['provider_id'=>$provider,'external_service_id'=>'qa-'.$suffix,'category'=>'QA','name'=>'Synthetic Instagram followers','service_type'=>'Default','rate_per_1000'=>1,'min_quantity'=>10,'max_quantity'=>10000,'is_available'=>1]);
$package=$db->insert('packages',['category_id'=>$source['category_id'],'name'=>'Instagram QA Takipçi','slug'=>'synthetic-social-'.$suffix,'short_description'=>'100 takipçi için sentetik QA hizmeti.','price'=>25,'min_quantity'=>1,'max_quantity'=>10,'status'=>'active']);
$db->insert('smm_package_links',['package_id'=>$package,'service_id'=>$service,'fulfillment_quantity'=>100,'field_key'=>'smm_link','enabled'=>1]);
$p=$db->fetch('SELECT * FROM packages WHERE id=?',[$package]);$p['quantity']=900;$p['fields']=[['field_key'=>'smm_link','field_label'=>'Profil','value'=>'https://instagram.com/synthetic.qa']];
$mappedOrder=(new \App\Services\OrderService())->createOrder((int)$user['id'],[$p],'bank_transfer','bank_transfer');
$job=$db->fetch('SELECT quantity,state FROM smm_order_jobs WHERE order_id=?',[$mappedOrder]);
$assert((int)$job['quantity']===100 && $job['state']==='queued','Mapped provider receives 100, never 1 or 90000');
$orderState=$db->fetch('SELECT payment_status FROM orders WHERE id=?',[$mappedOrder]);
$assert($orderState['payment_status']==='pending','No payment or provider dispatch performed');
echo "PASS: actual database price/quantity tamper defense, social-only scope, metadata persistence, immutable order snapshot, queued provider quantity=100; no external calls\n";
