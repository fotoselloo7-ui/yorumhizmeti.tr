<?php
/**
 * NetVera missing-only social catalogue restoration.
 *
 * php bin/netvera-social-catalog-restore.php                 (dry run)
 * php bin/netvera-social-catalog-restore.php --apply --ack=RESTORE-MISSING-SOCIAL-PACKAGES
 *
 * Reads audited public seed rows by slug; never replaces an existing
 * category/package or touches provider mappings, payments, blogs or software.
 * Always back up the current database before running --apply.
 */
declare(strict_types=1);
if(PHP_SAPI!=='cli'){http_response_code(403);exit(1);}
define('BASE_PATH',dirname(__DIR__));
foreach(@file(BASE_PATH.'/.env',FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES)?:[] as $line){
    $line=trim($line);
    if($line===''||$line[0]==='#'||!str_contains($line,'='))continue;
    [$key,$value]=explode('=',$line,2);
    $_ENV[trim($key)]=trim($value," \t\r\n\x00\x0B\"'");
}
spl_autoload_register(static function(string $class):void{
    if(!str_starts_with($class,'App\\'))return;
    $file=BASE_PATH.'/app/'.str_replace('\\','/',substr($class,4)).'.php';
    if(is_file($file))require $file;
});
require_once BASE_PATH.'/app/Core/Helpers.php';
$source=BASE_PATH.'/database/netvera-social-package-baseline.json';
$baseline=json_decode((string)file_get_contents($source),true,512,JSON_THROW_ON_ERROR);
if(($baseline['version']??0)!==1||count($baseline['categories']??[])!==60||
    count($baseline['packages']??[])!==185)throw new RuntimeException('Baseline validation failed; abort.');
$apply=in_array('--apply',$argv,true);
if($apply&&!in_array('--ack=RESTORE-MISSING-SOCIAL-PACKAGES',$argv,true)){
    throw new RuntimeException('Explicit --ack=RESTORE-MISSING-SOCIAL-PACKAGES required.');
}
$db=\App\Core\Database::getInstance();
$pdo=$db->getPdo();
$cats=$db->fetchAll('SELECT id,parent_id,name,slug,status FROM categories');
$pkgs=$db->fetchAll('SELECT id,category_id,slug,status FROM packages');
$bySlug=[];foreach($cats as $row)$bySlug[(string)$row['slug']]=$row;
$packageSlugs=[];foreach($pkgs as $row)$packageSlugs[(string)$row['slug']]=true;
$baselineIds=[];foreach($baseline['categories']as $c)$baselineIds[(int)$c['legacy_id']]=$c;
$missingCategories=[];$missingPackages=[];$excluded=[];
foreach($baseline['categories'] as $c){
    if(!isset($bySlug[$c['slug']]))$missingCategories[]=$c['slug'];
}
foreach($baseline['packages'] as $p){
    if(isset($packageSlugs[$p['slug']]))continue;
    $category=$baselineIds[(int)$p['category_legacy_id']]??null;
    if(!$category)throw new RuntimeException('Package points to missing source category: '.$p['slug']);
    $parent=isset($category['parent_legacy_id'])
        ? ($baselineIds[(int)$category['parent_legacy_id']]??null) : null;
    if(!$parent || empty($parent['slug']))throw new RuntimeException('Unexpected non-social package parent: '.$p['slug']);
    if(isset($bySlug[$category['slug']])&&$bySlug[$category['slug']]['status']!=='active'){
        $excluded[]=$p['slug'];continue;
    }
    $missingPackages[]=$p['slug'];
}
$report=[
    'created_at'=>gmdate('c'),
    'mode'=>$apply?'apply':'dry-run',
    'seed_platforms'=>11,'seed_subcategories'=>49,'seed_packages'=>185,
    'missing_category_slugs'=>$missingCategories,
    'missing_package_slugs'=>$missingPackages,
    'intentionally_inactive_category_packages_skipped'=>$excluded,
    'existing_category_count'=>count($cats),
    'existing_package_count'=>count($pkgs)
];
$folder=BASE_PATH.'/storage/backups';
if(!is_dir($folder)&&!mkdir($folder,0700,true)&&!is_dir($folder))
    throw new RuntimeException('Cannot create private backup folder.');
