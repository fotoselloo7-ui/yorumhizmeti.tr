<?php
namespace App\Services;

/** Additive presentation policy. Never changes routes, records, checkout or sessions. */
final class SeoMetadataService
{
    public static function webUrl(string $value): ?string
    {
        $value=trim($value);
        if($value===''||preg_match('/[\x00-\x20]/',$value)||!filter_var($value,FILTER_VALIDATE_URL))return null;
        $p=parse_url($value);
        if(!in_array(strtolower($p['scheme']??''),['http','https'],true)||empty($p['host'])||isset($p['user'])||isset($p['pass']))return null;
        return explode('#',$value,2)[0];
    }

    public static function preview(array $input): array
    {
        $out=[];
        foreach(['max_snippet'=>5000,'max_video_preview'=>3600]as $key=>$max){
            $v=$input[$key]??'';
            $out[$key]=is_scalar($v)&&preg_match('/^(?:-1|0|[1-9][0-9]{0,4})$/D',(string)$v)&&((int)$v<=$max)?(string)$v:'';
        }
        $v=$input['max_image_preview']??'';
        $out['max_image_preview']=is_string($v)&&in_array($v,['','none','standard','large'],true)?$v:'';
        return $out;
    }

    public static function robots(string $saved='',bool $forceNoindex=false,array $preview=[],string $default='index,follow,max-image-preview:large'): string
    {
        $parts=[];
        foreach(explode(',',strtolower($saved!==''?$saved:$default))as $part){
            $part=preg_replace('/\s+/','',trim($part));
            if($part==='none'){$parts['noindex']='noindex';$parts['nofollow']='nofollow';}
            elseif(in_array($part,['index','noindex','follow','nofollow','nosnippet','noimageindex','noarchive'],true))$parts[$part]=$part;
            elseif(preg_match('/^(max-snippet|max-video-preview):(-1|[0-9]{1,5})$/D',$part,$m))$parts[$m[1]]=$part;
            elseif(preg_match('/^max-image-preview:(none|standard|large)$/D',$part))$parts['max-image-preview']=$part;
        }
        foreach(self::preview($preview)as $key=>$value)if($value!==''){$key=str_replace('_','-',$key);$parts[$key]=$key.':'.$value;}
        if($forceNoindex)$parts['noindex']='noindex';
        if(isset($parts['noindex']))unset($parts['index']);
        if(isset($parts['nofollow']))unset($parts['follow']);
        if(!isset($parts['index'])&&!isset($parts['noindex']))$parts=['index'=>'index']+$parts;
        if(!isset($parts['follow'])&&!isset($parts['nofollow']))$parts['follow']='follow';
        return implode(',',array_values($parts));
    }

    public static function indexable(string $robots='',bool $noindex=false): bool
    {
        return !$noindex&&!in_array('noindex',explode(',',self::robots($robots)),true);
    }

    public static function lastmod(?string $value): ?string
    {
        $v=trim((string)$value);
        if(!preg_match('/^(\d{4})-(\d{2})-(\d{2})(?:$|[T ])/D',$v,$m)||!checkdate((int)$m[2],(int)$m[3],(int)$m[1]))return null;
        try{$date=new \DateTimeImmutable($v);}catch(\Throwable $e){return null;}
        return $date->getTimestamp()>time()+300?null:$date->format('Y-m-d');
    }

    public static function prepare(array $data,string $template,string $layout,string $request,int $status=200): array
    {
        $path=(string)(parse_url($request,PHP_URL_PATH)?:'/');
        $private=$layout!=='app'||(bool)preg_match('~^/(?:admin|hesabim|siparislerim|siparis|sepet|odeme|destek|giris|kayit|sifremi-unuttum|sifre-sifirla|cikis)(?:/|$)~',$path);
        $error=$template==='frontend/404'||$status>=400;
        $query=[];parse_str((string)(parse_url($request,PHP_URL_QUERY)??''),$query);
        $filtered=false;
        foreach(['q','sort','alt','grup','category','type','hizmet','min_price','max_price','min_rating','tag']as $key){
            if(isset($query[$key])&&$query[$key]!==''&&!($key==='sort'&&$query[$key]==='recommended'))$filtered=true;
        }
        $seo=is_array($data['nvSeoData']??null)?$data['nvSeoData']:[];
        $default=(string)setting('seo_default_robots','index,follow,max-image-preview:large');
        $data['nvRobots']=self::robots((string)($seo['robots']??''),$private||$error||$filtered||!empty($data['noindex']),$seo,$default);
        $data['seoPrivate']=$private;$data['seoError']=$error;
        if(!$private&&!$error){
            $data['canonicalUrl']=self::webUrl((string)($data['canonicalUrl']??''))??self::webUrl(url($path));
            $image=(string)($data['ogImage']??'');
            if(str_starts_with($image,'/')&&!str_starts_with($image,'//'))$image=url($image);
            $data['ogImage']=self::webUrl($image);
            $data['ogImageAlt']=(string)($data['ogImageAlt']??$data['categoryHero']['alt']??$data['catalogHero']['alt']??$data['post']['image_alt']??$data['package']['image_alt']??$data['pageTitle']??setting('site_name'));
        }else{unset($data['canonicalUrl']);$data['ogImage']=null;}
        return $data;
    }
}
