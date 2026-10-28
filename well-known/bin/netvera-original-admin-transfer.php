<?php
/**
 * Explicit, identity-verified original NetVera -> new super_admin transfer.
 *
 * php bin/netvera-original-admin-transfer.php            # read-only dry run
 * php bin/netvera-original-admin-transfer.php --apply --ack=TRANSFER-VERIFIED-NETVERA-ADMIN
 *
 * Only the admin_users record of the authenticated old NetVera administrator
 * is copied, with the original supported password hash. No customer, product,
 * order or license tables are altered. Existing fallback admins remain
 * unmodified except an optional exact demo placeholder (admin@netvera.tr)
 * made inactive after a successful verified transfer.
 */
declare(strict_types=1);
if(PHP_SAPI!=='cli'){http_response_code(403);exit(1);}
if(!extension_loaded('pdo_mysql')){fwrite(STDERR,"pdo_mysql is required.\n");exit(2);}
$root=dirname(__DIR__);
foreach(@file($root.'/.env',FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES)?:[] as $line){
    $line=trim($line);
    if($line===''||$line[0]==='#'||!str_contains($line,'='))continue;
    [$key,$value]=explode('=',$line,2);
    $key=trim($key);
    if(getenv($key)===false)putenv($key.'='.trim($value," \t\r\n\0\x0B\"'"));
}
$get=static fn(string $key):string=>(string)(getenv($key)?:'');
$must=['NETVERA_SOURCE_DB_HOST','NETVERA_SOURCE_DB_NAME','NETVERA_SOURCE_DB_USER',
       'NETVERA_SOURCE_DB_PASSWORD','NETVERA_ADMIN_EMAIL',
       'NETVERA_ADMIN_LOGIN_PROOF','DB_HOST','DB_NAME','DB_USER'];
foreach($must as $key)if($get($key)==='')throw new RuntimeException('Missing private config: '.$key);
$email=mb_strtolower(trim($get('NETVERA_ADMIN_EMAIL')),'UTF-8');
if(!filter_var($email,FILTER_VALIDATE_EMAIL))throw new RuntimeException('Original admin email invalid');
if($get('NETVERA_SOURCE_DB_HOST')===$get('DB_HOST') &&
   $get('NETVERA_SOURCE_DB_NAME')===$get('DB_NAME')){
    throw new RuntimeException('Source and target MySQL databases must be different.');
}
$connect=static function(string $host,string $db,string $username,string $pass):PDO{
    return new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4",$username,$pass,[
        PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES=>false,
    ]);
};
$source=$connect($get('NETVERA_SOURCE_DB_HOST'),$get('NETVERA_SOURCE_DB_NAME'),
  $get('NETVERA_SOURCE_DB_USER'),$get('NETVERA_SOURCE_DB_PASSWORD'));
$target=$connect($get('DB_HOST'),$get('DB_NAME'),$get('DB_USER'),$get('DB_PASS'));
$src=$source->prepare("SELECT id,name,email,password_hash,is_active FROM admin_users WHERE LOWER(email)=? LIMIT 2");
$src->execute([$email]);$matches=$src->fetchAll();
if(count($matches)!==1 || (int)$matches[0]['is_active']!==1)
    throw new RuntimeException('Expected one active, verified original NetVera administrator.');
$old=$matches[0];
$hash=(string)$old['password_hash'];
if(password_get_info($hash)['algo']===null ||
   !password_verify($get('NETVERA_ADMIN_LOGIN_PROOF'),$hash)){
    throw new RuntimeException('Original NetVera admin password verification failed. No changes made.');
}
$existing=$target->prepare('SELECT id,name,email,role,status FROM admins WHERE email=? LIMIT 1');
$existing->execute([$email]);$found=$existing->fetch();
$placeholder=$target->query("SELECT id,name,email,role,status FROM admins WHERE email='admin@netvera.tr' LIMIT 1")->fetch();
$apply=in_array('--apply',$argv,true);
$ack=in_array('--ack=TRANSFER-VERIFIED-NETVERA-ADMIN',$argv,true);
if($apply&&!$ack)throw new RuntimeException('Explicit --ack=TRANSFER-VERIFIED-NETVERA-ADMIN required.');
$preview=[
 'original_admin_verified'=>true,'new_account_action'=>$found?'update existing':'insert new',
 'will_promote'=>'super_admin','will_preserve_original_password_hash'=>true,
 'will_disable_only_demo_placeholder'=>
     (bool)($placeholder && $placeholder['email']!==$email &&
       $placeholder['name']==='Admin' && $placeholder['status']==='active'),
 'customers_orders_licenses_modified'=>false
];
echo json_encode($preview,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR)."\n";
if(!$apply){echo "DRY RUN: no changes. Back up both databases before applying.\n";exit(0);}
$target->beginTransaction();
try{
    $current=$target->prepare('SELECT id FROM admins WHERE email=? FOR UPDATE');
    $current->execute([$email]);$id=(int)($current->fetchColumn()?:0);
    if($id){
        $stmt=$target->prepare("UPDATE admins SET name=?,password=?,role='super_admin',
                                status='active',must_change_password=0 WHERE id=?");
        $stmt->execute([mb_substr((string)$old['name'],0,100),$hash,$id]);
    }else{
        $stmt=$target->prepare("INSERT INTO admins(name,email,password,role,status,must_change_password)
                VALUES (?,?,?,'super_admin','active',0)");
        $stmt->execute([mb_substr((string)$old['name'],0,100),$email,$hash]);
        $id=(int)$target->lastInsertId();
    }
    // Revoke only the default demo identity. Do not delete accounts, or wipe
    // real customer and order mappings. No blanket admin-table replacement.
    if($placeholder && $placeholder['email']!==$email && $placeholder['name']==='Admin'){
        $target->prepare("UPDATE admins SET status='inactive' WHERE id=? AND email='admin@netvera.tr' AND name='Admin'")->execute([(int)$placeholder['id']]);
    }
    $verify=$target->prepare("SELECT password,role,status FROM admins WHERE id=?");
    $verify->execute([$id]);$created=$verify->fetch();
    if(!$created || !password_verify($get('NETVERA_ADMIN_LOGIN_PROOF'),(string)$created['password']) ||
       $created['role']!=='super_admin'||$created['status']!=='active')
        throw new RuntimeException('New administrator verification failed; transaction rolled back.');
    $target->commit();
}catch(Throwable $e){
    if($target->inTransaction())$target->rollBack();
    throw $e;
}
echo "Original verified NetVera administrator is now active super_admin; original password hash retained.\n";
echo "The default demo admin was disabled only if it matched the known placeholder exactly. Log in through /admin/giris.\n";
