<?php
namespace App\Services;

use App\Core\Database;

/**
 * Fail closed for newly drafted competitor-priced social packages.
 * An inactive draft remains invisible to buyers; switching status to active
 * cannot bypass provider verification or silently sell below supplier cost.
 */
final class MarketProposalGuard
{
    private static array $origin=[];

    public static function origin(int $packageId):?array
    {
        if($packageId<=0)return null;
        if(array_key_exists($packageId,self::$origin))return self::$origin[$packageId];
        try{
            $data=Database::getInstance()->fetch(
                'SELECT setting_value FROM settings WHERE setting_key=? LIMIT 1',
                ['netvera_market_proposal_'.$packageId]
            );
            $decoded=json_decode((string)($data['setting_value']??''),true);
            return self::$origin[$packageId]=is_array($decoded)?$decoded:null;
        }catch(\Throwable $e){return self::$origin[$packageId]=null;}
    }

    public static function ready(array $package):bool
    {
        $id=(int)($package['id']??0);
        $origin=self::origin($id);
        if($origin===null)return true; // Existing real catalogue is untouched.
        if(($origin['kind']??'')!=='social')return true;
        $map=SmmCatalogService::mapping($id);
        if(!$map || empty($map['enabled']) || empty($map['is_available']) ||
           empty($map['provider_active']) || (string)($map['service_type']??'')!=='Default'){
            return false;
        }
        $qty=(int)($map['fulfillment_quantity']??0);
        if($qty<1 || $qty<(int)$map['min_quantity'] || $qty>(int)$map['max_quantity'])return false;
        // The quote's wholesale currency must be TRY. Do not convert USD/EUR
        // via fabricated FX rates or estimate unverified support/tax costs.
        try{
            $rate=Database::getInstance()->fetch(
                "SELECT s.rate_per_1000,p.currency FROM smm_services s
                 JOIN smm_providers p ON p.id=s.provider_id WHERE s.id=?",
                [(int)$map['service_id']]
            );
            if(strtoupper((string)($rate['currency']??''))!=='TRY')return false;
            $cost=(float)($rate['rate_per_1000']??0)*$qty/1000;
            $retail=(float)((!empty($package['discount_price']) &&
                  $package['discount_price']<$package['price'])
                  ?$package['discount_price']:$package['price']);
            if($cost<=0 || $retail<=0 || $retail < $cost/0.70)return false;
            return true;
        }catch(\Throwable $e){return false;}
    }
}
