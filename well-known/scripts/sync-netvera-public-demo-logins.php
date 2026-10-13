<?php
/**
 * Owner-approved migration of source NetVera PUBLIC demo accounts.
 * CLI only. NEVER push source SQL, logins, passwords or exported customer data
 * to GitHub, an Actions artifact, or a static public JSON file.
 *
 * Sources:
 *  1) Restored legacy script_products table (same or separate DB)
 *  2) Original NetVera SQL dump via --source-sql=/absolute/path/file.sql
 *
 * Dry run:
 *   php scripts/sync-netvera-public-demo-logins.php --source-sql=/path/netvera.sql
 *
 * Apply:
 *   NETVERA_DEMO_SYNC_APPROVED=1 NETVERA_PUBLIC_DEMO_LOGINS=1 \
 *   php scripts/sync-netvera-public-demo-logins.php --apply --source-sql=/path/netvera.sql
 *
 * Only products explicitly marked demo_is_active=1, demo_is_public=1 and
 * is_active=1 are imported. Customer/order tables are never read.
 */
declare(strict_types=1);

if (PHP_SAPI!=='cli') {
    http_response_code(403);
    exit("CLI only\n");
}

$root=dirname(__DIR__);
if (is_file($root.'/.env')) {
    foreach (file($root.'/.env',FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES) as $line) {
        $line=trim($line);
        if ($line===''||$line[0]==='#'||!str_contains($line,'=')) continue;
        [$key,$val]=explode('=',$line,2);
        $key=trim($key);
        if(!preg_match('/^[A-Z][A-Z0-9_]*$/D',$key)||getenv($key)!==false) continue;
        putenv($key.'='.trim($val," \t\r\n\"'"));
    }
}

$env=static fn(string $key):string=>(string)(getenv($key)?:'');
$apply=in_array('--apply',$argv,true);
$sourceSqlPath='';
foreach($argv as $arg){
    if(str_starts_with($arg,'--source-sql=')){
        $sourceSqlPath=trim(substr($arg,13));
        break;
    }
}

if($apply && ($env('NETVERA_DEMO_SYNC_APPROVED')!=='1'||$env('NETVERA_PUBLIC_DEMO_LOGINS')!=='1')){
    fwrite(STDERR,"Demo publishing requires explicit owner approval in private environment.\n");
    exit(2);
}

$connect=static function(string $host,string $name,string $user,string $pass):PDO{
    if(!preg_match('/^[A-Za-z0-9_.:-]+$/D',$host)||
       !preg_match('/^[A-Za-z0-9_-]+$/D',$name)||
       $name===''||$user===''){
        throw new RuntimeException('Invalid DB connection settings');
    }

    return new PDO(
        "mysql:host=".$host.";dbname=".$name.";charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES=>false,
            PDO::ATTR_TIMEOUT=>5,
        ]
    );
};

$decodeSqlString=static function(string $token):?string{
    $token=trim($token);
    if(strcasecmp($token,'NULL')===0)return null;

    if(strlen($token)>=2 && $token[0]==="'" && $token[strlen($token)-1]==="'"){
        $value=substr($token,1,-1);
        return strtr($value,[
            "\\0"=>"\0",
            "\\n"=>"\n",
            "\\r"=>"\r",
            "\\t"=>"\t",
            "\\Z"=>chr(26),
            "\\'"=>"'",
            '\\"'=>'"',
            "\\\\"=>"\\",
        ]);
    }

    return $token;
};

$splitSqlValues=static function(string $row):array{
    $row=trim($row);
    if($row!==''&&$row[0]==='(')$row=substr($row,1);
    if($row!==''&&substr($row,-1)===')')$row=substr($row,0,-1);

    $values=[];
    $buffer='';
    $quoted=false;
    $escaped=false;
    $len=strlen($row);

    for($i=0;$i<$len;$i++){
        $ch=$row[$i];

        if($escaped){
            $buffer.=$ch;
            $escaped=false;
            continue;
        }

        if($quoted&&$ch==='\\'){
            $buffer.=$ch;
            $escaped=true;
            continue;
        }

        if($ch==="'"){
            $quoted=!$quoted;
            $buffer.=$ch;
            continue;
        }

        if($ch===','&&!$quoted){
            $values[]=trim($buffer);
            $buffer='';
            continue;
        }

        $buffer.=$ch;
    }

    $values[]=trim($buffer);
    return $values;
};

