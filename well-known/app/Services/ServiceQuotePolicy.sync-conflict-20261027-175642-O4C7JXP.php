<?php
namespace App\Services;

/**
 * Real service work is scoped and quoted; fixed-quantity social packages retain
 * their admin/provider-controlled price. Imported NetVera scripts are untouched.
 */
final class ServiceQuotePolicy
{
    public static function needsQuote(array $category):bool
    {
        if(ServiceCategoryVisibility::isLegacySoftware($category))return false;
        return CatalogMenuService::bucket($category)!=='social';
    }

    public static function package(array $package):bool
    {
        $category=SocialPackageService::category((int)($package['category_id']??0));
        if(!$category)return false; // Unknown rows are not rewritten.
        $root=$category;$seen=[];
        while(!empty($root['parent_id'])){
            if(isset($seen[$root['id']]))return false;
            $seen[$root['id']]=true;
            $next=SocialPackageService::category((int)$root['parent_id']);
            if(!$next)break;
            $root=$next;
        }
        return self::needsQuote($root);
    }

    public static function url(array $package):string
    {
        $name=trim((string)($package['name']??'Dijital hizmet'));
        return '/iletisim?konu='.rawurlencode(mb_substr($name.' için teklif talebi',0,140));
    }
}
