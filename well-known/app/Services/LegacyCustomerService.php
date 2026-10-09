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
                'paid_total'=>0,'legacy_purchase_count'=>0,'tickets'=>[],'software'=>[]];
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
            // Show only products from verified paid orders mapped to this user.
            // Guest orders and failed payments cannot create purchase entitlements.
            $q=$pdo->prepare("SELECT i.old_item_id,i.item_name,i.item_slug,i.item_type,i.sale_price,
                o.order_no,o.paid_at,o.payment_status,
                CASE WHEN (o.entitlement_json IS NOT NULL OR i.entitlement_json IS NOT NULL
                           OR o.license_key_encrypted IS NOT NULL) THEN 1 ELSE 0 END AS has_rights
                FROM nv_private_order_items i
                INNER JOIN nv_private_orders o ON o.old_order_id=i.old_order_id
                WHERE o.new_user_id=? AND o.payment_status IN ('paid','completed','success')
                  AND i.item_type IN ('script','software')
                ORDER BY o.paid_at DESC,i.old_item_id DESC LIMIT 100");
            $q->execute([$userId]);$empty['software']=$q->fetchAll(PDO::FETCH_ASSOC);
            $ticketTable=$pdo->query("SHOW TABLES LIKE 'nv_private_support_tickets'");
            if ($ticketTable->fetchColumn()) {
                $q=$pdo->prepare("SELECT old_ticket_id,subject,priority,status,created_at
                    FROM nv_private_support_tickets WHERE new_user_id=?
                    ORDER BY created_at DESC LIMIT 12");
                $q->execute([$userId]);
                $empty['tickets']=$q->fetchAll(PDO::FETCH_ASSOC);
            }
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
