<?php
/** Read-only local inventory. Never prints credentials or customer/order data. */
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
$config = parse_ini_file(dirname(__DIR__).'/.env');
$schema = in_array('--qa', $argv, true) ? 'netvera_ui_audit_20261013' : $config['DB_NAME'];
$pdo = new PDO('mysql:host='.$config['DB_HOST'].';dbname='.$schema.';charset=utf8mb4', $config['DB_USER'], $config['DB_PASS'] ?? '', [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
$columns = $pdo->query('SHOW COLUMNS FROM packages')->fetchAll();
$categories = $pdo->query("SELECT id,parent_id,name,slug FROM categories WHERE name LIKE '%Instagram%' OR slug='sosyal-medya-hizmetleri' ORDER BY parent_id,id")->fetchAll();
$packages = $pdo->query("SELECT p.id,p.category_id,p.name,p.slug,p.price,p.min_quantity,p.max_quantity,LEFT(p.short_description,170) AS summary FROM packages p JOIN categories c ON c.id=p.category_id LEFT JOIN categories parent ON parent.id=c.parent_id WHERE c.name LIKE '%Instagram%' OR parent.name LIKE '%Instagram%' ORDER BY p.category_id,p.sort_order,p.id LIMIT 25")->fetchAll();
$mappings = $pdo->query('SELECT package_id,fulfillment_quantity,enabled FROM smm_package_links ORDER BY package_id LIMIT 30')->fetchAll();
define('BASE_PATH',dirname(__DIR__));
define('PUBLIC_PATH',BASE_PATH.'/public');
$_ENV=$config; $_ENV['DB_NAME']=$schema;
spl_autoload_register(static function(string $class):void { if(str_starts_with($class,'App\\')) require_once BASE_PATH.'/app/'.str_replace('\\','/',substr($class,4)).'.php'; });
require_once BASE_PATH.'/app/Core/Helpers.php';
$unknown=[]; $social=0;
foreach($pdo->query('SELECT * FROM packages')->fetchAll() as $package){
    $info=\App\Services\SocialPackageService::info($package);
    if($info===null)continue;
    $social++;
    if(!$info['quantity']||!$info['unit'])$unknown[]=['id'=>$package['id'],'slug'=>$package['slug'],'summary'=>$package['short_description']];
}
echo json_encode(['target'=>in_array('--qa',$argv,true)?'isolated QA':'configured local','social_count'=>$social,'unknown'=>$unknown,'categories'=>$categories,'instagram_sample'=>array_slice($packages,0,4),'mapping_count'=>count($mappings)],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT).PHP_EOL;
