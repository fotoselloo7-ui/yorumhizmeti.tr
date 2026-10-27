<?php
declare(strict_types=1);

// Offline regression guard: route precedence, internal links and sitemap URLs.
// No database, network or PHP server required.
$root = dirname(__DIR__);
require_once $root.'/app/Core/Helpers.php';
require_once $root.'/app/Services/PublicSeoUrls.php';
require_once $root.'/app/Services/CatalogMenuService.php';
use App\Services\PublicSeoUrls as Seo;

$assert = static function (bool $condition, string $message): void {
    if (!$condition) throw new RuntimeException($message);
};
$load = static fn(string $file): string => (string)file_get_contents($root.'/'.$file);

$assert(Seo::path('/blog/kategori', 'google-isletme-profili') === '/blog/kategori/google-isletme-profili', 'Blog path incorrect');
$assert(Seo::path('/kategori', 'içerik') === '/kategori/i%C3%A7erik', 'UTF-8 path is not encoded');
$assert(Seo::withQuery('/blog/kategori/google-isletme-profili', ['page'=>2, 'q'=>'seo ipuçları']) === '/blog/kategori/google-isletme-profili?page=2&q=seo%20ipu%C3%A7lar%C4%B1', 'Pagination filters incorrect');
$assert(Seo::withQuery('/blog', ['q'=>'', 'page'=>null]) === '/blog', 'Empty query should be removed');
$assert(Seo::isSlug('google-isletme-profili'), 'Valid slug rejected');
$scriptCategories = [
    ['legacy_id'=>10, 'parent_legacy_id'=>null, 'slug'=>'web-scriptleri'],
    ['legacy_id'=>11, 'parent_legacy_id'=>10, 'slug'=>'haber-scriptleri']
];
$assert(Seo::scriptCategory($scriptCategories[0], $scriptCategories)==='/hazir-scriptler/kategori/web-scriptleri', 'New parent path invalid');
$assert(Seo::scriptCategory($scriptCategories[1], $scriptCategories)==='/hazir-scriptler/web-scriptleri/haber-scriptleri', 'Indexed child path changed');
$assert(!Seo::isSlug('test/../../admin'), 'Unsafe slug accepted');
$assert(Seo::group('social')==='/sosyal-medya-hizmetleri','Social hub SEO slug incorrect');
$assert(Seo::socialService('tiktok-takipci', 'TikTok Kurumsal Takipçi')==='/sosyal-medya-hizmetleri/tiktok-takipci/kurumsal-takipci', 'Social service permalink incorrect');
$assert(Seo::socialService('instagram-takipci','Instagram Türk Takipçi')==='/sosyal-medya-hizmetleri/instagram-takipci/turk-takipci', 'Turkish service permalink incorrect');
$assert(Seo::socialService('tiktok-takipci','TikTok Takipçi')==='/sosyal-medya-hizmetleri/tiktok-takipci','Do not repeat exact category name in service URL');
$assert(Seo::category(['slug'=>'web-tasarim','name'=>'Web Tasarım'],['slug'=>'web-site-hizmetleri','name'=>'Web Site Hizmetleri'])==='/ajans-yazilim-hizmetleri/web-tasarim','Agency category URL');
$assert(Seo::category(['slug'=>'teknik-seo','name'=>'Teknik SEO'],['slug'=>'seo-hizmetleri','name'=>'SEO Hizmetleri'])==='/seo-dijital-pazarlama-hizmetleri/teknik-seo','SEO category URL');
$assert(Seo::group('agency')==='/ajans-yazilim-hizmetleri','Agency hub SEO slug incorrect');
$assert(Seo::group('marketing')==='/seo-dijital-pazarlama-hizmetleri','SEO marketing hub SEO slug incorrect');


