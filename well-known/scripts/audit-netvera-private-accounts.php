<?php
/**
 * PRIVATE, READ-ONLY Netvera account/order/license schema inventory.
 *
 * Usage (only in a trusted CLI environment):
 * NETVERA_SOURCE_DB_HOST=127.0.0.1 NETVERA_SOURCE_DB_NAME=old_netvera \
 * NETVERA_SOURCE_DB_USER=readonly NETVERA_SOURCE_DB_PASSWORD=... \
 * php scripts/audit-netvera-private-accounts.php
 *
 * This intentionally does NOT import users, reset admin credentials, query
 * password fields, or write data to the project. Do not commit its output.
 */
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("CLI only\n");
}
if (!extension_loaded('pdo_mysql')) {
    fwrite(STDERR, "Missing pdo_mysql extension.\n");
    exit(1);
}
$getEnv = static function (string $name): string {
    $v = getenv($name);
    return is_string($v) ? trim($v) : '';
};
$host = $getEnv('NETVERA_SOURCE_DB_HOST');
$name = $getEnv('NETVERA_SOURCE_DB_NAME');
$user = $getEnv('NETVERA_SOURCE_DB_USER');
$secret = getenv('NETVERA_SOURCE_DB_PASSWORD');
$port = $getEnv('NETVERA_SOURCE_DB_PORT');
$auditEmail = strtolower($getEnv('NETVERA_AUDIT_EMAIL'));
if ($auditEmail === '' || !filter_var($auditEmail, FILTER_VALIDATE_EMAIL)) {
    fwrite(STDERR, "Set private NETVERA_AUDIT_EMAIL to the account email you want inspected.\n");
    exit(2);
}
if ($host === '' || $name === '' || $user === '' || !is_string($secret)) {
    fwrite(STDERR, "Set NETVERA_SOURCE_DB_HOST, _NAME, _USER and _PASSWORD in your private shell/environment.\n");
    exit(2);
}
if (!preg_match('/^[A-Za-z0-9_.-]{1,120}$/D', $name) ||
    !preg_match('/^[A-Za-z0-9_.:-]{1,255}$/D', $host) ||
    ($port !== '' && (!ctype_digit($port) || (int)$port<1 || (int)$port>65535))) {
    fwrite(STDERR, "Invalid private connection parameters.\n");
    exit(2);
}
$dsn = "mysql:host=".$host.";dbname=".$name.";charset=utf8mb4";
if ($port !== '') $dsn .= ";port=".$port;
try {
    $db = new PDO($dsn, $user, $secret, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_TIMEOUT => 4,
    ]);
} catch (PDOException $e) {
    fwrite(STDERR, "Unable to connect to the OLD read-only Netvera database (details withheld).\n");
    exit(3);
}
$tables = $db->prepare(
    'SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA=? AND TABLE_TYPE=?'
);
$tables->execute([$name,'BASE TABLE']);
$tableNames = array_fill_keys(array_map('strval',$tables->fetchAll(PDO::FETCH_COLUMN)), true);
$schema = $db->prepare(
    'SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=? AND TABLE_NAME=? ORDER BY ORDINAL_POSITION'
);
$columns = static function (string $table) use ($tableNames,$schema,$name): array {
    if (!isset($tableNames[$table])) return [];
    $schema->execute([$name,$table]);
    return array_map('strval',$schema->fetchAll(PDO::FETCH_COLUMN));
};
$ident = static function (string $value): string {
    return '`'.str_replace('`','``',$value).'`';
};
$safeFields = ['id','name','full_name','email','role','status','is_active','created_at','updated_at','email_verified_at'];
$accounts = [];
foreach (['admins','admin_users','users','customers'] as $table) {
    $col = $columns($table);
    if (!$col) continue;
    $display = array_values(array_intersect($safeFields,$col));
    $emailField = in_array('email',$col,true) ? 'email' : null;
    if ($emailField === null || !$display) {
        $accounts[$table] = ['available_columns'=>$col,'note'=>'Cannot identify accounts without an email column'];
        continue;
    }
    $select = implode(',',array_map($ident,$display));
    // Only explicitly requested account or administrator-role records.
    $target = $db->prepare("SELECT ".$select." FROM ".$ident($table)." WHERE LOWER(".$ident($emailField).")=? LIMIT 3");
    $target->execute([$auditEmail]);
    $matched = $target->fetchAll();
    $entry = ['columns'=>$col,'target_account'=>$matched];
    if (in_array($table,['admins','admin_users'],true)) {
        $entry['admin_accounts'] = $db->query("SELECT ".$select." FROM ".$ident($table)." LIMIT 20")->fetchAll();
    } elseif (in_array('role',$col,true)) {
        $adminQuery = $db->prepare("SELECT ".$select." FROM ".$ident($table)." WHERE ".$ident('role')." IN ('admin','super_admin') LIMIT 20");
        $adminQuery->execute();
        $entry['admin_accounts'] = $adminQuery->fetchAll();
    }
    // No password hashes, sessions, phone numbers, license keys or tokens are retrieved.
    $accounts[$table] = $entry;
}
$related = [];
foreach (array_keys($tableNames) as $table) {
    if (!preg_match('/order|purchas|licen|entitle|activation|invoice|payment|script_product|user_profile|reseller|dealer|bayi|affiliate|wallet|balance|tier|commission|discount|subscription|customer/i',$table)) continue;
    $col = $columns($table);
    // Untrusted legacy names are quoted and remain database-local; metadata only.
    $related[$table] = ['columns'=>$col];
}
ksort($related);
$result = [
    'status'=>'READ_ONLY_SOURCE_AUDIT',
    'source_database'=>$name,
    'table_count'=>count($tableNames),
    'account_tables'=>$accounts,
    'purchase_license_related_tables'=>$related,
    'important'=>'No users, admins, orders or licenses were migrated. Passwords and keys were not read.',
];
echo json_encode($result,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_THROW_ON_ERROR)."\n";
