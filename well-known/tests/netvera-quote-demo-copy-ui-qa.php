<?php
/** Service quote/checkout card + intentionally public demo login UI regression. */
declare(strict_types=1);
$root=dirname(__DIR__);
$load=static fn(string $p):string=>(string)file_get_contents($root.'/'.$p);
$check=static function(bool $ok,string $label):void{
    if(!$ok){fwrite(STDERR,'FAIL: '.$label.PHP_EOL);exit(1);}
};
$service=$load('resources/views/frontend/package-detail.php');
$styles=$load('public/assets/css/netvera-service-detail-v1.css');
$software=$load('resources/views/frontend/netvera-script-detail.php');
$admin=$load('resources/views/admin/netvera-scripts/form.php');
$script=$load('public/assets/js/netvera-product-tabs-v60.js');
$check(str_contains($service,'yv-product-mini-trust-v5'),'Missing service trust footer');
$check(str_contains($service,'Teslim Planı'),'Delivery label not shortened');
$check(str_contains($styles,'/* FIX: the quote/secure process panel'),'Missing corrected trust layout');
$check(str_contains($styles,'grid-template-columns:minmax(0,1fr)!important'),'Trust items must stack instead of cramming 3 mini columns');
$check(str_contains($styles,'font:750 12px/1.4'),'Mini card headings must remain readable');
$check(str_contains($styles,'font:400 11px/1.55'),'Mini card supporting text must remain readable');
$check(str_contains($software,"demo_credentials_public"),'Demo publication consent gating removed');
$check(str_contains($software,"demo_is_public") && str_contains($software,"demo_is_active"),'Demo visibility gating removed');
$check(str_contains($software,'data-nv62-copy') && str_contains($software,'data-nv62-toggle'),'Copy/reveal controls missing');
$check(str_contains($software,'Yönetici') && str_contains($software,'Kullanıcı') && str_contains($software,'Demo Giriş Bilgileri'),'Different public demo account types not labeled');
$check(str_contains($admin,"demo_credentials_public") && str_contains($admin,'Herkese açık DEMO hesap'),'Missing explicit admin opt-in');
$check(str_contains($script,"[data-nv62-copy]") && str_contains($script,'navigator.clipboard'),'Clipboard interaction missing');
echo "PASS: readable vertical service trust details; public demo account types and explicit publication controls retained.\n";
