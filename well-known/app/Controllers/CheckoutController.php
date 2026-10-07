<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Auth;
use App\Core\Database;
use App\Core\Csrf;
use App\Services\OrderService;
use App\Services\PaymentGatewayManager;
use App\Services\BankTransferService;

class CheckoutController extends Controller
{
    public function index(): void
    {
        if (!Auth::check()) { flash('error', 'Lütfen giriş yapın.'); redirect('/giris'); }

        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) { redirect('/sepet'); }

        $db = Database::getInstance();
        $cartItems = [];
        $total = 0;
        foreach ($cart as $item) {
            $pkg = $db->fetch("SELECT * FROM packages WHERE id = ? AND status = 'active'", [$item['id']]);
            if ($pkg) {
                $price = ($pkg['discount_price'] && $pkg['discount_price'] < $pkg['price']) ? $pkg['discount_price'] : $pkg['price'];
                $qty = $item['quantity'] ?? 1;
                $fields = $db->fetchAll("SELECT * FROM package_fields WHERE package_id = ? ORDER BY sort_order", [$pkg['id']]);
                $cartItems[] = array_merge($pkg, ['quantity' => $qty, 'line_total' => $price * $qty, 'fields' => $fields]);
                $total += $price * $qty;
            }
        }

        $gatewayManager = new PaymentGatewayManager();
        $paymentOptions = $gatewayManager->getCheckoutOptions();

