<?php
/** Opt-in Netvera public-content importer. DOES NOT access live payment systems. */
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(403); exit(1); }
define('BASE_PATH', dirname(__DIR__));
$envPath=BASE_PATH.'/.env';
if (is_file($envPath)) {
    foreach (file($envPath,FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES) as $line) {
        $line=trim($line);
        if ($line==='' || $line[0]==='#' || !str_contains($line,'=')) continue;
        [$key,$val]=explode('=',$line,2);
        $_ENV[trim($key)]=trim($val," \t\n\r\0\x0B\"'");
    }
}
spl_autoload_register(static function(string $class):void {
    if(!str_starts_with($class,'App\\'))return;
    $file=BASE_PATH.'/app/'.str_replace('\\','/',substr($class,4)).'.php';
    if(is_file($file))require $file;
});
require BASE_PATH.'/app/Core/Helpers.php';
$source=$argv[1]??'';
$zip=null;
$trustedFixture=realpath(BASE_PATH.'/database/netvera-public-catalog.json');
$requested=realpath($source);
if(!$requested || !is_file($requested)){
    fwrite(STDERR,"Use the checked-in public catalog JSON or a verified public-only ZIP.\n");exit(2);
}
if($trustedFixture && hash_equals($trustedFixture,$requested)){
    // The version-controlled, allowlisted public snapshot needs no private ZIP.
    // Content has been filtered down to products, blogs and public image metadata.
    $json=file_get_contents($requested);
    if($json===false)exit(2);
}else{
    if(!class_exists('ZipArchive'))exit(2);
    $zip=new ZipArchive();
    if($zip->open($requested)!==true)exit(2);
    $json=$zip->getFromName('netvera-public-content.json');
    $manifest=$zip->getFromName('manifest.json');
    if($json===false || $manifest===false)exit(2);
    $info=json_decode($manifest,true,512,JSON_THROW_ON_ERROR);
    if(($info['content_sha256']??'')!==hash('sha256',$json)){
        fwrite(STDERR,"Private payload checksum mismatch.\n");exit(2);
    }
}
$data=json_decode($json,true,512,JSON_THROW_ON_ERROR);
if(($data['format']??'')!=='netvera-public-safelist-v1')exit(2);
$sections=['categories','products','images','blog_categories','blog_posts','approved_reviews'];
foreach($sections as $key)if(!is_array($data[$key]??null))exit(2);
foreach(['products','blog_posts','approved_reviews'] as $key)
  foreach($data[$key] as $row)
    foreach(array_keys($row) as $column)
      if(preg_match('/merchant|secret|salt|password|token|api.?key|demo_accounts|demo_admin|demo_user|shopier_payment/i',$column))exit(2);
