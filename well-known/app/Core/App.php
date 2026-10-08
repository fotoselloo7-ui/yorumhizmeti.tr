<?php
namespace App\Core;

class App
{
    private static ?App $instance = null;
    private Router $router;
    private Database $db;

    public function __construct()
    {
        self::$instance = $this;
        $this->db = Database::getInstance();
        $this->router = new Router();
        $this->registerRoutes();
    }

    public static function getInstance(): ?App
    {
        return self::$instance;
    }

    public function getDb(): Database
    {
        return $this->db;
    }

    public function run(): void
    {
        $this->router->dispatch();
    }

    private function registerRoutes(): void
    {
        $r = $this->router;

        // ── Frontend ──
        $r->get('/', 'HomeController@index');
        // Legacy Netvera SEO compatibility, staged and separate from service packages.
        $r->get('/hazir-scriptler', 'NetveraScriptController@index');
        $r->get('/hazir-scriptler/{mainSlug}/{subSlug}', 'NetveraScriptController@subCategoryPage');
        $r->get('/hazir-scriptler/{slug}', 'NetveraScriptController@detail');
        $r->get('/hazir-yazilimlar', 'SoftwareController@index');
        $r->get('/kategoriler', 'CategoryController@index');
        $r->get('/kategori/{slug}', 'CategoryController@show');
        $r->get('/paket/{slug}', 'PackageController@show');

        // Sepet
        $r->get('/sepet', 'CartController@index');
        $r->post('/sepet/ekle', 'CartController@add');
        $r->post('/sepet/guncelle', 'CartController@update');
        $r->post('/sepet/sil', 'CartController@remove');

        // Ödeme
        $r->get('/odeme', 'CheckoutController@index');
        $r->post('/odeme/islem', 'CheckoutController@process');
        $r->get('/odeme/basarili', 'CheckoutController@success');
        $r->get('/odeme/basarisiz', 'CheckoutController@fail');
        $r->post('/havale-bildirimi', 'CheckoutController@bankNotify');

        // Payment callbacks
        $r->post('/payment/paytr/callback', 'CheckoutController@paytrCallback');
        $r->post('/payment/iyzico/callback', 'CheckoutController@iyzicoCallback');

        // Auth
        $r->get('/giris', 'AuthController@login');
        $r->post('/giris', 'AuthController@loginPost');
        $r->get('/kayit', 'AuthController@register');
        $r->post('/kayit', 'AuthController@registerPost');
        $r->get('/cikis', 'AuthController@logout');
        $r->get('/sifremi-unuttum', 'AuthController@forgot');
        $r->post('/sifremi-unuttum', 'AuthController@forgotPost');
        $r->get('/sifre-sifirla/{token}', 'AuthController@reset');
        $r->post('/sifre-sifirla', 'AuthController@resetPost');

        // Hesabım
        $r->get('/hesabim', 'AccountController@index');
        $r->post('/hesabim/guncelle', 'AccountController@update');
        $r->post('/hesabim/sifre-degistir', 'AccountController@changePassword');
        $r->get('/siparislerim', 'AccountController@orders');
        $r->get('/siparis/{id}', 'AccountController@orderDetail');

        // Destek
        $r->get('/destek', 'SupportController@index');
        $r->get('/destek/yeni', 'SupportController@create');
        $r->post('/destek/olustur', 'SupportController@store');
        $r->get('/destek/{id}', 'SupportController@show');
        $r->post('/destek/{id}/yanit', 'SupportController@reply');

        // Blog
        $r->get('/blog', 'BlogController@index');
        $r->get('/blog/kategori/{slug}', 'BlogController@category');
        $r->get('/blog/{slug}', 'BlogController@show');

        // Sayfalar
        $r->get('/sss', 'PageController@faq');
        $r->get('/iletisim', 'PageController@contact');
        $r->post('/iletisim', 'PageController@contactPost');
        $r->post('/bulten/kayit', 'NewsletterController@subscribe');
        $r->get('/sayfa/{slug}', 'PageController@show');

        // SEO
        $r->get('/sitemap.xml', 'SitemapController@index');
        $r->get('/robots.txt', 'SitemapController@robots');

        // ── Admin ──
        $r->get('/admin/giris', 'Admin\\AuthController@login');
        $r->post('/admin/giris', 'Admin\\AuthController@loginPost');
        $r->get('/admin/cikis', 'Admin\\AuthController@logout');

        $r->get('/admin', 'Admin\\DashboardController@index');

        // Admin Kategoriler
        $r->get('/admin/kategoriler', 'Admin\\CategoryController@index');
        $r->post('/admin/kategoriler/toplu-islem', 'Admin\\CategoryController@bulkAction');
        $r->post('/admin/kategoriler/varsayilan-kur', 'Admin\\CategoryController@seedDefault');
        $r->get('/admin/kategoriler/export', 'Admin\\CategoryController@exportCsv');
        $r->get('/admin/kategori/ekle', 'Admin\\CategoryController@create');
        $r->post('/admin/kategori/kaydet', 'Admin\\CategoryController@store');
        $r->get('/admin/kategori/{id}/duzenle', 'Admin\\CategoryController@edit');
        $r->post('/admin/kategori/{id}/guncelle', 'Admin\\CategoryController@update');
        $r->post('/admin/kategori/{id}/sil', 'Admin\\CategoryController@delete');
        $r->post('/admin/kategori/{id}/durum', 'Admin\\CategoryController@toggleStatus');

        // Admin Paketler
        $r->get('/admin/paketler', 'Admin\\PackageController@index');
        $r->post('/admin/paketler/toplu-islem', 'Admin\\PackageController@bulkAction');
        $r->post('/admin/paketler/yayinla', 'Admin\\PackageController@publishCatalog');
        $r->get('/admin/yazilim/ekle', 'Admin\\PackageController@createSoftware');
        $r->post('/admin/yazilim/kaydet', 'Admin\\PackageController@storeSoftware');
        $r->get('/admin/yazilim/{id}/duzenle', 'Admin\\PackageController@editSoftware');
        $r->post('/admin/yazilim/{id}/guncelle', 'Admin\\PackageController@updateSoftware');
        $r->get('/admin/paket/ekle', 'Admin\\PackageController@create');
        $r->post('/admin/paket/kaydet', 'Admin\\PackageController@store');
        $r->get('/admin/paket/{id}/duzenle', 'Admin\\PackageController@edit');
        $r->post('/admin/paket/{id}/guncelle', 'Admin\\PackageController@update');
        $r->post('/admin/paket/{id}/sil', 'Admin\\PackageController@delete');
        $r->post('/admin/paket/{id}/durum', 'Admin\\PackageController@toggleStatus');
        $r->get('/admin/paket/{id}/alanlar', 'Admin\\PackageController@fields');
        $r->post('/admin/paket/{id}/alanlar-kaydet', 'Admin\\PackageController@saveFields');

        // Hazır Yazılımlar & Referanslar: authenticated admin-only modules.
        $r->get('/admin/hazir-yazilimlar', 'Admin\\SoftwareShowcaseController@index');
        $r->post('/admin/hazir-yazilimlar/kategorileri-kur', 'Admin\\SoftwareShowcaseController@install');
        $r->post('/admin/hazir-yazilimlar/kur-ve-ekle', 'Admin\\SoftwareShowcaseController@setupThenCreate');
        $r->post('/admin/hazir-yazilimlar/kaydet', 'Admin\\SoftwareShowcaseController@save');
        $r->get('/admin/referanslar', 'Admin\\ReferencesController@index');
        $r->post('/admin/referanslar/ekle', 'Admin\\ReferencesController@store');
        $r->post('/admin/referanslar/{id}/guncelle', 'Admin\\ReferencesController@update');
        $r->post('/admin/referanslar/{id}/sil', 'Admin\\ReferencesController@delete');

        // Admin Siparişler
        $r->get('/admin/siparisler', 'Admin\\OrderController@index');
        $r->get('/admin/siparis/{id}', 'Admin\\OrderController@show');
        $r->post('/admin/siparis/{id}/durum', 'Admin\\OrderController@updateStatus');
        $r->get('/admin/havale-bildirimleri', 'Admin\\OrderController@bankNotifications');
        $r->post('/admin/havale/{id}/onayla', 'Admin\\OrderController@confirmBankTransfer');
        $r->post('/admin/havale/{id}/reddet', 'Admin\\OrderController@rejectBankTransfer');

        // Admin Ödeme Modülleri
        $r->get('/admin/odeme-modulleri', 'Admin\\PaymentGatewayController@index');
        $r->post('/admin/odeme-modulleri/{id}/toggle', 'Admin\\PaymentGatewayController@toggle');
        $r->post('/admin/odeme-modulleri/{id}/varsayilan', 'Admin\\PaymentGatewayController@setDefault');
        $r->get('/admin/paytr-ayarlari', 'Admin\\PaymentGatewayController@paytrSettings');
        $r->post('/admin/paytr-ayarlari/kaydet', 'Admin\\PaymentGatewayController@savePaytrSettings');
        $r->get('/admin/iyzico-ayarlari', 'Admin\\PaymentGatewayController@iyzicoSettings');
        $r->post('/admin/iyzico-ayarlari/kaydet', 'Admin\\PaymentGatewayController@saveIyzicoSettings');

        // Admin Blog
        $r->get('/admin/blog', 'Admin\\BlogController@index');
        $r->get('/admin/blog/ekle', 'Admin\\BlogController@create');
        $r->post('/admin/blog/kaydet', 'Admin\\BlogController@store');
        $r->get('/admin/blog/{id}/duzenle', 'Admin\\BlogController@edit');
        $r->post('/admin/blog/{id}/guncelle', 'Admin\\BlogController@update');
        $r->post('/admin/blog/{id}/sil', 'Admin\\BlogController@delete');
        $r->post('/admin/blog/preview', 'Admin\\BlogController@preview');
        $r->post('/admin/blog/upload-image', 'Admin\\BlogController@uploadImage');
        $r->get('/admin/blog-kategorileri', 'Admin\\BlogController@categories');
        $r->post('/admin/blog-kategorisi/kaydet', 'Admin\\BlogController@storeCategory');
        $r->post('/admin/blog-kategorisi/{id}/sil', 'Admin\\BlogController@deleteCategory');

        // Admin Ayarlar
        // Üst menü yönetimi: admin girişi ve CSRF zorunlu.
        $r->get('/admin/menu', 'Admin\\NavigationController@index');
        $r->post('/admin/menu/kaydet', 'Admin\\NavigationController@save');
        $r->post('/admin/menu/sifirla', 'Admin\\NavigationController@reset');

        $r->get('/admin/site-ayarlari', 'Admin\\SettingsController@site');
        $r->post('/admin/site-ayarlari/kaydet', 'Admin\\SettingsController@saveSite');
        $r->get('/admin/smtp-ayarlari', 'Admin\\SettingsController@smtp');
        $r->post('/admin/smtp-ayarlari/kaydet', 'Admin\\SettingsController@saveSmtp');
        $r->get('/admin/banka-hesaplari', 'Admin\\SettingsController@bankAccounts');
        $r->post('/admin/banka-hesabi/kaydet', 'Admin\\SettingsController@storeBankAccount');
        $r->post('/admin/banka-hesabi/{id}/sil', 'Admin\\SettingsController@deleteBankAccount');
        $r->get('/admin/uyeler', 'Admin\\SettingsController@users');
        $r->get('/admin/admin-kullanicilari', 'Admin\\SettingsController@adminUsers');
        $r->get('/admin/sayfalar', 'Admin\\SettingsController@pages');
        $r->get('/admin/sayfa/{id}/duzenle', 'Admin\\SettingsController@editPage');
        $r->post('/admin/sayfa/{id}/guncelle', 'Admin\\SettingsController@updatePage');
        $r->get('/admin/sss', 'Admin\\SettingsController@faqs');
        $r->post('/admin/sss/kaydet', 'Admin\\SettingsController@storeFaq');
        $r->post('/admin/sss/{id}/sil', 'Admin\\SettingsController@deleteFaq');
        $r->get('/admin/loglar', 'Admin\\SettingsController@logs');
        $r->get('/admin/import', 'Admin\\SettingsController@importPage');
        $r->post('/admin/import/islem', 'Admin\\SettingsController@importProcess');
        $r->get('/admin/import/bos-sablon', 'Admin\\SettingsController@downloadBlankTemplate');
        $r->get('/admin/import/ornek-sablon', 'Admin\\SettingsController@downloadExampleTemplate');

        // Admin Destek
        // İletişim gelen kutusu ve bülten aboneleri
        $r->get('/admin/mesajlar', 'Admin\\MessageController@index');
        $r->post('/admin/mesaj/{id}/okundu', 'Admin\\MessageController@markRead');
        $r->post('/admin/bulten/{id}/iptal', 'Admin\\MessageController@unsubscribe');

        $r->get('/admin/destek', 'Admin\\SettingsController@supportIndex');
        $r->get('/admin/destek/{id}', 'Admin\\SettingsController@supportShow');
        $r->post('/admin/destek/{id}/yanit', 'Admin\\SettingsController@supportReply');

        // Admin Ödemeler (liste)
        $r->get('/admin/odemeler', 'Admin\\SettingsController@payments');

        // Admin SEO Merkezi
        $r->get('/admin/seo', 'Admin\\SettingsController@seoCenter');

        // Admin Ana Sayfa Yönetimi
        $r->get('/admin/ana-sayfa', 'Admin\\HomeSectionController@index');
        $r->get('/admin/ana-sayfa/{id}/duzenle', 'Admin\\HomeSectionController@edit');
        $r->post('/admin/ana-sayfa/{id}/guncelle', 'Admin\\HomeSectionController@update');
        $r->post('/admin/ana-sayfa/{id}/durum', 'Admin\\HomeSectionController@toggleStatus');

        // Admin Lisans Yönetimi
        $r->get('/admin/lisans', 'Admin\\LicenseController@index');
        $r->post('/admin/lisans/aktifle', 'Admin\\LicenseController@activate');
        $r->post('/admin/lisans/kontrol', 'Admin\\LicenseController@check');
        $r->post('/admin/lisans/devre-disi', 'Admin\\LicenseController@deactivate');
        $r->post('/admin/lisans/kaydet', 'Admin\\LicenseController@saveSettings');
    }
}
