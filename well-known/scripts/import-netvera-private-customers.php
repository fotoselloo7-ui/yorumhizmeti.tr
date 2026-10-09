<?php
/**
 * Netvera.tr private customer/affiliate/sale history -> isolated YorumHizmeti STAGING.
 *
 * Original 2026-10-08 Netvera schema: admin_users, customers, orders,
 * order_items, affiliate_accounts, affiliate_commissions.
 * No transaction gateway or native order table is modified.
 *
 * Usage:
 *   php scripts/import-netvera-private-customers.php           (dry-run)
 *   NETVERA_PRIVATE_IMPORT_ALLOWED=1 php scripts/import-netvera-private-customers.php --apply
 *
 * The SOURCE must first be restored into a SEPARATE, read-only Netvera MySQL DB.
 * Never put raw SQL, credentials, password hashes or license keys in GitHub.
 */
declare(strict_types=1);

if (PHP_SAPI !== 'cli') { http_response_code(403); exit("CLI only\n"); }
if (!extension_loaded('pdo_mysql') || !extension_loaded('openssl')) {
    fwrite(STDERR, "PDO MySQL and OpenSSL are required.\n"); exit(2);
}
$base=dirname(__DIR__);
$envFile=$base.'/.env';
if (!is_file($envFile)) { fwrite(STDERR,"Missing target staging .env.\n"); exit(2); }
foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
    $line=trim($line);
    if ($line==='' || $line[0]==='#' || !str_contains($line,'=')) continue;
    [$k,$v]=explode('=',$line,2); $k=trim($k);
    if (!preg_match('/^[A-Z][A-Z0-9_]*$/D',$k)) continue;
    if (getenv($k)===false) putenv($k.'='.trim($v," \t\r\n\"'"));
}
$get=static fn(string $key):string=>(string)(getenv($key)?:'');
$env=strtolower($get('APP_ENV'));
if ($env !== 'staging') {
    fwrite(STDERR,"REFUSED: production and unknown environments cannot run this importer.\n"); exit(2);
}
$apply=in_array('--apply',$argv,true);
if ($apply && $get('NETVERA_PRIVATE_IMPORT_ALLOWED')!=='1') {
    fwrite(STDERR,"REFUSED: NETVERA_PRIVATE_IMPORT_ALLOWED=1 required for apply.\n"); exit(2);
}
$required=['NETVERA_SOURCE_DB_HOST','NETVERA_SOURCE_DB_NAME','NETVERA_SOURCE_DB_USER',
    'NETVERA_SOURCE_DB_PASSWORD','DB_HOST','DB_NAME','DB_USER'];
