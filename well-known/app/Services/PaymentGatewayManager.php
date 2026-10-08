<?php
namespace App\Services;

use App\Core\Database;

/**
 * Payment Gateway Manager
 * Aktif ödeme modüllerini yönetir, doğru servisi döner.
 */
class PaymentGatewayManager
{
    private Database $db;
    private array $gateways = [];

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Tüm ödeme modüllerini getir
     */
    public function getAllGateways(): array
    {
        return $this->db->fetchAll("SELECT * FROM payment_gateways ORDER BY sort_order ASC");
    }

    /**
     * Aktif ödeme modüllerini getir
     */
    public function getActiveGateways(): array
    {
        return $this->db->fetchAll("SELECT * FROM payment_gateways WHERE is_active = 1 ORDER BY sort_order ASC");
    }

    /**
     * Aktif online POS sağlayıcılarını getir
     */
    public function getActiveOnlineGateways(): array
    {
        return $this->db->fetchAll("SELECT * FROM payment_gateways WHERE is_active = 1 AND type = 'online' ORDER BY sort_order ASC");
    }

    /**
     * Varsayılan online POS sağlayıcısını getir
     */
    public function getDefaultOnlineGateway(): ?array
    {
        return $this->db->fetch("SELECT * FROM payment_gateways WHERE is_active = 1 AND type = 'online' AND is_default = 1");
    }

    /**
     * Havale/EFT modülünü getir
     */
    public function getBankTransferGateway(): ?array
    {
        return $this->db->fetch("SELECT * FROM payment_gateways WHERE gateway_key = 'bank_transfer'");
    }

    /**
     * Gateway key ile modül getir
     */
    public function getGateway(string $key): ?array
    {
        return $this->db->fetch("SELECT * FROM payment_gateways WHERE gateway_key = ?", [$key]);
    }

    /**
     * Gateway service instance döner
     */
    public function getService(string $key): ?PaymentGatewayInterface
    {
        $gateway = $this->getGateway($key);
        if (!$gateway || !$gateway['is_active']) return null;

        $settings = json_decode($gateway['settings'] ?? '{}', true) ?: [];

        return match ($key) {
            'paytr' => new PaytrService($settings),
            'iyzico' => new IyzicoService($settings),
            'bank_transfer' => new BankTransferService($settings),
            default => null,
        };
    }

    /**
     * Gateway durumunu değiştir
     */
    public function toggleStatus(int $id): void
    {
        $gateway = $this->db->fetch("SELECT * FROM payment_gateways WHERE id = ?", [$id]);
        if ($gateway) {
            $newStatus = $gateway['is_active'] ? 0 : 1;
            $this->db->update('payment_gateways', ['is_active' => $newStatus], 'id = ?', [$id]);
        }
    }

    /**
     * Varsayılan online POS sağlayıcısını ayarla
     */
    public function setDefault(int $id): void
    {
        // Tüm online sağlayıcıların default'unu kaldır
        $this->db->query("UPDATE payment_gateways SET is_default = 0 WHERE type = 'online'");
        // Seçileni varsayılan yap
        $this->db->update('payment_gateways', ['is_default' => 1, 'is_active' => 1], 'id = ?', [$id]);
    }

    /**
     * Gateway ayarlarını güncelle
     */
    public function updateSettings(string $key, array $settings): void
    {
        $this->db->update(
            'payment_gateways',
            ['settings' => json_encode($settings, JSON_UNESCAPED_UNICODE)],
            'gateway_key = ?',
            [$key]
        );
    }

    /**
     * Checkout için aktif ödeme yöntemlerini hazırla
     */
    public function getCheckoutOptions(): array
    {
        $options = [];
        $activeGateways = $this->getActiveGateways();

        foreach ($activeGateways as $gw) {
            $settings = json_decode($gw['settings'] ?? '{}', true) ?: [];

            if ($gw['type'] === 'online') {
                // Online POS: API bilgileri dolu mu kontrol et
                $service = $this->getService($gw['gateway_key']);
                if ($service && $service->isConfigured()) {
                    $options[] = [
                        'key' => $gw['gateway_key'],
                        'name' => $gw['name'],
                        'type' => 'online',
                        'is_default' => (bool) $gw['is_default'],
                    ];
                }
            } elseif ($gw['gateway_key'] === 'bank_transfer') {
                // Banka hesabı bulunmayan havale modülünü müşteriye sunma.
                $service = $this->getService('bank_transfer');
                if ($service && $service->isConfigured()) {
                    $options[] = [
                        'key' => 'bank_transfer',
                        'name' => $gw['name'],
                        'type' => 'manual',
                        'is_default' => false,
                    ];
                }
            }
        }

        return $options;
    }
}
