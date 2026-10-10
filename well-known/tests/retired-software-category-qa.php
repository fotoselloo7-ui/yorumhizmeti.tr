<?php
declare(strict_types=1);

// The empty legacy software category is replaced by the real imported NetVera catalogue.
// Database and existing orders are intentionally left unchanged.
$root = dirname(__DIR__);
require_once $root.'/app/Services/ServiceCategoryVisibility.php';
use App\Services\ServiceCategoryVisibility as Visibility;

$assert = static function (bool $passed, string $message): void {
    if (!$passed) throw new RuntimeException($message);
};
$read = static fn(string $path): string => (string)file_get_contents($root.'/'.$path);
$oldRoot = ['slug'=>'hazir-yazilim-scriptleri','name'=>'Hazır Yazılımlar & Scriptler'];
$oldChild = ['slug'=>'eski-haber-scriptleri','name'=>'Haber Scriptleri'];

$assert(!Visibility::show($oldRoot), 'Legacy software root still shown in service menu');
$assert(!Visibility::show($oldChild,$oldRoot), 'Legacy software child still shown in service menu');
$assert(!Visibility::show(['slug'=>'eski-haber-scriptleri','parent_category_slug'=>'hazir-yazilim-scriptleri']), 'Legacy child without parent record still shown');
$assert(!Visibility::showPackage([
    'slug'=>'sample','category_slug'=>'eski-haber-scriptleri',
    'featured_group_slug'=>'hazir-yazilim-scriptleri',
]), 'Legacy package group still promoted');
$assert(Visibility::show(['slug'=>'web-site-hizmetleri','name'=>'Web Site Hizmetleri']), 'Unrelated active service hidden');

$categoryCtrl = $read('app/Controllers/CategoryController.php');
$assert(str_contains($categoryCtrl, 'ServiceCategoryVisibility::LEGACY_SOFTWARE_ROOT'), 'Legacy root redirect absent');
$assert(str_contains($categoryCtrl, 'ServiceCategoryVisibility::isLegacySoftware($category)'), 'Child category redirect absent');
$assert(substr_count($categoryCtrl, "header('Location: /hazir-scriptler', true, 301)") >= 2, '301 redirect missing');
$assert(strpos($categoryCtrl, 'ServiceCategoryVisibility::LEGACY_SOFTWARE_ROOT')
        < strpos($categoryCtrl, 'Database::getInstance();', strpos($categoryCtrl, 'public function show(')),
    'Retired root redirect must not depend on the database');
$assert(str_contains($read('app/Services/CatalogMenuService.php'), 'ServiceCategoryVisibility::show($cat)'), 'Menu does not filter retired categories');
$assert(str_contains($read('app/Controllers/HomeController.php'), 'ServiceCategoryVisibility::show($category)'), 'Homepage category discovery shows old software');
$assert(str_contains($read('app/Controllers/HomeController.php'), 'ServiceCategoryVisibility::showPackage($p)'), 'Homepage featured package block includes old software');
$assert(str_contains($read('app/Services/NavigationService.php'), "'/hazir-scriptler'"), 'Menu links not mapped to canonical software catalogue');
$assert(str_contains($read('app/Services/SitemapService.php'), 'ServiceCategoryVisibility::isLegacySoftware($cat)'), 'Redirect-only category still in sitemap');
$directory = $read('resources/views/frontend/categories.php');
$assert(str_contains($directory, 'href="/hazir-scriptler"'), 'Ready software tile not linked to imported catalogue');
$assert(str_contains($directory, '$liveScriptCount'), 'Ready software tile does not use the real imported catalogue');
$packageView = $read('resources/views/frontend/package-detail.php');
$assert(str_contains($packageView, 'ServiceCategoryVisibility::isLegacySoftware($package)'), 'Legacy package breadcrumbs link to retired path');
echo "PASS: retired category and children 301 to imported scripts, public navigation consolidated, sitemap clean, data untouched\n";