foreach($required as $key) if ($get($key)==='') {
    fwrite(STDERR,"Missing environment variable ".$key."\n"); exit(2);
}
if ($get('NETVERA_SOURCE_DB_NAME')===$get('DB_NAME') &&
    $get('NETVERA_SOURCE_DB_HOST')===$get('DB_HOST')) {
    fwrite(STDERR,"REFUSED: source and target database must be separate.\n"); exit(2);
}
$pdo=static function(string $host,string $db,string $user,string $password):PDO{
    if (!preg_match('/^[a-zA-Z0-9_.:-]+$/D',$host) ||
        !preg_match('/^[a-zA-Z0-9_-]+$/D',$db)) throw new RuntimeException('Invalid connection parameters');
    return new PDO("mysql:host=".$host.";dbname=".$db.";charset=utf8mb4",
        $user,$password,[
            PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES=>false
        ]);
};
try {
    $old=$pdo($get('NETVERA_SOURCE_DB_HOST'),$get('NETVERA_SOURCE_DB_NAME'),
        $get('NETVERA_SOURCE_DB_USER'),$get('NETVERA_SOURCE_DB_PASSWORD'));
    $new=$pdo($get('DB_HOST'),$get('DB_NAME'),$get('DB_USER'),$get('DB_PASS'));
    $tables=['admin_users','customers','orders','order_items','affiliate_accounts','affiliate_commissions','support_tickets','support_ticket_replies','affiliate_clicks','order_referrals'];
    $counts=[];
    foreach($tables as $table) {
        $counts[$table]=(int)$old->query('SELECT COUNT(*) FROM `'.$table.'`')->fetchColumn();
    }
    $selected=$old->prepare("SELECT COUNT(*) FROM admin_users WHERE LOWER(email)=?");
    $verifiedAdminEmail=strtolower($get('NETVERA_ADMIN_EMAIL'));
    if(!filter_var($verifiedAdminEmail,FILTER_VALIDATE_EMAIL)) {
        throw new RuntimeException('Set NETVERA_ADMIN_EMAIL securely in the import environment');
    }
    $selected->execute([$verifiedAdminEmail]);
    $adminFound=(int)$selected->fetchColumn()===1;
    $newCount=(int)$new->query('SELECT COUNT(*) FROM users')->fetchColumn();
    $newAdminCount=(int)$new->query('SELECT COUNT(*) FROM admins')->fetchColumn();
    $report=['mode'=>$apply?'STAGING_APPLY':'DRY_RUN','source_counts'=>$counts,
        'old_superadmin_account_found'=>$adminFound,
        'target_users_before'=>$newCount,'target_admins_before'=>$newAdminCount,
        'will_delete_existing_users'=>false,'will_touch_native_orders'=>false];
    echo json_encode($report,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR)."\n";
    if(!$adminFound) throw new RuntimeException('Expected original Netvera admin not found; refuse migration');
    if(!$apply){echo "SAFE: no target rows modified in dry run.\n";exit(0);}

    // A different database connection is not sufficient: production source is
    // permitted READ ONLY; destination is deliberately staging-only.
    $secret=$get('NETVERA_MIGRATION_SECRET');
    if(strlen($secret)<32) throw new RuntimeException('NETVERA_MIGRATION_SECRET must be 32+ random characters');
    $key=hash('sha256',$secret,true);
    $encrypt=static function(?string $plain) use ($key):?string {
        if(!$plain) return null;
        $iv=random_bytes(12);$tag='';
        $cipher=openssl_encrypt($plain,'aes-256-gcm',$key,OPENSSL_RAW_DATA,$iv,$tag);
        if($cipher===false)throw new RuntimeException('Private entitlement encryption failed');
        return base64_encode($iv.$tag.$cipher);
    };
    $migration=file_get_contents($base.'/database/migrations/netvera-private-customer-v1.sql');
    if($migration===false)throw new RuntimeException('Private target migration missing');
    foreach(explode(';',preg_replace('/^\s*--[^\r\n]*(?:\r?\n|$)/m','',$migration)) as $statement){
        $statement=trim($statement);if($statement==='')continue;
        if(!preg_match('/^CREATE TABLE IF NOT EXISTS nv_private_[a-z_]+/i',$statement))
            throw new RuntimeException('Unsafe migration DDL');
        $new->exec($statement);
    }

    $one=static function(PDO $db,string $sql,array $params):?array{
        $stmt=$db->prepare($sql);$stmt->execute($params);
        return $stmt->fetch()?:null;
    };
    $put=static function(PDO $db,string $sql,array $params):void{
        $stmt=$db->prepare($sql);$stmt->execute($params);
    };
    $all=static function(PDO $db,string $sql):array{
        return $db->query($sql)->fetchAll();
    };
    $new->beginTransaction();
    $userMap=[];
    $collisions=0;
    foreach($all($old,'SELECT * FROM customers') as $row){
        $email=mb_strtolower(trim((string)$row['email']),'UTF-8');
        if(!filter_var($email,FILTER_VALIDATE_EMAIL))throw new RuntimeException('Invalid legacy customer email');
        if(!password_get_info((string)$row['password_hash'])['algo'])
            throw new RuntimeException('Legacy password algorithm unsupported (customer ID '.(int)$row['id'].')');
        $found=$one($new,'SELECT id FROM users WHERE email=?',[$email]);
        if($found){
            $prior=$one($new,'SELECT new_user_id FROM nv_private_user_map WHERE old_customer_id=?',[(int)$row['id']]);
            $knownUserId=(int)($prior['new_user_id']??0);
            if($knownUserId>0 && $knownUserId!==(int)$found['id'])
                throw new RuntimeException('Old-to-new customer mapping changed unexpectedly');
            // A mapped account can be safely imported again. A new collision
            // requires an independent manual identity verification first.
            if($knownUserId===0 && $get('NETVERA_MERGE_EXISTING_EMAILS')!=='1')
                throw new RuntimeException('Unverified email collision; do not assign purchases without identity review');
            if($knownUserId===0) $collisions++;
            $userId=(int)$found['id'];
        }else{
            $oldStatus=(string)$row['status'];
            $status=(int)$row['is_active']===0?'inactive':($oldStatus==='banned'?'banned':($oldStatus==='suspended'?'inactive':'active'));
            $put($new,'INSERT INTO users (name,email,phone,password,status,email_verified_at,created_at) VALUES (?,?,?,?,?,?,?)',[
                mb_substr((string)$row['name'],0,100),$email,(string)$row['phone'],(string)$row['password_hash'],
                $status,$row['email_verified_at'],$row['created_at']
            ]);
            $userId=(int)$new->lastInsertId();
        }
        $oldId=(int)$row['id'];$userMap[$oldId]=$userId;
        $put($new,'INSERT INTO nv_private_user_map (old_customer_id,new_user_id,is_agency,want_dealer,source_status,source_profile_encrypted) VALUES (?,?,?,?,?,?) ON DUPLICATE KEY UPDATE new_user_id=VALUES(new_user_id),is_agency=VALUES(is_agency),want_dealer=VALUES(want_dealer),source_status=VALUES(source_status),source_profile_encrypted=VALUES(source_profile_encrypted)',[
            $oldId,$userId,(int)$row['is_agency'],(int)$row['want_dealer'],(string)$row['status'],
            $encrypt(json_encode(array_diff_key($row,array_flip(['password_hash','email_verify_code','sms_verify_code'])),JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR))
        ]);
    }
    $adminRows=$all($old,'SELECT id,name,email,password_hash,role,is_active FROM admin_users');
    foreach($adminRows as $a){
        if((int)$a['is_active']!==1)continue;
        $email=mb_strtolower(trim((string)$a['email']),'UTF-8');
        if(!filter_var($email,FILTER_VALIDATE_EMAIL) ||
            !password_get_info((string)$a['password_hash'])['algo'])
            throw new RuntimeException('Invalid source admin login or password hash');
        $existing=$one($new,'SELECT id FROM admins WHERE email=?',[$email]);
        // Always retain existing fallback admins; no bulk deletion or lockout.
        if($existing){
            $adminId=(int)$existing['id'];
            $put($new,'UPDATE admins SET name=?,password=?,role=?,status=?,must_change_password=1 WHERE id=?',[
                (string)$a['name'],(string)$a['password_hash'],(string)$a['role'],'active',$adminId
            ]);
        }else{
            $put($new,'INSERT INTO admins (name,email,password,role,status,must_change_password) VALUES (?,?,?,?,?,1)',[
                (string)$a['name'],$email,(string)$a['password_hash'],(string)$a['role'],'active'
            ]);
            $adminId=(int)$new->lastInsertId();
        }
        $put($new,'INSERT INTO nv_private_admin_map (old_admin_id,new_admin_id) VALUES (?,?) ON DUPLICATE KEY UPDATE new_admin_id=VALUES(new_admin_id)',[(int)$a['id'],$adminId]);
    }
    foreach($all($old,'SELECT * FROM orders') as $row){
        $oldCustomer=$row['customer_id']===null?null:(int)$row['customer_id'];
        // Guest purchases without an old customer ID remain unclaimed; never
        // grant product rights based only on potentially unverified email.
        $userId=$oldCustomer===null?null:($userMap[$oldCustomer]??null);
        $put($new,'INSERT INTO nv_private_orders (old_order_id,old_customer_id,new_user_id,customer_name,customer_email,customer_phone,source_order_json_encrypted,order_no,product_id,product_name,amount,currency,payment_status,order_status,order_type,entitlement_json,license_key_encrypted,paid_at,created_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE new_user_id=VALUES(new_user_id),customer_email=VALUES(customer_email),source_order_json_encrypted=VALUES(source_order_json_encrypted),payment_status=VALUES(payment_status),order_status=VALUES(order_status),entitlement_json=VALUES(entitlement_json),license_key_encrypted=VALUES(license_key_encrypted)',[
            (int)$row['id'],$oldCustomer,$userId,$row['customer_name'],$row['customer_email'],$row['customer_phone'],
            $encrypt(json_encode($row,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR)),
            (string)$row['order_no'],$row['product_id'],
            $row['product_name'],$row['amount'],(string)$row['currency'],(string)$row['payment_status'],
            (string)$row['order_status'],(string)$row['order_type'],$encrypt($row['entitlements_json']),
            $encrypt($row['license_key']),(string)$row['paid_at']?:null,(string)$row['created_at']?:null
        ]);
    }
    foreach($all($old,'SELECT id,order_id,item_type,item_id,item_name,item_slug,sale_price,entitlements_json FROM order_items') as $row){
        $put($new,'INSERT INTO nv_private_order_items (old_item_id,old_order_id,item_type,item_id,item_name,item_slug,sale_price,entitlement_json) VALUES (?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE entitlement_json=VALUES(entitlement_json)',[
            (int)$row['id'],(int)$row['order_id'],(string)$row['item_type'],(int)$row['item_id'],
            (string)$row['item_name'],$row['item_slug'],$row['sale_price'],$encrypt($row['entitlements_json'])
        ]);
    }
    foreach($all($old,'SELECT id,customer_id,referral_code,status,commission_rate FROM affiliate_accounts') as $row){
        $cid=(int)$row['customer_id'];
        $put($new,'INSERT INTO nv_private_affiliates (old_affiliate_id,old_customer_id,new_user_id,referral_code,status,commission_rate) VALUES (?,?,?,?,?,?) ON DUPLICATE KEY UPDATE new_user_id=VALUES(new_user_id),status=VALUES(status),commission_rate=VALUES(commission_rate)',[
            (int)$row['id'],$cid,$userMap[$cid]??null,$row['referral_code'],(string)$row['status'],$row['commission_rate']
        ]);
    }
    // Optional operational dealer program. A legacy 'approved' status is only
    // historical evidence; never auto-approve payouts or commissions on new site.
    $dealerTables=$new->query("SHOW TABLES LIKE 'nv_dealer_accounts'")->fetchColumn();
    if($dealerTables){
        foreach($all($old,'SELECT id,customer_id,referral_code,status FROM affiliate_accounts') as $legacyDealer){
            $oldCustomerId=(int)$legacyDealer['customer_id'];
            $newUserId=$userMap[$oldCustomerId]??null;
            if(!$newUserId)continue;
            $existing=$one($new,'SELECT id FROM nv_dealer_accounts WHERE user_id=?',[$newUserId]);
            if($existing)continue;
            $refCode='NV'.strtoupper(bin2hex(random_bytes(6)));
            $put($new,"INSERT INTO nv_dealer_accounts (user_id,legacy_affiliate_id,referral_code,status,tier,commission_rate,note) VALUES (?,?,?,'pending','starter',0,?)",[
                $newUserId,(int)$legacyDealer['id'],$refCode,'Eski NetVera bayilik hesabi, yonetici incelemesi bekliyor'
            ]);
            $dealerId=(int)$new->lastInsertId();
            $put($new,"INSERT INTO nv_dealer_audit (dealer_id,actor_type,actor_id,action,new_status,details) VALUES (?,'system',0,'legacy_import','pending',?)",[
                $dealerId,'Eski bayi kaydi aktarildi, yeni hak edis baslatilmadi'
            ]);
        }
    }
    foreach($all($old,'SELECT id,affiliate_id,order_id,amount,rate,status,paid_at,created_at FROM affiliate_commissions') as $row){
        $put($new,'INSERT INTO nv_private_affiliate_commissions (old_commission_id,old_affiliate_id,old_order_id,amount,rate,status,paid_at,created_at) VALUES (?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE status=VALUES(status),paid_at=VALUES(paid_at)',[
            (int)$row['id'],(int)$row['affiliate_id'],(int)$row['order_id'],$row['amount'],$row['rate'],
            (string)$row['status'],$row['paid_at'],$row['created_at']
        ]);
    }

    foreach($all($old,'SELECT id,customer_id,subject,initial_message,related_order_id,priority,status,created_at,updated_at FROM support_tickets') as $row){
        $cid=$row['customer_id']===null?null:(int)$row['customer_id'];
        $put($new,'INSERT INTO nv_private_support_tickets (old_ticket_id,old_customer_id,new_user_id,subject,initial_message,old_order_id,priority,status,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE new_user_id=VALUES(new_user_id)',[
            (int)$row['id'],$cid,$cid===null?null:($userMap[$cid]??null),$row['subject'],
            $row['initial_message'],$row['related_order_id'],$row['priority'],$row['status'],
            $row['created_at'],$row['updated_at']
        ]);
    }
    foreach($all($old,'SELECT id,ticket_id,sender_type,message,created_at FROM support_ticket_replies') as $row){
        $put($new,'INSERT INTO nv_private_support_replies (old_reply_id,old_ticket_id,sender_type,message,created_at) VALUES (?,?,?,?,?) ON DUPLICATE KEY UPDATE message=VALUES(message)',[
            (int)$row['id'],(int)$row['ticket_id'],$row['sender_type'],$row['message'],$row['created_at']
        ]);
    }
    foreach($all($old,'SELECT id,affiliate_id,visitor_token,landing_path,clicked_at FROM affiliate_clicks') as $row){
        $put($new,'INSERT INTO nv_private_affiliate_clicks (old_click_id,old_affiliate_id,visitor_token,landing_path,clicked_at) VALUES (?,?,?,?,?) ON DUPLICATE KEY UPDATE landing_path=VALUES(landing_path)',[
            (int)$row['id'],(int)$row['affiliate_id'],$row['visitor_token'],$row['landing_path'],$row['clicked_at']
        ]);
    }
    foreach($all($old,'SELECT order_id,affiliate_id,visitor_token,attributed_at FROM order_referrals') as $row){
        $put($new,'INSERT INTO nv_private_order_referrals (old_order_id,old_affiliate_id,visitor_token,attributed_at) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE attributed_at=VALUES(attributed_at)',[
            (int)$row['order_id'],(int)$row['affiliate_id'],$row['visitor_token'],$row['attributed_at']
        ]);
    }
    $new->commit();
    echo json_encode(['status'=>'STAGING_IMPORTED','users_mapped'=>count($userMap),
        'existing_users_merged'=>$collisions,'old_users_deleted'=>0,
        'native_orders_modified'=>0,'old_admins_retained'=>true],
        JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)."\n";
} catch(Throwable $error){
    if(isset($new) && $new->inTransaction())$new->rollBack();
    // No raw personal data, keys, payment references or SQL query values in CLI output.
    fwrite(STDERR,"Private import stopped safely (".get_class($error)."). No customer data logged.\n");
    exit(1);
}