        $this->render('frontend/checkout', [
            'pageTitle' => 'Ödeme - ' . setting('site_name'),
            'cartItems' => $cartItems,
            'total' => $total,
            'paymentOptions' => $paymentOptions,
            'user' => Auth::user(),
        ]);
    }

    public function process(): void
    {
        if (!Auth::check()) { redirect('/giris'); }
        Csrf::check();

        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) { redirect('/sepet'); }

        $db = Database::getInstance();
        $user = Auth::user();
        $paymentGateway = $_POST['payment_gateway'] ?? '';

        // Sipariş itemlerini hazırla
        $orderItems = [];
        foreach ($cart as $item) {
            $pkg = $db->fetch("SELECT * FROM packages WHERE id = ? AND status = 'active'", [$item['id']]);
            if (!$pkg) continue;
            $price = ($pkg['discount_price'] && $pkg['discount_price'] < $pkg['price']) ? $pkg['discount_price'] : $pkg['price'];

            $orderItem = $pkg;
            $orderItem['quantity'] = $item['quantity'] ?? 1;

            // Dinamik alanlar
            $fields = $db->fetchAll("SELECT * FROM package_fields WHERE package_id = ?", [$pkg['id']]);
            $itemFields = [];
            foreach ($fields as $field) {
                $fieldValue = $_POST['field_' . $pkg['id'] . '_' . $field['field_key']] ?? '';
                $itemFields[] = [
                    'field_key' => $field['field_key'],
                    'field_label' => $field['field_label'],
                    'value' => $fieldValue,
                ];
            }
            $orderItem['fields'] = $itemFields;
            $orderItems[] = $orderItem;
        }

        $paymentMethod = $paymentGateway === 'bank_transfer' ? 'bank_transfer' : 'online';

        $orderService = new OrderService();
        $orderId = $orderService->createOrder(
            $user['id'],
            $orderItems,
            $paymentMethod,
            $paymentGateway,
            $_POST['customer_note'] ?? null
        );

        $order = $db->fetch("SELECT * FROM orders WHERE id = ?", [$orderId]);

        // Mail bildirim
        \App\Services\MailService::sendTemplate('order_created', $user['email'], [
            'user_name' => $user['name'],
            'order_number' => $order['order_number'],
            'total_amount' => money($order['total_amount']),
            'payment_method' => $paymentGateway === 'bank_transfer' ? 'Havale/EFT' : 'Online Ödeme',
        ]);

        // Sepeti temizle
        unset($_SESSION['cart']);

        if ($paymentGateway === 'bank_transfer') {
            flash('success', 'Siparişiniz oluşturuldu. Havale bilgilerini aşağıda görebilirsiniz.');
            redirect('/odeme/basarili?method=bank_transfer&order=' . $order['order_number']);
            return;
        }

        // Online ödeme
        $gatewayManager = new PaymentGatewayManager();
        $service = $gatewayManager->getService($paymentGateway);

        if (!$service || !$service->isConfigured()) {
            flash('error', 'Ödeme modülü yapılandırılmamış. Lütfen Havale/EFT ile ödeme yapın.');
            redirect('/odeme/basarili?method=bank_transfer&order=' . $order['order_number']);
            return;
        }

        $result = $service->initiatePayment($order, $user);

        if ($result['success']) {
            if (!empty($result['iframe_token'])) {
                $_SESSION['paytr_token'] = $result['iframe_token'];
                $this->render('frontend/payment-iframe', [
                    'pageTitle' => 'Ödeme',
                    'iframeToken' => $result['iframe_token'],
                    'gateway' => $paymentGateway,
                ]);
                return;
            }
            if (!empty($result['redirect_url'])) {
                redirect($result['redirect_url']);
                return;
            }
        }

        flash('error', $result['error'] ?? 'Ödeme başlatılamadı.');
        redirect('/siparis/' . $orderId);
    }

    public function paytrCallback(): void
    {
        $gatewayManager = new PaymentGatewayManager();
        $service = $gatewayManager->getService('paytr');
        if (!$service) { echo 'OK'; exit; }

        $result = $service->handleCallback($_POST);
        if ($result['success'] && !empty($result['order_number'])) {
            $db = Database::getInstance();
            $order = $db->fetch("SELECT * FROM orders WHERE order_number = ?", [$result['order_number']]);
            if ($order) {
                $orderService = new OrderService();
                $orderService->updateStatus($order['id'], 'paid', 'PayTR ödeme onayı.', 'paytr');
                $db->insert('payments', [
                    'order_id' => $order['id'],
                    'gateway_key' => 'paytr',
                    'transaction_id' => $result['transaction_id'] ?? '',
                    'amount' => $order['total_amount'],
                    'status' => 'completed',
                    'raw_response' => json_encode($_POST),
                ]);
            }
        }
        echo 'OK';
        exit;
    }

    public function iyzicoCallback(): void
    {
        $gatewayManager = new PaymentGatewayManager();
        $service = $gatewayManager->getService('iyzico');
        if (!$service) { redirect('/odeme/basarisiz'); }

        $result = $service->handleCallback($_POST);
        if ($result['success'] && !empty($result['order_number'])) {
            $db = Database::getInstance();
            $order = $db->fetch("SELECT * FROM orders WHERE order_number = ?", [$result['order_number']]);
            if ($order) {
                $orderService = new OrderService();
                $orderService->updateStatus($order['id'], 'paid', 'iyzico ödeme onayı.', 'iyzico');
                $db->insert('payments', [
                    'order_id' => $order['id'],
                    'gateway_key' => 'iyzico',
                    'transaction_id' => $result['transaction_id'] ?? '',
                    'amount' => $order['total_amount'],
                    'status' => 'completed',
                    'raw_response' => json_encode($_POST),
                ]);
            }
            redirect('/odeme/basarili?order=' . $order['order_number']);
        }

        redirect('/odeme/basarisiz');
    }

    public function success(): void
    {
        $method = $_GET['method'] ?? '';
        $orderNumber = $_GET['order'] ?? '';
        $bankAccounts = [];
        if ($method === 'bank_transfer') {
            $bts = new BankTransferService([]);
            $bankAccounts = $bts->getActiveBankAccounts();
        }

        $this->render('frontend/payment-success', [
            'pageTitle' => 'Ödeme Başarılı',
            'method' => $method,
            'orderNumber' => $orderNumber,
            'bankAccounts' => $bankAccounts,
        ]);
    }

    public function fail(): void
    {
        $this->render('frontend/payment-fail', ['pageTitle' => 'Ödeme Başarısız']);
    }

    public function bankNotify(): void
    {
        if (!Auth::check()) { redirect('/giris'); }
        Csrf::check();

        $orderId = (int) ($_POST['order_id'] ?? 0);
        $order = $this->db->fetch("SELECT * FROM orders WHERE id = ? AND user_id = ?", [$orderId, Auth::id()]);
        if (!$order) { flash('error', 'Sipariş bulunamadı.'); redirect('/siparislerim'); }

        $receiptFile = null;
        if (!empty($_FILES['receipt_file']['name'])) {
            $receiptFile = \App\Core\Upload::receipt($_FILES['receipt_file'], 'receipts');
        }

        $this->db->insert('bank_transfer_notifications', [
            'order_id' => $orderId,
            'user_id' => Auth::id(),
            'bank_name' => $_POST['bank_name'] ?? '',
            'sender_name' => $_POST['sender_name'] ?? '',
            'amount' => (float) ($_POST['amount'] ?? 0),
            'receipt_file' => $receiptFile,
            'note' => $_POST['note'] ?? '',
            'status' => 'pending',
        ]);

        \App\Services\MailService::sendTemplate('bank_transfer_received', Auth::user()['email'], [
            'user_name' => Auth::user()['name'],
            'order_number' => $order['order_number'],
        ]);

        flash('success', 'Havale bildiriminiz alındı. En kısa sürede kontrol edilecektir.');
        redirect('/siparis/' . $orderId);
    }
}
