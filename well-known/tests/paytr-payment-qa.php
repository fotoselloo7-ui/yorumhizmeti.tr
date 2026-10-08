<?php
/**
 * CI-only PayTR callback simulation.
 * Fake API keys, no live get-token request and absolutely no real card details.
 */
declare(strict_types=1);
$host=getenv('QA_DB_HOST')?:'127.0.0.1';
$dbname=getenv('QA_DB_NAME')?:'paytr_qa';
$pass=getenv('QA_DB_PASS')?:'';
$origin=rtrim(getenv('QA_ORIGIN')?:'http://127.0.0.1:8006','/');
$pdo=new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4",'root',$pass,
    [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
$assert=static function(bool $okay,string $message):void{
    if(!$okay)throw new RuntimeException($message);
};
$settings=['merchant_id'=>'ci-not-real','merchant_key'=>'ci-only-fake-key',
    'merchant_salt'=>'ci-only-fake-salt','test_mode'=>'1'];
$pdo->prepare("UPDATE payment_gateways SET is_active=1,settings=? WHERE gateway_key='paytr'")
    ->execute([json_encode($settings,JSON_THROW_ON_ERROR)]);
$pdo->prepare("INSERT INTO users (name,email,password) VALUES ('QA PayTR','paytr-qa@example.invalid','not-an-actual-login')")
    ->execute();
$uid=(int)$pdo->lastInsertId();
$orderIds=[];
foreach(['SUCCESS'=>123.45,'FAILED'=>55.00,'AMOUNT'=>88.00,'UNKNOWN'=>37.00] as $type=>$amount){
    if($type==='UNKNOWN')continue;
    $number='QA-PAYTR-'.$type;
    $pdo->prepare("INSERT INTO orders (user_id,order_number,total_amount,payment_method,payment_gateway,payment_status,order_status)
                   VALUES (?,?,?,'online','paytr','pending','payment_pending')")
       ->execute([$uid,$number,$amount]);
    $orderIds[$type]=(int)$pdo->lastInsertId();
}
$send=static function(array $post)use($origin):array{
    $ch=curl_init($origin.'/payment/paytr/callback');
    curl_setopt_array($ch,[
        CURLOPT_POST=>true,
        CURLOPT_POSTFIELDS=>http_build_query($post),
        CURLOPT_RETURNTRANSFER=>true,
        CURLOPT_TIMEOUT=>10,
        CURLOPT_CONNECTTIMEOUT=>3,
    ]);
    $body=curl_exec($ch);
    $code=(int)curl_getinfo($ch,CURLINFO_HTTP_CODE);
    if($body===false)throw new RuntimeException('CI HTTP client failed: '.curl_error($ch));
    curl_close($ch);
    return [$code,trim((string)$body)];
};
$signed=static function(string $oid,string $status,int $amount)use($settings):array{
    $hash=base64_encode(hash_hmac('sha256',
        $oid.$settings['merchant_salt'].$status.$amount,
        $settings['merchant_key'],true));
    return ['merchant_oid'=>$oid,'status'=>$status,
        'total_amount'=>(string)$amount,'hash'=>$hash,'test_mode'=>'1','currency'=>'TL'];
};
$bad=$signed('QA-PAYTR-SUCCESS','success',12345);
$bad['hash']='invalid-tampered-value';
[$code,$body]=$send($bad);
$assert($code===403 && $body!=='OK','Invalid hash incorrectly acknowledged');
$assert((int)$pdo->query("SELECT COUNT(*) FROM payments WHERE gateway_key='paytr'")->fetchColumn()===0,
    'Unauthenticated callback inserted a payment');

$underpaid=$signed('QA-PAYTR-AMOUNT','success',1000);
[$code,$body]=$send($underpaid);
$assert($code===422 && $body!=='OK','Signed underpayment incorrectly acknowledged');

$unknown=$signed('QA-PAYTR-UNKNOWN','success',3700);
[$code,$body]=$send($unknown);
$assert($code===404 && $body!=='OK','Unknown order incorrectly acknowledged');

$failed=$signed('QA-PAYTR-FAILED','failed',0);
[$code,$body]=$send($failed);
$assert($code===200 && $body==='OK','Valid failed notification was not acknowledged');
$stmt=$pdo->query("SELECT payment_status FROM orders WHERE id={$orderIds['FAILED']}");
$assert($stmt->fetchColumn()==='failed','Failed payment did not update pending state');

$good=$signed('QA-PAYTR-SUCCESS','success',12345);
$good['payment_amount']='12345';
[$code,$body]=$send($good);
$assert($code===200 && $body==='OK','Signed success callback failed');
[$code,$body]=$send($good);
$assert($code===200 && $body==='OK','Repeated callback not idempotent');
$q=$pdo->prepare("SELECT COUNT(*) FROM payments WHERE order_id=? AND gateway_key='paytr' AND status='completed'");
$q->execute([$orderIds['SUCCESS']]);
$assert((int)$q->fetchColumn()===1,'Duplicate payment rows created');
$q=$pdo->prepare("SELECT order_status,payment_status FROM orders WHERE id=?");
$q->execute([$orderIds['SUCCESS']]);
$o=$q->fetch(PDO::FETCH_ASSOC);
$assert($o['order_status']==='paid'&&$o['payment_status']==='paid',
    'Valid callback failed to mark the order paid');
$q=$pdo->prepare("SELECT COUNT(*) FROM order_status_logs WHERE order_id=? AND new_status='paid'");
$q->execute([$orderIds['SUCCESS']]);
$assert((int)$q->fetchColumn()===1,'Duplicate paid status logs created');

$ch=curl_init($origin.'/odeme/paytr-onizleme');
curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_TIMEOUT=>10]);
$html=curl_exec($ch);
$code=(int)curl_getinfo($ch,CURLINFO_HTTP_CODE);
curl_close($ch);
$assert($code===200&&is_string($html),'Local payment design preview unavailable');
$assert(str_contains($html,'nv45-checkout')&&str_contains($html,'Yalnızca yerel tasarım önizlemesi'),
    'Branded PayTR preview did not render');
$assert(!str_contains($html,'id="paytriframe"'),
    'Local preview unexpectedly rendered a live card payment iframe');

echo "PASS: hash tampering, amount mismatch, unknown order, failed notification, signed success,
      duplicate callback idempotency and localhost-only PayTR payment page. No live transactions.\n";