$file=$folder.'/netvera-social-restore-'.gmdate('Ymd-His').'-'.getmypid().'.json';
if(file_put_contents($file,json_encode($report,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_THROW_ON_ERROR),LOCK_EX)===false)
    throw new RuntimeException('Audit could not be saved.');
@chmod($file,0600);
echo 'Missing categories: '.count($missingCategories).' | packages: '.count($missingPackages).PHP_EOL;
echo 'Audit: '.$file.PHP_EOL;
if(!$apply){echo "DRY RUN: no database changes.\n";exit(0);}
$createdCategory=0;$createdPackage=0;
try{
    $pdo->beginTransaction();
    // Seed canonical parents first. Keep original IDs only in source JSON;
    // actual foreign keys always resolve by unique category slug.
    foreach($baseline['categories'] as $c){
        if($c['parent_legacy_id']!==null)continue;
        $existing=$db->fetch('SELECT id FROM categories WHERE slug=? FOR UPDATE',[$c['slug']]);
        if($existing)continue;
        $stmt=$pdo->prepare('INSERT INTO categories (parent_id,name,slug,description,icon_key,sort_order,status,seo_title,seo_description) VALUES (NULL,?,?,?,?,?,?,?,?)');
        $stmt->execute([$c['name'],$c['slug'],$c['description'],$c['icon_key'],(int)$c['sort_order'],$c['status'],$c['seo_title'],$c['seo_description']]);
        $createdCategory++;
    }
    foreach($baseline['categories'] as $c){
        if($c['parent_legacy_id']===null)continue;
        $parent=$baselineIds[(int)$c['parent_legacy_id']]??null;
        if(!$parent)throw new RuntimeException('Parent baseline missing for '.$c['slug']);
        $actualParent=$db->fetch('SELECT id FROM categories WHERE slug=? LIMIT 1',[$parent['slug']]);
        if(!$actualParent)throw new RuntimeException('Database parent missing '.$parent['slug']);
        $existing=$db->fetch('SELECT id,parent_id,status FROM categories WHERE slug=? FOR UPDATE',[$c['slug']]);
        if($existing){
            if((int)$existing['parent_id']!==(int)$actualParent['id'])
                throw new RuntimeException('Existing category belongs to another root: '.$c['slug']);
            continue;
        }
        $stmt=$pdo->prepare('INSERT INTO categories (parent_id,name,slug,description,icon_key,sort_order,status,seo_title,seo_description) VALUES (?,?,?,?,?,?,?,?,?)');
        $stmt->execute([(int)$actualParent['id'],$c['name'],$c['slug'],$c['description'],$c['icon_key'],(int)$c['sort_order'],$c['status'],$c['seo_title'],$c['seo_description']]);
        $createdCategory++;
    }
    $insert=$pdo->prepare('INSERT INTO packages
        (category_id,name,slug,short_description,description,price,discount_price,
         delivery_time,min_quantity,max_quantity,badge,is_featured,status,sort_order,
         seo_title,seo_description)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
    foreach($baseline['packages'] as $p){
        if($db->fetch('SELECT id FROM packages WHERE slug=? LIMIT 1',[$p['slug']]))continue;
        $category=$baselineIds[(int)$p['category_legacy_id']]??null;
        if(!$category)throw new RuntimeException('Unexpected package category');
        $actual=$db->fetch('SELECT id,status FROM categories WHERE slug=? LIMIT 1',[$category['slug']]);
        if(!$actual||$actual['status']!=='active')continue;
        if((float)$p['price']<=0)throw new RuntimeException('Missing source price: '.$p['slug']);
        $insert->execute([(int)$actual['id'],$p['name'],$p['slug'],$p['short_description'],
            $p['description'],(float)$p['price'],$p['discount_price'],
            $p['delivery_time'],(int)$p['min_quantity'],(int)$p['max_quantity'],
            $p['badge'],(int)$p['is_featured'],$p['status'],(int)$p['sort_order'],
            $p['seo_title'],$p['seo_description']]);
        $createdPackage++;
    }
    $pdo->commit();
}catch(\Throwable $e){
    if($pdo->inTransaction())$pdo->rollBack();
    throw $e;
}
echo "Created $createdCategory missing categories and $createdPackage missing social packages. Existing data untouched.\n";
