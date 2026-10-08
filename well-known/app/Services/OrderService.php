<?php
namespace App\Services;

use App\Core\Database;

class OrderService
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function createOrder(int $userId, array $cartItems, string $paymentMethod, string $paymentGateway, ?string $customerNote = null): int
    {
        $orderNumber = $this->generateOrderNumber();
        $totalAmount = 0;

        foreach ($cartItems as $item) {
            $price = $this->effectivePrice($item);
            $totalAmount += $price * ($item['quantity'] ?? 1);
        }

        $orderId = $this->db->insert('orders', [
            'user_id' => $userId,
            'order_number' => $orderNumber,
            'total_amount' => $totalAmount,
            'payment_method' => $paymentMethod,
            'payment_gateway' => $paymentGateway,
            'payment_status' => 'pending',
            'order_status' => 'payment_pending',
            'customer_note' => $customerNote,
        ]);

        foreach ($cartItems as $item) {
            $price = $this->effectivePrice($item);
            $qty = $item['quantity'] ?? 1;

            $orderItemId = $this->db->insert('order_items', [
                'order_id' => $orderId,
                'package_id' => $item['id'],
                'package_name' => $item['name'],
                'quantity' => $qty,
                'price' => $price,
                'total' => $price * $qty,
            ]);

            // Dinamik alanlar
            if (!empty($item['fields'])) {
                foreach ($item['fields'] as $field) {
                    $this->db->insert('order_fields', [
                        'order_id' => $orderId,
                        'order_item_id' => $orderItemId,
                        'field_key' => $field['field_key'],
                        'field_label' => $field['field_label'],
                        'field_value' => $field['value'] ?? '',
                    ]);
                }
            }
        }

        $this->logStatus($orderId, null, 'payment_pending', 'Sipariş oluşturuldu.', 'system');

        return $orderId;
    }

    public function updateStatus(int $orderId, string $newStatus, ?string $note = null, string $createdBy = 'admin'): void
    {
        $order = $this->db->fetch("SELECT order_status FROM orders WHERE id = ?", [$orderId]);
        $oldStatus = $order['order_status'] ?? null;

        $updateData = ['order_status' => $newStatus];

        if ($newStatus === 'paid') {
            $updateData['payment_status'] = 'paid';
        } elseif ($newStatus === 'refunded') {
            $updateData['payment_status'] = 'refunded';
        } elseif ($newStatus === 'cancelled') {
            $updateData['payment_status'] = 'failed';
        }

        $this->db->update('orders', $updateData, 'id = ?', [$orderId]);
        $this->logStatus($orderId, $oldStatus, $newStatus, $note, $createdBy);
    }

    public function logStatus(int $orderId, ?string $oldStatus, string $newStatus, ?string $note, string $createdBy): void
    {
        $this->db->insert('order_status_logs', [
            'order_id' => $orderId,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'note' => $note,
            'created_by' => $createdBy,
        ]);
    }

    private function effectivePrice(array $item): float
    {
        $regular = (float) $item['price'];
        $discount = isset($item['discount_price']) ? (float) $item['discount_price'] : 0.0;
        return $discount > 0 && $discount < $regular ? $discount : $regular;
    }

    private function generateOrderNumber(): string
    {
        return 'YH' . date('ymd') . strtoupper(substr(uniqid(), -5));
    }
}
