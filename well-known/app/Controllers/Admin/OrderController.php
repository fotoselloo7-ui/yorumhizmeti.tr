<?php
namespace App\Controllers\Admin;
use App\Core\Controller;
use App\Core\Csrf;
use App\Services\OrderService;

class OrderController extends Controller
{
    public function index(): void
    {
        $status = $_GET['status'] ?? '';
        $where = '';
        $params = [];
        if ($status) { $where = 'WHERE o.order_status = ?'; $params[] = $status; }

        $orders = $this->db->fetchAll("SELECT o.*, u.name as user_name, u.email as user_email FROM orders o LEFT JOIN users u ON o.user_id = u.id {$where} ORDER BY o.created_at DESC LIMIT 100", $params);
        $statusCounts = [
            'all' => $this->db->count('orders'),
            'payment_pending' => $this->db->count('orders', "order_status = 'payment_pending'"),
            'paid' => $this->db->count('orders', "order_status = 'paid'"),
            'processing' => $this->db->count('orders', "order_status = 'processing'"),
            'completed' => $this->db->count('orders', "order_status = 'completed'"),
            'cancelled' => $this->db->count('orders', "order_status = 'cancelled'"),
        ];

        $this->renderAdmin('admin/orders/index', ['pageTitle' => 'Siparişler', 'orders' => $orders, 'statusCounts' => $statusCounts, 'currentStatus' => $status]);
    }

    public function show(string $id): void
    {
        $order = $this->db->fetch("SELECT o.*, u.name as user_name, u.email as user_email, u.phone as user_phone FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE o.id = ?", [(int) $id]);
        if (!$order) { redirect('/admin/siparisler'); }

        $items = $this->db->fetchAll("SELECT * FROM order_items WHERE order_id = ?", [(int) $id]);
        $fields = $this->db->fetchAll("SELECT * FROM order_fields WHERE order_id = ?", [(int) $id]);
        $logs = $this->db->fetchAll("SELECT * FROM order_status_logs WHERE order_id = ? ORDER BY created_at DESC", [(int) $id]);
        $payments = $this->db->fetchAll("SELECT * FROM payments WHERE order_id = ?", [(int) $id]);
        $bankNotifications = $this->db->fetchAll("SELECT * FROM bank_transfer_notifications WHERE order_id = ? ORDER BY created_at DESC", [(int) $id]);
        $smmJobs = \App\Services\SmmFulfillmentService::jobs((int)$id);

        $this->renderAdmin('admin/orders/detail', [
            'pageTitle' => 'Sipariş #' . $order['order_number'],
            'order' => $order, 'items' => $items, 'fields' => $fields,
            'logs' => $logs, 'payments' => $payments, 'bankNotifications' => $bankNotifications, 'smmJobs' => $smmJobs,
        ]);
    }

    public function updateStatus(string $id): void
    {
        Csrf::check();
        $newStatus = $_POST['status'] ?? '';
        $note = trim($_POST['note'] ?? '');

        $orderService = new OrderService();
        $orderService->updateStatus((int) $id, $newStatus, $note, 'admin');

        $order = $this->db->fetch("SELECT o.*, u.email, u.name as user_name FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE o.id = ?", [(int) $id]);

        \App\Services\MailService::sendTemplate('order_status_updated', $order['email'], [
            'user_name' => $order['user_name'],
            'order_number' => $order['order_number'],
            'new_status' => orderStatusLabel($newStatus),
            'note' => $note,
        ]);

        logActivity('order_status', "Sipariş #{$order['order_number']} durumu: {$newStatus}");
        flash('success', 'Sipariş durumu güncellendi.');
        redirect('/admin/siparis/' . $id);
    }

    public function bankNotifications(): void
    {
        $notifications = $this->db->fetchAll("SELECT bn.*, o.order_number, u.name as user_name FROM bank_transfer_notifications bn LEFT JOIN orders o ON bn.order_id = o.id LEFT JOIN users u ON bn.user_id = u.id ORDER BY bn.created_at DESC LIMIT 100");
        $this->renderAdmin('admin/orders/bank-notifications', ['pageTitle' => 'Havale Bildirimleri', 'notifications' => $notifications]);
    }

    public function confirmBankTransfer(string $id): void
    {
        Csrf::check();
        $notification = $this->db->fetch("SELECT * FROM bank_transfer_notifications WHERE id = ?", [(int) $id]);
        if (!$notification) { redirect('/admin/havale-bildirimleri'); }

        $this->db->update('bank_transfer_notifications', ['status' => 'confirmed'], 'id = ?', [(int) $id]);

        $orderService = new OrderService();
        $orderService->updateStatus($notification['order_id'], 'paid', 'Havale/EFT onaylandı.', 'admin');

        $this->db->insert('payments', [
            'order_id' => $notification['order_id'],
            'gateway_key' => 'bank_transfer',
            'amount' => $notification['amount'],
            'status' => 'completed',
        ]);

        logActivity('bank_confirm', "Havale onayı: Sipariş #{$notification['order_id']}");
        flash('success', 'Havale onaylandı.');
        redirect('/admin/havale-bildirimleri');
    }

    public function rejectBankTransfer(string $id): void
    {
        Csrf::check();
        $this->db->update('bank_transfer_notifications', ['status' => 'rejected', 'admin_note' => $_POST['note'] ?? ''], 'id = ?', [(int) $id]);
        flash('success', 'Havale bildirimi reddedildi.');
        redirect('/admin/havale-bildirimleri');
    }
}
