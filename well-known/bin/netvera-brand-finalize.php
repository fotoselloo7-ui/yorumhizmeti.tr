<?php
/**
 * NetVera legacy brand cleanup for EXISTING databases (not an SQL import).
 *
 * php bin/netvera-brand-finalize.php
 * php bin/netvera-brand-finalize.php --apply --ack=NETVERA-BRAND-REVIEWED
 *
 * Updates only public branded editorial and SEO fields after a private backup.
 * Never touches slugs, customer/admin logins, bank accounts, passwords,
 * blog records, orders, licenses, imported software or payment configuration.
 */
declare(strict_types=1);
if(PHP_SAPI!=='cli'){http_response_code(403);exit(1);}
define('BASE_PATH',dirname(__DIR__));
foreach(@file(BASE_PATH.'/.env',FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES)?:[] as $line){
    $line=trim($line);
    if($line===''||$line[0]==='#'||!str_contains($line,'='))continue;
    [$key,$value]=explode('=',$line,2);
    $_ENV[trim($key)]=trim($value," \t\r\n\0\x0B\"'");
}
spl_autoload_register(static function(string $class):void{
    if(!str_starts_with($class,'App\\'))return;
    $file=BASE_PATH.'/app/'.str_replace('\\','/',substr($class,4)).'.php';
    if(is_file($file))require $file;
});
require_once BASE_PATH.'/app/Core/Helpers.php';
$apply=in_array('--apply',$argv,true);
if($apply&&!in_array('--ack=NETVERA-BRAND-REVIEWED',$argv,true))
    throw new RuntimeException('Use explicit --ack=NETVERA-BRAND-REVIEWED.');
$pdo=\App\Core\Database::getInstance()->getPdo();
$targets=[
    'categories'=>['description','seo_title','seo_description','og_title','og_description','image_alt','image_title','canonical_url'],
    'packages'=>['description','short_description','seo_title','seo_description','og_title','og_description','image_alt','image_title','canonical_url'],
    'pages'=>['title','content','seo_title','seo_description','og_title','og_description','meta_title','meta_description'],
    'home_sections'=>['title','description','subtitle','content','seo_title','seo_description','seo_focus_keyword']
];
$globalKeys=array_fill_keys([
    'site_name','site_url','site_email','site_slogan','footer_text',
    'default_seo_title','default_seo_description','seo_og_title',
    'seo_og_description','brand_positioning','seo_org_description',
    'seo_geo_summary','seo_entity_topics','seo_home_question','seo_home_answer'
],true);
$replace=static function(string $value):string{
    // Preserve generic service names such as "Google yorum hizmeti".
    return str_replace(
        ['Yorum Hizmeti','YorumHizmeti.tr','YorumHizmeti','YorumPanel Pro',
         'YorumPanel','yorumhizmeti.tr'],
        ['NetVera Teknoloji Yazılım','netvera.tr','NetVera Teknoloji Yazılım',
         'NetVera Teknoloji Yazılım','NetVera Teknoloji Yazılım','netvera.tr'],
        $value
    );
};
$existing=$pdo->query('SELECT table_name FROM information_schema.tables WHERE table_schema=DATABASE()')->fetchAll(\PDO::FETCH_COLUMN);
$tables=array_fill_keys($existing,true);
$changes=[];
foreach($targets as $table=>$fields){
    if(!isset($tables[$table]))continue;
    $cols=$pdo->query('SHOW COLUMNS FROM '.$table)->fetchAll(\PDO::FETCH_COLUMN);
    if(!in_array('id',$cols,true))continue;
    foreach($fields as $field){
        if(!in_array($field,$cols,true))continue;
        $stmt=$pdo->query('SELECT id, '.$field.' AS original_value FROM '.$table);
        while($row=$stmt->fetch(\PDO::FETCH_ASSOC)){
            if($row['original_value']===null)continue;
            $new=$replace((string)$row['original_value']);
            if($new===(string)$row['original_value'])continue;
            $changes[]=['table'=>$table,'field'=>$field,'id'=>(int)$row['id'],
                'old'=>(string)$row['original_value'],'new'=>$new];
        }
    }
}
if(isset($tables['settings'])){
    $cols=$pdo->query('SHOW COLUMNS FROM settings')->fetchAll(\PDO::FETCH_COLUMN);
    if(in_array('setting_key',$cols,true)&&in_array('setting_value',$cols,true)&&in_array('id',$cols,true)){
        $stmt=$pdo->query('SELECT id,setting_key,setting_value FROM settings');
        foreach($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row){
            if(!isset($globalKeys[$row['setting_key']])||$row['setting_value']===null)continue;
            $new=$replace((string)$row['setting_value']);
            if($new===(string)$row['setting_value'])continue;
            $changes[]=['table'=>'settings','field'=>'setting_value','id'=>(int)$row['id'],
                'old'=>(string)$row['setting_value'],'new'=>$new];
        }
    }
}
$dir=BASE_PATH.'/storage/backups';
if(!is_dir($dir)&&!mkdir($dir,0700,true)&&!is_dir($dir))
    throw new RuntimeException('Private backup directory unavailable.');
$backup=$dir.'/netvera-brand-before-'.gmdate('Ymd-His').'-'.getmypid().'.json';
if(file_put_contents($backup,json_encode([
    'created_at'=>gmdate('c'),'mode'=>$apply?'apply':'dry-run','changes'=>$changes
],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_THROW_ON_ERROR),LOCK_EX)===false)
    throw new RuntimeException('Backup failed; no database changes were made.');
@chmod($backup,0600);
$counts=[];
foreach($changes as $row)$counts[$row['table']]=($counts[$row['table']]??0)+1;
echo 'Old-brand fields: '.count($changes).' '.json_encode($counts).PHP_EOL;
echo 'Private before-image audit: '.$backup.PHP_EOL;
if(!$apply){echo "DRY RUN: no SQL updated.\n";exit(0);}
try{
    $pdo->beginTransaction();
    $cache=[];
    foreach($changes as $row){
        $table=$row['table'];$field=$row['field'];$key=$table.'.'.$field;
        if(!isset($cache[$key]))
            $cache[$key]=$pdo->prepare('UPDATE '.$table.' SET '.$field.'=? WHERE id=? AND '.$field.'=?');
        $cache[$key]->execute([$row['new'],$row['id'],$row['old']]);
    }
    $pdo->commit();
}catch(\Throwable $e){
    if($pdo->inTransaction())$pdo->rollBack();
    throw $e;
}
echo 'Updated '.count($changes)." branded metadata fields. Customers and software untouched.\n";
