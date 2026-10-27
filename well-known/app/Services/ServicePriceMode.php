<?php
namespace App\Services;

use App\Core\Database;

/**
 * Admin-selected public pricing mode for non-imported packages.
 * Stored in existing settings, no schema migration and no software mutation.
 * auto = historical per-category behavior; fixed = cart checkout;
 * quote = "Fiyat Al"; discuss = "Projenizi Konuşalım".
 */
final class ServicePriceMode
{
    private static array $cache=[];
    private const MODES=['auto','fixed','quote','discuss'];

    public static function normalize(string $value):string
    {
        return in_array($value,self::MODES,true)?$value:'auto';
    }

    public static function forPackage(array $package):string
    {
        $id=(int)($package['id']??0);
        if($id<1)return 'auto';
        if(!isset(self::$cache[$id])){
            try{
                $row=Database::getInstance()->fetch(
                    'SELECT setting_value FROM settings WHERE setting_key=? LIMIT 1',
                    ['package_price_mode_'.$id]
                );
                self::$cache[$id]=self::normalize((string)($row['setting_value']??'auto'));
            }catch(\Throwable $e){self::$cache[$id]='auto';}
        }
        return self::$cache[$id];
    }

    public static function mandatoryQuote(array $package):bool
    {
        if(ServiceCategoryVisibility::isLegacySoftware($package))return false;
        // Actual imported software uses its own product pricing machinery.
        $root=SocialPackageService::category((int)($package['category_id']??0));
        if($root!==null){
            $seen=[];
            while(!empty($root['parent_id'])){
                if(isset($seen[$root['id']]))break;
                $seen[$root['id']]=true;
                $next=SocialPackageService::category((int)$root['parent_id']);
                if(!$next)break;
                $root=$next;
            }
        }
        $slug=mb_strtolower((string)($root['slug']??$package['parent_category_slug']??$package['category_slug']??''),'UTF-8');
        $name=mb_strtolower((string)($root['name']??$package['parent_category_name']??$package['category_name']??''),'UTF-8');
        if(preg_match('/yaz[iı]l[iı]m|yazilim|software|script|hazir|hazır|mobil-uygulama|ios-android|web-site|web-tasarim|wordpress|eticaret|e-ticaret/u',$slug.' '.$name))return true;
        return false;
    }

    public static function quote(array $package,bool $legacyDefault):bool
    {
        if(self::mandatoryQuote($package))return true;
        return match(self::forPackage($package)){
            'fixed'=>false,
            'quote','discuss'=>true,
            default=>$legacyDefault
        };
    }

    public static function cta(array $package):string
    {
        return self::forPackage($package)==='discuss'?'Projenizi Konuşalım':'Fiyat Al';
    }

    public static function save(int $id,string $mode):void
    {
        if($id<=0)throw new \InvalidArgumentException('Paket ID eksik.');
        $mode=self::normalize($mode);
        Database::getInstance()->query(
            "INSERT INTO settings (setting_key,setting_value,setting_group)
             VALUES (?,?,?) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)",
            ['package_price_mode_'.$id,$mode,'packages']
        );
        self::$cache[$id]=$mode;
    }
}
