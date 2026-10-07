<?php
namespace App\Services;

/**
 * iyzico Ödeme Entegrasyonu
 */
class IyzicoService implements PaymentGatewayInterface
{
    private array $settings;

    public function __construct(array $settings = [])
    {
        $this->settings = $settings;
    }

    public function getName(): string { return 'iyzico'; }
    public function getKey(): string { return 'iyzico'; }

    public function isConfigured(): bool
    {
        return !empty($this->settings['api_key'])
            && !empty($this->settings['secret_key']);
    }

    public function initiatePayment(array $order, array $user): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'iyzico API bilgileri eksik. Admin panelden ayarları girin.'];
        }

        $apiKey = $this->settings['api_key'];
        $secretKey = $this->settings['secret_key'];
        $baseUrl = $this->settings['base_url'] ?? 'https://sandbox-api.iyzipay.com';

        $conversationId = $order['order_number'];
        $price = number_format($order['total_amount'], 2, '.', '');
        $paidPrice = $price;
        $callbackUrl = url('/payment/iyzico/callback');

        $request = [
            'locale' => 'tr',
            'conversationId' => $conversationId,
            'price' => $price,
            'paidPrice' => $paidPrice,
            'currency' => 'TRY',
            'basketId' => $order['order_number'],
            'paymentGroup' => 'PRODUCT',
            'callbackUrl' => $callbackUrl,
            'enabledInstallments' => [1, 2, 3, 6, 9],
            'buyer' => [
                'id' => (string) $user['id'],
                'name' => explode(' ', $user['name'])[0],
                'surname' => explode(' ', $user['name'])[1] ?? $user['name'],
                'email' => $user['email'],
                'identityNumber' => '11111111111',
                'registrationAddress' => 'Türkiye',
                'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
                'city' => 'Istanbul',
                'country' => 'Turkey',
            ],
            'shippingAddress' => [
                'contactName' => $user['name'],
                'city' => 'Istanbul',
                'country' => 'Turkey',
                'address' => 'Türkiye',
            ],
            'billingAddress' => [
                'contactName' => $user['name'],
                'city' => 'Istanbul',
                'country' => 'Turkey',
                'address' => 'Türkiye',
            ],
            'basketItems' => [
                [
                    'id' => $order['order_number'],
                    'name' => 'Sipariş #' . $order['order_number'],
                    'category1' => 'Dijital Hizmet',
                    'itemType' => 'VIRTUAL',
                    'price' => $price,
                ],
            ],
        ];

        $requestJson = json_encode($request);

        // iyzico API imzası
        $pki = $this->generatePkiString($request);
        $hashStr = $apiKey . $pki . $secretKey;
        $token = base64_encode(sha1($hashStr, true));

        $authorizationStr = 'IYZWS ' . $apiKey . ':' . $token;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $baseUrl . '/payment/iyzipos/checkoutform/initialize/auth/ecom');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $requestJson);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: ' . $authorizationStr,
            'x-iyzi-rnd: ' . uniqid(),
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $result = curl_exec($ch);
        curl_close($ch);

        if (!$result) {
            return ['success' => false, 'error' => 'iyzico sunucusuna bağlanılamadı.'];
        }

        $response = json_decode($result, true);

        if (($response['status'] ?? '') === 'success') {
            return [
                'success' => true,
                'redirect_url' => $response['paymentPageUrl'] ?? null,
                'iframe_token' => $response['token'] ?? null,
            ];
        }

        return ['success' => false, 'error' => $response['errorMessage'] ?? 'iyzico hatası.'];
    }

    public function handleCallback(array $data): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'API bilgileri eksik.'];
        }

        $token = $data['token'] ?? '';

        $apiKey = $this->settings['api_key'];
        $secretKey = $this->settings['secret_key'];
        $baseUrl = $this->settings['base_url'] ?? 'https://sandbox-api.iyzipay.com';

        // Token ile ödeme sonucunu sorgula
        $request = [
            'locale' => 'tr',
            'conversationId' => uniqid(),
            'token' => $token,
        ];

        $requestJson = json_encode($request);
        $pki = $this->generatePkiString($request);
        $hashStr = $apiKey . $pki . $secretKey;
        $authToken = base64_encode(sha1($hashStr, true));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $baseUrl . '/payment/iyzipos/checkoutform/auth/ecom/detail');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $requestJson);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: IYZWS ' . $apiKey . ':' . $authToken,
            'x-iyzi-rnd: ' . uniqid(),
        ]);
        $result = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($result ?? '', true);

        if (($response['paymentStatus'] ?? '') === 'SUCCESS') {
            return [
                'success' => true,
                'order_number' => $response['basketId'] ?? '',
                'transaction_id' => $response['paymentId'] ?? '',
            ];
        }

        return ['success' => false, 'error' => $response['errorMessage'] ?? 'Ödeme doğrulanamadı.'];
    }

    private function generatePkiString(array $data): string
    {
        $pki = '[';
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $pki .= $key . '=' . json_encode($value) . ',';
            } else {
                $pki .= $key . '=' . $value . ',';
            }
        }
        $pki = rtrim($pki, ',') . ']';
        return $pki;
    }
}
