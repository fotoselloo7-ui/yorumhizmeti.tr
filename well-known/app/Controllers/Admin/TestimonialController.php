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
        // Actual customer-owned NetVera product reviews are a separate,
        // read-only source from editorial testimonials in this admin form.
        $netveraReviews=[];
        try{
            $netveraReviews=$this->db->fetchAll(
                "SELECT r.old_review_id,r.rating,r.comment,r.status,r.created_at,
                        u.name AS customer_name,u.email AS customer_email,
                        p.name AS product_name,p.slug AS product_slug
                 FROM nv_private_content_reviews r
                 INNER JOIN users u ON u.id=r.new_user_id
                 LEFT JOIN nv_legacy_script_products p
                   ON r.target_type='script' AND p.legacy_id=r.product_legacy_id
                 ORDER BY r.created_at DESC,r.old_review_id DESC LIMIT 150"
            );
        }catch(\Throwable $e){
            // Fresh installations without an imported private DB still work.
            error_log('NetVera customer review overview unavailable: '.get_class($e));
        }
        $this->renderAdmin('admin/testimonials/index',[
            'pageTitle'=>'Müşteri Yorumları','reviews'=>$rows,'categories'=>$categories,'packages'=>$packages,
            'netveraReviews'=>$netveraReviews
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