<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Csrf;
use App\Services\SmmCatalogService;
use App\Services\SmmFulfillmentService;
use App\Services\SmmApiClient;

final class SmmController extends Controller
{
    private function ownerOnly(): void
    {
        if (($_SESSION['admin_role'] ?? '') !== 'super_admin') {
            http_response_code(403);
            exit('Bu işlem için süper yönetici yetkisi gerekiyor.');
        }
    }

    public function index(): void
    {
        $installed=SmmCatalogService::installed();
        $providers=$installed?SmmCatalogService::providers():[];
        $provider=(int)($_GET['provider']??0);
        $search=mb_substr(trim((string)($_GET['q']??'')),0,80);
        $this->renderAdmin('admin/smm/index',[
            'pageTitle'=>'Sosyal Medya API ve Servis Yönetimi',
            'installed'=>$installed,'providers'=>$providers,
            'services'=>$installed?SmmCatalogService::services($provider,$search):[],
            'categories'=>SmmCatalogService::socialCategories(),
            'links'=>$installed?SmmCatalogService::linkedPackages():[],
            'jobs'=>$installed?SmmFulfillmentService::jobs():[],
            'currentProvider'=>$provider,'search'=>$search,
            'canEditProviders'=>($_SESSION['admin_role']??'')==='super_admin',
        ]);
    }

    public function install(): void
    {
        $this->ownerOnly();
        Csrf::check();
        try {
            SmmCatalogService::install();
            flash('success','SMM veritabanı tabloları kuruldu. Mevcut paket/sipariş verileri değiştirilmedi.');
        } catch (\Throwable $e) {
            error_log('SMM database install: '.get_class($e));
            flash('error','Kurulum başarısız. SQL yetkilerini ve sunucu hata kayıtlarını kontrol edin.');
        }
        redirect('/admin/smm');
    }

    public function saveProvider(): void
    {
        $this->ownerOnly();
        Csrf::check();
        try {
            $id=SmmCatalogService::saveProvider($_POST);
            logActivity('smm_provider','Tedarikçi kaydedildi: '.$id);
            flash('success','Tedarikçi bağlantısı kaydedildi. Şimdi servis listesini eşitleyebilirsiniz.');
        } catch (\Throwable $e) {
            flash('error',$e->getMessage());
        }
        redirect('/admin/smm');
    }

    public function sync(string $id): void
    {
        Csrf::check();
        try {
            $count=SmmCatalogService::sync((int)$id);
            flash('success',$count.' API servisi eşitlendi. Mevcut paket açıklamaları, SEO ve satış fiyatları korunuyor.');
            logActivity('smm_sync','Servis senkronizasyonu: '.$id.' / '.$count);
        } catch (\Throwable $e) {
            error_log('SMM sync provider '.$id.': '.get_class($e));
            flash('error','API senkronizasyonu başarısız: '.$e->getMessage());
        }
        redirect('/admin/smm?provider='.(int)$id);
    }

    public function balance(string $id): void
    {
        Csrf::check();
        try {
            $data=SmmCatalogService::api((int)$id)->call('balance');
            $amount=(string)($data['balance']??'?');
            $currency=(string)($data['currency']??'?');
            flash('success','Tedarikçi bakiyesi: '.mb_substr($amount,0,40).' '.mb_substr($currency,0,8));
        } catch (\Throwable $e) {
            flash('error','API testi başarısız: '.$e->getMessage());
        }
        redirect('/admin/smm');
    }

    public function createCategory(): void
    {
        Csrf::check();
        try {
            $id=SmmCatalogService::createCategory((string)($_POST['name']??''));
            flash('success','Sosyal medya alt kategorisi oluşturuldu (#'.$id.').');
        } catch (\Throwable $e) {
            flash('error',$e->getMessage());
        }
        redirect('/admin/smm');
    }

    public function publish(): void
    {
        Csrf::check();
        try {
            $id=SmmCatalogService::publish($_POST);
            logActivity('smm_package','API servisinden özgün paket oluşturuldu: '.$id);
            flash('success','Paket oluşturuldu. Kendi satış açıklamanızı, fiyatınızı ve SEO alanlarını normal paket editöründen yönetebilirsiniz.');
            redirect('/admin/paket/'.$id.'/duzenle');
            return;
        } catch (\Throwable $e) {
            flash('error',$e->getMessage());
        }
        redirect('/admin/smm?provider='.(int)($_POST['provider_filter']??0));
    }

