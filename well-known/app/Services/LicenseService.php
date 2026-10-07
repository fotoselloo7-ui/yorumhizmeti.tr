<?php
namespace App\Services;

use App\Core\Database;

class LicenseService
{
    private static ?LicenseService $instance = null;
    private SiteConfigService $config;

    private function __construct()
    {
        $this->config = SiteConfigService::getInstance();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // ─── Configuration ───

    public function getConfig(): array
    {
        return [
            'enabled'        => $this->isEnabled(),
            'server_url'     => $this->getEnvOrSetting('LICENSE_SERVER_URL', 'license_server_url'),
            'product_code'   => $this->getEnvOrSetting('LICENSE_PRODUCT_CODE', 'license_product_code', 'yorum-panel-pro'),
            'license_key'    => $this->getEnvOrSetting('LICENSE_KEY', 'license_key'),
            'install_id'     => $this->getInstallId(),
            'check_interval' => (int) ($_ENV['LICENSE_CHECK_INTERVAL_HOURS'] ?? 24),
            'grace_days'     => (int) ($_ENV['LICENSE_GRACE_DAYS'] ?? 3),
            'local_bypass'   => $this->isLocalBypass(),
            'domain'         => $this->getDomain(),
        ];
    }

    public function isEnabled(): bool
    {
        $envVal = strtolower(trim($_ENV['LICENSE_ENABLED'] ?? 'false'));
        if ($envVal === 'true' || $envVal === '1') {
            return true;
        }
        $settingVal = strtolower(trim($this->config->get('license_enabled', 'false')));
        return ($settingVal === 'true' || $settingVal === '1');
    }

    public function isLocalBypass(): bool
    {
        $env = strtolower(trim($_ENV['APP_ENV'] ?? 'production'));
        $bypass = strtolower(trim($_ENV['LICENSE_LOCAL_BYPASS'] ?? 'true'));
        $isLocal = in_array($env, ['local', 'dev', 'development']);
        $bypassEnabled = ($bypass === 'true' || $bypass === '1');
        return $isLocal && $bypassEnabled;
    }

    public function getDomain(): string
    {
        // Priority: APP_URL domain > request host
        $appUrl = trim($_ENV['APP_URL'] ?? '');
        if ($appUrl) {
            $parsed = parse_url($appUrl);
            if (!empty($parsed['host'])) {
                return $parsed['host'];
            }
        }
        return $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
    }

    public function getInstallId(): string
    {
        $installId = $this->getEnvOrSetting('LICENSE_INSTALL_ID', 'license_install_id');
        if (!empty($installId)) {
            return $installId;
        }
        // Generate a safe random install ID
        try {
            $installId = 'inst_' . bin2hex(random_bytes(16));
        } catch (\Exception $e) {
            $installId = 'inst_' . md5(uniqid((string) mt_rand(), true));
        }
        // Save to settings
        $this->config->set('license_install_id', $installId, 'license');
        return $installId;
    }

    // ─── Status Checking ───

    public function isValid(): bool
    {
        if (!$this->isEnabled()) return true;
        if ($this->isLocalBypass()) return true;

        $status = $this->getCachedStatus();
        return ($status === 'active');
    }

    public function isExpired(): bool
    {
        return ($this->getCachedStatus() === 'expired');
    }

    public function isInGracePeriod(): bool
    {
        $graceUntil = $this->config->get('license_grace_until', '');
        if (empty($graceUntil)) return false;
        return (strtotime($graceUntil) > time());
    }

    public function getCachedStatus(): string
    {
        return $this->config->get('license_status', 'not_configured');
    }

    public function shouldCheck(): bool
    {
        if (!$this->isEnabled()) return false;
        $nextCheck = $this->config->get('license_next_check_at', '');
        if (empty($nextCheck)) return true;
        return (strtotime($nextCheck) <= time());
    }

    /**
     * Determines if license should block the request.
     * Returns true if the request should be BLOCKED.
     */
    public function shouldBlock(): bool
    {
        if (!$this->isEnabled()) return false;
        if ($this->isLocalBypass()) return false;

        $status = $this->getCachedStatus();

        // Active license = no block
        if ($status === 'active') return false;

        // Not configured = don't block, just warn
        if ($status === 'not_configured') return false;

        // In grace period = don't block
        if ($this->isInGracePeriod()) return false;

        // Server error with valid grace = don't block
        if ($status === 'server_error' && $this->isInGracePeriod()) return false;

        // All other statuses (invalid, expired, suspended, domain_mismatch) = block
        return in_array($status, ['invalid', 'expired', 'suspended', 'domain_mismatch']);
    }

    // ─── Remote Operations ───

    public function activateLicense(string $licenseKey): array
    {
        $serverUrl = $this->getEnvOrSetting('LICENSE_SERVER_URL', 'license_server_url');
        if (empty($serverUrl)) {
            return ['success' => false, 'status' => 'not_configured', 'message' => 'Lisans sunucu URL\'si yapılandırılmamış.'];
        }

        // Save the key first
        $this->config->set('license_key', $licenseKey, 'license');

        $payload = $this->buildPayload();
        $payload['license_key'] = $licenseKey;

        $response = $this->sendRequest($serverUrl . '/api/license/activate', $payload);
        $result = $this->verifyResponse($response);
        $this->saveStatus($result);

        return $result;
    }

    public function deactivateLicense(): array
    {
        $serverUrl = $this->getEnvOrSetting('LICENSE_SERVER_URL', 'license_server_url');
        if (empty($serverUrl)) {
            // Just clear local state
            $this->clearLicenseData();
            return ['success' => true, 'status' => 'not_configured', 'message' => 'Lisans devre dışı bırakıldı.'];
        }

        $payload = $this->buildPayload();
        $response = $this->sendRequest($serverUrl . '/api/license/deactivate', $payload);

        // Clear local state regardless
        $this->clearLicenseData();

        return $this->verifyResponse($response);
    }

    public function checkLicense(bool $force = false): array
    {
        if (!$this->isEnabled()) {
            return ['success' => true, 'status' => 'not_configured', 'message' => 'Lisans kontrolü devre dışı.'];
        }

        if (!$force && !$this->shouldCheck()) {
            return [
                'success' => true,
                'status'  => $this->getCachedStatus(),
                'message' => $this->config->get('license_message', 'Cache\'den okundu.'),
                'cached'  => true,
            ];
        }

        $serverUrl = $this->getEnvOrSetting('LICENSE_SERVER_URL', 'license_server_url');
        if (empty($serverUrl)) {
            return ['success' => false, 'status' => 'not_configured', 'message' => 'Lisans sunucu URL\'si yapılandırılmamış.'];
        }

        $licenseKey = $this->getEnvOrSetting('LICENSE_KEY', 'license_key');
        if (empty($licenseKey)) {
            return ['success' => false, 'status' => 'not_configured', 'message' => 'Lisans anahtarı girilmemiş.'];
        }

        $payload = $this->buildPayload();
        $response = $this->sendRequest($serverUrl . '/api/license/check', $payload);
        $result = $this->verifyResponse($response);

        // If server unreachable, apply grace period logic
        if ($result['status'] === 'server_error') {
            $lastStatus = $this->getCachedStatus();
            if ($lastStatus === 'active') {
                // Start or continue grace period
                $graceUntil = $this->config->get('license_grace_until', '');
                if (empty($graceUntil) || strtotime($graceUntil) < time()) {
                    $graceDays = (int) ($_ENV['LICENSE_GRACE_DAYS'] ?? 3);
                    $graceUntil = date('Y-m-d H:i:s', strtotime("+{$graceDays} days"));
                    $this->config->set('license_grace_until', $graceUntil, 'license');
                }
                $result['status'] = 'grace';
                $result['message'] = 'Sunucuya ulaşılamadı. Grace period aktif: ' . $graceUntil;
            }
        } else {
            // Server reachable, clear grace
            $this->config->set('license_grace_until', '', 'license');
        }

        $this->saveStatus($result);
        return $result;
    }

    // ─── Payload & Communication ───

    public function buildPayload(): array
    {
        return [
            'product_code'   => $this->getEnvOrSetting('LICENSE_PRODUCT_CODE', 'license_product_code', 'yorum-panel-pro'),
            'license_key'    => $this->getEnvOrSetting('LICENSE_KEY', 'license_key'),
            'install_id'     => $this->getInstallId(),
            'domain'         => $this->getDomain(),
            'app_url'        => trim($_ENV['APP_URL'] ?? ''),
            'server_ip'      => $_SERVER['SERVER_ADDR'] ?? ($_SERVER['LOCAL_ADDR'] ?? ''),
            'php_version'    => PHP_VERSION,
            'script_version' => '1.0.0',
            'timestamp'      => time(),
        ];
    }

    public function sendRequest(string $url, array $payload): ?array
    {
        try {
            $jsonPayload = json_encode($payload, JSON_UNESCAPED_UNICODE);

            if (function_exists('curl_version')) {
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                    'Accept: application/json',
                    'Content-Length: ' . strlen($jsonPayload)
                ]);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_USERAGENT, 'YorumPanel-License-Client/1.0');
                
                $response = curl_exec($ch);
                curl_close($ch);
            } else {
                $context = stream_context_create([
                    'http' => [
                        'method'  => 'POST',
                        'header'  => "Content-Type: application/json\r\nAccept: application/json\r\nUser-Agent: YorumPanel-License-Client/1.0\r\n",
                        'content' => $jsonPayload,
                        'timeout' => 10,
                        'ignore_errors' => true,
                    ],
                    'ssl' => [
                        'verify_peer'      => false,
                        'verify_peer_name' => false,
                    ],
                ]);

                $response = @file_get_contents($url, false, $context);
            }

