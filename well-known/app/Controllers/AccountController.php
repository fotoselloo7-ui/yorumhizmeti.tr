<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Auth;
use App\Core\Csrf;
use App\Services\LegacyCustomerService;

class AccountController extends Controller
{
    public function index(): void
    {
        $user = Auth::user();
        $recentOrders = $this->db->fetchAll("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 5", [$user['id']]);
        $totalOrders = $this->db->count('orders', 'user_id = ?', [$user['id']]);
        $pendingOrders = $this->db->count('orders', "user_id = ? AND order_status IN ('payment_pending','preparing','processing','waiting_customer_info')", [$user['id']]);
        $completedOrders = $this->db->count('orders', "user_id = ? AND order_status = 'completed'", [$user['id']]);
        $openTickets = $this->db->count('support_tickets', "user_id = ? AND status != 'closed'", [$user['id']]);
        $recentTickets = $this->db->fetchAll("SELECT * FROM support_tickets WHERE user_id = ? ORDER BY updated_at DESC LIMIT 3", [$user['id']]);

        $this->render('frontend/account/dashboard', [
            'pageTitle' => 'Hesabım',
            'user' => $user,
            'recentOrders' => $recentOrders,
            'totalOrders' => $totalOrders,
            'pendingOrders' => $pendingOrders,
            'completedOrders' => $completedOrders,
            'openTickets' => $openTickets,
            'recentTickets' => $recentTickets,
            'netveraHistory' => LegacyCustomerService::overview((int)$user['id']),
        ]);
    }

    public function update(): void
    {
        Csrf::check();
        $user = Auth::user();
        $this->db->update('users', [
            'name' => trim($_POST['name'] ?? $user['name']),
            'phone' => trim($_POST['phone'] ?? ''),
        ], 'id = ?', [$user['id']]);
        flash('success', 'Profil bilgileriniz güncellendi.');
        redirect('/hesabim');
    }

    public function changePassword(): void
    {
        Csrf::check();
        $user = Auth::user();

        if (!password_verify($_POST['current_password'] ?? '', $user['password'])) {
            flash('error', 'Mevcut şifre hatalı.');
            redirect('/hesabim');
        }

        $newPassword = $_POST['new_password'] ?? '';
        if (strlen($newPassword) < 6) {
            flash('error', 'Yeni şifre en az 6 karakter olmalıdır.');
            redirect('/hesabim');
        }

        $this->db->update('users', ['password' => password_hash($newPassword, PASSWORD_DEFAULT)], 'id = ?', [$user['id']]);
        flash('success', 'Şifreniz güncellendi.');
        redirect('/hesabim');
    }

    public function orders(): void
    {
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 10;
        $offset = ($page - 1) * $perPage;
        $userId = Auth::id();

        $total = $this->db->count('orders', 'user_id = ?', [$userId]);
        $orders = $this->db->fetchAll("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT {$perPage} OFFSET {$offset}", [$userId]);

        $this->render('frontend/account/orders', [
            'pageTitle' => 'Siparişlerim',
            'orders' => $orders,
            'page' => $page,
            'totalPages' => max(1, ceil($total / $perPage)),
            'netveraHistory' => LegacyCustomerService::overview((int)$userId),
        ]);
    }

    public function orderDetail(string $id): void
    {
        $order = $this->db->fetch("SELECT * FROM orders WHERE id = ? AND user_id = ?", [(int) $id, Auth::id()]);
        if (!$order) { flash('error', 'Sipariş bulunamadı.'); redirect('/siparislerim'); }

        $items = $this->db->fetchAll("SELECT * FROM order_items WHERE order_id = ?", [$order['id']]);
        $fields = $this->db->fetchAll("SELECT * FROM order_fields WHERE order_id = ?", [$order['id']]);
        $logs = $this->db->fetchAll("SELECT * FROM order_status_logs WHERE order_id = ? ORDER BY created_at DESC", [$order['id']]);
        $bankNotifications = $this->db->fetchAll("SELECT * FROM bank_transfer_notifications WHERE order_id = ? ORDER BY created_at DESC", [$order['id']]);
        $bankAccounts = $this->db->fetchAll("SELECT * FROM bank_accounts WHERE status = 'active' ORDER BY sort_order");

        $this->render('frontend/account/order-detail', [
            'pageTitle' => 'Sipariş #' . $order['order_number'],
            'order' => $order,
            'items' => $items,
            'fields' => $fields,
            'logs' => $logs,
            'bankNotifications' => $bankNotifications,
            'bankAccounts' => $bankAccounts,
        ]);
    }
}
