<?php
namespace App\Services;

/**
 * Private server-to-server PerfectPanel-compatible API v2 adapter.
 * Provider credentials, order ids and upstream errors must never reach public templates.
 */
final class SmmApiClient
{
    private string $endpoint;
    private string $key;

    public function __construct(string $endpoint, string $key)
    {
        $this->endpoint = self::validateEndpoint($endpoint);
        if (trim($key) === '') {
            throw new \RuntimeException('API anahtarı boş.');
        }
        $this->key = $key;
    }

    public static function validateEndpoint(string $value): string
    {
        $value = trim($value);
        $parts = parse_url($value);
        if (!$parts || ($parts['scheme'] ?? '') !== 'https'
            || empty($parts['host']) || isset($parts['user']) || isset($parts['pass'])
            || isset($parts['query']) || isset($parts['fragment'])
            || (isset($parts['port']) && (int)$parts['port'] !== 443)) {
            throw new \InvalidArgumentException('API adresi HTTPS kullanmalı; kullanıcı bilgisi, port veya sorgu içermemeli.');
        }
        $host = strtolower($parts['host']);
        if (filter_var($host, FILTER_VALIDATE_IP)
            || !preg_match('/^(?:[a-z0-9-]+\.)+[a-z]{2,63}$/i', $host)
            || str_ends_with($host, '.local') || str_ends_with($host, '.internal')) {
            throw new \InvalidArgumentException('Geçersiz API alan adı.');
        }
        return $value;
    }

    public static function encryptionKey(): string
    {
        $raw = trim((string)($_ENV['SMM_ENCRYPTION_KEY'] ?? getenv('SMM_ENCRYPTION_KEY') ?: ''));
        if (str_starts_with($raw, 'base64:')) {
            $key = base64_decode(substr($raw, 7), true);
        } elseif (preg_match('/^[a-f0-9]{64}$/i', $raw)) {
            $key = hex2bin($raw);
        } else {
            $key = false;
        }
        if ($key === false || strlen($key) !== 32) {
            throw new \RuntimeException('SMM_ENCRYPTION_KEY tanımlanmamış. .env dosyasına 32 baytlık rastgele anahtar ekleyin.');
        }
        return $key;
    }

    public static function encrypt(string $secret): string
    {
        $iv = random_bytes(12);
        $cipher = openssl_encrypt($secret, 'aes-256-gcm', self::encryptionKey(), OPENSSL_RAW_DATA, $iv, $tag);
        if ($cipher === false) {
            throw new \RuntimeException('API anahtarı şifrelenemedi.');
        }
        return 'v1:' . base64_encode($iv . $tag . $cipher);
    }

    public static function decrypt(string $encrypted): string
    {
        if (!str_starts_with($encrypted, 'v1:')) {
            throw new \RuntimeException('API anahtarı biçimi desteklenmiyor.');
        }
        $binary = base64_decode(substr($encrypted, 3), true);
        if ($binary === false || strlen($binary) < 29) {
            throw new \RuntimeException('API anahtarı çözümlenemedi.');
        }
        $value = openssl_decrypt(substr($binary, 28), 'aes-256-gcm', self::encryptionKey(),
            OPENSSL_RAW_DATA, substr($binary, 0, 12), substr($binary, 12, 16));
        if ($value === false) {
            throw new \RuntimeException('API anahtarı çözümlenemedi; şifreleme anahtarını kontrol edin.');
        }
        return $value;
    }

    public function call(string $action, array $args = []): array
    {
        $allowed = ['services', 'balance', 'add', 'status', 'refill', 'refill_status', 'cancel'];
        if (!in_array($action, $allowed, true)) {
            throw new \InvalidArgumentException('Desteklenmeyen API işlemi.');
        }
        if (!extension_loaded('curl')) {
            throw new \RuntimeException('Sunucuda PHP cURL eklentisi etkin olmalı.');
        }

        $host = (string)parse_url($this->endpoint, PHP_URL_HOST);
        $ips = gethostbynamel($host);
        if (!$ips) {
            throw new \RuntimeException('Tedarikçi alan adı çözümlenemedi.');
        }
        $approved = array_values(array_filter($ips, static function ($ip) {
            return (bool) filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
        }));
        if (count($approved) !== count($ips)) {
            throw new \RuntimeException('API adresi özel/ağ içi IP adresine yönleniyor.');
        }

        $handle = curl_init($this->endpoint);
        if ($handle === false) {
            throw new \RuntimeException('Bağlantı başlatılamadı.');
        }
        $payload = array_merge(['key' => $this->key, 'action' => $action], $args);
        curl_setopt_array($handle, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($payload, '', '&', PHP_QUERY_RFC1738),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_CONNECTTIMEOUT => 7,
            CURLOPT_TIMEOUT => 22,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_HTTPHEADER => ['Accept: application/json', 'Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_RESOLVE => [$host . ':443:' . $approved[0]],
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            CURLOPT_PROXY => '',
            CURLOPT_USERAGENT => 'YorumHizmeti-Private-Integration/1.0',
        ]);
        $response = curl_exec($handle);
        $error = curl_error($handle);
        $status = (int)curl_getinfo($handle, CURLINFO_HTTP_CODE);
        curl_close($handle);
        if ($response === false) {
            throw new \RuntimeException('API bağlantı hatası: ' . mb_substr($error, 0, 180));
        }
        if (strlen($response) > 4 * 1024 * 1024) {
            throw new \RuntimeException('API yanıtı izin verilen boyutu aşıyor.');
        }
        if ($status === 429) {
            throw new \RuntimeException('Tedarikçi API istek sınırına ulaşıldı; daha sonra deneyin.');
        }
        if ($status < 200 || $status >= 300) {
            throw new \RuntimeException('Tedarikçi API HTTP hatası (' . $status . ').');
        }
        $data = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($data)) {
            throw new \RuntimeException('API beklenen JSON yanıtını döndürmedi.');
        }
        if (isset($data['error'])) {
            throw new \RuntimeException('Tedarikçi işlem hatası: ' . mb_substr((string)$data['error'], 0, 220));
        }
        return $data;
    }
}
