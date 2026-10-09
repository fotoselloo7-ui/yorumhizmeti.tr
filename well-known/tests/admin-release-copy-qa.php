<?php
/**
 * Release UI hygiene check. No database, network, .env, or secrets needed.
 * Developer-only notices belong in developer docs, not administrative screens.
 */
declare(strict_types=1);
$base=dirname(__DIR__);
$folders=[
    $base.'/resources/views/admin',
    $base.'/resources/views/frontend',
    $base.'/resources/views/layouts',
];
$patterns=[
    '/\blocalhost\b/iu',
    '/\b127\.0\.0\.1\b/u',
    '/github[\s-]*pages/iu',
    '/\bstaging\b/iu',
    '/NETVERA_(?:SOURCE|IMPORT|TELEGRAM)_/u',
    '/yerel\s+paytr/iu',
    '/[A-Za-z0-9._-]+\.md\s+dosya/iu',
];
$total=0;
foreach($folders as $folder) {
    $files=new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folder,FilesystemIterator::SKIP_DOTS));
    foreach($files as $file) {
        if(!$file->isFile() || strtolower($file->getExtension())!=='php')continue;
        $content=file_get_contents((string)$file);
        foreach($patterns as $pattern) {
            if(preg_match($pattern,$content)) {
                throw new RuntimeException('Developer environment text in public/admin UI: '.substr((string)$file,strlen($base)+1).' / '.$pattern);
            }
        }
        $total++;
    }
}
$paytr=file_get_contents($base.'/resources/views/admin/payment-gateways/paytr.php');
foreach(['name="merchant_id"','name="merchant_key"','name="merchant_salt"','name="test_mode"','action="/admin/paytr-ayarlari/kaydet"','csrfField()'] as $required){
    if(!str_contains($paytr,$required))throw new RuntimeException('Required PayTR settings form control missing: '.$required);
}
if(str_contains($paytr,'/odeme/paytr-onizleme') || str_contains($paytr,'adm-alert'))
    throw new RuntimeException('Development PayTR notice still on administrator screen');
$inbox=file_get_contents($base.'/resources/views/admin/netvera-inbox/index.php');
if(!str_contains($inbox,'/admin/netvera-gelen-kutusu/kur'))
    throw new RuntimeException('Gelen kutusu etkinleştirme düğmesi eksik');
$routes=file_get_contents($base.'/app/Core/App.php');
if(!str_contains($routes,"'/admin/netvera-gelen-kutusu/kur'"))
    throw new RuntimeException('Inbox initialization route missing');
echo "PASS: {$total} admin/storefront templates free from staging/localhost notices; PayTR controls and inbox setup preserved\n";
