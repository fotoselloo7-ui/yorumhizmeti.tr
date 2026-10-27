<?php
/**
 * Publish existing NetVera market alternatives in the public catalogue.
 *
 * php bin/netvera-market-publish.php
 * php bin/netvera-market-publish.php --apply --ack=MARKET-PUBLIC-CATALOG
 *
 * Public visibility is separate from accepting payment:
 * every unverified market proposal uses an enquiry-only CTA. This tool
 * only touches the 278 marker-owned products created by expand.php.
 * No pricing, provider links, ready software, customer records or blog writes.
 */
declare(strict_types=1);
if(PHP_SAPI!=='cli'){http_response_code(403);exit(1);}
define('BASE_PATH',dirname(__DIR__));
define('PUBLIC_PATH',BASE_PATH.'/public');
foreach(@file(BASE_PATH.'/.env',FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES)?:[] as $line){
    $line=trim($line);
    if($line===''||$line[0]==='#'||!str_contains($line,'='))continue;
    [$k,$v]=explode('=',$line,2);
    $_ENV[trim($k)]=trim($v," \t\r\n\0\x0B\"'");
}
spl_autoload_register(static function(string $class):void{
    if(!str_starts_with($class,'App\\'))return;
    $file=BASE_PATH.'/app/'.str_replace('\\','/',substr($class,4)).'.php';
    if(is_file($file))require $file;
});
require_once BASE_PATH.'/app/Core/Helpers.php';
$apply=in_array('--apply',$argv,true);
if($apply&&!in_array('--ack=MARKET-PUBLIC-CATALOG',$argv,true))
    throw new RuntimeException('Use explicit --ack=MARKET-PUBLIC-CATALOG');
$db=\App\Core\Database::getInstance();$pdo=$db->getPdo();
$rows=$db->fetchAll(
    "SELECT p.id,p.slug,p.status,p.category_id,p.price,p.discount_price,
            c.name category_name,c.slug category_slug,
            s.setting_key,s.setting_value
     FROM settings s
     JOIN packages p ON s.setting_key=CONCAT('netvera_market_proposal_',p.id)
     JOIN categories c ON c.id=p.category_id AND c.status='active'
     WHERE s.setting_key LIKE 'netvera_market_proposal_%'
     ORDER BY p.id"
);
$planned=[];$already=0;$invalid=[];
foreach($rows as $row){
    $record=json_decode((string)$row['setting_value'],true);
    $id=(int)$row['id'];$slug=(string)$row['slug'];
    if(!is_array($record)||!in_array($record['kind']??'', ['social','professional'],true)
       ||!str_starts_with($slug,'nv-') || (float)$row['price']<=0){
        $invalid[]=$slug;continue;
    }
    if($row['status']==='active'){$already++;continue;}
    if($row['status']!=='inactive'){$invalid[]=$slug;continue;}
    // No product becomes orderable just because it becomes visible.
    $planned[]=['id'=>$id,'slug'=>$slug,'status'=>$row['status'],
        'kind'=>$record['kind'],'price'=>(float)$row['price'],
        'setting_key'=>$row['setting_key'],
        'price_mode_before'=>\App\Services\ServicePriceMode::forPackage($row)
    ];
}
if($invalid)throw new RuntimeException('Some product markers failed validation: '.implode(', ',array_slice($invalid,0,10)));
$backupDir=BASE_PATH.'/storage/backups';
if(!is_dir($backupDir)&&!mkdir($backupDir,0700,true)&&!is_dir($backupDir))
    throw new RuntimeException('Cannot create private audit folder');
$backupFile=$backupDir.'/market-publication-'.gmdate('Ymd-His').'-'.getmypid().'.json';
file_put_contents($backupFile,json_encode([
    'date'=>gmdate('c'),'mode'=>$apply?'apply':'dry-run','already_public'=>$already,
    'publish_count'=>count($planned),'records'=>$planned
],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_THROW_ON_ERROR),LOCK_EX);
@chmod($backupFile,0600);
echo 'Eligible: '.count($planned).' | already public: '.$already.' | invalid: '.count($invalid).PHP_EOL;
echo 'Private audit: '.$backupFile.PHP_EOL;
if(!$apply){echo "DRY RUN: nothing changed. Products become public but enquiry-only. \n";exit(0);}
$changed=0;
try{
    $pdo->beginTransaction();
    $update=$pdo->prepare("UPDATE packages SET status='active'
        WHERE id=? AND slug=? AND status='inactive'");
    $mode=$pdo->prepare("INSERT INTO settings(setting_key,setting_value,setting_group)
        VALUES (?,?,?) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)");
    foreach($planned as $item){
        // Mark proposal products for manual quoting until supplier/delivery and
        // equivalent quality/cost conditions have been verified.
        $mode->execute(['package_price_mode_'.$item['id'],'quote','packages']);
        $update->execute([$item['id'],$item['slug']]);
        $changed+=$update->rowCount();
    }
    $pdo->commit();
}catch(Throwable $e){
    if($pdo->inTransaction())$pdo->rollBack();
    throw $e;
}

echo "Published $changed market alternatives in storefront as ENQUIRY ONLY. Existing products unchanged.\n";
// Persist canonical category/offer SEO defaults, but preserve all editor-owned
// fields, original ready software and blog routes. Category snapshot is
// separate from the individual package backup created by the SEO service.
$categories=$db->fetchAll("SELECT c.* FROM categories c ORDER BY c.id");
$seoBefore=[];
foreach($categories as $category){
    if(\App\Services\CategorySeoCompleteService::recommendations($category)===[])continue;
    $seoBefore[]=['category'=>$category,'extra'=>\App\Services\NetveraSeoBridge::get('category',(int)$category['id'])];
}
$seoBackup=$backupDir.'/market-category-seo-before-'.gmdate('Ymd-His').'-'.getmypid().'.json';
if(file_put_contents($seoBackup,json_encode($seoBefore,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_THROW_ON_ERROR),LOCK_EX)===false)
    throw new RuntimeException('Category SEO backup failed; SEO not applied.');
@chmod($seoBackup,0600);
$categoryReport=\App\Services\CategorySeoCompleteService::apply();
$packageReport=\App\Services\ServicePackageSeoCompleteService::apply();
echo 'Category SEO normalized: '.json_encode($categoryReport,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR).PHP_EOL;
echo 'Package SEO normalized: '.json_encode($packageReport,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR).PHP_EOL;
echo "Admin-authored copy, ready software, blogs and existing customer records preserved.\n";

