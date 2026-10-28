<?php
/**
 * Build the archive whose CONTENTS (not a nested well-known folder) match
 * the cPanel netvera.tr document-root shown in the owner's screenshot.
 *
 * From C:\Projeler\yorumhizmeti.tr\well-known:
 *   php bin/build-netvera-root-zip.php
 *
 * Upload the generated ZIP to /home/<cpanel-user>/netvera.tr then Extract.
 * IMPORTANT: ZIP deliberately REPLACES .env with a fresh production template.
 * BACK UP the live .env and database before extraction, then fill in DB_*.
 * This is file distribution only, NOT customer-data migration.
 */
declare(strict_types=1);
if(PHP_SAPI!=='cli'){http_response_code(403);exit(1);}
$root=dirname(__DIR__);
if(!extension_loaded('zip')){fwrite(STDERR,"PHP ZipArchive extension required. Enable php_zip in local PHP.\n");exit(2);}
$required=['app','public','resources','database','vendor'];
foreach($required as $dir){
    if(!is_dir($root.'/'.$dir))throw new RuntimeException('Missing project folder '.$dir);
}
foreach (['public/index.php', 'vendor/autoload.php', 'database/database.sql', '.env.example', '.htaccess'] as $needed) {
    if (!is_file($root.'/'.$needed)) throw new RuntimeException('Release requirement missing: '.$needed);
}
$filename=dirname($root).'/netvera-tr-cpanel-root-'.date('Ymd-His').'.zip';
$zip=new ZipArchive();
if($zip->open($filename,ZipArchive::CREATE|ZipArchive::EXCL)!==true)
    throw new RuntimeException('Cannot create release ZIP');
$skip=function(string $relative):bool{
    $s=str_replace('\\','/',$relative);
    $base=basename($s);
    if(str_contains($s,'/.git/') || str_contains($s,'sync-conflict') ||
       str_contains($s,'/error_log') || $base==='.env' || str_starts_with($base,'.env.') ||
       $base==='Thumbs.db' || $base==='.DS_Store' ||
       (str_ends_with(strtolower($base), '.sql') && $s!=='database/database.sql') ||
       preg_match('~^public/uploads(?:/|$)~D',$s) ||
       preg_match('~^storage/(?:backups|cache|logs|sessions)(?:/|$)~D',$s) ||
       preg_match('~\.(?:log|bak|backup|zip)$~iD',$s))return true;
    return false;
};
$included=[];
foreach(['app','public','resources','database','vendor','bin','scripts'] as $folder){
    if(!is_dir($root.'/'.$folder))continue;
    $zip->addEmptyDir($folder);
    $di=new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($root.'/'.$folder,FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    foreach($di as $file){
        $source=$file->getPathname();
        $relative=str_replace('\\','/',substr($source,strlen($root)+1));
        if($skip($relative))continue;
        if($file->isLink())continue; // No symlink escapes from source tree.
        if($file->isDir())$zip->addEmptyDir($relative);
        elseif($file->isFile()){
            $zip->addFile($source,$relative);
            $included[]=$relative;
        }
    }
}
foreach(['.htaccess','composer.json','composer.lock','php.ini'] as $file){
    if(is_file($root.'/'.$file)){
        $zip->addFile($root.'/'.$file,$file);
        $included[]=$file;
    }
}
// Exactly one live .env: loaded only from BASE_PATH/.env by public/index.php.
// This placeholder configuration intentionally replaces the previous live .env.
// Do not embed production passwords/API credentials in the repository or archive.
$env = file_get_contents($root.'/.env.example');
if ($env === false || !str_contains($env, 'DB_NAME=') || !str_contains($env, 'DB_USER=')) {
    throw new RuntimeException('Invalid .env.example; cannot make safe production .env');
}
$zip->addFromString('.env', $env);
$included[] = '.env';
$zip->addFromString('NETVERA-ILK-OKU.txt',
    "NETVERA.TR - CPANEL ROOT RELEASE\n\n".
    "1) EXTRACT ONCE in the netvera.tr document root; no well-known nesting.\n".
    "2) BEFORE EXTRACT, back up the current .env, full site files and live MySQL database.\n".
    "3) This ZIP OVERWRITES .env with placeholders. Edit root .env: DB_HOST, DB_NAME, DB_USER, DB_PASS.\n".
    "4) Only root .env is loaded by public/index.php. .env.example and .env.production.example are not live settings.\n".
    "5) database/database.sql is for EMPTY DATABASES ONLY: contains DROP TABLE. NEVER import over existing live DB.\n".
    "6) This ZIP does NOT migrate or delete customers, orders, licenses, uploads or subdomain folders.\n".
    "7) Existing old .env.example and old root database.sql may remain on the server: Extraction does not delete old files.\n".
    "8) Ensure PHP extensions required by composer.json are enabled on cPanel.\n");
foreach(['storage','storage/cache','storage/logs','storage/uploads','storage/backups'] as $folder){
    $zip->addEmptyDir($folder);
}
$zip->setArchiveComment('NetVera cPanel root release: ONE placeholder .env plus ONE empty-install SQL, vendor, app. BACK UP old .env and MySQL. No subdomains/uploads/customer data.');
$zip->close();
echo "Created: ".$filename.PHP_EOL;
echo "Contains ".count($included)." application files, no extra well-known/ folder.\n";
echo "Contains one PLACEHOLDER .env (overwrites live .env) and database/database.sql for EMPTY databases only.\n";
echo "Back up your current .env and MySQL before extracting.\n";
echo "Never delete or overwrite subdomain folders before extraction.\n";
