<?php
namespace App\Core;

class Router
{
    private array $routes = [];
    private array $params = [];

    public function get(string $path, string $action): void
    {
        $this->addRoute('GET', $path, $action);
    }

    public function post(string $path, string $action): void
    {
        $this->addRoute('POST', $path, $action);
    }

    private function addRoute(string $method, string $path, string $action): void
    {
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $path);
        $pattern = '#^' . $pattern . '$#';
        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'action' => $action,
        ];
    }

    public function dispatch(): void
    {
        $uri = $this->getUri();
        $method = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) continue;
            if (preg_match($route['pattern'], $uri, $matches)) {
                $this->params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $this->callAction($route['action']);
                return;
            }
        }

        $this->notFound();
    }

    private function getUri(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = rtrim($uri, '/') ?: '/';
        return $uri;
    }

    private function callAction(string $action): void
    {
        [$controllerName, $methodName] = explode('@', $action);

        $controllerClass = 'App\\Controllers\\' . $controllerName;

        if (!class_exists($controllerClass)) {
            if ($_ENV['APP_DEBUG'] ?? false) {
                die("Controller bulunamadı: {$controllerClass}");
            }
            $this->notFound();
            return;
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $methodName)) {
            if ($_ENV['APP_DEBUG'] ?? false) {
                die("Method bulunamadı: {$controllerClass}@{$methodName}");
            }
            $this->notFound();
            return;
        }

        // Admin middleware
        if (str_starts_with($controllerName, 'Admin\\') && $controllerName !== 'Admin\\AuthController') {
            if (!AdminAuth::check()) {
                redirect('/admin/giris');
                return;
            }
        }

        // User middleware (account, support, orders)
        $protectedControllers = ['AccountController', 'SupportController'];
        if (in_array($controllerName, $protectedControllers)) {
            if (!Auth::check()) {
                redirect('/giris');
                return;
            }
        }

        // License middleware
        $this->checkLicense($this->getUri(), $controllerName);

        call_user_func_array([$controller, $methodName], $this->params);
    }

    private function checkLicense(string $uri, string $controllerName): void
    {
        try {
            $license = \App\Services\LicenseService::getInstance();

            // If not enabled or local bypass, skip entirely
            if (!$license->isEnabled() || $license->isLocalBypass()) {
                return;
            }

            // Whitelist: these routes are NEVER blocked
            $bypassPrefixes = [
                '/admin/lisans',   // License management page
                '/admin/giris',    // Admin login
                '/admin/cikis',    // Admin logout
                '/giris',          // User login
                '/cikis',          // User logout
                '/sitemap.xml',    // SEO
                '/robots.txt',     // SEO
                '/assets/',        // Static assets
                '/uploads/',       // Uploaded files
            ];
            foreach ($bypassPrefixes as $prefix) {
                if (str_starts_with($uri, $prefix)) {
                    return;
                }
            }

            // Perform background check if interval has passed (non-blocking)
            if ($license->shouldCheck()) {
                $license->checkLicense(false);
            }

            // If license should block and this is NOT an admin page, show block page
            if ($license->shouldBlock()) {
                if (!str_starts_with($controllerName, 'Admin\\')) {
                    // Frontend block
                    http_response_code(503);
                    $blockFile = BASE_PATH . '/resources/views/frontend/license-block.php';
                    if (file_exists($blockFile)) {
                        require $blockFile;
                    } else {
                        echo '<h1>Lisans doğrulama gerekli</h1><p>Lütfen yönetim panelinden lisans bilgilerinizi kontrol edin.</p>';
                    }
                    exit;
                }
                // Admin pages: don't block, controller/dashboard will show warning
            }
        } catch (\Exception $e) {
            // License check should never crash the site
            return;
        }
    }

    private function notFound(): void
    {
        http_response_code(404);
        $view = new View();
        $view->render('frontend/404', ['pageTitle' => 'Sayfa Bulunamadı'], 'app');
    }
}