    public function syncAll(): void
    {
        $this->ownerOnly();
        Csrf::check();
        try {
            $stats=SmmCatalogService::syncAll();
            logActivity('smm_sync_all','Panel sayısı: '.$stats['providers'].'; servis: '.$stats['services']);
            $message=$stats['providers'].' tedarikçi eşitlendi, '.$stats['services'].' servis okundu.';
            if($stats['errors']) $message.=' Hatalar: '.implode(' | ',$stats['errors']);
            flash($stats['errors']?'warning':'success',$message);
        } catch (\Throwable $e) {flash('error',$e->getMessage());}
        redirect('/admin/smm');
    }

    public function bulkPublish(): void
    {
        Csrf::check();
        try {
            $result=SmmCatalogService::bulkPublish($_POST);
            logActivity('smm_bulk_import','Toplu paket oluşturma: '.$result['created'].' başarılı, '.count($result['skipped']).' atlandı');
            $summary=$result['created'].' paket taslak olarak oluşturuldu.';
            if($result['skipped']) $summary.=' Atlananlar: '.implode(', ',array_slice($result['skipped'],0,8));
            flash($result['skipped']?'warning':'success',$summary);
        } catch (\Throwable $e) {flash('error',$e->getMessage());}
        redirect('/admin/smm?provider='.(int)($_POST['provider_filter']??0).'#smm-bulk');
    }

    public function mapping(string $id): void
    {
        Csrf::check();
        try {
            SmmCatalogService::updateMapping((int)$id,(int)($_POST['service_id']??0),
                (int)($_POST['fulfillment_quantity']??0),!empty($_POST['enabled']));
            flash('success','Paket bağlantısı güncellendi. Açıklama, SEO, fiyat ve URL değişmedi.');
        } catch (\Throwable $e) {
            flash('error',$e->getMessage());
        }
        redirect('/admin/smm');
    }

    public function run(): void
    {
        Csrf::check();
        try {
            $result=SmmFulfillmentService::process(20);
            flash('success','İşlenen kuyruk: '.$result['sent'].' gönderildi, '.$result['updated']
                .' durum güncellendi, '.$result['review'].' manuel kontrol gerekiyor.');
        } catch (\Throwable $e) {
            error_log('SMM worker error: '.get_class($e));
            flash('error','Sipariş işlem kuyruğu tamamlanamadı. Sunucu kayıtlarını inceleyin.');
        }
        redirect('/admin/smm');
    }

    public function refill(string $id): void
    {
        Csrf::check();
        try {
            SmmFulfillmentService::refill((int)$id);
            flash('success','Yenileme talebi gönderildi.');
        } catch (\Throwable $e) {
            flash('error',$e->getMessage());
        }
        redirect('/admin/smm');
    }

    public function cancel(string $id): void
    {
        Csrf::check();
        try {
            SmmFulfillmentService::cancel((int)$id);
            flash('success','Tedarikçiye iptal isteği iletildi. Müşteri iadesi otomatik başlatılmadı.');
        } catch (\Throwable $e) {
            flash('error',$e->getMessage());
        }
        redirect('/admin/smm');
    }
    public function reconcile(string $id): void
    {
        $this->ownerOnly();
        Csrf::check();
        try {
            SmmFulfillmentService::reconcile(
                (int)$id,
                (string)($_POST['mode'] ?? ''),
                (string)($_POST['upstream_order_id'] ?? ''),
                ($_POST['verify_absent'] ?? '') === '1'
            );
            logActivity('smm_reconcile', 'Tedarikçi sipariş uzlaştırma: '.(int)$id);
            flash('success', 'Tedarikçi siparişi yönetici tarafından güvenli şekilde uzlaştırıldı.');
        } catch (\Throwable $e) {
            flash('error', $e->getMessage());
        }
        redirect('/admin/smm');
    }

}
