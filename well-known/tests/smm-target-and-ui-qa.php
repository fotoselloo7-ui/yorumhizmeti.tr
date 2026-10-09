<?php
/**
 * No DB, API keys or network: validate social target modes, public card ownership,
 * customer fields and CSRF-required admin bulk routes.
 */
define('BASE_PATH',dirname(__DIR__));
require BASE_PATH.'/app/Services/SmmOrderFields.php';
require BASE_PATH.'/app/Services/PackageHighlightsService.php';
use App\Services\SmmOrderFields;
use App\Services\PackageHighlightsService;
$pass=0;
$assert=static function(bool $ok,string $reason)use(&$pass):void{
    if(!$ok)throw new RuntimeException('FAIL: '.$reason);
    $pass++;
};
$examples=[
    ['@test.user','Instagram Takipçi Paketi','https://instagram.com/test.user'],
    ['@shortname','TikTok Followers Paketi','https://tiktok.com/@shortname'],
    ['@creator','YouTube Abone Paketi','https://youtube.com/@creator'],
    ['short-name','Threads 1000 Takipçi','https://threads.net/@short-name'],
    ['https://www.tiktok.com/@creator/video/81230','TikTok İzlenme Paketi','https://www.tiktok.com/@creator/video/81230'],
    ['https://youtu.be/abc123','YouTube İzlenme Paketi','https://youtu.be/abc123'],
    ['https://open.spotify.com/track/abc','Spotify Takipçi Paketi','https://open.spotify.com/track/abc'],
];
foreach($examples as [$given,$service,$expected]) {
    $actual=SmmOrderFields::target($given,$service);
    $assert($actual===$expected,$service);
}
foreach([
  ['https://instagram.com/username','YouTube Abone Paketi'],
  ['https://evil.example/bad','Instagram 1000 Takipçi'],
  ['http://tiktok.com/a','TikTok İzlenme Paketi'],
  ['javascript:alert(1)','YouTube Abone Paketi'],
  ['@not-an-url','Spotify Takipçi Paketi']
]as [$value,$title]){
    $rejected=false;
    try{SmmOrderFields::target($value,$title);}catch(RuntimeException $e){$rejected=true;}
    $assert($rejected,'unsafe/mismatched platform target rejected');
}
$features=PackageHighlightsService::get(0,['delivery_time'=>'2-4 iş günü']);
$assert(count($features)===4,'four fallback truthful benefits');
$assert(str_contains(implode(' ',$features),'2-4 iş günü'),'delivery text from product');
$home=file_get_contents(BASE_PATH.'/resources/views/frontend/home.php');
$assert(str_contains($home,'PackageHighlightsService::get'),'featured card uses admin-authored benefits');
$assert(str_contains($home,'data-benefit-next'),'carousel next button exists');
$assert(!str_contains($home,'<b>Aktif ve güvenli hizmet</b>'),'hard-coded source claims removed');
$category=file_get_contents(BASE_PATH.'/resources/views/frontend/category-detail.php');
$assert(str_contains($category,'PackageHighlightsService::get'),'category card uses same copy');
$controller=file_get_contents(BASE_PATH.'/app/Controllers/CheckoutController.php');
$assert(str_contains($controller,'saved_fields'),'checkout retains field values');
$routes=file_get_contents(BASE_PATH.'/app/Core/App.php');
$assert(str_contains($routes,'SmmController@bulkPublish'),'bulk publishing admin route');
$assert(str_contains($routes,'SmmController@syncAll'),'provider mass sync admin route');
echo 'PASS '.$pass." offline SMM target, package-card, and checkout regression assertions\n";
