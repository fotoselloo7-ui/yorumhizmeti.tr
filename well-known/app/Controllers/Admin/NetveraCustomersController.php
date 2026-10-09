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
        $ready=false;$members=[];$stats=['customers'=>0,'orders'=>0,'licenses'=>0,'dealers'=>0];
        try {
            $pdo=$this->db->getPdo();
            $table=$pdo->query("SHOW TABLES LIKE 'nv_private_user_map'");
            $ready=(bool)$table->fetchColumn();
            if ($ready) {
                $stats['customers']=(int)$pdo->query("SELECT COUNT(*) FROM nv_private_user_map")->fetchColumn();
                $stats['orders']=(int)$pdo->query("SELECT COUNT(*) FROM nv_private_orders")->fetchColumn();
                $stats['licenses']=(int)$pdo->query("SELECT COUNT(*) FROM nv_private_orders WHERE license_key_encrypted IS NOT NULL OR entitlement_json IS NOT NULL")->fetchColumn();
                $stats['dealers']=(int)$pdo->query("SELECT COUNT(*) FROM nv_private_affiliates")->fetchColumn();
                $members=$this->db->fetchAll("SELECT u.id,u.name,u.email,u.status,
                    m.old_customer_id,m.is_agency,m.want_dealer,m.source_status,
                    (SELECT COUNT(*) FROM nv_private_orders o WHERE o.new_user_id=u.id) AS legacy_orders,
                    (SELECT COUNT(*) FROM nv_private_orders o WHERE o.new_user_id=u.id AND
                      (o.license_key_encrypted IS NOT NULL OR o.entitlement_json IS NOT NULL)) AS licenses,
                    a.status AS dealer_status,a.referral_code
                    FROM nv_private_user_map m INNER JOIN users u ON u.id=m.new_user_id
                    LEFT JOIN nv_private_affiliates a ON a.new_user_id=u.id
                    ORDER BY legacy_orders DESC,u.id ASC LIMIT 250");
            }
        } catch (\Throwable $error) {
            error_log('Netvera private account overview unavailable: '.get_class($error));
            $ready=false;
        }
        $this->renderAdmin('admin/netvera-customers/index',[
            'pageTitle'=>'NetVera Müşteriler ve Bayilik',
            'ready'=>$ready,'members'=>$members,'stats'=>$stats
        ]);
    }
}