$loadFromSql=static function(string $file) use ($decodeSqlString,$splitSqlValues):array{
    if($file===''||!is_file($file)||!is_readable($file))
        throw new RuntimeException('Readable --source-sql file required');

    $size=filesize($file);
    if($size===false||$size>50*1024*1024)
        throw new RuntimeException('Source SQL file too large');

    $sql=file_get_contents($file);
    if($sql===false)throw new RuntimeException('Source SQL cannot be read');

    $products=[];
    $tick=chr(96);
    $table=preg_quote($tick.'script_products'.$tick,'/');
    $pattern='/INSERT\s+INTO\s+'.$table.'\s*\((.*?)\)\s*VALUES\s*(.*?);/si';

    if(!preg_match_all($pattern,$sql,$blocks,PREG_SET_ORDER))
        throw new RuntimeException('script_products INSERT statements not found');

    foreach($blocks as $block){
        preg_match_all('/'.preg_quote($tick,'/').'([^'.preg_quote($tick,'/').']+)'.preg_quote($tick,'/').'/',$block[1],$cm);
        $columns=$cm[1]??[];
        if(!$columns)continue;

        $rows=[];
        $buf='';
        $depth=0;
        $quoted=false;
        $escaped=false;
        $valuesSql=$block[2];
        $len=strlen($valuesSql);

        for($i=0;$i<$len;$i++){
            $ch=$valuesSql[$i];

            if($escaped){
                $buf.=$ch;
                $escaped=false;
                continue;
            }

            if($quoted&&$ch==='\\'){
                $buf.=$ch;
                $escaped=true;
                continue;
            }

            if($ch==="'"){
                $quoted=!$quoted;
                $buf.=$ch;
                continue;
            }

            if(!$quoted){
                if($ch==='(')$depth++;
                elseif($ch===')')$depth--;

                if($ch===','&&$depth===0){
                    if(trim($buf)!=='')$rows[]=trim($buf);
                    $buf='';
                    continue;
                }
            }

            $buf.=$ch;
        }

        if(trim($buf)!=='')$rows[]=trim($buf);

        foreach($rows as $row){
            $parts=$splitSqlValues($row);
            if(count($parts)!==count($columns))continue;

            $item=[];
            foreach($columns as $idx=>$column){
                $item[$column]=$decodeSqlString($parts[$idx]);
            }

            if((int)($item['demo_is_active']??0)!==1||
               (int)($item['demo_is_public']??0)!==1||
               (int)($item['is_active']??0)!==1){
                continue;
            }

            if(trim((string)($item['slug']??''))==='')continue;
            $products[]=$item;
        }
    }

    return $products;
};

