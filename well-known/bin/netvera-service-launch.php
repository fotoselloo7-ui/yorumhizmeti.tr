<?php
/**
 * NetVera service catalog audit and optional safe SEO normalization.
 * Usage:
 *   php bin/netvera-service-launch.php
 *   php bin/netvera-service-launch.php --apply-seo --ack=SERVICE-SEO-REVIEWED
 *
 * Never changes price, slug, category, social provider mapping, imported
 * ready software, customer, checkout or order. Only blank/former-brand SEO
 * service fields can change and only after an explicit command.
 */
declare(strict_types=1);
if(PHP_SAPI!=='cli'){http_response_code(403);exit;}
define('BASE_PATH',dirname(__DIR__));
define('PUBLIC_PATH',BASE_PATH.'/public');
$env=BASE_PATH.'/.env';
if(is_file($env)){
    foreach(file($env,FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES) as $line){
        $line=trim($line);
        if($line===''||$line[0]==='#'||!str_contains($line,'='))continue;
        [$key,$value]=explode('=',$line,2);
        $_ENV[trim($key)]=trim($value," \t\n\r\0\x0B\"'");
    }
}
spl_autoload_register(static function(string $class):void{
    if(str_starts_with($class,'App\\')){
        $file=BASE_PATH.'/app/'.str_replace('\\','/',substr($class,4)).'.php';
        if(is_file($file))require $file;
    }
});
require_once BASE_PATH.'/app/Core/Helpers.php';

use App\Core\Database;
use App\Services\PackageSeoLaunchService as PackageSEO;
use App\Services\ServiceQuotePolicy as Quote;
$db=Database::getInstance();
$pdo=$db->getPdo();
$rows=$db->fetchAll('SELECT p.*,c.name AS category_name,c.slug AS category_slug,
 parent.name AS parent_category_name,parent.slug AS parent_category_slug
 FROM packages p INNER JOIN categories c ON c.id=p.category_id
 LEFT JOIN categories parent ON parent.id=c.parent_id ORDER BY p.id');
$changes=[];$protected=[];$quoteCount=0;$socialCount=0;
foreach($rows as $row){
    if(!PackageSEO::eligible($row)){
        $protected[]=['id'=>(int)$row['id'],'slug'=>$row['slug'],'reason'=>'Software or protected imported catalog'];
        continue;
    }
    $quote=Quote::package($row);
    if($quote)$quoteCount++;else $socialCount++;
    $fields=PackageSEO::proposedUpdates($row,$quote);
    if(!$fields)continue;
    $changes[]=['id'=>(int)$row['id'],'slug'=>$row['slug'],'old'=>array_intersect_key($row,$fields),'proposed'=>$fields];
}
$report=[
 'at'=>gmdate('c'),
 'mode'=>in_array('--apply-seo',$argv,true)?'apply-seo':'audit-only',
 'total_packages'=>count($rows),
 'quote_service_packages'=>$quoteCount,
 'fixed_social_packages'=>$socialCount,
 'protected_software'=>count($protected),
 'seo_rows_needing_work'=>count($changes),
 'changes'=>$changes,
 'protected'=>$protected,
];
$dir=BASE_PATH.'/storage/backups';
if(!is_dir($dir)&&!mkdir($dir,0700,true)&&!is_dir($dir))throw new RuntimeException('Cannot create private backup directory.');
$dest=$dir.'/netvera-service-seo-'.gmdate('Ymd-His').'-'.getmypid().'.json';
if(file_put_contents($dest,json_encode($report,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_THROW_ON_ERROR),LOCK_EX)===false)throw new RuntimeException('Backup could not be written; aborting.');
@chmod($dest,0600);
fwrite(STDOUT,"Catalog: ".count($rows)." | Quote services: $quoteCount | Social fixed price: $socialCount | Protected: ".count($protected)." | SEO changes: ".count($changes)."\n");
fwrite(STDOUT,"Audit and original fields saved: $dest\n");
if(!in_array('--apply-seo',$argv,true)){
    fwrite(STDOUT,"DRY RUN: no SQL updated. For reviewed application run --apply-seo --ack=SERVICE-SEO-REVIEWED\n");
    exit(0);
}
if(!in_array('--ack=SERVICE-SEO-REVIEWED',$argv,true))throw new RuntimeException('Explicit acknowledgment required to apply SEO changes.');
$applied=0;
try{
    $pdo->beginTransaction();
    foreach($changes as $change){
        // Compare current field values with audit originals under row lock
        // so a simultaneous admin edit never gets overwritten.
        $current=$db->fetch('SELECT * FROM packages WHERE id=? FOR UPDATE',[$change['id']]);
        if(!$current)continue;
        $safe=[];
        foreach($change['proposed'] as $field=>$value){
            if((string)($current[$field]??'')===(string)($change['old'][$field]??'')){
                $safe[$field]=$value;
            }
        }
        if($safe){
            $db->update('packages',$safe,'id=?',[$change['id']]);$applied++;
        }
    }
    $pdo->commit();
}catch(Throwable $e){
    if($pdo->inTransaction())$pdo->rollBack();
    throw $e;
}
fwrite(STDOUT,"Applied safe service SEO updates to $applied packages. Prices, product URLs, ready software and order history untouched.\n");
