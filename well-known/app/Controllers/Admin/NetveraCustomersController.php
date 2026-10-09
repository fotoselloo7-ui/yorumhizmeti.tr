<?php
namespace App\Controllers\Admin;

use App\Core\Controller;

/**
 * Administrator-only read view. No destructive customer/admin mutations.
 * Source customer, dealer and paid-order data must first pass staging import.
 */
final class NetveraCustomersController extends Controller
{
    public function index(): void
    {
        $ready=false;$members=[];$unclaimedOrders=[];$stats=['customers'=>0,'orders'=>0,'licenses'=>0,'dealers'=>0,'unclaimed'=>0,'tickets'=>0];
        try {
            $pdo=$this->db->getPdo();
            $table=$pdo->query("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema=DATABASE() AND table_name IN ('nv_private_user_map','nv_private_orders','nv_private_affiliates','nv_private_support_tickets')");
            $ready=(int)$table->fetchColumn()===4;
            if ($ready) {
                $stats['customers']=(int)$pdo->query("SELECT COUNT(*) FROM nv_private_user_map")->fetchColumn();
                $stats['orders']=(int)$pdo->query("SELECT COUNT(*) FROM nv_private_orders")->fetchColumn();
                $stats['licenses']=(int)$pdo->query("SELECT COUNT(*) FROM nv_private_orders WHERE payment_status IN ('paid','completed','success') AND (license_key_encrypted IS NOT NULL OR entitlement_json IS NOT NULL)")->fetchColumn();
                $stats['dealers']=(int)$pdo->query("SELECT COUNT(*) FROM nv_private_affiliates")->fetchColumn();
                $stats['unclaimed']=(int)$pdo->query("SELECT COUNT(*) FROM nv_private_orders WHERE new_user_id IS NULL")->fetchColumn();
                $stats['tickets']=(int)$pdo->query("SELECT COUNT(*) FROM nv_private_support_tickets")->fetchColumn();
                $unclaimedOrders=$this->db->fetchAll("SELECT old_order_id,order_no,customer_name,customer_email,product_name,amount,payment_status,created_at FROM nv_private_orders WHERE new_user_id IS NULL ORDER BY created_at DESC,old_order_id DESC LIMIT 70");
                $members=$this->db->fetchAll("SELECT u.id,u.name,u.email,u.status,
                    m.old_customer_id,m.is_agency,m.want_dealer,m.source_status,
                    (SELECT COUNT(*) FROM nv_private_orders o WHERE o.new_user_id=u.id) AS legacy_orders,
                    (SELECT COUNT(*) FROM nv_private_orders o WHERE o.new_user_id=u.id AND
                      o.payment_status IN ('paid','completed','success') AND (o.license_key_encrypted IS NOT NULL OR o.entitlement_json IS NOT NULL)) AS licenses,
                    a.status AS dealer_status,a.referral_code
                    FROM nv_private_user_map m INNER JOIN users u ON u.id=m.new_user_id
                    LEFT JOIN nv_private_affiliates a ON a.new_user_id=u.id
                    ORDER BY legacy_orders DESC,u.id ASC LIMIT 250");
            }
        } catch (\Throwable $error) {
            error_log('Netvera private account overview unavailable: '.get_class($error));
            $ready=false;
        }

        // Native customers and real paid orders always work independently of legacy SQL import.
        $nativeUsers=[];$nativeOrders=[];$nativeDealerAccounts=[];$nativeStats=[
            'users'=>0,'paid_orders'=>0,'orders'=>0,'dealers'=>0,'waiting'=>0
        ];
        try {
            $nativeStats['users']=$this->db->count('users');
            $nativeStats['orders']=$this->db->count('orders');
            $nativeStats['paid_orders']=$this->db->count('orders',"payment_status='paid'");
            $nativeUsers=$this->db->fetchAll(
                "SELECT u.id,u.name,u.email,u.status,u.created_at,
                  (SELECT COUNT(*) FROM orders o WHERE o.user_id=u.id) AS order_count,
                  (SELECT COALESCE(SUM(o.total_amount),0) FROM orders o WHERE o.user_id=u.id AND o.payment_status='paid') AS paid_total
                 FROM users u ORDER BY u.id DESC LIMIT 150"
            );
            $nativeOrders=$this->db->fetchAll(
                "SELECT o.id,o.order_number,o.total_amount,o.payment_status,o.order_status,o.created_at,
                        u.name AS customer_name,u.email AS customer_email
                 FROM orders o LEFT JOIN users u ON u.id=o.user_id ORDER BY o.id DESC LIMIT 70"
            );
            if (\App\Services\DealerProgramService::ready()) {
                $nativeDealerAccounts=\App\Services\DealerProgramService::adminList();
                $nativeStats['dealers']=count($nativeDealerAccounts);
                $nativeStats['waiting']=count(array_filter($nativeDealerAccounts,
                    static fn($d)=>($d['status']??'')==='pending'));
            }
        } catch (\Throwable $error) {
            error_log('Native customer dashboard unavailable: '.get_class($error));
        }

        $this->renderAdmin('admin/netvera-customers/index',[
            'pageTitle'=>'NetVera Müşteriler ve Bayilik',
            'ready'=>$ready,'members'=>$members,'unclaimedOrders'=>$unclaimedOrders,'stats'=>$stats,
            'nativeUsers'=>$nativeUsers,'nativeOrders'=>$nativeOrders,
            'nativeDealerAccounts'=>$nativeDealerAccounts,'nativeStats'=>$nativeStats
        ]);
    }
}
