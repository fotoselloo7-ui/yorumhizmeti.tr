<?php
declare(strict_types=1);
$root=dirname(__DIR__);
$get=static fn(string $p):string=>(string)file_get_contents($root.'/'.$p);
$ok=static function(bool $v,string $message):void{
    if(!$v){fwrite(STDERR,'FAIL: '.$message.PHP_EOL);exit(1);}
};
$package=$get('resources/views/frontend/package-detail.php');
$category=$get('resources/views/frontend/category-detail.php');
$socialPackage=$get('resources/views/frontend/social/package.php');
$socialCategory=$get('resources/views/frontend/social/category.php');
$adminPackage=$get('resources/views/admin/packages/form.php');
$adminCategory=$get('resources/views/admin/categories/form.php');
$ctrlPackage=$get('app/Controllers/Admin/PackageController.php');
$ctrlCategory=$get('app/Controllers/Admin/CategoryController.php');
$frontPackage=$get('app/Controllers/PackageController.php');
$frontCategory=$get('app/Controllers/CategoryController.php');
$gallery=$get('app/Services/ServicePackageGalleryService.php');
$refs=$get('app/Services/ServiceReferenceSelectionService.php');
$referenceView=$get('resources/views/frontend/partials/service-references.php');
$css=$get('public/assets/css/netvera-service-detail-v1.css');
$js=$get('public/assets/js/netvera-service-detail-v1.js');
$layout=$get('resources/views/layouts/app.php');
$ok(!str_contains($package,'yv-product-thumbs-v12') &&
    !str_contains($package,'img/hero-woman-cutout.png') &&
    !str_contains($package,'img/support-woman-cutout.png'),
    'Fake, unclickable package demo thumbnails have returned');
$ok(str_contains($package,'nv-real-product-gallery-thumbs') &&
    str_contains($package,'data-nv-gallery-thumb') &&
    str_contains($js,"data-nv-gallery-thumb") &&
    str_contains($js,"aria-pressed"),'Real gallery buttons must work');
$ok(str_contains($gallery,"Upload::image") &&
    str_contains($gallery,'gallery_keep') &&
    str_contains($adminPackage,'name="gallery_images[]"') &&
    str_contains($adminPackage,'name="gallery_keep[]"'),
    'Real image upload and retention missing');
$ok(str_contains($package,'Güvenli Teklif Süreci') &&
    str_contains($css,'white-space:nowrap!important') &&
    str_contains($css,'.yv-product-mini-trust-v5'),
    'Quote card must have single-line heading and responsive trust controls');
$ok(!str_contains($package,'+500 gerçek etkileşim') &&
    !str_contains($package,'1.324 değerlendirme') &&
    !str_contains($package,'4.9 <b>'),
    'Unsupported rating/benefit facts must not appear on service details');
foreach([$package,$category,$socialPackage,$socialCategory] as $page){
    $ok(str_contains($page,"service-references.php"),'Every service template needs reference showcase');
}
$ok(str_contains($adminPackage,"service-references-picker.php") &&
    str_contains($adminCategory,"service-references-picker.php") &&
    str_contains($ctrlPackage,"ServiceReferenceSelectionService::save") &&
    str_contains($ctrlCategory,"ServiceReferenceSelectionService::save"),
    'Both service admin forms must offer reference selections');
$ok(str_contains($frontPackage,"ServiceReferenceSelectionService::selected") &&
    str_contains($frontCategory,"ServiceReferenceSelectionService::selected"),
    'Only selected real references must render');
$ok(str_contains($refs,"ReferencesService::all(true)") &&
    str_contains($referenceView,"ReferencesService::externalPlayer") &&
    str_contains($referenceView,"data-nv-ref-player"),
    'No invented reviews; approved existing reference player only');
$ok(str_contains($layout,'netvera-service-detail-v1.css') &&
    str_contains($layout,'netvera-service-detail-v1.js'),
    'Service UX assets missing from templates');
$ok(str_contains($css,'@media(max-width:650px)') &&
    str_contains($css,'.nv-service-references-grid'),
    'Mobile service references/gallery missing');
echo "PASS: real clickable service galleries, no fake thumbnails/statistics, aligned quote cards, admin-selected reference portfolio on service/category/social pages.\n";
