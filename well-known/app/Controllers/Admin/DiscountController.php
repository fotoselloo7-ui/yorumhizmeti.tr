<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Csrf;
use App\Services\DiscountService;

final class DiscountController extends Controller
{
    public function index(): void
    {
        $this->renderAdmin('admin/discounts/index',[
            'pageTitle'=>'Kupon & İndirim Yönetimi',
            'campaigns'=>DiscountService::all(),
        ]);
    }

    public function create(): void
    {
        $presetTarget='';
        $raw=trim((string)($_GET['target']??''));
        if(preg_match('/^(package|service_category|script|script_category):([1-9][0-9]*)$/',$raw,$m)){
            $presetTarget=$m[1].':'.(int)$m[2];
        }
        $this->renderAdmin('admin/discounts/form',[
            'pageTitle'=>'Yeni Kupon / İndirim',
            'campaign'=>null,
            'targetOptions'=>DiscountService::targetOptions(),
            'presetTarget'=>$presetTarget,
            'presetName'=>mb_substr(trim((string)($_GET['name']??'')),0,160),
        ]);
    }

    public function edit(string $id): void
    {
        $campaign=DiscountService::find((int)$id);
        if(!$campaign){ flash('error','Kupon bulunamadı.');redirect('/admin/kuponlar');return; }
        $this->renderAdmin('admin/discounts/form',[
            'pageTitle'=>'Kupon / İndirim Düzenle',
            'campaign'=>$campaign,
            'targetOptions'=>DiscountService::targetOptions(),
        ]);
    }

    public function save(): void
    {
        Csrf::check();
        $id=max(0,(int)($_POST['id']??0));
        $return=$id?'/admin/kuponlar/'.$id.'/duzenle':'/admin/kuponlar/yeni';
        try{
            $saved=DiscountService::save($_POST,$_POST['targets']??[],$id?:null);
            logActivity('discount_save','Kupon/indirim kampanyası kaydedildi. ID: '.$saved);
            flash('success','Kupon / indirim kampanyası kaydedildi.');
            redirect('/admin/kuponlar/'.$saved.'/duzenle');
        }catch(\Throwable $e){
            flash('error',$e instanceof \InvalidArgumentException?$e->getMessage():'Kupon kaydedilemedi.');
            redirect($return);
        }
    }

    public function toggle(string $id): void
    {
        Csrf::check();
        DiscountService::toggle((int)$id);
        flash('success','Kampanya durumu güncellendi.');
        redirect('/admin/kuponlar');
    }

    public function delete(string $id): void
    {
        Csrf::check();
        DiscountService::delete((int)$id);
        flash('success','Kampanya arşivlendi.');
        redirect('/admin/kuponlar');
    }
}
