<?php
/**
 * Synthetic DB proof that original account can log in and approved NetVera
 * review/stars show exactly once on the proper imported software product.
 */
declare(strict_types=1);
if(PHP_SAPI!=='cli')exit(1);
define('BASE_PATH',dirname(__DIR__));
foreach(@file(BASE_PATH.'/.env',FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES)?:[] as $line){
    $line=trim($line);
    if(!$line||$line[0]==='#'||!str_contains($line,'='))continue;
    [$key,$value]=explode('=',$line,2);
    $_ENV[trim($key)]=trim($value," \t\r\n\"'");
}
spl_autoload_register(static function(string $class):void{
    if(!str_starts_with($class,'App\\'))return;
    $file=BASE_PATH.'/app/'.str_replace('\\','/',substr($class,4)).'.php';
    if(is_file($file))require_once $file;
});
require_once BASE_PATH.'/app/Core/Helpers.php';
$assert=static function(bool $valid,string $message):void{
    if(!$valid){fwrite(STDERR,'FAIL: '.$message.PHP_EOL);exit(1);}
};
$db=\App\Core\Database::getInstance();
$user=$db->fetch('SELECT u.id,u.password,m.old_customer_id FROM users u
    JOIN nv_private_user_map m ON m.new_user_id=u.id
    WHERE u.email=?',['qa-buyer@example.test']);
$assert($user!==null && (int)$user['old_customer_id']===10,'Original customer identity was not mapped');
$assert(password_verify('synthetic-only-qa-password',(string)$user['password']),
    'Legacy customer password must still pass the native password_verify login');
$all=$db->fetchAll('SELECT old_review_id,new_user_id,target_type,target_legacy_id,
                    product_legacy_id,rating,status FROM nv_private_content_reviews ORDER BY old_review_id');
$assert(count($all)===2,'Source reviews were lost or duplicated');
$assert((int)$all[0]['new_user_id']===(int)$user['id']
    && (int)$all[0]['product_legacy_id']===11
    && $all[0]['status']==='approved','Review ownership or product was changed');
$assert($all[1]['status']==='pending','Review moderation status must be preserved');
$reviews=\App\Services\NetveraBridgeService::reviews(11);
$assert(count($reviews)===1 && (int)$reviews[0]['rating']===5
    && str_contains((string)$reviews[0]['comment'],'Verified synthetic'),
    'Approved review must be visible exactly once, pending must not be shown');
$products=\App\Services\NetveraBridgeService::all();
$chosen=null;
foreach($products as $product){
    if((int)$product['legacy_id']===11){$chosen=$product;break;}
}
$assert($chosen!==null
    && (int)$chosen['review_count']===1
    && (float)$chosen['average_rating']===5.0,
    'Category product star count must reflect only mapped approved reviews');
echo "PASS: original password hash authenticates; customer/product review mapping, moderation and star count are correct and idempotent.\n";
