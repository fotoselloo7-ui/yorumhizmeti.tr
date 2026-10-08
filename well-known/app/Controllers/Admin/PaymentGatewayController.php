<?php
namespace App\Controllers\Admin;
use App\Core\Controller;
use App\Core\Csrf;
use App\Services\PaymentGatewayManager;

class PaymentGatewayController extends Controller
{
    public function index(): void
    {
        $manager = new PaymentGatewayManager();
        $gateways = $manager->getAllGateways();
        $this->renderAdmin('admin/payment-gateways/index', ['pageTitle' => 'Ödeme Modülleri', 'gateways' => $gateways]);
    }

    public function toggle(string $id): void
    {
        Csrf::check();
        $manager = new PaymentGatewayManager();
        $manager->toggleStatus((int) $id);
        logActivity('gateway_toggle', "Ödeme modülü durumu değiştirildi: ID {$id}");
        flash('success', 'Ödeme modülü durumu güncellendi.');
        redirect('/admin/odeme-modulleri');
    }

    public function setDefault(string $id): void
    {
        Csrf::check();
        $manager = new PaymentGatewayManager();
        $manager->setDefault((int) $id);
        logActivity('gateway_default', "Varsayılan ödeme modülü değiştirildi: ID {$id}");
        flash('success', 'Varsayılan ödeme modülü güncellendi.');
        redirect('/admin/odeme-modulleri');
    }

    public function paytrSettings(): void
    {
        $manager = new PaymentGatewayManager();
        $gateway = $manager->getGateway('paytr');
        $settings = json_decode($gateway['settings'] ?? '{}', true) ?: [];
        $this->renderAdmin('admin/payment-gateways/paytr', ['pageTitle' => 'PayTR Ayarları', 'settings' => $settings, 'gateway' => $gateway]);
    }

    public function savePaytrSettings(): void
    {
        Csrf::check();
        $manager = new PaymentGatewayManager();
        $gateway = $manager->getGateway('paytr');
        if(!$gateway){
            flash('error','PayTR ödeme sağlayıcısı kayıtlı değil.');
            redirect('/admin/odeme-modulleri');return;
        }
        $old = json_decode((string)($gateway['settings']??'{}'), true) ?: [];
        // Masked secret inputs intentionally submit empty values when the admin
        // is not rotating keys. Never accidentally erase working credentials.
        $settings = [
            'merchant_id' => trim((string)($_POST['merchant_id']??'')) ?: (string)($old['merchant_id']??''),
            'merchant_key' => trim((string)($_POST['merchant_key']??'')) ?: (string)($old['merchant_key']??''),
            'merchant_salt' => trim((string)($_POST['merchant_salt']??'')) ?: (string)($old['merchant_salt']??''),
            'test_mode' => ($_POST['test_mode']??'1')==='0'?'0':'1',
        ];
        $manager->updateSettings('paytr', $settings);
        logActivity('paytr_settings', 'PayTR ayarları güncellendi (gizli bilgiler loglanmadı).');
        flash('success', 'PayTR ayarları kaydedildi. Gizli alanlar boş bırakıldığında eski değerleri korunur.');
        redirect('/admin/paytr-ayarlari');
    }

    public function iyzicoSettings(): void
    {
        $manager = new PaymentGatewayManager();
        $gateway = $manager->getGateway('iyzico');
        $settings = json_decode($gateway['settings'] ?? '{}', true) ?: [];
        $this->renderAdmin('admin/payment-gateways/iyzico', ['pageTitle' => 'iyzico Ayarları', 'settings' => $settings, 'gateway' => $gateway]);
    }

    public function saveIyzicoSettings(): void
    {
        Csrf::check();
        $settings = [
            'api_key' => trim($_POST['api_key'] ?? ''),
            'secret_key' => trim($_POST['secret_key'] ?? ''),
            'base_url' => trim($_POST['base_url'] ?? 'https://sandbox-api.iyzipay.com'),
        ];
        (new PaymentGatewayManager())->updateSettings('iyzico', $settings);
        logActivity('iyzico_settings', 'iyzico ayarları güncellendi.');
        flash('success', 'iyzico ayarları kaydedildi.');
        redirect('/admin/iyzico-ayarlari');
    }
}
