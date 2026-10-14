<?php
declare(strict_types=1);
namespace App\Services { final class NetveraInquiryService { public static function ready():bool{return false;} } }
namespace {
define('BASE_PATH',dirname(__DIR__));
require BASE_PATH.'/app/Services/NetveraBridgeService.php';
function e($value):string{return htmlspecialchars((string)$value,ENT_QUOTES,'UTF-8');}
function icon(...$args):string{return '';}
function money($value):string{return number_format((float)$value,2);}
function upload_url($value):string{return (string)$value;}
$render=static function(array $flags):string{
 $product=['legacy_id'=>999,'slug'=>'synthetic-ui-product','name'=>'Synthetic software','price'=>100,'description'=>'Synthetic description'];
 $productData=array_merge([
  'demo_is_active'=>1,'demo_is_public'=>1,'demo_credentials_public'=>1,
  'demo_admin_url'=>'https://demo.example.test/admin',
  'demo_admin_username'=>'ui-demo@example.test','demo_admin_password'=>'synthetic-demo-only'
 ],$flags);
 $demoUrl='https://demo.example.test';
 $gallery=$modules=$technical=$license=$faqs=$reviews=$productCoupons=[];
 ob_start();require BASE_PATH.'/resources/views/frontend/netvera-script-detail.php';return ob_get_clean();
};
foreach(['demo_is_active','demo_is_public','demo_credentials_public'] as $flag){
 foreach([0,'0',null] as $off){
  $html=$render([$flag=>$off]);
  if(str_contains($html,'ui-demo@example.test')||str_contains($html,'synthetic-demo-only'))throw new \RuntimeException('Credential permission bypass: '.$flag);
 }
}
$html=$render([]);
foreach(['ui-demo@example.test','synthetic-demo-only','class="is-hidden" data-nv62-value data-nv62-secret','aria-pressed="false"','href="https://demo.example.test/admin" target="_blank"'] as $expected){
 if(!str_contains($html,$expected))throw new \RuntimeException('Public demo render contract missing: '.$expected);
}
if(str_contains($html,'<script>'))throw new \RuntimeException('Inline demo event binding returned');
echo "PASS: three independent demo publication gates, masked initial render, admin login target and external event binding. Synthetic values only.\n";
}
