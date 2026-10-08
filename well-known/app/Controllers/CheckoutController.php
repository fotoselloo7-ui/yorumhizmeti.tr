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
        foreach ($cart as $key => $item) {
            $pkg = $db->fetch("SELECT p.* FROM packages p
                 INNER JOIN categories c ON p.category_id = c.id AND c.status = 'active'
                 LEFT JOIN categories parent ON parent.id = c.parent_id
                 WHERE p.id = ? AND p.status = 'active'
                   AND (c.parent_id IS NULL OR parent.status = 'active')", [$item['id']]);
            if ($pkg) {
                $price = ($pkg['discount_price'] && $pkg['discount_price'] < $pkg['price']) ? $pkg['discount_price'] : $pkg['price'];
                $qty = $item['quantity'] ?? 1;
                $fields = $db->fetchAll("SELECT * FROM package_fields WHERE package_id = ? ORDER BY sort_order", [$pkg['id']]);
                $cartItems[] = array_merge($pkg, ['quantity' => $qty, 'line_total' => $price * $qty, 'fields' => $fields, 'cart_key' => $key]);
                $total += $price * $qty;
            }
        }

        if (!$cartItems || count($cartItems) !== count($cart)) {
            flash('error', 'Sepetinizde artık satışta olmayan paketler var. Lütfen sepetinizi güncelleyin.');
            redirect('/sepet');
            return;
        }

        $gatewayManager = new PaymentGatewayManager();
        $paymentOptions = $gatewayManager->getCheckoutOptions();

        $testimonialSection = null;
        try {
            $testimonialSection = $db->fetch("SELECT * FROM home_sections WHERE section_key = 'testimonials' AND status = 'active' LIMIT 1");
            if ($testimonialSection) {
                $testimonialSection['extra'] = !empty($testimonialSection['extra_data']) ? json_decode($testimonialSection['extra_data'], true) : [];
            }
        } catch (\Exception $e) {
            $testimonialSection = null;
        }

        $this->render('frontend/checkout', [
            'pageTitle' => 'Ödeme - ' . setting('site_name'),
            'cartItems' => $cartItems,
            'total' => $total,
            'paymentOptions' => $paymentOptions,
            'user' => Auth::user(),
            'testimonialSection' => $testimonialSection,
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
        $paymentGateway = trim((string)($_POST['payment_gateway'] ?? ''));
        $gatewayManager = new PaymentGatewayManager();
        $selectedPayment = null;
        foreach ($gatewayManager->getCheckoutOptions() as $option) {
            if ($option['key'] === $paymentGateway) {
                $selectedPayment = $option;
                break;
            }
        }
        if (!$selectedPayment) {
            flash('error', 'Seçilen ödeme yöntemi şu anda kullanılamıyor. Lütfen geçerli bir yöntem seçin.');
            redirect('/odeme');
            return;
        }
        if (($_POST['terms_accepted'] ?? '') !== '1') {
            flash('error', 'Sipariş oluşturmak için satış sözleşmesini kabul etmeniz gerekiyor.');
            redirect('/odeme');
            return;
        }

        // Sipariş itemlerini hazırla
        $orderItems = [];
        foreach ($cart as $item) {
            $pkg = $db->fetch("SELECT p.* FROM packages p
                 INNER JOIN categories c ON p.category_id = c.id AND c.status = 'active'
                 LEFT JOIN categories parent ON parent.id = c.parent_id
                 WHERE p.id = ? AND p.status = 'active'
                   AND (c.parent_id IS NULL OR parent.status = 'active')", [$item['id']]);
            if (!$pkg) continue;
            $price = ($pkg['discount_price'] && $pkg['discount_price'] < $pkg['price']) ? $pkg['discount_price'] : $pkg['price'];

            $orderItem = $pkg;
            $minQty = max(1, (int)$pkg['min_quantity']);
            $maxQty = max($minQty, (int)$pkg['max_quantity']);
            $orderItem['quantity'] = max($minQty, min($maxQty, (int)($item['quantity'] ?? $minQty)));

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

        if (!$orderItems || count($orderItems) !== count($cart)) {
            flash('error', 'Sepetinizde artık satışta olmayan paketler var. Lütfen sepetinizi güncelleyin.');
            redirect('/sepet');
            return;
        }

        $paymentMethod = $selectedPayment['type'] === 'manual' ? 'bank_transfer' : 'online';

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
            flash('error', 'Online ödeme şu anda başlatılamıyor. Siparişiniz ödeme bekliyor; destek ekibinden yardım alabilirsiniz.');
            redirect('/siparis/' . $orderId);
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
                    'order' => $order,
                    'user' => $user,
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

    /**
     * Purely local visual preview. No real PayTR token, orders or transactions.
     * The merchant credentials/callback cannot be exercised via localhost.
     */
    public function paytrPreview(): void
    {
        $env=strtolower((string)($_ENV['APP_ENV']??'production'));
        $host=strtolower((string)($_SERVER['HTTP_HOST']??''));
        if (!in_array($env,['local','development'],true)
            || !preg_match('/^(?:127\.0\.0\.1|localhost)(?::\d{1,5})?$/',$host)) {
            http_response_code(404);
            $this->render('frontend/404',['pageTitle'=>'Sayfa Bulunamadı']);
            return;
        }
        $this->render('frontend/payment-iframe',[
            'pageTitle'=>'PayTR Ödeme Tasarım Önizlemesi',
            'iframeToken'=>'',
            'gateway'=>'paytr',
            'preview'=>true,
            'order'=>['order_number'=>'ORNEK-001','total_amount'=>6990.00],
            'user'=>[],
        ]);
    }

    /**
     * PayTR Step 2 is a backend-to-backend callback, not a customer redirect.
     * Never acknowledge an unauthenticated or unknown order and NEVER create
     * a second payment for a repeated (legitimate) PayTR notification.
     */
    public function paytrCallback(): void
    {
        header('Content-Type: text/plain; charset=utf-8');
        $manager = new PaymentGatewayManager();
        $service = $manager->getService('paytr');
        if (!$service || !$service->isConfigured()) {
            http_response_code(503);
            echo 'GATEWAY UNAVAILABLE';
            return;
        }
        $result=$service->handleCallback($_POST);
        if (empty($result['verified']) || empty($result['order_number'])) {
            http_response_code(403);
            echo 'INVALID SIGNATURE';
            return;
        }
        $db=Database::getInstance();
        $pdo=$db->getPdo();
        try {
            $pdo->beginTransaction();
            $order=$db->fetch('SELECT * FROM orders WHERE order_number=? FOR UPDATE',
                [(string)$result['order_number']]);
            if(!$order || ($order['payment_gateway']??'')!=='paytr'){
                $pdo->rollBack();
                http_response_code(404);
                echo 'ORDER NOT FOUND';
                return;
            }
            $expected=(int)round(((float)$order['total_amount'])*100);
            $paid=(int)($_POST['total_amount']??0);
            // PayTR total_amount can exceed payment_amount for instalments;
            // never approve an underpayment, even with a valid signed callback.
            $originalAmount=(string)($_POST['payment_amount']??'');
            if ($expected<1 || $paid<$expected ||
                ($originalAmount!=='' && (!ctype_digit($originalAmount) || (int)$originalAmount!==$expected))){
                $pdo->rollBack();
                http_response_code(422);
                echo 'AMOUNT MISMATCH';
                return;
            }
            if(!empty($result['success'])){
                // Preserve fulfilled orders and make duplicated callbacks harmless.
                if(($order['payment_status']??'')!=='paid'){
                    $payment = $db->fetch(
                        "SELECT id FROM payments WHERE order_id=? AND gateway_key='paytr' AND status='completed' LIMIT 1",
                        [(int)$order['id']]
                    );
                    if(!$payment){
                        (new OrderService())->updateStatus((int)$order['id'],'paid','PayTR ödeme onayı.','paytr');
                        $safeResponse=[
                          'merchant_oid'=>(string)($result['order_number']??''),
                          'status'=>'success',
                          'total_amount'=>$paid,
                          'currency'=>substr((string)($_POST['currency']??'TL'),0,4),
                          'test_mode'=>($_POST['test_mode']??'')==='1'?1:0
                        ];
                        $db->insert('payments',[
                          'order_id'=>(int)$order['id'],
                          'gateway_key'=>'paytr',
                          'transaction_id'=>(string)$result['transaction_id'],
                          'amount'=>$order['total_amount'],
                          'status'=>'completed',
                          'raw_response'=>json_encode($safeResponse,JSON_UNESCAPED_UNICODE)
                        ]);
                    }
                }
            }else if(($order['payment_status']??'')==='pending'){
                // A failed attempt must not downgrade a previously paid order.
                $db->update('orders',['payment_status'=>'failed'],'id=?',[(int)$order['id']]);
            }
            $pdo->commit();
            echo 'OK';
        }catch(\Throwable $e){
            if($pdo->inTransaction())$pdo->rollBack();
            error_log('PayTR callback persistence: '.get_class($e));
            http_response_code(503);
            echo 'RETRY';
        }
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
