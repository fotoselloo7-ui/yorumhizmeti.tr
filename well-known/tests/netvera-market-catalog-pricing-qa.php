<?php
declare(strict_types=1);
// No DB, network or external services. Pricing and protected-schema guards.
define('BASE_PATH',dirname(__DIR__));
require_once BASE_PATH.'/app/Services/ServicePriceMode.php';
use App\Services\ServicePriceMode as Mode;
$ok=static function(bool $condition,string $name):void{
    if(!$condition)throw new RuntimeException($name);
};
$source=json_decode((string)file_get_contents(BASE_PATH.'/database/netvera-market-competitor-samples-2026-10-27.json'),true,512,JSON_THROW_ON_ERROR);
$baseline=json_decode((string)file_get_contents(BASE_PATH.'/database/netvera-social-package-baseline.json'),true,512,JSON_THROW_ON_ERROR);
$ok($source['discount_rate']===0.1,'10% benchmark ratio');
$ok(count($baseline['categories'])===60 && count($baseline['packages'])===185,'Original social snapshot intact');
$ok(count($source['social'])>=7 && count($source['professional'])>=10,'Researched coverage minimum');
$seen=[];
foreach($source['social'] as $entry){
    $ok(str_starts_with($entry['url'],'https://') && count($entry['quantity_price'])>=8,'Social market source insufficient');
    foreach($entry['quantity_price'] as $qty=>$price){
        $k=$entry['category'].'|'.$entry['variant'].'|'.$qty;
        $ok(!isset($seen[$k]) && (int)$qty>0 && (float)$price>0,'Peer quantity/price invalid');
        $seen[$k]=true;
        $calculated=round((float)$price*0.9,2);
        $ok($calculated>0 && $calculated<=(float)$price,'Social proposed discount invalid');
    }
}
$ok(count($seen)>=56,'Exact social peer quantities incomplete');
foreach($source['professional'] as $entry){
    $ok((float)$entry['peer_try']>0 && !empty($entry['scope']) && str_starts_with($entry['source'],'https://'),'Unscoped peer benchmark');
    $ok(round($entry['peer_try']*0.9,2)<$entry['peer_try'],'Professional 10% discount invalid');
    $ok(!preg_match('/^(web|wordpress|mobil-uygulama|ios-android|yazilim|hazir-script)/',$entry['category']),'Protected software/web/mobile accidentally benchmarked');
}
$ok(Mode::normalize('quote')==='quote' && Mode::normalize('discuss')==='discuss' &&
    Mode::normalize('fixed')==='fixed' && Mode::normalize('<script>')==='auto','Admin pricing modes are allowlisted');
$form=file_get_contents(BASE_PATH.'/resources/views/admin/packages/form.php');
foreach(['name="price_mode"','value="fixed"','value="quote"','value="discuss"'] as $needle){
    $ok(str_contains($form,$needle),'Admin mode missing: '.$needle);
}
$controller=file_get_contents(BASE_PATH.'/app/Controllers/Admin/PackageController.php');
$ok(substr_count($controller,'ServicePriceMode::save')===2,'Create/edit modes must both persist');
$policy=file_get_contents(BASE_PATH.'/app/Services/ServiceQuotePolicy.php');
$ok(str_contains($policy,'ServicePriceMode::quote'),'Quote checkout not integrated');
$view=file_get_contents(BASE_PATH.'/resources/views/frontend/package-detail.php');
$ok(str_contains($view,'ServicePriceMode::cta'),'Custom CTA not integrated');
$cart=file_get_contents(BASE_PATH.'/app/Controllers/CartController.php');
$checkout=file_get_contents(BASE_PATH.'/app/Controllers/CheckoutController.php');
$ok(str_contains($cart,'ServiceQuotePolicy::package') &&
    str_contains($checkout,'ServiceQuotePolicy::package'),'Quote checkout protection removed');
$smm=file_get_contents(BASE_PATH.'/app/Services/SocialPackageService.php');
$guard=file_get_contents(BASE_PATH.'/app/Services/MarketProposalGuard.php');
$ok(str_contains($smm,'MarketProposalGuard::ready') &&
    str_contains($guard,'rate_per_1000') && str_contains($guard,'0.70'),'New SMM offers must check supplier and 30% margin');
$script=file_get_contents(BASE_PATH.'/bin/netvera-market-catalog-expand.php');
$ok(str_contains($script,"'inactive','kind'=>'social'") &&
    str_contains($script,"'status'=>'inactive'") &&
    str_contains($script,'--apply-drafts'),'Market proposals must never auto-publish');
echo "PASS: 56+ exact peer quantity prices, 10+ scoped professional offers, safe drafts, admin pricing modes, protected checkout\n";
