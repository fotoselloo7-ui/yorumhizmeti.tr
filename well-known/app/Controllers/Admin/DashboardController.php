<?php
namespace App\Controllers\Admin;
use App\Core\Controller;
use App\Core\Database;

class DashboardController extends Controller
{
    public function index(): void
    {
        $db = Database::getInstance();

        $stats = [
            'orders_today' => $db->count('orders', 'DATE(created_at) = CURDATE()'),
            'orders_total' => $db->count('orders'),
            'orders_pending' => $db->count('orders', "order_status IN ('payment_pending','preparing','processing','waiting_customer_info')"),
            'orders_completed' => $db->count('orders', "order_status = 'completed'"),
            'total_users' => $db->count('users'),
            'open_tickets' => $db->count('support_tickets', "status != 'closed'"),
            'waiting_customer_tickets' => $db->count('support_tickets', "status = 'customer_reply'"),
            'total_revenue' => $db->fetch("SELECT COALESCE(SUM(total_amount), 0) as total FROM orders WHERE payment_status = 'paid'")['total'] ?? 0,
            'revenue_30d' => $db->fetch("SELECT COALESCE(SUM(total_amount), 0) as total FROM orders WHERE payment_status = 'paid' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)")['total'] ?? 0,
            'pending_payments' => $db->count('orders', "payment_status = 'pending'"),
            'pending_transfers' => $db->count('bank_transfer_notifications', "status = 'pending'"),
        ];

        // Kritik uyarılar
        $alerts = [];
        $paytrKey = setting('paytr_merchant_id');
        $iyzicoKey = setting('iyzico_api_key');
        if (empty($paytrKey) && empty($iyzicoKey)) {
            $alerts[] = ['type' => 'warning', 'icon' => 'alert-triangle', 'text' => 'Ödeme API bilgileri tanımlanmamış.', 'link' => '/admin/odeme-modulleri', 'linkText' => 'Ayarla'];
        }
        if ($stats['pending_transfers'] > 0) {
            $alerts[] = ['type' => 'warning', 'icon' => 'inbox', 'text' => $stats['pending_transfers'] . ' adet bekleyen havale bildirimi var.', 'link' => '/admin/havale-bildirimleri', 'linkText' => 'İncele'];
        }
        if ($stats['waiting_customer_tickets'] > 0) {
            $alerts[] = ['type' => 'info', 'icon' => 'headphones', 'text' => $stats['waiting_customer_tickets'] . ' adet cevap bekleyen destek talebi var.', 'link' => '/admin/destek', 'linkText' => 'Görüntüle'];
        }

        // License status alert
        try {
            $licenseService = \App\Services\LicenseService::getInstance();
            if ($licenseService->isEnabled() && !$licenseService->isLocalBypass()) {
                $licenseStatus = $licenseService->getCachedStatus();
                $licenseBadge = $licenseService->getStatusBadge();
                if (!in_array($licenseStatus, ['active', 'not_configured'])) {
                    $alertType = in_array($licenseStatus, ['grace', 'server_error']) ? 'warning' : 'danger';
                    $alerts[] = [
                        'type' => $alertType,
                        'icon' => 'shield',
                        'text' => 'Lisans durumu: ' . $licenseBadge['label'] . '. Lütfen lisans bilgilerinizi kontrol edin.',
                        'link' => '/admin/lisans',
                        'linkText' => 'Lisans Yönetimi',
                    ];
                }
            }
        } catch (\Exception $e) {
            // License check should never crash the dashboard
        }

        $recentOrders = $db->fetchAll("SELECT o.*, u.name as user_name, u.email as user_email FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 10");
        $recentTickets = $db->fetchAll("SELECT st.*, u.name as user_name FROM support_tickets st LEFT JOIN users u ON st.user_id = u.id WHERE st.status != 'closed' ORDER BY st.updated_at DESC LIMIT 5");

        $this->renderAdmin('admin/dashboard', [
            'pageTitle' => 'Dashboard',
            'stats' => $stats,
            'alerts' => $alerts,
            'recentOrders' => $recentOrders,
            'recentTickets' => $recentTickets,
        ]);
    }
}
