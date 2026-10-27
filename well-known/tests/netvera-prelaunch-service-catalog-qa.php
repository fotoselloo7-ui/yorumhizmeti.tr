<?php
declare(strict_types=1);
define('BASE_PATH',dirname(__DIR__));
spl_autoload_register(static function(string $class):void{
    if(!str_starts_with($class,'App\\'))return;
    $path=BASE_PATH.'/app/'.str_replace('\\','/',substr($class,4)).'.php';
    if(is_file($path))require $path;
});
function verify(bool $condition,string $message):void{
    if(!$condition){fwrite(STDERR,"FAIL: ".$message."\n");exit(1);}
}
use App\Services\PublicSeoUrls as Urls;
use App\Services\ServiceQuotePolicy as Quote;
use App\Services\SocialFollowerBenchmarkService as Market;
use App\Services\ServiceEditorialBlocks as Copy;
use App\Services\ServicePackageSeoCompleteService as PackageSeo;

verify(Urls::group('social')==='/sosyal-medya-hizmetleri','social URL');
verify(Urls::group('agency')==='/ajans-yazilim-hizmetleri','agency URL');
verify(Urls::group('marketing')==='/seo-dijital-pazarlama-hizmetleri','SEO URL');
verify(Urls::group('unknown')==='/kategoriler/grup/unknown','unknown group is never guessed');
verify(Quote::needsQuote(['name'=>'SEO Hizmetleri','slug'=>'seo-hizmetleri']), 'professional SEO is quoted');
verify(Quote::needsQuote(['name'=>'Web Site Hizmetleri','slug'=>'web-site-hizmetleri']), 'web development is quoted');
verify(!Quote::needsQuote(['name'=>'Instagram Hizmetleri','slug'=>'instagram-hizmetleri']), 'fixed social prices remain priced');
verify(!Quote::needsQuote(['name'=>'Hazır Yazılım Scriptleri','slug'=>'hazir-yazilim-scriptleri']), 'old ready software category untouched');
verify(!PackageSeo::eligible(['name'=>'NetVera Emlak Script','slug'=>'netvera-emlak-script','category_id'=>22]),'legacy script packages cannot be rewritten');
verify(PackageSeo::eligible(['name'=>'Web Tasarım Danışmanlığı','slug'=>'web-tasarim-danismanligi','category_id'=>84]),'ordinary service package may receive blank SEO fields');

$matched=['name'=>'Instagram Takipçi','slug'=>'instagram-takipci-1000','category_slug'=>'instagram-takipci','price'=>1000,'discount_price'=>null];
$info=['unit'=>'takipçi','quantity'=>1000,'service'=>'Instagram Takipçi'];
$unknownCost=Market::recommend($matched,$info,null);
verify(!empty($unknownCost['eligible'])&&empty($unknownCost['safe_to_apply']), 'unverified wholesale cost cannot change price');
$lowCost=Market::recommend($matched,$info,100);
verify(!empty($lowCost['safe_to_apply'])&&$lowCost['recommended_new_price_try']>100,'verified supplier cost and margin can produce suggestion');
$tooHighCost=Market::recommend($matched,$info,800);
verify(empty($tooHighCost['safe_to_apply']),'loss-making follower price must be blocked');
$incorrect=$matched;$incorrect['slug']='instagram-premium-takipci-1000';
verify(empty(Market::recommend($incorrect,$info,100)['eligible']), 'premium quality must not compare to standard');

$copy=Copy::category(['name'=>'SEO Hizmetleri','slug'=>'seo-hizmetleri'],true);
verify(count($copy['paragraphs'])>=7&&count($copy['faq'])>=4,'service intent contains substantial visible copy and four questions');
verify(str_contains(implode(' ',$copy['paragraphs']),'canonical'),'SEO services have technical, non-generic guidance');
$copy=Copy::package(['name'=>'Kurumsal Web Tasarım','category_name'=>'Web Site Hizmetleri','short_description'=>'Mobil uyumlu kurumsal web tasarım hizmeti'],true);
verify(count($copy['paragraphs'])>=6,'package context has topical value');

$app=file_get_contents(BASE_PATH.'/app/Core/App.php');
$category=file_get_contents(BASE_PATH.'/app/Controllers/CategoryController.php');
$sitemap=file_get_contents(BASE_PATH.'/app/Services/SitemapService.php');
$nav=file_get_contents(BASE_PATH.'/app/Services/NavigationService.php');
$view=file_get_contents(BASE_PATH.'/resources/views/frontend/landing/editorial-deep.php');
$theme=file_get_contents(BASE_PATH.'/resources/views/frontend/landing/themes/page.php');
$packageView=file_get_contents(BASE_PATH.'/resources/views/frontend/package-detail.php');
$cart=file_get_contents(BASE_PATH.'/app/Controllers/CartController.php');
$checkout=file_get_contents(BASE_PATH.'/app/Controllers/CheckoutController.php');
verify(str_contains($app,"'/kategoriler/grup/{slug}'")&&str_contains($app,"'/sosyal-medya-hizmetleri'"),'legacy route and friendly route coexist');
verify(str_contains($category,"true,301")&&str_contains($category,'PublicSeoUrls::group'),'old service group URLs redirect to canonical');
verify(str_contains($sitemap,'PublicSeoUrls::group'),'sitemap lists new group URLs');
verify(str_contains($nav,'PublicSeoUrls::group'),'menu uses friendly group URLs');
verify(str_contains($view,'RegionalEditorialGuideService::fallback'),'existing regional guides render even with empty editor');
verify(str_contains($theme,'editorial-deep.php'),'landing thematic page includes its long guide');
$regionalCss=file_get_contents(BASE_PATH.'/public/assets/css/editorial-landings.css');
verify(str_contains($regionalCss,'.nvl-deep-guide')&&str_contains($regionalCss,'.nvl-deep-layout'),'regional long-form guide needs a visible responsive style');
verify(str_contains($view, 'if($nvLongBody'),'partial editorial edits must not hide the long guide');
verify(str_contains($app,"'/sosyal-medya-hizmetleri/{categorySlug}/{serviceSlug}'"),'indexable social-service route missing');

verify(str_contains($packageView,'quoteMode')&&str_contains($packageView,'Teklif Al'),'quote-only product cannot show a fake fixed retail price');
verify(str_contains($cart,'ServiceQuotePolicy::package')&&str_contains($checkout,'ServiceQuotePolicy::package'),'cart and checkout independently guard scope-quoted purchases');
verify(str_contains($app,"'/admin/paketler/seo-tamamla'"),'admin bulk SEO fill registered');
echo "PASS: three descriptive URLs with legacy 301, ready-script protection, guarded quotes, verified margin, service/purchase SEO and regional editorial guide\n";
