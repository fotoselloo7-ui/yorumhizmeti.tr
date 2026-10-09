<?php
/** Real HTTP regression for isolated Netvera inbox. No live Telegram, users or payments. */
declare(strict_types=1);
if(PHP_SAPI!=='cli')exit(2);
$base=(string)(getenv('QA_ORIGIN')?:'http://127.0.0.1:8917');
$cookie=tempnam(sys_get_temp_dir(),'nv-chat-a');
$other=tempnam(sys_get_temp_dir(),'nv-chat-b');
register_shutdown_function(static function()use($cookie,$other):void{@unlink($cookie);@unlink($other);});
function httpRequest(string $url,string $cookie,?array $data=null):array {
    $ch=curl_init($url);
    $options=[
        CURLOPT_RETURNTRANSFER=>true,CURLOPT_FOLLOWLOCATION=>false,CURLOPT_TIMEOUT=>9,
        CURLOPT_COOKIEJAR=>$cookie,CURLOPT_COOKIEFILE=>$cookie
    ];
    if($data!==null){
        $options[CURLOPT_POST]=true;
        $options[CURLOPT_POSTFIELDS]=http_build_query($data);
        $options[CURLOPT_HTTPHEADER]=['Content-Type: application/x-www-form-urlencoded'];
    }
    curl_setopt_array($ch,$options);
    $body=curl_exec($ch);
    $status=(int)curl_getinfo($ch,CURLINFO_HTTP_CODE);
    curl_close($ch);
    if($body===false)throw new RuntimeException('HTTP request failed');
    return [$status,$body];
}
function test(bool $value,string $message):void {
    if(!$value)throw new RuntimeException('FAIL: '.$message);
}
try{
    [$pageStatus,$page]=httpRequest($base.'/hazir-scriptler/haber-sitesi-scripti',$cookie);
    test($pageStatus===200,'product page status');
    test(str_contains($page,'Teklif Talebi Gönder'),'quote form visible on real product');
    test(str_contains($page,'Canlı Destek'),'sitewide chat widget visible');
    test(preg_match('/name="_csrf_token" value="([0-9a-f]{64})"/',$page,$m)===1,'CSRF token in session');
    $csrf=$m[1];
    [$status,$body]=httpRequest($base.'/netvera/canli-destek/gonder',$cookie,[
        '_csrf_token'=>$csrf,'source_type'=>'chat','transport'=>'json',
        'name'=>'Automated QA','phone'=>'05551234567',
        'message'=>'Netvera staging CRM end to end test.','website'=>''
    ]);
    $result=json_decode($body,true);
    test($status===200 && ($result['ok']??false)===true,
        'chat created, HTTP '.$status.' response='.mb_substr(strip_tags($body),0,300));
    $id=(int)($result['inquiry_id']??0);
    test($id>0,'created chat ID');
    [$status,$body]=httpRequest($base.'/netvera/canli-destek/mesajlar?id='.$id,$cookie);
    $visible=json_decode($body,true);
    test($status===200 && count($visible['messages']??[])===1,'owner sees first message');
    [$status,$body]=httpRequest($base.'/netvera/canli-destek/mesajlar?id='.$id,$other);
    test($status===404,'separate session cannot read chat');
    [$status,$body]=httpRequest($base.'/netvera/canli-destek/gonder',$cookie,[
        '_csrf_token'=>$csrf,'source_type'=>'chat','transport'=>'json',
        'inquiry_id'=>(string)$id,'message'=>'A second message from the same visitor.'
    ]);
    test($status===200,'visitor can continue existing chat');
    [$status,$body]=httpRequest($base.'/netvera/canli-destek/mesajlar?id='.$id,$cookie);
    test(count(json_decode($body,true)['messages']??[])===2,'two messages saved');
    [$status,$body]=httpRequest($base.'/netvera/canli-destek/gonder',$other,[
        '_csrf_token'=>'invalid','source_type'=>'chat','transport'=>'json',
        'name'=>'Intruder','contact'=>'blocked@example.invalid',
        'message'=>'Attempt to bypass CSRF'
    ]);
    test($status===403,'CSRF rejected');
    [$status,$body]=httpRequest($base.'/netvera/canli-destek/gonder',$cookie,[
        '_csrf_token'=>$csrf,'source_type'=>'offer','transport'=>'json',
        'name'=>'Automated QA','contact'=>'qa@example.invalid',
        'product_slug'=>'haber-sitesi-scripti',
        'message'=>'Request a quote for this product.'
    ]);
    $offer=json_decode($body,true);
    test($status===200 && ($offer['ok']??false)===true,'product quote created');
    [$status,$body]=httpRequest($base.'/admin/netvera-gelen-kutusu',$other);
    test($status===302,'anonymous cannot access admin inbox');
    echo "PASS: real product offer, live chat session, ownership, CSRF, admin boundary.\n";
}catch(Throwable $error){
    fwrite(STDERR,$error->getMessage()."\n");exit(1);
}
