<?php
namespace App\Services;

/**
 * PayTR iFrame Ödeme Entegrasyonu
 */
class PaytrService implements PaymentGatewayInterface
{
    private array $settings;

    public function __construct(array $settings = [])
    {
        $this->settings = $settings;
    }

    public function getName(): string { return 'PayTR'; }
    public function getKey(): string { return 'paytr'; }

    public function isConfigured(): bool
    {
        return !empty($this->settings['merchant_id'])
            && !empty($this->settings['merchant_key'])
            && !empty($this->settings['merchant_salt']);
    }

    public function initiatePayment(array $order, array $user): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'PayTR API bilgileri eksik. Admin panelden ayarları girin.'];
        }

        $merchantId = $this->settings['merchant_id'];
        $merchantKey = $this->settings['merchant_key'];
        $merchantSalt = $this->settings['merchant_salt'];
        $testMode = (string)($this->settings['test_mode'] ?? '1') === '0' ? '0' : '1';

        $userIp = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $merchantOid = $order['order_number'];
        $email = $user['email'];
        $paymentAmount = (int) round((float)$order['total_amount'] * 100); // kuruş
        $userName = $user['name'];

        $userBasket = base64_encode(json_encode([
            [(string)$order['order_number'], number_format((float)$order['total_amount'], 2, '.', ''), 1]
        ], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));

        $merchantOkUrl = url('/odeme/basarili');
        $merchantFailUrl = url('/odeme/basarisiz');

        $hashStr = $merchantId . $userIp . $merchantOid . $email
            . $paymentAmount . $userBasket . '0' . '0'
            . 'TL' . $testMode;

        $paytrToken = base64_encode(hash_hmac('sha256', $hashStr . $merchantSalt, $merchantKey, true));

        $postData = [
            'merchant_id' => $merchantId,
            'user_ip' => $userIp,
            'merchant_oid' => $merchantOid,
            'email' => $email,
            'payment_amount' => $paymentAmount,
            'paytr_token' => $paytrToken,
            'user_basket' => $userBasket,
            'debug_on' => $testMode ? 1 : 0,
            'no_installment' => 0,
            'max_installment' => 0,
            'user_name' => $userName,
            'user_phone' => $user['phone'] ?? '',
            'user_address' => trim((string)($user['address'] ?? '')) ?: 'Dijital hizmet / online teslimat',
            'lang' => 'tr',
            'timeout_limit' => 30,
            'iframe_v2' => 1,
            'merchant_ok_url' => $merchantOkUrl,
            'merchant_fail_url' => $merchantFailUrl,
            'currency' => 'TL',
            'test_mode' => $testMode,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.paytr.com/odeme/api/get-token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $result = curl_exec($ch);
        curl_close($ch);

        if (!$result) {
            return ['success' => false, 'error' => 'PayTR sunucusuna bağlanılamadı.'];
        }

        $response = json_decode($result, true);
        if (($response['status'] ?? '') === 'success' && !empty($response['token'])) {
            return [
                'success' => true,
                'iframe_token' => $response['token'],
                'redirect_url' => null,
            ];
        }

        return ['success' => false, 'error' => $response['reason'] ?? 'PayTR hatası.'];
    }

    public function handleCallback(array $data): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'API bilgileri eksik.'];
        }

        $merchantKey = $this->settings['merchant_key'];
        $merchantSalt = $this->settings['merchant_salt'];

        $hash = base64_encode(hash_hmac('sha256',
            ($data['merchant_oid'] ?? '') . ($merchantSalt) . ($data['status'] ?? '') . ($data['total_amount'] ?? ''),
            $merchantKey, true
        ));

        if (!isset($data['merchant_oid'], $data['status'], $data['total_amount'], $data['hash'])
            || !is_scalar($data['merchant_oid']) || !is_scalar($data['status'])
            || !is_scalar($data['total_amount']) || !is_string($data['hash'])
            || (string)$data['merchant_oid']===''
            || !in_array($data['status'],['success','failed'],true)
            || !ctype_digit((string)$data['total_amount'])
            || !hash_equals($hash, (string)$data['hash'])) {
            return ['success' => false, 'error' => 'Hash doğrulaması başarısız.'];
        }

        $status = $data['status'] ?? '';

        if ($status === 'success') {
            return [
                'success' => true,
                'verified' => true,
                'order_number' => $data['merchant_oid'] ?? '',
                'transaction_id' => $data['merchant_oid'] ?? '',
                'error' => null,
            ];
        }

        return ['success' => false, 'verified' => true, 'error' => 'Ödeme başarısız.', 'order_number' => $data['merchant_oid'] ?? ''];
    }
}
