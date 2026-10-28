<?php
/** Demo credentials stay private unless ALL THREE checkboxes and the demo link are enabled. */
declare(strict_types=1);
$root=dirname(__DIR__);
$view=(string)file_get_contents($root.'/resources/views/frontend/netvera-script-detail.php');
$form=(string)file_get_contents($root.'/resources/views/admin/netvera-scripts/form.php');
$save=(string)file_get_contents($root.'/app/Controllers/Admin/NetveraScriptController.php');
$check=static function(bool $condition,string $what):void{
    if(!$condition){fwrite(STDERR,'FAIL: '.$what.PHP_EOL);exit(1);}
};
$check(str_contains($form,'id="nvpa-demo-publication-status"') &&
    str_contains($form,'data-nvpa-demo-gate') &&
    str_contains($form,'DEMO girişleri ziyaretçilere kapalı'),
    'Admin must explain unchecked public demo permission');
$check(str_contains($form,'refreshDemoStatus') &&
    str_contains($form,'DEMO girişleri yayına hazır'),
    'Admin live status must update when publication consent changes');
$check(str_contains($save,'demo_credentials_public') &&
    str_contains($save,'$hasDemoName') &&
    str_contains($save,'$newData[\'demo_is_active\']') &&
    str_contains($save,'$newData[\'demo_is_public\']'),
    'Credentials must require all three explicit publication checks');
$check(str_contains($save,'DEMO GİRİŞ BİLGİLERİ YAYINDA DEĞİL') &&
    str_contains($save,'Herkese açık TEST demo giriş bilgileri'),
    'Save feedback must distinguish stored and public credentials');
$check(str_contains($view,'$nv60ShowAccounts=$nv40DemoAllowed') &&
    str_contains($view,"(int)(\$productData['demo_credentials_public']??0)===1"),
    'Public storefront must gate credentials on publication opt-in');
$check(str_contains($view,'Herkese açık demo hesap bilgisi bu ürün için yayınlanmamıştır.'),
    'Public demo panel must not promise non-published accounts');
echo "PASS: explicit demo publication opt-in, understandable admin status and accurate public login panel.\n";
