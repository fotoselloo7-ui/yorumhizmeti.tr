<?php
namespace App\Controllers\Admin;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Upload;
use App\Services\NetveraBridgeService;

/**
 * Netvera product management; isolated from service-package/order tables.
 * No payment settings and no private demo credentials are copied here.
 */
final class NetveraScriptController extends Controller
{
    public function index():void
    {
        $products=NetveraBridgeService::ready()
            ? $this->db->fetchAll("SELECT * FROM nv_legacy_script_products ORDER BY sort_order,legacy_id DESC")
            : [];
        $this->renderAdmin('admin/netvera-scripts/index',[
            'pageTitle'=>'Netvera Yazılımları',
            'products'=>$products,
            'ready'=>NetveraBridgeService::ready()
        ]);
    }

    public function create():void
    {
        $this->renderAdmin('admin/netvera-scripts/form',[
            'pageTitle'=>'Netvera Yazılımı Ekle',
            'product'=>null,'data'=>[],
            'categories'=>NetveraBridgeService::categories()
        ]);
    }

    public function edit(string $id):void
    {
        $p=$this->db->fetch('SELECT * FROM nv_legacy_script_products WHERE legacy_id=?',[(int)$id]);
        if(!$p){flash('error','Yazılım bulunamadı.');redirect('/admin/netvera-yazilimlar');return;}
        $this->renderAdmin('admin/netvera-scripts/form',[
            'pageTitle'=>'Netvera Yazılımı Düzenle',
            'product'=>$p,
            'data'=>NetveraBridgeService::jsonFields($p),
            'categories'=>NetveraBridgeService::categories(),
            'gallery'=>NetveraBridgeService::gallery((int)$id)
        ]);
    }

