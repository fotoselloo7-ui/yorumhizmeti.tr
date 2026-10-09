<?php
namespace App\Services;

use App\Core\Database;

class SiteConfigService
{
    private static ?SiteConfigService $instance = null;
    private array $settings = [];

    private function __construct()
    {
        $this->loadSettings();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function loadSettings(): void
    {
        try {
            $db = Database::getInstance();
            $rows = $db->fetchAll("SELECT setting_key, setting_value FROM settings");
            foreach ($rows as $row) {
                $this->settings[$row['setting_key']] = $row['setting_value'];
            }
        } catch (\Exception $e) {
            $this->settings = [];
        }
    }

    public function get(string $key, string $default = ''): string
    {
        $value=(string)($this->settings[$key]??$default);
        // Rebrand legacy DEFAULT UI settings without mutating customer/package content.
        if($key==='site_name' && preg_match('/^(?:yorum\s*hizmeti|yorumhizmeti(?:\.tr)?)$/iu',trim($value)))
            return 'NetVera Teknoloji Yazılım';
        if($key==='site_url' && preg_match('~^https?://(?:www\.)?yorumhizmeti\.tr/?$~i',trim($value)))
            return 'https://netvera.tr';
        if(in_array($key,['default_seo_title','footer_text','site_slogan'],true)){
            $value=preg_replace('/YorumHizmeti\.tr/iu','NetVera Teknoloji Yazılım',$value);
            $value=preg_replace('/\bYorum Hizmeti\s*[|—-]\s*Türkiye\b/iu','NetVera Teknoloji Yazılım',$value);
        }
        return $value;
    }

    public function all(): array
    {
        return $this->settings;
    }

    public function getByGroup(string $group): array
    {
        try {
            $db = Database::getInstance();
            return $db->fetchAll("SELECT * FROM settings WHERE setting_group = ?", [$group]);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function set(string $key, ?string $value, string $group = 'general'): void
    {
        $db = Database::getInstance();
        $existing = $db->fetch("SELECT id FROM settings WHERE setting_key = ?", [$key]);
        if ($existing) {
            $db->update('settings', ['setting_value' => $value], 'setting_key = ?', [$key]);
        } else {
            $db->insert('settings', [
                'setting_key' => $key,
                'setting_value' => $value,
                'setting_group' => $group,
            ]);
        }
        $this->settings[$key] = $value;
    }

    public function reload(): void
    {
        $this->loadSettings();
    }
}