$routes = $load('app/Core/App.php');
$required = [
    '/blog/kategori/{slug}', '/blog/etiket/{slug}',
    '/kategori/{slug}', '/paket/{slug}',
    '/hazir-scriptler/kategori/{slug}', '/hazir-scriptler/tur/{slug}',
    '/hazir-yazilimlar/tur/{slug}', '/kategoriler/grup/{slug}',
    '/sosyal-medya-hizmetleri/{categorySlug}/{serviceSlug}',
    '/sosyal-medya-hizmetleri/{slug}', '/ajans-yazilim-hizmetleri/{slug}',
    '/seo-dijital-pazarlama-hizmetleri/{slug}'
];
foreach ($required as $route) {
    $assert(str_contains($routes, "'".$route."'"), 'Missing route: '.$route);
}
$assert(strpos($routes, "'/hazir-scriptler/kategori/{slug}'") < strpos($routes, "'/hazir-scriptler/{mainSlug}/{subSlug}'"), 'Category route shadowed by legacy route');
$assert(strpos($routes, "'/hazir-scriptler/tur/{slug}'") < strpos($routes, "'/hazir-scriptler/{mainSlug}/{subSlug}'"), 'Type route shadowed by legacy route');
foreach (['/hazir-scriptler/{slug}', '/blog/{slug}', '/paket/{slug}'] as $productPath) {
    $assert(str_contains($routes, "'".$productPath."'"), 'Indexed URL structure changed: '.$productPath);
}

$checks = [
    'resources/views/frontend/blog/index.php' => ['/blog/kategori/', 'blogPaginationBase'],
    'resources/views/frontend/blog/detail.php' => ['/blog/kategori/', '/blog/etiket/'],
    'resources/views/frontend/categories.php' => ['PublicSeoUrls::group'],
    'resources/views/frontend/category-detail.php' => ['PublicSeoUrls::category'], 
    'resources/views/frontend/netvera-scripts.php' => ['PublicSeoUrls::scriptCategory', '/hazir-scriptler/tur'],
    'resources/views/frontend/software.php' => ['/hazir-yazilimlar/tur'],
    'resources/views/layouts/app.php' => ['PublicSeoUrls::scriptCategory'],
    'app/Services/SitemapService.php' => ['/blog/kategori', '/hazir-scriptler/kategori'],
];
foreach ($checks as $file=>$needles) {
    $body = $load($file);
    foreach ($needles as $needle) {
        $assert(str_contains($body, $needle), 'Missing canonical URL integration: '.$file.' / '.$needle);
    }
}
foreach ([
    'resources/views/frontend/blog/index.php',
    'resources/views/frontend/blog/detail.php',
    'resources/views/layouts/app.php',
    'resources/views/frontend/home.php',
    'app/Controllers/HomeController.php',
    'app/Services/NavigationService.php',
    'app/Services/SitemapService.php',
] as $file) {
    $body = $load($file);
    foreach (['/blog?category=', '/blog?tag=', '/kategoriler?grup=', '/hazir-scriptler?category=', '/hazir-scriptler?type='] as $legacy) {
        $assert(!str_contains($body, $legacy), 'Legacy navigation remains: '.$file.' / '.$legacy);
    }
}
// The project is new. Legacy GET filters may still render, but must NOT
// create HTTP redirects: direct links already point to the clean routes.
foreach ([
    'app/Controllers/BlogController.php',
    'app/Controllers/CategoryController.php',
    'app/Controllers/SoftwareController.php',
] as $controller) {
    $assert(!str_contains($load($controller), 'redirectLegacyFacet'), 'Unneeded project redirect remains: '.$controller);
}
$scriptController = $load('app/Controllers/NetveraScriptController.php');
$assert(!str_contains($scriptController, 'redirectLegacyFacet'), 'New-project script filter redirect remains');
$assert(str_contains($scriptController, 'function legacyRedirect'), 'Imported NetVera alias handling changed');
$assert(!str_contains($load('app/Services/PublicSeoUrls.php'), 'redirectLegacyFacet'), 'Old 301 helper remains');
$assert(!str_contains($load('app/Controllers/CategoryController.php'),"header('Location:"),'New project category URLs must not redirect');
$assert(str_contains($load('app/Services/SitemapService.php'),'PublicSeoUrls::group'),'Sitemap must use new canonical service group paths');
$assert(str_contains($load('app/Services/SocialPackageService.php'), 'PublicSeoUrls::socialService'), 'Social catalog must link clean service pages');
$assert(!str_contains($load('app/Services/SocialPackageService.php'), "'?hizmet='"), 'Service navigation must not emit filter URLs');
$assert(str_contains($load('app/Controllers/CategoryController.php'),'$categoryCanonical'), 'Clean canonical path must be rendered directly');
$assert(str_contains($load('app/Services/SitemapService.php'),'PublicSeoUrls::socialService'), 'Sitemap missing social service permalinks');
$assert(str_contains($load('resources/views/frontend/social/category.php'),"service-guide.php"), 'Service landing editorial FAQs missing');

echo "PASS: direct category SEO URLs without redirects, original NetVera script and blog paths retained\n";
