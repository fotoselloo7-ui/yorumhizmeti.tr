<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Services\NetveraBridgeService as Catalog;
use App\Services\PaymentGatewayManager;
use App\Services\OrderService;

/**
 * Standalone paid-ready-script checkout, sharing the existing PayTR gateway
 * and signed, idempotent /payment/paytr/callback handler.
 * Does not mutate the service-package cart or invent a license entitlement.
 */
final class ScriptCheckoutController extends Controller
{
    private function product(string $slug, bool $lock = false): ?array
    {
        // Only live database products may be sold: no snapshot-only orders.
        $query = 'SELECT * FROM nv_legacy_script_products WHERE slug=? AND active=1 LIMIT 1';
        if ($lock) $query .= ' FOR UPDATE';
        try {
            $p = $this->db->fetch($query, [$slug]);
        } catch (\Throwable $e) {
            error_log('Script checkout product unavailable: '.get_class($e));
            return null;
        }
        if (!$p) return null;
        $data = Catalog::jsonFields($p);
        $price = (float)($p['price'] ?? 0);
        if (!Catalog::purchaseEnabled($p, $data)
            || !is_finite($price) || $price < 0.01 || $price > 99999999.99) return null;
        return $p;
    }

    /**
     * Collect only installation metadata, never hosting passwords, API tokens
     * or credentials. Every field is server-validated before an order is saved.
     */
    private function installationFields(array $input): array
    {
        $value = static function(string $key, int $max) use ($input): string {
            $raw = $input[$key] ?? '';
            if (!is_string($raw) || mb_strlen($raw, 'UTF-8') > $max) {
                throw new \InvalidArgumentException('Kurulum bilgilerindeki alan uzunluklarını kontrol edin.');
            }
            return trim($raw);
        };
        $domainMode = $value('install_domain_mode', 24);
        $hostingMode = $value('install_hosting_mode', 24);
        if (!in_array($domainMode, ['existing', 'need_domain'], true) ||
            !in_array($hostingMode, ['existing', 'need_hosting'], true)) {
            throw new \InvalidArgumentException('Alan adı ve hosting durumunu seçin.');
        }
        $domain = strtolower(rtrim($value('install_domain', 253), '.'));
        if ($domainMode === 'existing') {
            if ($domain === '' || !str_contains($domain, '.') ||
                filter_var($domain, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) === false ||
                filter_var($domain, FILTER_VALIDATE_IP) !== false) {
                throw new \InvalidArgumentException('Kurulum yapılacak alan adını örnek.com biçiminde yazın.');
            }
        } else {
            $domain = '';
        }
        $provider = $value('install_hosting_provider', 120);
        $panel = $value('install_hosting_panel', 250);
        $username = $value('install_hosting_username', 100);
        if ($hostingMode === 'existing') {
            if ($provider === '') {
                throw new \InvalidArgumentException('Hosting firmanızı yazın veya hosting hizmeti istiyorum seçeneğini işaretleyin.');
            }
            if ($panel !== '') {
                $parts = parse_url($panel);
                if (!filter_var($panel, FILTER_VALIDATE_URL) ||
                    ($parts['scheme'] ?? '') !== 'https' ||
                    empty($parts['host']) || isset($parts['user']) || isset($parts['pass'])) {
                    throw new \InvalidArgumentException('Hosting panel bağlantısı HTTPS adresi olmalı; bağlantıya şifre eklemeyin.');
                }
            }
        } else {
            $provider = $panel = $username = '';
        }
        $note = $value('install_notes', 500);
        // Avoid encouraging common password/token disclosures in free text.
        if (preg_match('/(?:şifre|sifre|password|passwd|api[\s_-]*key|secret|access[\s_-]*token|gizli[\s_-]*anahtar)\s*(?:[:=]|-|\s+\S)/iu',
            implode(' ', [$provider, $panel, $username, $note]))) {
            throw new \InvalidArgumentException('Hosting şifresi veya API anahtarı bu formdan paylaşılmamalı. Lütfen gizli bilgileri kaldırın.');
        }
        $fields = [
            ['nv_install_domain_mode', 'Alan Adı Durumu', $domainMode === 'existing' ? 'Mevcut alan adım var' : 'Yeni alan adı istiyorum'],
            ['nv_install_hosting_mode', 'Hosting Durumu', $hostingMode === 'existing' ? 'Mevcut hostingim var' : 'NetVera hosting desteği istiyorum'],
        ];
        if ($domain !== '') $fields[] = ['nv_install_domain', 'Kurulacak Domain', $domain];
        if ($provider !== '') $fields[] = ['nv_install_hosting_provider', 'Hosting Firması', $provider];
        if ($panel !== '') $fields[] = ['nv_install_hosting_panel', 'Hosting Panel URL', $panel];
        if ($username !== '') $fields[] = ['nv_install_hosting_username', 'Hosting Kullanıcı Adı', $username];
        if ($note !== '') $fields[] = ['nv_install_notes', 'Kurulum Notları', $note];
        return $fields;
    }

    private function paytr(): ?\App\Services\PaymentGatewayInterface
    {
        $service = (new PaymentGatewayManager())->getService('paytr');
        return $service && $service->isConfigured() ? $service : null;
    }

