<?php
namespace App\Services;

use App\Core\Upload;

/** Structured editorial content, never arbitrary executable HTML or hidden keywords. */
final class LandingContentService
{
    public static function text(mixed $value,int $max):string
    {
        if(!is_string($value)||!mb_check_encoding($value,'UTF-8')||mb_strlen($value)>$max)throw new \InvalidArgumentException('İçerik alanının biçimini ve uzunluğunu kontrol edin.');
        return trim(preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/u','',strip_tags($value))??'');
    }
    public static function localPath(mixed $value):string
    {
        $value=self::text($value,500);$decoded=rawurldecode($value);
        if(!preg_match('~^/(?!/)[\pL\pN/_.,~%#?=&+\-]*$~uD',$value)||preg_match('~[\\\\\x00-\x20]|\.\.|^//~u',$decoded)
            ||preg_match('~^/(?:admin|payment|netvera|sepet|odeme|cikis|destek/dosya)(?:/|$)~i',$decoded))throw new \InvalidArgumentException('Bağlantı güvenli bir site içi yol olmalıdır.');
        return $value;
    }
    public static function image(mixed $value):string
    {
        $value=self::text($value,500);
        if($value!==''&&(!preg_match('~^/(?:uploads|assets/img)/[a-zA-Z0-9_./-]+\.(?:webp|png|jpe?g|gif)$~D',$value)||str_contains($value,'..')||str_contains($value,'//')))throw new \InvalidArgumentException('Yerel JPG, PNG, WebP veya GIF görseli seçin.');
        return $value;
    }
    public static function upload(array $file):string
    {
        if(($file['error']??null)!==UPLOAD_ERR_OK||!is_uploaded_file($file['tmp_name']??'')||!in_array(strtolower(pathinfo($file['name']??'',PATHINFO_EXTENSION)),['jpg','jpeg','png','webp','gif'],true)||!@getimagesize($file['tmp_name']))throw new \InvalidArgumentException('Geçerli bir görsel yükleyin.');
        $path=Upload::image($file,'landing-pages');
        if(!$path)throw new \InvalidArgumentException('Görsel yüklenemedi. En fazla 5 MB dosya kullanın.');
        return $path;
    }
    public static function rows(mixed $rows,string $kind,int $limit=12):array
    {
        if(!is_array($rows)||count($rows)>$limit)throw new \InvalidArgumentException('İçerik satırı sınırı aşıldı.');
        $out=[];
        foreach($rows as $row){
            if(!is_array($row))throw new \InvalidArgumentException('Geçersiz içerik satırı.');
            $clean=[];
            $fields=match($kind){'faqs'=>['question'=>240,'answer'=>1800],'links'=>['label'=>100,'path'=>500],default=>['title'=>220,'body'=>6000,'subheading'=>220,'detail'=>3000,'image'=>500,'alt'=>220]};
            foreach($fields as $field=>$max)$clean[$field]=self::text($row[$field]??'',$max);
            if(implode('',$clean)==='')continue;
            if($kind==='links'){$clean['path']=self::localPath($clean['path']);if($clean['label']==='')throw new \InvalidArgumentException('İç bağlantı için açıklayıcı bir metin yazın.');}
            elseif($kind==='faqs'){if($clean['question']===''||$clean['answer']==='')throw new \InvalidArgumentException('Soru ve yanıt birlikte doldurulmalıdır.');}
            else{if($clean['title']===''||$clean['body']==='')throw new \InvalidArgumentException('İçerik bölümünde H2 başlık ve metin olmalıdır.');$clean['image']=self::image($clean['image']);if($clean['image']!==''&&$clean['alt']==='')throw new \InvalidArgumentException('Görselin alt metnini yazın.');}
            $out[]=$clean;
        }
        return $out;
    }
    public static function seo(mixed $input):array
    {
        if(!is_array($input))throw new \InvalidArgumentException('Geçersiz SEO alanı.');
        $seo=[];
        foreach(['title'=>200,'description'=>500,'focus_keyword'=>100,'secondary_keywords'=>1000,'entity_topics'=>500,'geo_summary'=>500,'main_question'=>300,'direct_answer'=>800,'author_name'=>120,'reviewer_name'=>120,'last_reviewed'=>10,'sources'=>2500,'canonical'=>500,'og_title'=>200,'og_description'=>500,'og_image'=>500,'service_area'=>300]as $key=>$max)$seo[$key]=self::text($input[$key]??'',$max);
        $seo['robots']=$input['robots']??'noindex,follow';
        if(!is_string($seo['robots'])||!in_array($seo['robots'],['index,follow','index,follow,max-image-preview:large','noindex,follow','noindex,nofollow'],true))throw new \InvalidArgumentException('Geçerli bir robots tercihi seçin.');
        if($seo['canonical']!=='')$seo['canonical']=self::localPath($seo['canonical']);
        $seo['og_image']=self::image($seo['og_image']);
        if($seo['last_reviewed']!==''&&!SeoMetadataService::lastmod($seo['last_reviewed']))throw new \InvalidArgumentException('Kontrol tarihi geçerli ve gelecekte olmayan bir tarih olmalıdır.');
        $sources=[];foreach(preg_split('/\R/u',$seo['sources'])?:[]as $source){if(trim($source)==='')continue;$safe=SeoMetadataService::webUrl(trim($source));if(!$safe)throw new \InvalidArgumentException('Kaynaklar HTTP/HTTPS bağlantısı olmalıdır.');$sources[]=$safe;}
        $seo['sources']=implode("\n",array_slice(array_unique($sources),0,15));
        return $seo+SeoMetadataService::preview($input);
    }
    /** Plain text plus explicit [descriptive anchor](/internal/path) syntax. */
    public static function rich(string $text):string
    {
        $out='';foreach(preg_split('~(\[[^\]\n]{1,100}\]\(/[^)\s]{1,500}\))~u',$text,-1,PREG_SPLIT_DELIM_CAPTURE)?:[]as $part){
            if(preg_match('~^\[([^\]\n]+)\]\((/[^)\s]+)\)$~uD',$part,$m)){
                try{$path=self::localPath($m[2]);$out.='<a href="'.e($path).'">'.e($m[1]).'</a>';continue;}catch(\InvalidArgumentException $ignored){}
            }
            $out.=nl2br(e($part));
        }return $out;
    }
    public static function diagnostics(array $data):array
    {
        $seo=$data['seo']??[];$copy=($data['title']??'').' '.($data['summary']??'').' '.($data['body']??'').' '.implode(' ',array_column($data['sections']??[],'body'));
        $notes=[];$focus=trim($seo['focus_keyword']??'');
        if($focus!==''){$count=mb_substr_count(mb_strtolower($copy),mb_strtolower($focus));$notes[]='Odak ifadenin görünür metindeki kullanımı: '.$count.'. Doğal kullanım hedefleyin; belirli bir yoğunluk sıralama garantisi değildir.';}
        if(mb_strlen($seo['title']??'')>65)$notes[]='Meta başlık uzun; arama sonuçlarında kısalabilir.';
        if(mb_strlen($seo['description']??'')>170)$notes[]='Meta açıklama uzun; kısa ve ayırt edici yazmayı değerlendirin.';
        if(empty($seo['direct_answer']))$notes[]='Kullanıcının ana sorusuna doğrulanabilir kısa bir yanıt ekleyebilirsiniz.';
        if(empty($data['links']))$notes[]='İlgili hizmetlere açıklayıcı iç bağlantılar ekleyin.';
        $notes[]='H1 şablondan tek kez üretilir. Anahtar kelimeler gizli metin veya otomatik tekrar olarak basılmaz.';
        return $notes;
    }
}
