<?php
namespace App\Controllers\Admin;
use App\Core\Controller;
use App\Core\Csrf;
use App\Services\LandingContentService as Content;
use App\Services\RegionalLandingService as Landing;
use App\Services\HomeKnowledgeService as Knowledge;

final class LandingController extends Controller
{
    public function index():void
    {
        $page=filter_var($_GET['page']??1,FILTER_VALIDATE_INT)?:1;$q=is_string($_GET['q']??null)?mb_substr(trim($_GET['q']),0,100):'';
        $this->renderAdmin('admin/landing/index',['pageTitle'=>'Konumsal Hizmet Sayfaları','listing'=>Landing::listing($page,$q),'landingQuery'=>$q]);
    }
    public function create():void{$kind=is_string($_GET['sablon']??null)?$_GET['sablon']:'';$this->form(Landing::starter($kind));}
    public function edit(string $id):void
    {$row=Landing::byId((int)$id);if(!$row){redirect('/admin/konumsal-sayfalar');return;}$this->form(Landing::decode($row),$row);}
    private function form(array $data,?array $row=null):void
    {
        $this->renderAdmin('admin/landing/form',['pageTitle'=>$row?'Konumsal Sayfayı Düzenle':'Konumsal Sayfa Oluştur','landing'=>$data,'landingRow'=>$row,'landingCategories'=>$this->db->fetchAll("SELECT id,name FROM categories WHERE status='active' ORDER BY name"),'landingReferences'=>\App\Services\ReferencesService::all(true),'landingNotes'=>Content::diagnostics($data)]);
    }
    public function save():void
    {
        Csrf::check();$id=filter_var($_POST['id']??0,FILTER_VALIDATE_INT)?:0;$back=$id?'/admin/konumsal-sayfalar/'.$id.'/duzenle':'/admin/konumsal-sayfalar/yeni';
        $newUploads=[];
        try{
            $data=Landing::validate($_POST);
            foreach(['image','mobile_image']as $key)if(($_FILES[$key.'_upload']['error']??UPLOAD_ERR_NO_FILE)!==UPLOAD_ERR_NO_FILE){$data[$key]=Content::upload($_FILES[$key.'_upload']);$newUploads[]=$data[$key];}
            $id=Landing::save($data,$id,Content::text($_POST['revision']??'',64));flash('success','Sayfa kaydedildi. Slug değiştiyse eski adres 301 ile korunur.');redirect('/admin/konumsal-sayfalar/'.$id.'/duzenle');
        }catch(\InvalidArgumentException $e){flash('error',$e->getMessage());}
        catch(\Throwable $e){flash('error','Sayfa kaydedilemedi. Tekrar deneyin; slug çakışmasını ve veritabanı erişimini kontrol edin.');}
        Content::discardNewUploads($newUploads);
        redirect($back);
    }
    public function duplicate(string $id):void
    {
        Csrf::check();$row=Landing::byId((int)$id);if(!$row){redirect('/admin/konumsal-sayfalar');return;}
        $data=Landing::decode($row);$data['status']='inactive';$data['slug']=mb_substr($data['slug'],0,150).'-taslak-'.bin2hex(random_bytes(3));$data['seo']['robots']='noindex,follow';$data['seo']['canonical']='';
        try{$new=Landing::save(Landing::validate($data));flash('success','Taslak kopyası oluşturuldu. Özgün içerikleri tamamlamadan yayınlamayın.');redirect('/admin/konumsal-sayfalar/'.$new.'/duzenle');}catch(\Throwable $e){flash('error','Taslak kopyası oluşturulamadı.');redirect('/admin/konumsal-sayfalar');}
    }
    public function home():void
    {$this->renderAdmin('admin/landing/home',['pageTitle'=>'Ana Sayfa Bilgi Alanı','knowledge'=>Knowledge::get(),'knowledgeRevision'=>Knowledge::revision()]);}
    public function saveHome():void
    {
        Csrf::check();try{$data=Knowledge::validate($_POST);Knowledge::save($data,Content::text($_POST['revision']??'',64));flash('success','Üçlü bilgi alanı güncellendi.');}catch(\InvalidArgumentException $e){flash('error',$e->getMessage());}catch(\Throwable $e){flash('error','Bilgi alanı kaydedilemedi.');}redirect('/admin/bilgi-alani');
    }
}
