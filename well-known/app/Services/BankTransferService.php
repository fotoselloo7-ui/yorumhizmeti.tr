<?php
namespace App\Services;

use App\Core\Database;

/**
 * Havale/EFT Ödeme Servisi
 */
class BankTransferService implements PaymentGatewayInterface
{
    private array $settings;

    public function __construct(array $settings = [])
    {
        $this->settings = $settings;
    }

    public function getName(): string { return 'Havale/EFT'; }
    public function getKey(): string { return 'bank_transfer'; }

    public function isConfigured(): bool
    {
        // Havale/EFT her zaman aktif - banka hesabı olması yeterli
        $db = Database::getInstance();
        $count = $db->count('bank_accounts', "status = 'active'");
        return $count > 0;
    }

    public function initiatePayment(array $order, array $user): array
    {
        // Havale/EFT'de redirect yok, sipariş oluşturulduktan sonra
        // banka bilgileri gösterilir
        return [
            'success' => true,
            'redirect_url' => '/odeme/basarili?method=bank_transfer&order=' . $order['order_number'],
            'iframe_token' => null,
        ];
    }

    public function handleCallback(array $data): array
    {
        // Havale/EFT'de otomatik callback yok
        return ['success' => false, 'error' => 'Havale/EFT callback desteklenmiyor.'];
    }

    /**
     * Aktif banka hesaplarını getir
     */
    public function getActiveBankAccounts(): array
    {
        $db = Database::getInstance();
        return $db->fetchAll("SELECT * FROM bank_accounts WHERE status = 'active' ORDER BY sort_order ASC");
    }
}
