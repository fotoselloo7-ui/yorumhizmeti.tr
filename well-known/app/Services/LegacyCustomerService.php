<?php
namespace App\Services;

use App\Core\Database;
use PDO;

/**
 * Read-only legacy Netvera history from isolated nv_private_* tables.
 * Live checkout/payment/callback and native package orders are untouched.
 */
final class LegacyCustomerService
{
    public static function overview(int $userId): array
    {
        $empty=['orders'=>[],'affiliate'=>null,'commission_total'=>0,'pending_total'=>0,
                'paid_total'=>0,'legacy_purchase_count'=>0];
        if ($userId<=0) return $empty;
        try {
            $pdo=Database::getInstance()->getPdo();
            $test=$pdo->query("SHOW TABLES LIKE 'nv_private_orders'");
            if (!$test->fetchColumn()) return $empty;
            $q=$pdo->prepare("SELECT old_order_id,order_no,product_name,amount,currency,payment_status,order_status,order_type,
                       (CASE WHEN payment_status IN ('paid','completed','success') AND (license_key_encrypted IS NOT NULL OR entitlement_json IS NOT NULL) THEN 1 ELSE 0 END) as has_entitlements,
                       paid_at,created_at
                       FROM nv_private_orders WHERE new_user_id=? ORDER BY created_at DESC LIMIT 60");
            $q->execute([$userId]); $empty['orders']=$q->fetchAll(PDO::FETCH_ASSOC);
            $empty['legacy_purchase_count']=count($empty['orders']);
            $q=$pdo->prepare("SELECT old_affiliate_id,referral_code,status,commission_rate
                              FROM nv_private_affiliates WHERE new_user_id=? LIMIT 1");
            $q->execute([$userId]);$empty['affiliate']=$q->fetch(PDO::FETCH_ASSOC)?:null;
            if ($empty['affiliate']) {
                $q=$pdo->prepare("SELECT status,COALESCE(SUM(amount),0) AS total
                         FROM nv_private_affiliate_commissions
                         WHERE old_affiliate_id=? GROUP BY status");
                $q->execute([(int)$empty['affiliate']['old_affiliate_id']]);
                foreach($q->fetchAll(PDO::FETCH_ASSOC) as $row){
                    if ($row['status']==='paid') $empty['paid_total']=(float)$row['total'];
                    if ($row['status']==='pending'||$row['status']==='approved')
                        $empty['pending_total']+=(float)$row['total'];
                    $empty['commission_total']+=(float)$row['total'];
                }
            }
        } catch (\Throwable $e) {
            // Not imported or migration schema unavailable; do not break native account.
            error_log('Legacy Netvera account overview unavailable: '.get_class($e));
        }
        return $empty;
    }
}
