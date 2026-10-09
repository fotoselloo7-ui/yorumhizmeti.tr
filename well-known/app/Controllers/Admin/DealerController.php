<?php
namespace App\Controllers\Admin;

use App\Core\AdminAuth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Services\DealerProgramService;

final class DealerController extends Controller
{
    public function index(): void
    {
        $this->renderAdmin('admin/dealers/index',[
          'pageTitle'=>'Bayilik Yönetimi',
          'ready'=>DealerProgramService::ready(),
          'dealers'=>DealerProgramService::adminList(),
          'history'=>DealerProgramService::auditTrail(),
          'canApprove'=>(AdminAuth::admin()['role']??'')==='super_admin'
        ]);
    }
    public function review(string $id): void
    {
        Csrf::check();
        if((AdminAuth::admin()['role']??'')!=='super_admin'){
            http_response_code(403);echo 'Bayilik kararları için süper yönetici yetkisi gerekir.';return;
        }
        $status=trim((string)($_POST['status']??''));
        $tier=trim((string)($_POST['tier']??''));
        $rateRaw=(string)($_POST['commission_rate']??'');
        if(!preg_match('/^(?:\d{1,2})(?:\.\d{1,2})?$/D',$rateRaw)){
            flash('error','Komisyon oranı 0–30 arasında ve en fazla iki ondalıklı olmalıdır.');
            redirect('/admin/bayilik');return;
        }
        try{
            DealerProgramService::review((int)$id,(int)AdminAuth::id(),$status,$tier,(float)$rateRaw);
            logActivity('netvera_dealer_review','Bayilik kararı güncellendi. Bayi ID: '.(int)$id);
            flash('success','Bayilik durumu ve oranı kaydedildi. Bu işlem otomatik ödeme başlatmaz.');
        }catch(\Throwable $e){flash('error',$e->getMessage());}
        redirect('/admin/bayilik');
    }
}
