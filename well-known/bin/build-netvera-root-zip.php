<?php
/**
 * Build the archive whose CONTENTS (not a nested well-known folder) match
 * the cPanel netvera.tr document-root shown in the owner's screenshot.
 *
 * From C:\Projeler\yorumhizmeti.tr\well-known:
 *   php bin/build-netvera-root-zip.php
 *
 * Upload the generated ZIP to /home/<cpanel-user>/netvera.tr then Extract.
 * IMPORTANT: this is a file layout only. Requires an existing secure .env
 * and provisioned MySQL data. It does NOT migrate customer/SQL data.
 */
declare(strict_types=1);
if(PHP_SAPI!=='cli'){http_response_code(403);exit(1);}
$root=dirname(__DIR__);
if(!extension_loaded('zip')){fwrite(STDERR,"PHP ZipArchive extension required. Enable php_zip in local PHP.\n");exit(2);}
$required=['app','public','resources'];
foreach($required as $dir){
    if(!is_dir($root.'/'.$dir))throw new RuntimeException('Missing project folder '.$dir);
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
       $base==='Thumbs.db' || $base==='.DS_Store' || str_ends_with($base,'.sql') ||
       preg_match('~^public/uploads(?:/|$)~D',$s) ||
       preg_match('~^storage/(?:backups|cache|logs|sessions)(?:/|$)~D',$s) ||
       preg_match('~\.(?:log|bak|backup|zip)$~iD',$s))return true;
    return false;
};
$included=[];
foreach(['app','public','resources','vendor','bin','scripts'] as $folder){
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
foreach(['.htaccess','.user.ini','composer.json','composer.lock','php.ini'] as $file){
    if(is_file($root.'/'.$file) && $file!=='.user.ini'){
        $zip->addFile($root.'/'.$file,$file);
        $included[]=$file;
    }
}
foreach(['storage','storage/cache','storage/logs','storage/uploads','storage/backups'] as $folder){
    $zip->addEmptyDir($folder);
}
$zip->setArchiveComment('NetVera root-ready application files only. No .env, SQL drop/import, user uploads, subdomains or customer credentials.');
$zip->close();
echo "Created: ".$filename.PHP_EOL;
echo "Contains ".count($included)." application files, no extra well-known/ folder.\n";
echo "Does not contain DB or production .env: keep these separately.\n";
echo "Never delete or overwrite subdomain folders before extraction.\n";
