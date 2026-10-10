<?php
declare(strict_types=1);

// Offline regression guard: route precedence, internal links and sitemap URLs.
// No database, network or PHP server required.
$root = dirname(__DIR__);
require_once $root.'/app/Services/PublicSeoUrls.php';
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
$assert(!Seo::isSlug('test/../../admin'), 'Unsafe slug accepted');

$routes = $load('app/Core/App.php');
$required = [
    '/blog/kategori/{slug}', '/blog/etiket/{slug}',
    '/kategori/{slug}', '/paket/{slug}',
    '/hazir-scriptler/kategori/{slug}', '/hazir-scriptler/tur/{slug}',
    '/hazir-yazilimlar/tur/{slug}', '/kategoriler/grup/{slug}'
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
    'resources/views/frontend/categories.php' => ['/kategoriler/grup/'],
    'resources/views/frontend/category-detail.php' => ['/kategori/'],
    'resources/views/frontend/netvera-scripts.php' => ['/hazir-scriptler/kategori', '/hazir-scriptler/tur'],
    'resources/views/frontend/software.php' => ['/hazir-yazilimlar/tur'],
    'resources/views/layouts/app.php' => ['/kategoriler/grup/', '/hazir-scriptler/kategori/'],
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
foreach ([
    'app/Controllers/BlogController.php',
    'app/Controllers/CategoryController.php',
    'app/Controllers/NetveraScriptController.php',
    'app/Controllers/SoftwareController.php',
] as $controller) {
    $assert(str_contains($load($controller), 'redirectLegacyFacet'), 'Legacy redirect missing: '.$controller);
}
$assert(str_contains($load('app/Services/PublicSeoUrls.php'), ', true, 301)'), 'Permanent redirect must be 301');
echo "PASS: canonical taxonomy URLs, UTF-8 safe paths, old query aliases, preserved product slugs and internal navigation\n";
