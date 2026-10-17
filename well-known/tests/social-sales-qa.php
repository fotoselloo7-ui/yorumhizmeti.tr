<?php
/** Offline contract tests; no database, provider or payment request. */
declare(strict_types=1);
define('BASE_PATH',dirname(__DIR__));
define('PUBLIC_PATH',BASE_PATH.'/public');
require BASE_PATH.'/app/Core/Helpers.php';
require BASE_PATH.'/app/Services/SocialPackageService.php';
use App\Services\SocialPackageService as Sale;
$count=0;
$assert=static function(bool $ok,string $message)use(&$count):void { if(!$ok)throw new RuntimeException($message);$count++; };
$p=['id'=>1,'category_id'=>14,'category_name'=>'Instagram Takipçi','name'=>'','seo_title'=>'Instagram Türk Takipçi Başlangıç Paketi','slug'=>'instagram-turk-takipci-100','short_description'=>'100 takipçi için Türk karışık hizmet paketi.'];
$info=Sale::describe($p);
$assert($info['quantity']===100 && $info['unit']==='takipçi','Exact legacy quantity/unit');
$assert($info['service']==='Instagram Türk Takipçi','Separate Turkish follower service');
$assert(Sale::describe(array_replace($p,['slug'=>'instagram-bot-takipci-100','seo_title'=>'Instagram Bot Takipçi']))['service']==='Instagram Bot Takipçi','Admin/catalog bot service without inventing products');
$assert(Sale::describe(array_replace($p,['slug'=>'instagram-dusmeyen-takipci-100','seo_title'=>'Instagram Düşmeyen Takipçi']))['service']==='Instagram Düşmeyen Takipçi','Separate non-drop service');
foreach(['1.000 izlenme için hizmet.','1 000 izlenme için hizmet.','1000 i̇zlenme için hizmet.','1000 İZLENME İÇİN hizmet.'] as $summary){
 $value=Sale::describe(array_replace($p,['short_description'=>$summary]));
 $assert($value['quantity']===1000 && $value['unit']==='izlenme','Unicode/thousand separator amount: '.$summary);
}
$assert(Sale::describe(array_replace($p,['short_description'=>'Başlangıç fiyatı 100 TL.']))['quantity']===0,'Never derive quantity from a price or arbitrary copy');
$options=Sale::validateOptions(['social_quantity'=>'500','social_unit'=>'takipçi','social_service'=>'Instagram Türk Takipçi']);
$assert(Sale::describe($p,$options)['quantity']===500,'Explicit admin amount authoritative');
$assert(!Sale::describe($p,$options,['fulfillment_quantity'=>100])['mapping_matches'],'Provider mismatch closed');
$assert(Sale::describe($p,[],['fulfillment_quantity'=>250])['quantity']===250,'Unconfigured SMM package uses mapped quantity');
foreach([0,-1,100000001,'abc']as $bad){try{Sale::validateOptions(['social_quantity'=>$bad,'social_unit'=>'takipçi','social_service'=>'Instagram']);$okay=false;}catch(RuntimeException $e){$okay=true;}$assert($okay,'Invalid admin amount rejected');}
$view=file_get_contents(BASE_PATH.'/resources/views/frontend/package-detail.php');
$assert(str_contains($view,'empty($socialSale) &&'),'No social package multiplier');
$assert(str_contains($view,'type="submit" name="checkout" value="1"'),'Social Buy Now uses normal CSRF form submission');
$assert(str_contains($view,'elseif(!empty($variantPackages)'),'Non-social variant flow retained');
$controller=file_get_contents(BASE_PATH.'/app/Controllers/CheckoutController.php');
$assert(str_contains($controller,"'field_line_'.\$cartKey"),'Separate same-package customer targets');
echo "PASS $count social package quantity, service hierarchy, admin/mapping and non-social contracts\n";
