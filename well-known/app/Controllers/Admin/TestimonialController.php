<?php
namespace App\Controllers\Admin;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Upload;
use App\Services\TestimonialManager;
final class TestimonialController extends Controller {
    public function index(): void {
        $rows=TestimonialManager::rows();
        $categories=$this->db->fetchAll('SELECT id,name FROM categories ORDER BY name LIMIT 500');
        $packages=$this->db->fetchAll('SELECT id,category_id,name FROM packages ORDER BY name LIMIT 2000');
        $this->renderAdmin('admin/testimonials/index',[
            'pageTitle'=>'Müşteri Yorumları','reviews'=>$rows,'categories'=>$categories,'packages'=>$packages
        ]);
    }
    public function save(): void {
        Csrf::check();
        try{
            $image=null;
            if(!empty($_FILES['image']['name'])) {
                $image=Upload::image($_FILES['image'],'testimonials');
                if(!$image)throw new \RuntimeException('Profil fotoğrafı yüklenemedi.');
            }
            TestimonialManager::save($_POST,$image);
            logActivity('testimonial_save','Yorum içeriği düzenlendi.');
            flash('success','Yorum kaydedildi.');
        }catch(\Throwable $e){flash('error',$e->getMessage());}
        redirect('/admin/yorumlar');
    }
    public function remove(): void {
        Csrf::check();
        try{
            TestimonialManager::remove((string)($_POST['id']??''));
            logActivity('testimonial_remove','Yorum kaydı kaldırıldı.');
            flash('success','Yorum kaldırıldı.');
        }catch(\Throwable $e){flash('error',$e->getMessage());}
        redirect('/admin/yorumlar');
    }
}