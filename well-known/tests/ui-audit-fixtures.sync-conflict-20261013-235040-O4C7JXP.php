<?php
/** Synthetic content for the named, disposable UI audit schema only. */
declare(strict_types=1);
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
$config=parse_ini_file(dirname(__DIR__).'/.env');
$pdo=new PDO('mysql:host='.$config['DB_HOST'].';dbname=netvera_ui_audit_20261013;charset=utf8mb4',$config['DB_USER'],$config['DB_PASS']??'',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
$user=$pdo->query("SELECT id FROM users WHERE email='ui-user@example.test'")->fetchColumn();
if(!$user)throw new RuntimeException('Synthetic UI account missing');
$pdo->prepare("INSERT INTO orders (user_id,order_number,total_amount,order_status) VALUES (?,'UI-AUDIT-001',799,'payment_pending') ON DUPLICATE KEY UPDATE user_id=VALUES(user_id)")->execute([$user]);
$order=$pdo->query("SELECT id FROM orders WHERE order_number='UI-AUDIT-001'")->fetchColumn();
if(!(int)$pdo->query('SELECT COUNT(*) FROM order_items WHERE order_id='.(int)$order)->fetchColumn()){
 $pdo->prepare('INSERT INTO order_items (order_id,package_name,quantity,price,total) VALUES (?,?,1,799,799)')->execute([$order,'Sentetik UI hizmet paketi']);
}
$pdo->prepare("INSERT INTO support_tickets (user_id,order_id,ticket_number,subject) VALUES (?,?,'UI-TICKET-001','Sentetik arayüz kontrolü') ON DUPLICATE KEY UPDATE user_id=VALUES(user_id)")->execute([$user,$order]);
$reset=$pdo->query("SELECT id FROM password_resets WHERE token='ui-audit-synthetic-token'")->fetchColumn();
if($reset){$pdo->exec("UPDATE password_resets SET created_at=NOW() WHERE token='ui-audit-synthetic-token'");}
else{$pdo->prepare('INSERT INTO password_resets (email,token,type) VALUES (?,?,?)')->execute(['ui-user@example.test','ui-audit-synthetic-token','user']);}
$row=$pdo->query("SELECT legacy_id,public_json FROM nv_legacy_script_products WHERE slug='netvera-emlak-script-yazilimi-pro'")->fetch(PDO::FETCH_ASSOC);
$data=json_decode($row['public_json'],true,512,JSON_THROW_ON_ERROR);
$data=array_merge($data,[
 'demo_is_active'=>1,'demo_is_public'=>1,'demo_credentials_public'=>in_array('--private-demo',$argv,true)?0:1,
 'demo_admin_url'=>'https://demo.example.test/admin','demo_admin_username'=>'ui-demo-admin@example.test','demo_admin_password'=>'synthetic-public-demo-only'
]);
$pdo->prepare('UPDATE nv_legacy_script_products SET public_json=? WHERE legacy_id=?')->execute([json_encode($data,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR),$row['legacy_id']]);
echo json_encode(['order'=>$order,'ticket'=>$pdo->query("SELECT id FROM support_tickets WHERE ticket_number='UI-TICKET-001'")->fetchColumn(),'demo'=>'synthetic only']).PHP_EOL;
