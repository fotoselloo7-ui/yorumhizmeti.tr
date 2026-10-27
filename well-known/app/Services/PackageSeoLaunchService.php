<?php
namespace App\Services;

/**
 * Service package editorial improvement suggestions.
 * Never fabricates deliverables, performance numbers, guarantees or package
 * prices. Imported NetVera ready software and custom manual SEO are protected.
 */
final class PackageSeoLaunchService
{
    public static function eligible(array $package):bool
    {
        if(ServiceCategoryVisibility::isLegacySoftware($package))return false;
        $id=(int)($package['category_id']??0);
        if($id>0 && SoftwareCatalogService::isSoftwareCategory($id))return false;
        $slug=(string)($package['category_slug']??'');
        return !preg_match('/hazir.yazilim|script|tema|eklenti|software/i',$slug);
    }

    public static function name(array $p):string
    {
        $name=trim((string)($p['name']??''));
        if($name!=='')return $name;
        $seo=trim((string)($p['seo_title']??''));
        $seo=preg_replace('/\s*[|–]\s*(?:Yorum\s*Hizmet(?:i)?|NetVera).*$/iu','',$seo)??'';
        if($seo!=='')return $seo;
        return trim((string)($p['category_name']??'Hizmet').' '.str_replace('-',' ',(string)($p['slug']??'')));
    }

    public static function recommend(array $package,bool $quote):array
    {
        $name=self::name($package);
        $category=trim((string)($package['category_name']??'Dijital hizmet'));
        $short=trim(strip_tags((string)($package['short_description']??'')));
        $basic=mb_strlen($short,'UTF-8')>=70 && !preg_match('/YorumHizmeti|Yorum Hizmeti/u',$short)
            ? $short
            : $name.' seçeneği, '.$category.' kapsamında sunulur. Hizmetin uygulama koşullarını ve ihtiyaç duyulan bilgileri inceleyin.';
        $offer=$quote
            ? 'Kapsam ve fiyat proje ihtiyaçlarına göre teklif aşamasında belirlenir.'
            : 'Miktarı, teslim koşullarını ve güncel satış fiyatını ürün detayında inceleyin.';
        $description=trim($basic.' '.$offer);
        $title=mb_substr($name.' | NetVera',0,200,'UTF-8');
        $focus=mb_substr($name,0,100,'UTF-8');
        return [
            'seo_title'=>$title,
            'seo_description'=>mb_substr($description,0,250,'UTF-8'),
            'seo_focus_keyword'=>$focus,
            'og_title'=>$title,
            'og_description'=>mb_substr($description,0,300,'UTF-8'),
            'short_description'=>mb_substr($basic,0,490,'UTF-8'),
        ];
    }

    public static function stale(string $field,?string $existing):bool
    {
        $s=trim((string)$existing);
        if($s==='')return true;
        if(preg_match('/YorumHizmeti|Yorum Hizmet|YorumPanel/u',$s))return true;
        if(in_array($field,['seo_description','og_description'],true) && mb_strlen($s,'UTF-8')<65)return true;
        return false;
    }

    public static function proposedUpdates(array $package,bool $quote):array
    {
        $suggestions=self::recommend($package,$quote);
        $updates=[];
        foreach($suggestions as $field=>$value){
            if(self::stale($field,$package[$field]??null))$updates[$field]=$value;
        }
        return $updates;
    }
}
