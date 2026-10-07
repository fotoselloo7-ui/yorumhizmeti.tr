<?php
namespace App\Services;

/**
 * Payment Gateway Interface
 * Tüm ödeme modülleri bu arayüzü implement eder.
 */
interface PaymentGatewayInterface
{
    /**
     * Gateway'in yapılandırılmış ve kullanılabilir olup olmadığını kontrol eder.
     */
    public function isConfigured(): bool;

    /**
     * Ödeme başlat
     * @param array $order Sipariş bilgileri
     * @param array $user Kullanıcı bilgileri
     * @return array ['success' => bool, 'redirect_url' => string|null, 'iframe_token' => string|null, 'error' => string|null]
     */
    public function initiatePayment(array $order, array $user): array;

    /**
     * Callback/webhook doğrulaması
     * @param array $data POST verileri
     * @return array ['success' => bool, 'order_id' => int|null, 'transaction_id' => string|null, 'error' => string|null]
     */
    public function handleCallback(array $data): array;

    /**
     * Gateway adını döner
     */
    public function getName(): string;

    /**
     * Gateway key'ini döner
     */
    public function getKey(): string;
}