    public function index(string $slug): void
    {
        $slug = rawurldecode($slug);
        $product = $this->product($slug);
        if (!$product) {
            http_response_code(404);
            $this->render('frontend/404', ['pageTitle' => 'Satışta Yazılım Bulunamadı']);
            return;
        }
        if (!Auth::check()) {
            // Session-owned exact slug, never a caller-supplied redirect URL.
            $_SESSION['nv_script_checkout_return'] = (string)$product['slug'];
            flash('info', 'Satın almaya devam etmek için giriş yapın veya hesap oluşturun.');
            redirect('/giris');
            return;
        }
        $service = $this->paytr();
        $nonce = bin2hex(random_bytes(24));
        $_SESSION['nv_script_checkout_nonce'] = [
            'slug' => (string)$product['slug'],
            'value' => $nonce,
            'created' => time(),
        ];
        header('Cache-Control: private, no-store');
        header('X-Robots-Tag: noindex, nofollow');
        $this->render('frontend/script-checkout', [
            'pageTitle' => 'Güvenli Yazılım Satın Alma',
            'product' => $product,
            'user' => Auth::user(),
            'paytrReady' => $service !== null,
            'purchaseNonce' => $nonce,
            'canonicalUrl' => url('/hazir-scriptler/'.rawurlencode((string)$product['slug'])),
            'noindex' => true,
        ]);
    }

    public function pay(string $slug): void
    {
        if (!Auth::check() || !Auth::user()) { redirect('/giris'); return; }
        Csrf::check();
        $slug = rawurldecode($slug);
        $nonce = $_SESSION['nv_script_checkout_nonce'] ?? null;
        if (!is_array($nonce) || !is_string($_POST['purchase_nonce'] ?? null)
            || !hash_equals((string)($nonce['value'] ?? ''), $_POST['purchase_nonce'])
            || ($nonce['slug'] ?? '') !== $slug
            || time() - (int)($nonce['created'] ?? 0) > 1800) {
            flash('error', 'Ödeme oturumu geçersiz. Lütfen yeniden deneyin.');
            redirect('/yazilim-satin-al/'.rawurlencode($slug));
            return;
        }
        if (($_POST['terms_accepted'] ?? '') !== '1') {
            flash('error', 'Ödeme için mesafeli satış sözleşmesini onaylayın.');
            redirect('/yazilim-satin-al/'.rawurlencode($slug));
            return;
        }
        try {
            $installationFields = $this->installationFields($_POST);
        } catch (\InvalidArgumentException $e) {
            flash('error', $e->getMessage());
            redirect('/yazilim-satin-al/'.rawurlencode($slug));
            return;
        }
        $gateway = $this->paytr();
        if (!$gateway) {
            flash('error', 'PayTR şu anda aktif veya yapılandırılmış değil. Ödeme alınamadı.');
            redirect('/yazilim-satin-al/'.rawurlencode($slug));
            return;
        }

        $pdo = $this->db->getPdo();
        try {
            $pdo->beginTransaction();
            $product = $this->product($slug, true);
            if (!$product) {
                $pdo->rollBack();
                flash('error', 'Bu ürün artık satın alınamıyor.');
                redirect('/hazir-scriptler/'.rawurlencode($slug));
                return;
            }
            // Price is read and locked server-side: never trust client or query prices.
            $price = round((float)$product['price'], 2);
            $number = 'NV'.date('ymd').strtoupper(bin2hex(random_bytes(5)));
            $orderId = $this->db->insert('orders', [
                'user_id' => (int)Auth::id(),
                'order_number' => $number,
                'total_amount' => $price,
                'payment_method' => 'online',
                'payment_gateway' => 'paytr',
                'payment_status' => 'pending',
                'order_status' => 'payment_pending',
                'customer_note' => null,
            ]);
            $itemId = $this->db->insert('order_items', [
                'order_id' => $orderId,
                'package_id' => null,
                'package_name' => (string)$product['name'],
                'quantity' => 1,
                'price' => $price,
                'total' => $price,
            ]);
            foreach ([
                ['netvera_script_id', 'Yazılım Ürün Kimliği', (string)$product['legacy_id']],
                ['netvera_script_slug', 'Yazılım SEO Adresi', (string)$product['slug']],
                ...$installationFields,
            ] as [$key, $label, $value]) {
                $this->db->insert('order_fields', [
                    'order_id' => $orderId,
                    'order_item_id' => $itemId,
                    'field_key' => $key,
                    'field_label' => $label,
                    'field_value' => $value,
                ]);
            }
            (new OrderService())->logStatus($orderId, null, 'payment_pending',
                'Hazır yazılım PayTR ödeme talebi oluşturuldu.', 'system');
            $pdo->commit();
            // Consume nonce after persistence. Refresh/back cannot make a second charge.
            unset($_SESSION['nv_script_checkout_nonce']);
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            error_log('Script checkout order failed: '.get_class($e));
            flash('error', 'Sipariş oluşturulamadı; ödeme alınmadı.');
            redirect('/yazilim-satin-al/'.rawurlencode($slug));
            return;
        }

        $order = $this->db->fetch('SELECT * FROM orders WHERE id=? AND user_id=?',
            [$orderId, (int)Auth::id()]);
        try {
            $result = $gateway->initiatePayment($order, Auth::user());
        } catch (\Throwable $e) {
            error_log('Script PayTR initiation: '.get_class($e));
            $result = ['success' => false, 'error' => 'PayTR ödeme bağlantısı başlatılamadı.'];
        }
        if (!empty($result['success']) && !empty($result['iframe_token'])) {
            header('Cache-Control: private, no-store');
            header('X-Robots-Tag: noindex, nofollow');
            $this->render('frontend/payment-iframe', [
                'pageTitle' => 'NetVera PayTR Güvenli Ödeme',
                'iframeToken' => $result['iframe_token'],
                'gateway' => 'paytr',
                'order' => $order,
                'user' => Auth::user(),
                'noindex' => true,
            ]);
            return;
        }
        flash('error', 'PayTR başlatılamadı. Sipariş ödeme bekliyor; tekrar tahsilat yapılmadı. '.($result['error'] ?? ''));
        redirect('/siparis/'.$orderId);
    }
}
