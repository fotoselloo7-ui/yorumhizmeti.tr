<?php
namespace App\Services;

/**
 * Publicly displayed October 2026 peer-price reference, NOT provider costs.
 * Comparability is intentionally narrow: platform + follower type + exact qty.
 * Never directly modifies live product pricing, cart or provider rate.
 *
 * Takipevi (Instagram Standard): https://www.takipevi.com.tr/instagram-takipci-satin-al
 * TurkInstagram (Instagram Türk): https://www.turkinstagram.com/paketler/
 * SosyalTaktik (TikTok Standard): https://www.sosyaltaktik.com/tiktok-takipci
 * 2026-10-27 audit snapshot. Retail prices change without notice.
 */
final class SocialFollowerBenchmarkService
{
    public const REVIEW_DATE='2026-10-27';
    public const DISCOUNT=0.02; // modestly below like-for-like displayed peer.
    public const MIN_GROSS_MARGIN=0.30; // 30% BEFORE taxes, overhead and support.

    /**
     * Source listings checked 27 October 2026. Prices include only the posted
     * packages, not unverified provider wholesale rates or inferred bonuses.
     * These are independent retail products, not a guarantee of equivalent quality.
     */
    private const PRICES=[
        'instagram_standard'=>[100=>149.90,250=>249.90,500=>449.90,1000=>849.90,
                                2500=>1249.90,5000=>1899.90,10000=>2399.90],
        'instagram_turk'=>[250=>364.78,500=>501.58,1000=>866.38,2000=>1504.78,
                           3000=>2097.58,5000=>3191.98,7500=>4377.58,10000=>6019.18],
        'tiktok_standard'=>[100=>44.90,250=>109,500=>219,1000=>399,1500=>727.90,
                            2000=>970.90,2500=>989,3000=>1455.90,5000=>1899,10000=>5232.90],
    ];
    private const SOURCES=[
        'instagram_standard'=>'https://www.takipevi.com.tr/instagram-takipci-satin-al',
        'instagram_turk'=>'https://www.turkinstagram.com/paketler/',
        'tiktok_standard'=>'https://www.sosyaltaktik.com/tiktok-takipci',
    ];

    public static function type(array $package,array $info):?string
    {
        if(($info['unit']??'')!=='takipçi' || (int)($info['quantity']??0)<1)return null;
        $haystack=mb_strtolower(
            (string)($package['slug']??'').' '.(string)($package['name']??'').' '.
            (string)($package['category_slug']??'').' '.(string)($info['service']??''),'UTF-8');
        $lower=preg_replace('/\x{0307}/u','',$haystack);
        // "Türk", "premium", "organik", "VIP", "garantili", "düşmeyen" are different.
        $turkish=(bool)preg_match('/türk|turk/u',$lower);
        if(preg_match('/organik|vip|premium|bayan|kad[ıi]n|erkek|garanti|d[üu]şmeyen|dusmeyen|ger[çc]ek/u',$lower))return null;
        if(str_contains($lower,'instagram'))return $turkish?'instagram_turk':'instagram_standard';
        if($turkish)return null; // unbenchmarked local variants
        if(str_contains($lower,'tiktok'))return 'tiktok_standard';
        return null;
    }

    public static function recommend(array $package,array $info,?float $supplierCostTry):array
    {
        $type=self::type($package,$info);
        $qty=(int)($info['quantity']??0);
        if($type===null || !isset(self::PRICES[$type][$qty])){
            return ['eligible'=>false,'reason'=>'No exact peer category+quality+quantity match'];
        }
        $peer=(float)self::PRICES[$type][$qty];
        $target=floor($peer*(1-self::DISCOUNT));
        $now=(float)($package['discount_price']>0 && $package['discount_price']<$package['price']?
            $package['discount_price']:$package['price']);
        $row=[
            'eligible'=>true,'type'=>$type,'quantity'=>$qty,'peer_price_try'=>$peer,
            'suggested_ceiling_try'=>$target,'current_price_try'=>$now,
            'source'=>self::SOURCES[$type],'checked'=>self::REVIEW_DATE
        ];
        if($supplierCostTry===null || $supplierCostTry<=0){
            return $row+['safe_to_apply'=>false,'reason'=>'Provider total TRY cost unknown; no price changes permitted'];
        }
        $minRetail=ceil($supplierCostTry/(1-self::MIN_GROSS_MARGIN));
        $row['supplier_total_try']=round($supplierCostTry,2);
        $row['minimum_retail_for_margin_try']=$minRetail;
        if($minRetail>$target){
            return $row+['safe_to_apply'=>false,'reason'=>'Competing at this price would violate gross margin floor'];
        }
        if($now<=$target){
            return $row+['safe_to_apply'=>false,'reason'=>'Existing selling price is already at or below comparable market ceiling'];
        }
        return $row+['safe_to_apply'=>true,'recommended_new_price_try'=>$target,
            'reason'=>'Candidate only: verify comparable fulfillment, refund terms, taxes and provider availability'];
    }
}