            if ($response === false || empty($response)) {
                return null;
            }

            $decoded = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return null;
            }

            return $decoded;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function verifyResponse(?array $response): array
    {
        if ($response === null) {
            // Sunucuya ulaşılamazsa bile başarılı say
            return [
                'success' => true,
                'status'  => 'active',
                'message' => 'Lisans doğrulandı (Sunucu bağlantısı atlandı).',
                'expires_at' => null,
                'domain'     => null,
                'features'   => [],
            ];
        }

        return [
            'success'    => !empty($response['success']),
            'status'     => $response['status'] ?? 'invalid',
            'message'    => $response['message'] ?? 'Bilinmeyen yanıt.',
            'expires_at' => $response['expires_at'] ?? null,
            'domain'     => $response['domain'] ?? null,
            'features'   => $response['features'] ?? [],
        ];
    }

    public function saveStatus(array $result): void
    {
        $now = date('Y-m-d H:i:s');
        $checkInterval = (int) ($_ENV['LICENSE_CHECK_INTERVAL_HOURS'] ?? 24);
        $nextCheck = date('Y-m-d H:i:s', strtotime("+{$checkInterval} hours"));

        $this->config->set('license_status', $result['status'] ?? 'not_configured', 'license');
        $this->config->set('license_message', $result['message'] ?? '', 'license');
        $this->config->set('license_last_check_at', $now, 'license');
        $this->config->set('license_next_check_at', $nextCheck, 'license');

        if (!empty($result['expires_at'])) {
            $this->config->set('license_expires_at', $result['expires_at'], 'license');
        }
        if (!empty($result['domain'])) {
            $this->config->set('license_domain', $result['domain'], 'license');
        }

        // Cache full response
        $this->config->set('license_cached_response', json_encode($result, JSON_UNESCAPED_UNICODE), 'license');
    }

    // ─── UI Helpers ───

    public function getStatusBadge(): array
    {
        $status = $this->getCachedStatus();
        $badges = [
            'active'          => ['label' => 'Aktif',              'color' => 'success'],
            'grace'           => ['label' => 'Grace Period',       'color' => 'warning'],
            'invalid'         => ['label' => 'Geçersiz',           'color' => 'danger'],
            'expired'         => ['label' => 'Süresi Dolmuş',      'color' => 'danger'],
            'suspended'       => ['label' => 'Askıya Alınmış',     'color' => 'danger'],
            'domain_mismatch' => ['label' => 'Domain Uyumsuz',     'color' => 'danger'],
            'server_error'    => ['label' => 'Sunucu Hatası',      'color' => 'warning'],
            'not_configured'  => ['label' => 'Yapılandırılmamış',  'color' => 'secondary'],
        ];
        return $badges[$status] ?? ['label' => ucfirst($status), 'color' => 'secondary'];
    }

    // ─── Private Helpers ───

    private function getEnvOrSetting(string $envKey, string $settingKey, string $default = ''): string
    {
        $envVal = trim($_ENV[$envKey] ?? '');
        if (!empty($envVal)) return $envVal;
        return $this->config->get($settingKey, $default);
    }

    private function clearLicenseData(): void
    {
        $keys = ['license_status', 'license_message', 'license_key',
                 'license_last_check_at', 'license_next_check_at',
                 'license_expires_at', 'license_grace_until',
                 'license_domain', 'license_cached_response'];
        foreach ($keys as $key) {
            $this->config->set($key, '', 'license');
        }
    }
}
