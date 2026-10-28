<?php
namespace App\Services;

use App\Core\Upload;

/** Real package-gallery uploads only: no fabricated demo thumbnails. */
final class ServicePackageGalleryService
{
    private static function key(int $id):string {return 'nv_service_gallery_'.$id;}
    public static function images(int $id):array
    {
        if($id<1)return [];
        $data=json_decode((string)setting(self::key($id),'[]'),true);
        if(!is_array($data))return [];
        return array_slice(array_values(array_filter($data,static fn($p)=>
            is_string($p)&&preg_match('~^/uploads/packages/gallery/[a-zA-Z0-9_.-]+\\.(?:jpe?g|png|webp|gif)$~D',$p)===1
        )),0,6);
    }
    public static function save(int $id,array $post,array $files):void
    {
        if($id<1)throw new \InvalidArgumentException('Paket eksik.');
        $current=self::images($id);
        // Existing gallery images are retained unless explicitly unticked.
        $keep=is_array($post['gallery_keep']??null)?$post['gallery_keep']:[];
        $selected=array_values(array_filter($current,static fn($p)=>in_array($p,$keep,true)));
        $incoming=$files['gallery_images']??null;
        if(is_array($incoming)&&is_array($incoming['name']??null)){
            foreach($incoming['name'] as $i=>$filename){
                if(!$filename)continue;
                if(count($selected)>=6)break;
                $error=(int)($incoming['error'][$i]??UPLOAD_ERR_NO_FILE);
                if($error!==UPLOAD_ERR_OK)throw new \InvalidArgumentException('Galeri görseli yüklenemedi.');
                $file=['name'=>(string)$filename,'type'=>$incoming['type'][$i]??'',
                    'tmp_name'=>$incoming['tmp_name'][$i]??'',
                    'error'=>$error,'size'=>(int)($incoming['size'][$i]??0)];
                $path=Upload::image($file,'packages/gallery');
                if(!$path)throw new \InvalidArgumentException('Galeri görseli geçersiz; JPG, PNG veya WebP (en çok 5 MB).');
                $selected[]=$path;
            }
        }
        SiteConfigService::getInstance()->set(self::key($id),
            json_encode($selected,JSON_UNESCAPED_SLASHES|JSON_THROW_ON_ERROR),'packages');
    }
}
