<?php
/**
 * SAFE NETVERA PUBLIC IMAGE MIRROR.
 * Downloads ONLY publicly listed /uploads/scripts or /uploads/blog images from
 * the hard-coded original brand origin https://netvera.tr. No cookies, sessions,
 * request body, credentials, PHP, backup, customer or support files.
 *
 * Check only: php scripts/mirror-netvera-public-images.php
 * Staging mirror: NETVERA_MEDIA_MIRROR_ALLOWED=1 php scripts/mirror-netvera-public-images.php --apply
 */
declare(strict_types=1);
if(PHP_SAPI!=='cli'){http_response_code(403);exit(2);}
define('BASE_PATH',dirname(__DIR__));
$apply=in_array('--apply',$argv,true);
$allowed=(string)(getenv('NETVERA_MEDIA_MIRROR_ALLOWED')?:'');
$branch=(string)(getenv('GITHUB_REF_NAME')?:'');
if($apply && ($allowed!=='1' || ($branch!==''&&$branch!=='staging/netvera-source-integration'))){
    fwrite(STDERR,"Protected mirror requires explicit staging opt-in.\n");exit(3);
}
$filename=BASE_PATH.'/database/netvera-public-catalog.json';
$data=json_decode((string)file_get_contents($filename),true,512,JSON_THROW_ON_ERROR);
if(($data['format']??'')!=='netvera-public-safelist-v1')exit(3);
$paths=[];
foreach(($data['products']??[]) as $p) $paths[]=$p['image']??'';
foreach(($data['images']??[]) as $i) $paths[]=$i['image_path']??'';
foreach(($data['blog_posts']??[]) as $p){
    $paths[]=$p['image']??'';
    $paths[]=$p['og_image']??'';
}
$paths=array_values(array_unique(array_filter(array_map(static function($raw):string{
    if(!is_string($raw))return '';
    $path=trim($raw);
    if(str_starts_with($path,'https://netvera.tr/'))$path=substr($path,strlen('https://netvera.tr'));
    if(!str_starts_with($path,'/'))$path='/'.$path;
    // Strict image extension and allowed tree, no traversal or encoded paths.
    return preg_match('~^/uploads/(?:scripts|blog)/[a-zA-Z0-9_./-]+\.(?:png|jpg|jpeg|webp|gif)$~D',$path)
      && !str_contains($path,'..') ? $path : '';
},$paths))));
if(count($paths)!==63){
    fwrite(STDERR,"Unexpected media manifest cardinality: ".count($paths)."\n");exit(3);
}
echo "Netvera public image manifest: ".count($paths)." paths.\n";
if(!$apply){echo "DRY RUN, no network downloads or filesystem changes.\n";exit(0);}
if(!extension_loaded('curl') || !extension_loaded('fileinfo')){
    fwrite(STDERR,"cURL and fileinfo required.\n");exit(3);
}
$copied=0;$present=0;$failed=[];
foreach($paths as $path){
    $target=BASE_PATH.'/public'.$path;
    if(is_file($target) && filesize($target)>0){$present++;continue;}
    $url='https://netvera.tr'.$path;
    $ch=curl_init($url);
    curl_setopt_array($ch,[
        CURLOPT_FOLLOWLOCATION=>false,
        CURLOPT_CONNECTTIMEOUT=>6,
        CURLOPT_TIMEOUT=>16,
        CURLOPT_RETURNTRANSFER=>true,
        CURLOPT_SSL_VERIFYPEER=>true,
        CURLOPT_SSL_VERIFYHOST=>2,
        CURLOPT_USERAGENT=>'NetVeraSafeMediaMigration/1.0',
        CURLOPT_MAXREDIRS=>0,
        CURLOPT_PROTOCOLS=>CURLPROTO_HTTPS,
        CURLOPT_HTTPHEADER=>['Accept: image/webp,image/png,image/jpeg,image/gif'],
    ]);
    $body=curl_exec($ch);
    $status=(int)curl_getinfo($ch,CURLINFO_HTTP_CODE);
    $mime=(string)curl_getinfo($ch,CURLINFO_CONTENT_TYPE);
    curl_close($ch);
    $expected=strtolower(pathinfo($path,PATHINFO_EXTENSION));
    $expectedMimes=match($expected){
        'jpg','jpeg'=>['image/jpeg'],
        'png'=>['image/png'],
        'webp'=>['image/webp'],
        'gif'=>['image/gif'],
        default=>[]
    };
    if(!is_string($body)||$status!==200||strlen($body)<40||
       strlen($body)>8*1024*1024||!in_array(strtolower(trim(explode(';',$mime)[0])), $expectedMimes,true)){
        $failed[]=$path.' (HTTP '.$status.')';continue;
    }
    $detected=(new finfo(FILEINFO_MIME_TYPE))->buffer($body);
    if(!in_array($detected,$expectedMimes,true) || @getimagesizefromstring($body)===false){
        $failed[]=$path.' (invalid image)';continue;
    }
    if(!is_dir(dirname($target))&&!mkdir(dirname($target),0755,true)&&!is_dir(dirname($target))){
        $failed[]=$path.' (directory error)';continue;
    }
    $temp=$target.'.importing-'.bin2hex(random_bytes(5));
    if(file_put_contents($temp,$body,LOCK_EX)!==strlen($body)){
        @unlink($temp);$failed[]=$path.' (write error)';continue;
    }
    if(is_file($target)){@unlink($temp);$present++;continue;}
    if(!rename($temp,$target)){@unlink($temp);$failed[]=$path.' (rename error)';continue;}
    @chmod($target,0644);
    $copied++;
}
echo json_encode(['total'=>count($paths),'copied'=>$copied,'already_present'=>$present,
 'failed'=>count($failed)],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT)."\n";
foreach(array_slice($failed,0,12) as $err)fwrite(STDERR,$err."\n");
if($failed)exit(1); // Partial files remain valid and can be retried safely.
