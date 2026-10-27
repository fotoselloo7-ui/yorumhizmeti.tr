<?php
/**
 * Usage: php bin/netvera-follower-market-audit.php
 *
 * 2026-10-27 retail peer benchmarks. DO NOT auto-apply prices.
 * Without a live SMM supplier in TRY and a verified cost, price suggestions
 * are market ceilings, not approved final sales prices.
 */
declare(strict_types=1);
if(PHP_SAPI!=='cli'){http_response_code(403);exit;}
define('BASE_PATH',dirname(__DIR__));define('PUBLIC_PATH',BASE_PATH.'/public');
$env=BASE_PATH.'/.env';
if(is_file($env))foreach(file($env,FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES)as $line){
    $line=trim($line);if($line===''||$line[0]==='#'||!str_contains($line,'='))continue;
    [$k,$v]=explode('=',$line,2);$_ENV[trim($k)]=trim($v," \t\n\r\0\x0B\"'");
}
spl_autoload_register(static function(string $class):void{
    if(!str_starts_with($class,'App\\'))return;
    $file=BASE_PATH.'/app/'.str_replace('\\','/',substr($class,4)).'.php';
    if(is_file($file))require $file;
});
require_once BASE_PATH.'/app/Core/Helpers.php';

use App\Core\Database;
use App\Services\SocialPackageService as Social;
use App\Services\SocialFollowerBenchmarkService as Bench;
use App\Services\SmmCatalogService as Supplier;

$db=Database::getInstance();
$rows=$db->fetchAll("SELECT p.*,c.name AS category_name,c.slug AS category_slug
 FROM packages p JOIN categories c ON c.id=p.category_id
 WHERE p.status='active' ORDER BY p.id");
$supplierCosts=[];
if(Supplier::installed()){
    $costs=$db->fetchAll("SELECT m.package_id,m.fulfillment_quantity,
         s.rate_per_1000,pv.currency,pv.is_active AS provider_active,s.is_available
         FROM smm_package_links m
         JOIN smm_services s ON s.id=m.service_id
         JOIN smm_providers pv ON pv.id=s.provider_id");
    foreach($costs as $c){
        // USD/EUR cannot be treated as TRY; user must set a verified FX cost.
        if($c['currency']!=='TRY'||!(int)$c['provider_active']||!(int)$c['is_available'])continue;
        $supplierCosts[(int)$c['package_id']] =
            (float)$c['rate_per_1000']*(int)$c['fulfillment_quantity']/1000;
    }
}
$audit=[];$count=0;
foreach($rows as $row){
    if(!Social::isSocialCategory((int)$row['category_id']))continue;
    $info=Social::info($row);
    if(!$info)continue;
    $candidate=Bench::recommend($row,$info,$supplierCosts[(int)$row['id']]??null);
    if(!$candidate['eligible'])continue;
    $audit[]=['id'=>(int)$row['id'],'slug'=>$row['slug'],'name'=>$row['name'],
             'category'=>$row['category_name'],'quantity'=>$info['quantity'],'detail'=>$candidate];
    $count++;
}
$dir=BASE_PATH.'/storage/backups';
if(!is_dir($dir)&&!mkdir($dir,0700,true)&&!is_dir($dir))throw new RuntimeException('Could not create private audit directory.');
$dest=$dir.'/social-follower-market-'.gmdate('Ymd-His').'.json';
file_put_contents($dest,json_encode(['date'=>Bench::REVIEW_DATE,'notice'=>'PROPOSAL ONLY. NEVER changes prices or API/provider mappings.','candidates'=>$audit],JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR),LOCK_EX);
@chmod($dest,0600);
fwrite(STDOUT,"Compared $count exact quantity and product-type matches. Report: $dest\n");
fwrite(STDOUT,"No prices, package names, payment systems or provider mappings changed.\n");
