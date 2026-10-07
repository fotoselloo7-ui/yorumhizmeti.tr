<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Csrf;
use App\Services\LicenseService;
use App\Services\SiteConfigService;

class LicenseController extends Controller
{
    private LicenseService $license;

    public function __construct()
    {
        parent::__construct();
        $this->license = LicenseService::getInstance();
    }

    public function index(): void
    {
        $config = $this->license->getConfig();
        $badge  = $this->license->getStatusBadge();

        $info = [
            'status'          => $this->license->getCachedStatus(),
            'badge'           => $badge,
            'product_code'    => $config['product_code'],
            'domain'          => $config['domain'],
            'install_id'      => $config['install_id'],
            'server_url'      => $config['server_url'],
            'license_key'     => $config['license_key'],
            'enabled'         => $config['enabled'],
            'local_bypass'    => $config['local_bypass'],
            'last_check'      => setting('license_last_check_at', '-'),
            'next_check'      => setting('license_next_check_at', '-'),
            'expires_at'      => setting('license_expires_at', '-'),
            'grace_until'     => setting('license_grace_until', '-'),
            'message'         => setting('license_message', 'Henüz kontrol yapılmadı.'),
            'check_interval'  => $config['check_interval'],
            'grace_days'      => $config['grace_days'],
        ];

        $this->renderAdmin('admin/settings/license', [
            'pageTitle' => 'Lisans Yönetimi',
            'info'      => $info,
        ]);
    }

    public function activate(): void
    {
        Csrf::check();
        $key = trim($_POST['license_key'] ?? '');

        if (empty($key)) {
            flash('error', 'Lisans anahtarı boş olamaz.');
            redirect('/admin/lisans');
            return;
        }

        $result = $this->license->activateLicense($key);

        if ($result['success']) {
            logActivity('license_activate', 'Lisans aktifleştirildi: ' . ($result['status'] ?? ''));
            flash('success', 'Lisans başarıyla aktifleştirildi: ' . ($result['message'] ?? ''));
        } else {
            flash('error', 'Lisans aktifleştirilemedi: ' . ($result['message'] ?? 'Bilinmeyen hata.'));
        }

        redirect('/admin/lisans');
    }

    public function check(): void
    {
        Csrf::check();
        $result = $this->license->checkLicense(true);

        if ($result['success']) {
            flash('success', 'Lisans kontrolü tamamlandı: ' . ($result['message'] ?? ''));
        } else {
            flash('error', 'Lisans kontrolü başarısız: ' . ($result['message'] ?? 'Bilinmeyen hata.'));
        }

        redirect('/admin/lisans');
    }

    public function deactivate(): void
    {
        Csrf::check();
        $result = $this->license->deactivateLicense();

        logActivity('license_deactivate', 'Lisans devre dışı bırakıldı.');
        flash('success', 'Lisans devre dışı bırakıldı.');

        redirect('/admin/lisans');
    }

    public function saveSettings(): void
    {
        Csrf::check();
        $config = SiteConfigService::getInstance();

        $serverUrl = trim($_POST['license_server_url'] ?? '');
        $licenseKey = trim($_POST['license_key'] ?? '');
        $enabled = isset($_POST['license_enabled']) ? 'true' : 'false';

        $config->set('license_server_url', $serverUrl, 'license');
        $config->set('license_key', $licenseKey, 'license');
        $config->set('license_enabled', $enabled, 'license');

        logActivity('license_settings', 'Lisans ayarları güncellendi.');
        flash('success', 'Lisans ayarları kaydedildi.');

        redirect('/admin/lisans');
    }
}