try{
    $target=$connect(
        $env('DB_HOST'),
        $env('DB_NAME'),
        $env('DB_USER'),
        $env('DB_PASS')
    );

    if($sourceSqlPath!==''){
        $sourceDemos=$loadFromSql($sourceSqlPath);
        $sourceMode='SQL_FILE';
    }else{
        $source=$connect(
            $env('NETVERA_SOURCE_DB_HOST')?:$env('DB_HOST'),
            $env('NETVERA_SOURCE_DB_NAME')?:$env('DB_NAME'),
            $env('NETVERA_SOURCE_DB_USER')?:$env('DB_USER'),
            $env('NETVERA_SOURCE_DB_PASSWORD')?:$env('DB_PASS')
        );

        $sourceTable=$source->query("SHOW TABLES LIKE 'script_products'")->fetchColumn();
        if(!$sourceTable)throw new RuntimeException('Legacy script_products table missing');

        $select=$source->query(
            "SELECT id,slug,demo_url,demo_admin_url,demo_user_url,demo_username,demo_password,
                    demo_admin_username,demo_admin_password,demo_note,demo_accounts_json,demo_video_url,
                    demo_is_active,demo_is_public,demo_display_mode,is_active
             FROM script_products
             WHERE demo_is_active=1 AND demo_is_public=1 AND is_active=1"
        );

        $sourceDemos=$select->fetchAll();
        $sourceMode='DATABASE';
    }

    $targetTable=$target->query("SHOW TABLES LIKE 'nv_legacy_script_products'")->fetchColumn();
    if(!$targetTable)throw new RuntimeException('Destination product catalog table missing');

    $lookup=$target->prepare(
        "SELECT legacy_id,slug,public_json
         FROM nv_legacy_script_products
         WHERE legacy_id=? AND slug=? LIMIT 1"
    );

    $update=$target->prepare(
        "UPDATE nv_legacy_script_products
         SET public_json=?
         WHERE legacy_id=? AND slug=?"
    );

    $changed=0;
    $skip=0;

    if($apply)$target->beginTransaction();

    foreach($sourceDemos as $src){
        $lookup->execute([(int)$src['id'],(string)$src['slug']]);
        $dst=$lookup->fetch();

        if(!$dst){
            $skip++;
            continue;
        }

        $data=json_decode((string)$dst['public_json'],true);
        if(!is_array($data))
            throw new RuntimeException('Invalid target product JSON');

        foreach(['demo_url','demo_admin_url','demo_user_url','demo_video_url'] as $key){
            $url=trim((string)($src[$key]??''));

            if($url!=='' && (
                !filter_var($url,FILTER_VALIDATE_URL)||
                !in_array(strtolower((string)parse_url($url,PHP_URL_SCHEME)),['https','http'],true)
            )){
                throw new RuntimeException('Invalid source demo URL; no changes committed');
            }

            $data[$key]=mb_substr($url,0,500);
        }

        foreach(['demo_username','demo_password','demo_admin_username','demo_admin_password','demo_note'] as $key){
            $data[$key]=mb_substr(
                (string)($src[$key]??''),
                0,
                $key==='demo_note'?2000:190
            );
        }

        $extra=json_decode((string)($src['demo_accounts_json']??'[]'),true);
        if(!is_array($extra)||!array_is_list($extra))$extra=[];
        $extra=array_slice($extra,0,6);

        $data['demo_accounts_json']=json_encode(
            $extra,
            JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES
        );
        $data['demo_is_active']=1;
        $data['demo_is_public']=1;
        $data['demo_display_mode']='public';
        $data['demo_credentials_public']=(
            trim((string)($src['demo_username']??''))!=='' ||
            trim((string)($src['demo_admin_username']??''))!=='' ||
            !empty($extra)
        ) ? 1 : 0;

        $json=json_encode(
            $data,
            JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_THROW_ON_ERROR
        );

        if($apply){
            $update->execute([
                $json,
                (int)$src['id'],
                (string)$src['slug'],
            ]);
        }

        $changed++;
    }

    if($apply)$target->commit();

    echo json_encode([
        'mode'=>$apply?'UPDATED_PRIVATE_DATABASE':'DRY_RUN_NO_WRITES',
        'source_mode'=>$sourceMode,
        'source_public_demo_products'=>count($sourceDemos),
        'matched_products'=>$changed,
        'skipped_missing_target'=>$skip,
        'customer_data_accessed'=>false,
        'credential_values_logged'=>false,
    ],JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)."\n";
}catch(Throwable $e){
    if(isset($target)&&$target instanceof PDO&&$target->inTransaction()){
        $target->rollBack();
    }

    fwrite(
        STDERR,
        "Demo migration stopped without disclosing source data (".get_class($e).").\n"
    );
    exit(1);
}
