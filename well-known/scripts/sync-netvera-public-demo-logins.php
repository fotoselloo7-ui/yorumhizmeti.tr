<?php
/**
 * Owner-approved migration of source NetVera PUBLIC demo accounts.
 * CLI only. NEVER push source SQL, logins, passwords or exported customer data
 * to GitHub, an Actions artifact, or a static public JSON file.
 *
 * Both source script_products and target nv_legacy_script_products must already
 * exist. Uses SQL/database access in the destination hosting only.
 *
 * Dry run: php scripts/sync-netvera-public-demo-logins.php
 * Apply: NETVERA_DEMO_SYNC_APPROVED=1 NETVERA_PUBLIC_DEMO_LOGINS=1
 *        php scripts/sync-netvera-public-demo-logins.php --apply
 *
 * NETVERA_SOURCE_DB_* may point to a separate restored source database.
 * When omitted, reads original script_products from the existing DB_NAME,
 * useful when NetVera's legacy table and new tables coexist during cutover.
 */
declare(strict_types=1);

if (PHP_SAPI!=='cli') { http_response_code(403); exit("CLI only\n"); }
$root=dirname(__DIR__);
if (is_file($root.'/.env')) foreach (file($root.'/.env',FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES) as $line) {
    $line=trim($line);
    if ($line===''||$line[0]==='#'||!str_contains($line,'='))continue;
    [$key,$val]=explode('=',$line,2);
    $key=trim($key);
    if(!preg_match('/^[A-Z][A-Z0-9_]*$/D',$key)||getenv($key)!==false)continue;
    putenv($key.'='.trim($val," \t\r\n\"'"));
}
$env=static fn(string $key):string=>(string)(getenv($key)?:'');
$apply=in_array('--apply',$argv,true);
if($apply && ($env('NETVERA_DEMO_SYNC_APPROVED')!=='1'||$env('NETVERA_PUBLIC_DEMO_LOGINS')!=='1')){
    fwrite(STDERR,"Demo publishing requires explicit owner approval in private environment.\n");exit(2);
}
$connect=static function(string $host,string $name,string $user,string $pass):PDO{
    if(!preg_match('/^[A-Za-z0-9_.:-]+$/D',$host)||!preg_match('/^[A-Za-z0-9_-]+$/D',$name)
       ||$name===''||$user==='')throw new RuntimeException('Invalid DB connection settings');
    return new PDO("mysql:host=".$host.";dbname=".$name.";charset=utf8mb4",
      $user,$pass,[
        PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES=>false,
        PDO::ATTR_TIMEOUT=>5
      ]);
};
try{
    $target=$connect($env('DB_HOST'),$env('DB_NAME'),$env('DB_USER'),$env('DB_PASS'));
    $source=$connect(
      $env('NETVERA_SOURCE_DB_HOST')?:$env('DB_HOST'),
      $env('NETVERA_SOURCE_DB_NAME')?:$env('DB_NAME'),
      $env('NETVERA_SOURCE_DB_USER')?:$env('DB_USER'),
      $env('NETVERA_SOURCE_DB_PASSWORD')?:$env('DB_PASS')
    );
    $sourceTable=$source->query("SHOW TABLES LIKE 'script_products'")->fetchColumn();
    $targetTable=$target->query("SHOW TABLES LIKE 'nv_legacy_script_products'")->fetchColumn();
    if(!$sourceTable||!$targetTable)throw new RuntimeException('Legacy and destination product catalog tables both required');
    $select=$source->query("SELECT id,slug,demo_url,demo_admin_url,demo_user_url,demo_username,demo_password,
           demo_admin_username,demo_admin_password,demo_note,demo_accounts_json,demo_video_url,
           demo_is_active,demo_is_public,demo_display_mode,is_active
           FROM script_products WHERE demo_is_active=1 AND demo_is_public=1 AND is_active=1");
    $sourceDemos=$select->fetchAll();
    $lookup=$target->prepare("SELECT legacy_id,slug,public_json FROM nv_legacy_script_products WHERE legacy_id=? AND slug=? LIMIT 1");
    $update=$target->prepare("UPDATE nv_legacy_script_products SET public_json=? WHERE legacy_id=? AND slug=?");
    $changed=0;$skip=0;
    if($apply)$target->beginTransaction();
    foreach($sourceDemos as $src){
        $lookup->execute([(int)$src['id'],(string)$src['slug']]);
        $dst=$lookup->fetch();
        if(!$dst){$skip++;continue;}
        $data=json_decode((string)$dst['public_json'],true);
        if(!is_array($data))throw new RuntimeException('Invalid target product JSON');
        foreach(['demo_url','demo_admin_url','demo_user_url','demo_video_url'] as $key){
            $url=trim((string)($src[$key]??''));
            if($url!=='' && (!filter_var($url,FILTER_VALIDATE_URL)||
                !in_array(strtolower((string)parse_url($url,PHP_URL_SCHEME)),['https','http'],true))){
                throw new RuntimeException('Invalid source demo URL; no changes committed');
            }
            $data[$key]=mb_substr($url,0,500);
        }
        foreach(['demo_username','demo_password','demo_admin_username','demo_admin_password','demo_note'] as $key)
            $data[$key]=mb_substr((string)($src[$key]??''),0,$key==='demo_note'?2000:190);
        $extra=json_decode((string)($src['demo_accounts_json']??'[]'),true);
        if(!is_array($extra)||!array_is_list($extra))$extra=[];
        $data['demo_accounts_json']=json_encode(array_slice($extra,0,6),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        $data['demo_is_active']=1;
        $data['demo_is_public']=1;
        // Intentionally publishing demo-only sample credentials is optional and
        // requires two explicit environment confirmations plus --apply.
        $data['demo_credentials_public']=
           (trim((string)($src['demo_username']??''))!=='' ||
            trim((string)($src['demo_admin_username']??''))!==''||
            !empty($extra))?1:0;
        $json=json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_THROW_ON_ERROR);
        if($apply)$update->execute([$json,(int)$src['id'],(string)$src['slug']]);
        $changed++;
    }
    if($apply)$target->commit();
    echo json_encode([
      'mode'=>$apply?'UPDATED_PRIVATE_DATABASE':'DRY_RUN_NO_WRITES',
      'source_public_demo_products'=>count($sourceDemos),
      'matched_products'=>$changed,
      'skipped_missing_target'=>$skip,
      'customer_data_accessed'=>false,
      'secret_values_logged'=>false,
    ],JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)."\n";
} catch(Throwable $e){
    if(isset($target)&&$target->inTransaction())$target->rollBack();
    fwrite(STDERR,"Demo migration stopped without disclosing source data (".get_class($e).").\n");
    exit(1);
}