$counts=[];
foreach($sections as $key)$counts[$key]=count($data[$key]);
echo json_encode($counts,JSON_UNESCAPED_UNICODE)."\n";
if(!in_array('--apply',$argv,true)){echo "DRY RUN; no writes.\n";exit(0);}
// CLI operator must explicitly opt into a disposable staging DB; never production.
if((string)($_ENV['NETVERA_IMPORT_ALLOWED']??getenv('NETVERA_IMPORT_ALLOWED'))!=='1'
 || !in_array(strtolower((string)($_ENV['APP_ENV']??'production')),
   ['staging','local','testing','development'],true)){
    fwrite(STDERR,"Staging opt-in required; production import blocked.\n");exit(3);
}
$db=\App\Core\Database::getInstance();
$pdo=$db->getPdo();
foreach(['netvera-legacy-bridge-v1.sql','netvera-inquiries-v1.sql'] as $migration){
    $schema=file_get_contents(BASE_PATH.'/database/migrations/'.$migration);
    if($schema===false)exit(3);
    foreach(explode(';',$schema) as $part){
        $part=trim(preg_replace('/^\s*--[^\n]*$/m','',$part));
        if($part!=='')$pdo->exec($part);
    }
}
function nvImportRow(PDO $pdo,string $table,array $record):void {
    $cols=array_keys($record);
    $sql='INSERT INTO '.$table.' ('.implode(',',$cols).') VALUES ('
        .implode(',',array_fill(0,count($cols),'?')).') ON DUPLICATE KEY UPDATE ';
    $pk=$cols[0];
    $sql.=$pk.'='.$pk; // Import is repeatable; never overwrite edits.
    $pdo->prepare($sql)->execute(array_values($record));
}
$categoryIdMap=[];
$pdo->beginTransaction();
try {
    foreach($data['categories'] as $r) {
        nvImportRow($pdo,'nv_legacy_script_categories',[
            'legacy_id'=>(int)$r['id'],'parent_legacy_id'=>$r['parent_id'],
            'slug'=>$r['slug'],'name'=>$r['name'],'description'=>$r['description'],
            'meta_title'=>$r['meta_title'],'meta_description'=>$r['meta_description'],
            'sort_order'=>(int)$r['sort_order'],'active'=>(int)$r['is_active']
        ]);
    }
    foreach($data['products'] as $r) {
        nvImportRow($pdo,'nv_legacy_script_products',[
            'legacy_id'=>(int)$r['id'],'category_legacy_id'=>(int)$r['category_id'],
            'slug'=>$r['slug'],'name'=>$r['name'],'short_desc'=>$r['short_desc'],
            'description'=>$r['description'],'cover_image'=>$r['image'],
            'price'=>$r['price'],'old_price'=>$r['old_price'],'badge'=>$r['badge'],
            'meta_title'=>$r['meta_title'],'meta_description'=>$r['meta_description'],
            'focus_keyword'=>$r['focus_keyword'],
            'public_json'=>json_encode($r,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_THROW_ON_ERROR),
            'sort_order'=>(int)$r['sort_order'],'active'=>(int)$r['is_active']
        ]);
    }
    foreach($data['images'] as $r) {
        nvImportRow($pdo,'nv_legacy_script_images',[
            'legacy_id'=>(int)$r['id'],'product_legacy_id'=>(int)$r['script_product_id'],
            'image_path'=>$r['image_path'],'alt_text'=>$r['alt_text'],
            'caption'=>$r['caption'],'sort_order'=>(int)$r['sort_order'],'active'=>(int)$r['is_active']
        ]);
    }
    foreach($data['approved_reviews'] as $r) {
        if($r['target_type']!=='script' || $r['status']!=='approved')continue;
        nvImportRow($pdo,'nv_legacy_public_reviews',[
            'legacy_id'=>(int)$r['id'],'product_legacy_id'=>(int)$r['target_id'],
            'rating'=>max(1,min(5,(int)$r['rating'])),
            'comment'=>$r['comment'],'created_at'=>$r['created_at']
        ]);
    }
    foreach($data['blog_categories'] as $r) {
        $category=$db->fetch('SELECT id FROM blog_categories WHERE slug = ? LIMIT 1',[$r['slug']]);
        if(!$category){
            $id=$db->insert('blog_categories',[
                'name'=>$r['name'],'slug'=>$r['slug'],'sort_order'=>(int)$r['sort_order'],
                'status'=>(int)$r['is_active']?'active':'inactive'
            ]);
        }else $id=(int)$category['id'];
        $categoryIdMap[(int)$r['id']]=$id;
    }
    foreach($data['blog_posts'] as $r) {
        $old=$db->fetch('SELECT id FROM blog_posts WHERE slug = ? LIMIT 1',[$r['slug']]);
        if(!$old){
            $id=$db->insert('blog_posts',[
                'blog_category_id'=>$categoryIdMap[(int)$r['category_id']]??null,
                'title'=>$r['title'],'slug'=>$r['slug'],'excerpt'=>$r['excerpt'],
                'content'=>$r['content'],'raw_content'=>$r['content'],
                'image'=>preg_replace('~^/?uploads/~','',(string)$r['image']),'status'=>'active',
                'published_at'=>$r['published_at'],'seo_title'=>$r['seo_title'],
                'seo_description'=>$r['seo_description'],
                'seo_focus_keyword'=>$r['focus_keyword'],
                'canonical_url'=>$r['canonical_url'],'og_title'=>$r['og_title'],
                'og_description'=>$r['og_description'],
                'og_image'=>preg_replace('~^/?uploads/~','',(string)$r['og_image'])
            ]);
        }else $id=(int)$old['id'];
        nvImportRow($pdo,'nv_legacy_blog_meta',[
            'blog_post_id'=>$id,'original_slug'=>$r['slug'],
            'original_author'=>$r['author_name'],'robots'=>$r['robots'],
            'focus_keyword'=>$r['focus_keyword'],
            'secondary_keywords'=>$r['secondary_keywords'],
            'source_json'=>json_encode($r,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR)
        ]);
    }
    $pdo->commit();
}catch(Throwable $error){
    if($pdo->inTransaction())$pdo->rollBack();
    fwrite(STDERR,'DB migration aborted: '.get_class($error)."\n");exit(4);
}
// Only public blog/software image assets. Never copy chat, personal uploads or PHP.
$count=0;
if($zip instanceof ZipArchive)for($i=0;$i<$zip->numFiles;$i++){
    $entry=$zip->getNameIndex($i);
    if(!preg_match('~^public/uploads/(scripts|blog)/[a-z0-9_./-]+\.(?:jpg|jpeg|png|webp|gif)$~i',$entry)
      || str_contains($entry,'..'))continue;
    $target=BASE_PATH.'/'.$entry;
    if(is_file($target))continue;
    if(!is_dir(dirname($target)) && !mkdir(dirname($target),0755,true))continue;
    $from=$zip->getStream($entry);
    if(!$from)continue;
    $to=fopen($target,'xb');
    if(!$to){fclose($from);continue;}
    stream_copy_to_stream($from,$to,20_000_000);
    fclose($from);fclose($to);$count++;
}
echo "Staging public import complete. Images copied: $count\n";
echo "Payment, PayTR, customers, orders and Telegram untouched.\n";
