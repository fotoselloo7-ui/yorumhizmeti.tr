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
$assert(str_contains($categoryCtrl, 'ServiceCategoryVisibility::LEGACY_SOFTWARE_ROOT'), 'Retired root guard absent');
$assert(str_contains($categoryCtrl, 'ServiceCategoryVisibility::isLegacySoftware($category)'), 'Retired child guard absent');
$assert(!str_contains($categoryCtrl, "header('Location: /hazir-scriptler'"), 'Old project page must not redirect');
$assert(strpos($categoryCtrl, 'ServiceCategoryVisibility::LEGACY_SOFTWARE_ROOT')
        < strpos($categoryCtrl, 'Database::getInstance();', strpos($categoryCtrl, 'public function show(')),
    'Retired root 404 must not depend on the database');
$assert(str_contains($read('app/Services/CatalogMenuService.php'), 'ServiceCategoryVisibility::show($cat)'), 'Menu does not filter retired categories');
$assert(str_contains($read('app/Controllers/HomeController.php'), 'ServiceCategoryVisibility::show($category)'), 'Homepage category discovery shows old software');
$assert(str_contains($read('app/Controllers/HomeController.php'), 'ServiceCategoryVisibility::showPackage($p)'), 'Homepage featured package block includes old software');
$assert(str_contains($read('app/Services/NavigationService.php'), "'/hazir-scriptler'"), 'Menu links not mapped to canonical software catalogue');
$assert(str_contains($read('app/Services/SitemapService.php'), 'ServiceCategoryVisibility::isLegacySoftware($cat)'), 'Retired category still in sitemap');
$directory = $read('resources/views/frontend/categories.php');
$assert(str_contains($directory, 'href="/hazir-scriptler"'), 'Ready software tile not linked to imported catalogue');
$assert(str_contains($directory, '$liveScriptCount'), 'Ready software tile does not use the real imported catalogue');
$packageView = $read('resources/views/frontend/package-detail.php');
$assert(str_contains($packageView, 'ServiceCategoryVisibility::isLegacySoftware($package)'), 'Legacy package breadcrumbs link to retired path');
$assert(str_contains($categoryCtrl, 'PublicSeoUrls::notFound'), 'Retired category must return 404');
$adminMenu = $read('resources/views/layouts/admin.php');
$assert(!str_contains($adminMenu, 'href="/admin/hazir-yazilimlar"'), 'Duplicate admin software entry remains');
$assert(str_contains($adminMenu, 'href="/admin/netvera-yazilimlar"'), 'Real NetVera admin entry missing');
$routes = $read('app/Core/App.php');
$assert(str_contains($routes, "'/admin/hazir-yazilimlar'"), 'Existing admin bookmark route missing');
$assert(str_contains($routes, 'NetveraScriptController@index'), 'Legacy admin route not mapped to imported catalogue');
$softwareCode = $read('app/Services/SoftwareCatalogService.php');
$assert(str_contains($softwareCode, "return ['created'=>0, 'existing'=>0, 'conflicts'=>0];"), 'Obsolete category setup remains writable');
$assert(!str_contains($softwareCode, "\\$db->insert('categories'"), 'Old category installer can still duplicate records');
$assert(is_file($root.'/database/migrations/retired-software-links-v1.sql'), 'DB link replacement migration missing');
echo "PASS: old local-only category removed, internal links point to imported scripts, no 301, database entries preserved\n";
