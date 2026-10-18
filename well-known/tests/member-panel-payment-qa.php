<?php
/** Run existing PayTR callback regression once on a fresh member QA schema. No live get-token. */
declare(strict_types=1);
if(PHP_SAPI!=='cli')exit(2);
$memberConfig=parse_ini_file(dirname(__DIR__).'/.env');
$memberPaymentPdo=new PDO('mysql:host='.$memberConfig['DB_HOST'].';dbname=netvera_member_qa_20261018;charset=utf8mb4',$memberConfig['DB_USER'],$memberConfig['DB_PASS']??'',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
if(!$memberPaymentPdo->query("SELECT id FROM users WHERE email='member-owner@example.test'")->fetchColumn())throw new RuntimeException('Synthetic member database required');
if($memberPaymentPdo->query("SELECT id FROM users WHERE email='paytr-qa@example.invalid'")->fetchColumn())throw new RuntimeException('Payment regression requires fresh canonical member QA fixtures; existing records are never removed automatically');
$memberGateway=$memberPaymentPdo->query("SELECT settings,is_active FROM payment_gateways WHERE gateway_key='paytr'")->fetch(PDO::FETCH_ASSOC);
putenv('QA_DB_NAME=netvera_member_qa_20261018');putenv('QA_ORIGIN=http://127.0.0.1:8019');putenv('QA_DB_HOST='.$memberConfig['DB_HOST']);putenv('QA_DB_PASS='.($memberConfig['DB_PASS']??''));
try{require __DIR__.'/paytr-payment-qa.php';}
finally{
 $memberPaymentPdo->prepare("UPDATE payment_gateways SET settings=?,is_active=? WHERE gateway_key='paytr'")->execute([$memberGateway['settings'],$memberGateway['is_active']]);
}
