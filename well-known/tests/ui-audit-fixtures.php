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
$popup=$pdo->query("SELECT id FROM marketing_popups WHERE internal_name='UI-AUDIT-POPUP'")->fetchColumn();
if(!$popup){
 $pdo->exec("INSERT INTO marketing_popups (internal_name,title,description,image,image_alt,primary_button_text,primary_button_url,page_mode,path_rules,open_delay_ms,reopen_hours) VALUES ('UI-AUDIT-POPUP','Sentetik arayüz testi','Mobil ve masaüstü popup sınırlarını doğrulayan test içeriği.','/assets/img/slider/agency-software-v95.webp','Sentetik popup görsel kontrolü','Hizmetleri incele','/kategoriler','include','/sss',0,0)");
 $popup=$pdo->lastInsertId();
}
$pdo->prepare('UPDATE marketing_popups SET status=? WHERE id=?')->execute([in_array('--popup',$argv,true)?'active':'inactive',$popup]);
$inquiry=$pdo->query("SELECT id FROM nv_public_inquiries WHERE visitor_contact='ui-inquiry@example.test'")->fetchColumn();
if(!$inquiry){
 $pdo->prepare('INSERT INTO nv_public_inquiries (source_type,visitor_name,visitor_contact,inquiry_text,session_hash) VALUES (?,?,?,?,?)')->execute(['chat','Sentetik UI Ziyaretçisi','ui-inquiry@example.test','Sentetik destek ekranı kontrolü',hash('sha256','ui-audit-synthetic-session')]);
 $inquiry=$pdo->lastInsertId();
 $pdo->prepare('INSERT INTO nv_public_inquiry_replies (inquiry_id,sender,message) VALUES (?,?,?)')->execute([$inquiry,'visitor','Sentetik destek ekranı kontrolü']);
}
echo json_encode(['order'=>$order,'ticket'=>$pdo->query("SELECT id FROM support_tickets WHERE ticket_number='UI-TICKET-001'")->fetchColumn(),'demo'=>'synthetic only']).PHP_EOL;