    public function save():void
    {
        Csrf::check();
        if(!NetveraBridgeService::ready()){
            flash('error','Önce Netvera genel içerik aktarımını staging ortamına kurun.');
            redirect('/admin/netvera-yazilimlar');return;
        }
        $id=max(0,(int)($_POST['id']??0));
        $old=$id?$this->db->fetch('SELECT * FROM nv_legacy_script_products WHERE legacy_id=?',[$id]):null;
        if($id&&!$old){flash('error','Yazılım bulunamadı.');redirect('/admin/netvera-yazilimlar');return;}
        $category=(int)($_POST['category_legacy_id']??0);
        if(!$this->db->fetch('SELECT legacy_id FROM nv_legacy_script_categories WHERE legacy_id=? AND active=1',[$category])){
            flash('error','Netvera kategorisi seçin.');redirect('/admin/netvera-yazilimlar');return;
        }
        $name=trim(mb_substr((string)($_POST['name']??''),0,250));
        if($name===''){flash('error','Yazılım adı boş olamaz.');redirect('/admin/netvera-yazilimlar');return;}
        $slug=trim((string)($_POST['slug']??''));
        $slug=slugify($slug?:$name);
        if($old && $slug!==$old['slug'] && empty($_POST['allow_slug_change'])){
            $slug=$old['slug'];
        }
        $collision=$this->db->fetch('SELECT legacy_id FROM nv_legacy_script_products WHERE slug=? AND legacy_id<>? LIMIT 1',[$slug,$id]);
        if($collision){flash('error','Bu slug başka ürüne ait.');redirect('/admin/netvera-yazilimlar');return;}
        $newData=$old?NetveraBridgeService::jsonFields($old):[];
        $knownFields=[
          'demo_url','demo_video_url','current_version','last_updated_on',
          'install_type','install_info','update_duration_type','update_duration_months',
          'support_duration_type','support_duration_months',
          'tags','focus_keyword','secondary_keywords','showcase_button_label',
          'og_title','og_description','og_image','buy_url',
        ];
        foreach($knownFields as $key){
            $value=trim((string)($_POST[$key]??($newData[$key]??'')));
            $newData[$key]=mb_substr($value,0,in_array($key,['install_info','tags','secondary_keywords'],true)?1000:500);
        }
        foreach(['modules_json','specs_json','license_json','faq_json'] as $key){
            $raw=(string)($_POST[$key]??'[]');
            $decoded=json_decode($raw,true);
            if(!is_array($decoded)){
                flash('error',$key.' alanı geçerli bir JSON listesi olmalı.');
                redirect('/admin/netvera-yazilimlar');return;
            }
            $newData[$key]=json_encode($decoded,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }
        $newData['demo_is_active']=isset($_POST['demo_is_active'])?1:0;
        $newData['demo_is_public']=isset($_POST['demo_is_public'])?1:0;
        $newData['is_featured']=isset($_POST['is_featured'])?1:0;
        $newData['is_popular']=isset($_POST['is_popular'])?1:0;
        $newData['is_new']=isset($_POST['is_new'])?1:0;
        // Payment checkout remains quarantined; do not save merchant/secret fields.
        $price=max(0,round((float)($_POST['price']??0),2));
        $oldPrice=($_POST['old_price']??'')===''?null:max(0,round((float)$_POST['old_price'],2));
        $image=$old['cover_image']??null;
        if(!empty($_FILES['image']['name'])){
            $image=Upload::image($_FILES['image'],'scripts');
        }
        $newData=array_merge($newData,[
            'id'=>$id,'name'=>$name,'slug'=>$slug,'category_id'=>$category,
            'price'=>$price,'old_price'=>$oldPrice,'image'=>$image
        ]);
        $record=[
            'category_legacy_id'=>$category,'name'=>$name,'slug'=>$slug,
            'short_desc'=>trim((string)($_POST['short_desc']??'')),
            'description'=>trim((string)($_POST['description']??'')),
            'cover_image'=>$image,'price'=>$price,'old_price'=>$oldPrice,
            'badge'=>trim((string)($_POST['badge']??'')),
            'meta_title'=>trim((string)($_POST['meta_title']??'')),
            'meta_description'=>trim((string)($_POST['meta_description']??'')),
            'focus_keyword'=>$newData['focus_keyword'],
            'sort_order'=>(int)($_POST['sort_order']??0),
            'active'=>isset($_POST['is_active'])?1:0,
            'public_json'=>json_encode($newData,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_THROW_ON_ERROR)
        ];
        if($old){
            $this->db->update('nv_legacy_script_products',$record,'legacy_id=?',[$id]);
        }else{
            $id=$this->db->insert('nv_legacy_script_products',$record);
        }
        logActivity('netvera_software_save','Netvera yazılımı güncellendi: '.$name);
        flash('success','Netvera yazılım kaydı kaydedildi. Ödeme sistemi değiştirilmedi.');
        redirect('/admin/netvera-yazilimlar');
    }
    /** Product gallery: add images, never delete original files used by indexed URLs. */
    public function galleryAdd():void
    {
        Csrf::check();
        $id=(int)($_POST['product_id']??0);
        if($id<=0 || !NetveraBridgeService::ready() ||
           !$this->db->fetch('SELECT legacy_id FROM nv_legacy_script_products WHERE legacy_id=?',[$id])){
            flash('error','Yazılım bulunamadı.');
            redirect('/admin/netvera-yazilimlar');return;
        }
        if(empty($_FILES['image']['name'])){
            flash('error','Galeri görseli seçin.');
            redirect('/admin/netvera-yazilimlar/'.$id.'/duzenle');return;
        }
        try{$image=Upload::image($_FILES['image'],'scripts');}
        catch(\Throwable $e){
            flash('error','Galeri görseli yüklenemedi.');
            redirect('/admin/netvera-yazilimlar/'.$id.'/duzenle');return;
        }
        $row=$this->db->fetch('SELECT MAX(sort_order) AS max_order FROM nv_legacy_script_images WHERE product_legacy_id=?',[$id]);
        $this->db->insert('nv_legacy_script_images',[
            'product_legacy_id'=>$id,
            'image_path'=>$image,'alt_text'=>mb_substr(trim((string)($_POST['alt_text']??'')),0,290),
            'caption'=>mb_substr(trim((string)($_POST['caption']??'')),0,1500),
            'sort_order'=>(int)($row['max_order']??0)+1,'active'=>1
        ]);
        logActivity('netvera_gallery_add','Yazılım galeri görseli eklendi. ID: '.$id);
        flash('success','Galeri görseli eklendi.');
        redirect('/admin/netvera-yazilimlar/'.$id.'/duzenle');
    }

    public function galleryHide():void
    {
        Csrf::check();
        $id=(int)($_POST['product_id']??0);
        $imageId=(int)($_POST['image_id']??0);
        if($id>0 && $imageId>0 && NetveraBridgeService::ready()){
            $this->db->update('nv_legacy_script_images',['active'=>0],
                'legacy_id=? AND product_legacy_id=?',[$imageId,$id]);
            logActivity('netvera_gallery_hide','Yazılım galeri görseli pasife alındı. ID: '.$imageId);
            flash('success','Galeri görseli gizlendi. Orijinal dosya SEO için korundu.');
        }
        redirect('/admin/netvera-yazilimlar/'.$id.'/duzenle');
    }

}
