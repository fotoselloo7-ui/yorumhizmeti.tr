<?php
declare(strict_types=1);
define('BASE_PATH',dirname(__DIR__));
require_once BASE_PATH.'/app/Services/ServiceIntentFaqService.php';
use App\Services\ServiceIntentFaqService as Intent;
$base=json_decode((string)file_get_contents(BASE_PATH.'/database/netvera-social-package-baseline.json'),true,512,JSON_THROW_ON_ERROR);
$assert=static function(bool $b,string $message):void{
    if(!$b){fwrite(STDERR,"FAIL: ".$message."\n");exit(1);}
};
$checked=0;$answers=[];
foreach($base['categories'] as $category){
    if($category['parent_legacy_id']===null)continue;
    $faq=Intent::category($category);
    $assert(count($faq)>=4,'At least four topical FAQs: '.$category['slug']);
    $questions=[];
    foreach($faq as $item){
        $question=trim((string)($item['question']??''));
        $answer=trim((string)($item['answer']??''));
        $assert(mb_strlen($question,'UTF-8')>=20 && mb_strlen($answer,'UTF-8')>=100,
            'Short question or answer in '.$category['slug']);
        $assert(!isset($questions[$question]),'Repeated question '.$question);
        $questions[$question]=true;
    }
    $answers[$category['slug']]=array_column($faq,'question');
    $checked++;
}
$assert($checked===49,'Expected all 49 social subcategories with tailored FAQs');
$assert($answers['tiktok-takipci']!==$answers['instagram-reels-izlenme'],
    'Different purchase intents must have different questions');
$assert($answers['instagram-begeni']!==$answers['youtube-abone'],
    'Different social service types must have different questions');
foreach([
 'seo-hizmetleri'=>['SEO Hizmetleri','seo-hizmetleri'],
 'yerel-seo'=>['Yerel SEO','yerel-seo'],
 'google-ads'=>['Google Ads','google-ads'],
 'mobil-uygulama-hizmetleri'=>['Mobil Uygulama Hizmetleri','mobil-uygulama-hizmetleri'],
 'grafik-tasarim'=>['Grafik Tasarım','grafik-tasarim'],
] as $slug=>[$name,$value]){
    $faq=Intent::category(['name'=>$name,'slug'=>$value]);
    $assert(count($faq)>=4,'Professional category intent FAQ: '.$slug);
}
$script=(string)file_get_contents(BASE_PATH.'/bin/netvera-market-publish.php');
foreach(['--ack=MARKET-PUBLIC-CATALOG',"SET status='active'",'SocialStorefrontService::save',
          'ServicePackageSeoCompleteService::apply','CategorySeoCompleteService::apply',
          "'quote'","market-category-seo-before-"] as $needle){
    $assert(str_contains($script,$needle),'Missing safe activation/SEO step: '.$needle);
}
$assert(!str_contains($script,'UPDATE packages SET price='),'Market activation must never edit prices');
$store=(string)file_get_contents(BASE_PATH.'/app/Services/SocialStorefrontService.php');
$assert(str_contains($store,"ServiceIntentFaqService::category"),'Social FAQ not used');
$assert(str_contains($store,'Sosyal medya platformlarının otomatik denetimi'),
    'Social editorial legal context missing');
$general=(string)file_get_contents(BASE_PATH.'/app/Services/ServiceEditorialBlocks.php');
$assert(str_contains($general,'ServiceIntentFaqService::category'),'Professional intent FAQs not used');
$detail=(string)file_get_contents(BASE_PATH.'/resources/views/frontend/social/package.php');
$assert(str_contains($detail,'if($nvQuote||$closed)'),'Unverified active social packages must not show buy form');
$meta=(string)file_get_contents(BASE_PATH.'/app/Controllers/PackageController.php');
$assert(str_contains($meta,"'noindex,follow'"),'Draft detail pages must stay noindex until fulfilled');
echo "PASS: 49 targeted social FAQs, professional intent, safe public activation, visible editorial, preserved purchase guard and SEO backups\n";
