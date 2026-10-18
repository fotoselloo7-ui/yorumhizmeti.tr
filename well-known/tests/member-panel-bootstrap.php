<?php
/** One-time canonical installation + synthetic member fixtures. Refuses existing databases. */
declare(strict_types=1);
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
$root=dirname(__DIR__);$config=parse_ini_file($root.'/.env');$name='netvera_member_qa_20261018';
$pdo=new PDO('mysql:host='.$config['DB_HOST'].';charset=utf8mb4',$config['DB_USER'],$config['DB_PASS']??'',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
$q=$pdo->prepare('SELECT SCHEMA_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME=?');$q->execute([$name]);
if($q->fetchColumn()){fwrite(STDERR,"Member QA database exists; refusing to overwrite.\n");exit(2);}
$pdo->exec('CREATE DATABASE '.$name.' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');$pdo->exec('USE '.$name);
$pdo->exec(file_get_contents($root.'/database/database.sql'));
$pdo->exec("UPDATE payment_gateways SET is_active=0");
$pdo->exec("UPDATE settings SET setting_value='' WHERE setting_key IN ('smtp_host','smtp_username','smtp_password')");
$pdo->prepare('UPDATE admins SET email=?,password=?,must_change_password=0 WHERE id=1')->execute(['member-admin@example.test',password_hash('synthetic-member-qa-only',PASSWORD_DEFAULT)]);
$users=[];
foreach([['Elif Yılmaz','member-owner@example.test'],['Deniz Test','member-other@example.test']]as [$label,$email]){
 $pdo->prepare("INSERT INTO users(name,email,password,phone,status) VALUES(?,?,?,'','active')")->execute([$label,$email,password_hash('synthetic-member-qa-only',PASSWORD_DEFAULT)]);$users[]=(int)$pdo->lastInsertId();
}
$pkg=$pdo->query("SELECT id,slug FROM packages WHERE status='active' ORDER BY id LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$orders=[];
foreach(range(1,14)as $i){
 $status=['processing','payment_pending','waiting_customer_info','completed','cancelled','paid','preparing'][$i%7];
 if($i===1)$status='processing';if($i===2)$status='payment_pending';if($i===3)$status='waiting_customer_info';
 $pay=$status==='payment_pending'?'pending':($status==='cancelled'?'failed':'paid');
 $pdo->prepare('INSERT INTO orders(user_id,order_number,total_amount,payment_method,payment_status,order_status,customer_visible_note,admin_note,created_at) VALUES(?,?,?,?,?,?,?,?,DATE_SUB(NOW(),INTERVAL ? HOUR))')
  ->execute([$users[0],sprintf('MEMBER-QA-%03d',$i),100+$i,'bank_transfer',$pay,$status,$i===3?'İşleme devam edebilmemiz için profil bağlantınızı destek talebinden iletin.':null,'SYNTHETIC-STAFF-NOTE-NOT-PUBLIC',$i]);
 $id=(int)$pdo->lastInsertId();$orders[]=$id;
 $pdo->prepare('INSERT INTO order_items(order_id,package_id,package_name,quantity,price,total) VALUES(?,?,?,1,?,?)')->execute([$id,$pkg['id'],$i===1?'Instagram Takipçi · 1.000 Takipçi':'Sentetik dijital hizmet paketi '.$i,100+$i,100+$i]);
 $item=(int)$pdo->lastInsertId();
 $pdo->prepare("INSERT INTO order_status_logs(order_id,new_status,note,created_by) VALUES(?,?,'SYNTHETIC-INTERNAL-LOG-NOT-PUBLIC','system')")->execute([$id,$status]);
 if($i===1){
  $pdo->prepare("INSERT INTO smm_providers(name,endpoint,api_key_enc,is_active) VALUES('Synthetic inactive provider','https://provider.example.invalid','synthetic-not-a-key',0)")->execute();$provider=(int)$pdo->lastInsertId();
  $pdo->prepare("INSERT INTO smm_order_jobs(order_id,order_item_id,package_id,provider_id,external_service_id,quantity,target_link,state,remains,last_checked_at,last_error) VALUES(?,?,?,?,'synthetic-service',1000,'https://www.instagram.com/synthetic.qa/','in_progress',350,NOW(),'SYNTHETIC-PROVIDER-ERROR-NOT-PUBLIC')")->execute([$id,$item,$pkg['id'],$provider]);
 }
}
$pdo->prepare("INSERT INTO orders(user_id,order_number,total_amount,payment_status,order_status) VALUES(?,'MEMBER-FOREIGN-001',999999,'paid','completed')")->execute([$users[1]]);$foreign=(int)$pdo->lastInsertId();
$pdo->prepare("INSERT INTO support_tickets(user_id,order_id,ticket_number,subject,status) VALUES(?,?,'MEMBER-TICKET-001','Siparişimin teslimatı hakkında bilgi','answered')")->execute([$users[0],$orders[0]]);$ticket=(int)$pdo->lastInsertId();
$pdo->prepare("INSERT INTO support_messages(ticket_id,sender_type,sender_id,message) VALUES(?,'user',?,'Siparişimin teslimatını nereden takip edebilirim?')")->execute([$ticket,$users[0]]);
$pdo->prepare("INSERT INTO support_messages(ticket_id,sender_type,sender_id,message) VALUES(?,'admin',1,'Merhaba Elif, sipariş detayınızdaki teslimat takibinden son bildirilen durumu görebilirsiniz.')")->execute([$ticket]);
$pdo->prepare("INSERT INTO support_tickets(user_id,ticket_number,subject,status) VALUES(?,'MEMBER-FOREIGN-T','Başka üyeye ait özel destek kaydı','closed')")->execute([$users[1]]);
$pdo->prepare("INSERT INTO nv_private_orders(old_order_id,new_user_id,order_no,product_name,amount,payment_status,order_status,order_type,entitlement_json,paid_at,created_at) VALUES(990018,?,'SYNTHETIC-OLD-001','NetVera Sentetik Yazılım',3900,'paid','completed','software','{\"fixture\":true}',NOW(),NOW())")->execute([$users[0]]);
$pdo->exec("INSERT INTO nv_private_order_items(old_item_id,old_order_id,item_type,item_id,item_name,item_slug,sale_price) VALUES(990018,990018,'software',1,'NetVera Sentetik Yazılım','netvera-emlak-script-yazilimi-pro',3900)");
echo "Created isolated canonical Member QA database; 2 synthetic members, 15 orders, delivery and software fixtures. Payments and SMTP disabled.\n";
