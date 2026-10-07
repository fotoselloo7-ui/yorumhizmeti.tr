<?php
namespace App\Core;

use App\Services\SiteConfigService;

class View
{
    private string $viewPath;

    public function __construct()
    {
        $this->viewPath = BASE_PATH . '/resources/views';
    }

    public function render(string $template, array $data = [], string $layout = 'app'): void
    {
        // Site ayarları her view'da erişilebilir
        try {
            $siteConfig = SiteConfigService::getInstance();
            $data['site'] = $siteConfig;
        } catch (\Exception $e) {
            $data['site'] = null;
        }

        // Flash messages
        $data['flash'] = $this->getFlash();
        $data['errors'] = $_SESSION['flash_errors'] ?? [];
        $data['old'] = $_SESSION['flash_old'] ?? [];
        unset($_SESSION['flash_errors'], $_SESSION['flash_old']);

        // CSRF token
        $data['csrf'] = Csrf::token();

        extract($data);

        $contentFile = $this->viewPath . '/' . str_replace('.', '/', $template) . '.php';

        if (!file_exists($contentFile)) {
            if ($_ENV['APP_DEBUG'] ?? false) {
                die("View bulunamadı: {$template}");
            }
            http_response_code(500);
            die('Bir hata oluştu.');
        }

        // İçeriği buffer'a al
        ob_start();
        require $contentFile;
        $content = ob_get_clean();

        // Layout varsa uygula
        if ($layout) {
            $layoutFile = $this->viewPath . '/layouts/' . $layout . '.php';
            if (file_exists($layoutFile)) {
                require $layoutFile;
            } else {
                echo $content;
            }
        } else {
            echo $content;
        }
    }

    private function getFlash(): array
    {
        $flash = $_SESSION['flash_messages'] ?? [];
        unset($_SESSION['flash_messages']);
        return $flash;
    }
}
