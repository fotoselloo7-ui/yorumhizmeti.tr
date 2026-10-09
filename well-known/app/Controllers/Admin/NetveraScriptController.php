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
        $tablesReady=NetveraBridgeService::ready();
        $products=$tablesReady
            ? $this->db->fetchAll("SELECT * FROM nv_legacy_script_products ORDER BY sort_order,legacy_id DESC")
            : [];
        // Show authentic 16 imported-source products on localhost even before
        // administrator runs the opt-in database import.
        if (!$products) $products=NetveraBridgeService::all();
        $this->renderAdmin('admin/netvera-scripts/index',[
            'pageTitle'=>'Netvera Yazılımları',
            'products'=>$products,
            'ready'=>$tablesReady
        ]);
    }

    public function create():void
    {
        $categories=NetveraBridgeService::categories();
        if(!$categories){
            flash('error','Yazılım eklemeden önce Netvera kategorisini oluşturun veya mevcut içerikleri aktarın.');
            redirect('/admin/netvera-kategoriler');return;
        }
        $this->renderAdmin('admin/netvera-scripts/form',[
            'pageTitle'=>'Netvera Yazılımı Ekle',
            'product'=>null,'data'=>[],
            'categories'=>$categories
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
            flash('error','Yazılım yönetimi veritabanı henüz kurulmamış. Yöneticiye başvurun.');
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
        // Never silently change indexed Netvera product paths. Renames require
        // an explicit, audited 301-mapping migration rather than a form checkbox.
        if($old) $slug=(string)$old['slug'];
        $collision=$this->db->fetch('SELECT legacy_id FROM nv_legacy_script_products WHERE slug=? AND legacy_id<>? LIMIT 1',[$slug,$id]);
        if($collision){flash('error','Bu slug başka ürüne ait.');redirect('/admin/netvera-yazilimlar');return;}
        $newData=$old?NetveraBridgeService::jsonFields($old):[];
        $knownFields=[
          'demo_url','demo_admin_url','demo_user_url','demo_video_url',
          'demo_username','demo_password','demo_admin_username','demo_admin_password',
          'demo_note','current_version','last_updated_on',
          'install_type','install_info','update_duration_type','update_duration_months',
          'support_duration_type','support_duration_months',
          'tags','focus_keyword','secondary_keywords','showcase_button_label',
          'og_title','og_description','og_image','buy_url',
        ];
        foreach($knownFields as $key){
            $value=trim((string)($_POST[$key]??($newData[$key]??'')));
            $newData[$key]=mb_substr($value,0,in_array($key,['install_info','tags','secondary_keywords','demo_note'],true)?2000:500);
        }
        foreach(['demo_url','demo_admin_url','demo_user_url','demo_video_url'] as $urlField){
            $value=trim((string)($newData[$urlField]??''));
            if($value!=='' && !\App\Services\NetveraBridgeService::publicUrl($value)){
                flash('error',$urlField.' alanında geçerli HTTPS veya HTTP bağlantısı kullanın.');
                redirect($id?'/admin/netvera-yazilimlar/'.$id.'/duzenle':'/admin/netvera-yazilimlar/ekle');
                return;
            }
        }
        $demoAccountsRaw=(string)($_POST['demo_accounts_json']??($newData['demo_accounts_json']??'[]'));
        $demoAccounts=json_decode($demoAccountsRaw,true);
        if(!is_array($demoAccounts) || !array_is_list($demoAccounts) || count($demoAccounts)>6){
            flash('error','Ek demo hesapları en fazla 6 elemanlı bir JSON dizisi olmalıdır.');
            redirect($id?'/admin/netvera-yazilimlar/'.$id.'/duzenle':'/admin/netvera-yazilimlar/ekle');
            return;
        }
        $safeDemoAccounts=[];
        foreach($demoAccounts as $account){
            if(!is_array($account))continue;
            $username=mb_substr(trim((string)($account['username']??'')),0,190);
            $password=mb_substr((string)($account['password']??''),0,190);
            $label=mb_substr(trim((string)($account['label']??'Demo Hesabı')),0,80);
            $demoAccountUrl=trim((string)($account['url']??''));
            if($demoAccountUrl!==''&&!\App\Services\NetveraBridgeService::publicUrl($demoAccountUrl)){
                flash('error','Ek demo hesaplarının bağlantıları geçerli URL olmalıdır.');
                redirect($id?'/admin/netvera-yazilimlar/'.$id.'/duzenle':'/admin/netvera-yazilimlar/ekle');
                return;
            }
            if($username==='')continue;
            $safeDemoAccounts[]=['label'=>$label,'username'=>$username,'password'=>$password,'url'=>$demoAccountUrl];
        }
        $newData['demo_accounts_json']=json_encode($safeDemoAccounts,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_THROW_ON_ERROR);
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
        // Publishing demo credentials is an explicit decision, restricted to
        // accounts made only for public software demonstrations.
        $newData['demo_credentials_public']=isset($_POST['demo_credentials_public'])?1:0;
        $newData['is_featured']=isset($_POST['is_featured'])?1:0;
        $newData['is_popular']=isset($_POST['is_popular'])?1:0;
        $newData['is_new']=isset($_POST['is_new'])?1:0;
        // Payment checkout remains quarantined; do not save merchant/secret fields.
        $price=max(0,round((float)($_POST['price']??0),2));
        $oldPrice=($_POST['old_price']??'')===''?null:max(0,round((float)$_POST['old_price'],2));
        $image=$old['cover_image']??null;
        if(!empty($_FILES['image']['name'])){
            try{$uploaded=Upload::image($_FILES['image'],'scripts');}
            catch(\Throwable $e){$uploaded=null;}
            // A failed upload must never erase the existing indexed cover image.
            if(!$uploaded){
                flash('error','Kapak görseli yüklenemedi; mevcut görsel korunuyor.');
                redirect($id?'/admin/netvera-yazilimlar/'.$id.'/duzenle':'/admin/netvera-yazilimlar/ekle');
                return;
            }
            $image=$uploaded;
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
        flash('success','Yazılım bilgileri ve demo ayarları kaydedildi.');
        redirect('/admin/netvera-yazilimlar');
    }
    /** Isolated legacy categories. The original indexed slug is immutable on edit. */
    public function categories():void
    {
        $ready=NetveraBridgeService::ready();
        $categories=$ready?$this->db->fetchAll(
            'SELECT c.*, (SELECT COUNT(*) FROM nv_legacy_script_products p
             WHERE p.category_legacy_id=c.legacy_id) AS product_count
             FROM nv_legacy_script_categories c ORDER BY c.sort_order,c.legacy_id'
        ):[];
        $this->renderAdmin('admin/netvera-scripts/categories',[
            'pageTitle'=>'Netvera Yazılım Kategorileri',
            'ready'=>$ready,'categories'=>$categories
        ]);
    }

    /** One-click schema initialization is restricted to a disposable staging database. */
    public function installBridge():void
    {
        Csrf::check();
        $env=strtolower(trim((string)($_ENV['APP_ENV']??'production')));
        if(!in_array($env,['staging','testing','development','local'],true) ||
            (string)($_ENV['NETVERA_IMPORT_ALLOWED']??'')!=='1'){
            flash('error','Bu işlem bu ortamda kullanılamıyor.');
            redirect('/admin/netvera-kategoriler');return;
        }
        if(NetveraBridgeService::ready() && \App\Services\NetveraInquiryService::ready()){
            flash('success','Netvera katalog ve gelen kutusu tabloları zaten kurulu.');
            redirect('/admin/netvera-kategoriler');return;
        }
        try{
            foreach(['netvera-legacy-bridge-v1.sql','netvera-inquiries-v1.sql'] as $filename){
                $source=file_get_contents(BASE_PATH.'/database/migrations/'.$filename);
                if($source===false)throw new \RuntimeException('Migration source unavailable');
                foreach(explode(';',$source) as $statement){
                    $statement=trim(preg_replace('/^\s*--[^\n]*(?:\n|$)/m','',$statement));
                    if($statement==='')continue;
                    if(!preg_match('/^CREATE TABLE IF NOT EXISTS nv_(?:legacy|public)_/i',$statement))
                        throw new \RuntimeException('Non-allowlisted staging migration');
                    $this->db->getPdo()->exec($statement);
                }
            }
            flash('success','Katalog tabloları hazır.');
        }catch(\Throwable $e){
            error_log('Netvera bridge staging schema setup failed: '.$e->getMessage());
            flash('error','Katalog kurulumu tamamlanamadı. Sunucu kayıtlarını kontrol edin.');
        }
        redirect('/admin/netvera-kategoriler');
    }

    public function saveCategory():void
    {
        Csrf::check();
        if(!NetveraBridgeService::ready()){
            flash('error','Yazılım kataloğu yönetim tabloları şu anda kullanılamıyor.');
            redirect('/admin/netvera-kategoriler');return;
        }
        $id=max(0,(int)($_POST['id']??0));
        $original=$id?$this->db->fetch(
            'SELECT * FROM nv_legacy_script_categories WHERE legacy_id=?',[$id]
        ):null;
        if($id&&!$original){
            flash('error','Kategori bulunamadı.');
            redirect('/admin/netvera-kategoriler');return;
        }
        $name=mb_substr(trim((string)($_POST['name']??'')),0,240,'UTF-8');
        if($name===''){
            flash('error','Kategori adı zorunludur.');
            redirect('/admin/netvera-kategoriler');return;
        }
        $slug=$original?(string)$original['slug']:slugify(trim((string)($_POST['slug']??''))?:$name);
        if($slug===''){
            flash('error','Geçerli bir kategori adresi oluşturulamadı.');
            redirect('/admin/netvera-kategoriler');return;
        }
        $existingSlug=$this->db->fetch(
            'SELECT legacy_id FROM nv_legacy_script_categories WHERE slug=? AND legacy_id<>?',
            [$slug,$id]
        );
        if($existingSlug){
            flash('error','Bu kategori SEO adresi zaten kullanılıyor.');
            redirect('/admin/netvera-kategoriler');return;
        }
        $parentId=$original?($original['parent_legacy_id']??null):
            max(0,(int)($_POST['parent_legacy_id']??0));
        if(!$original && $parentId){
            $parent=$this->db->fetch(
                'SELECT legacy_id,parent_legacy_id FROM nv_legacy_script_categories WHERE legacy_id=?',
                [$parentId]
            );
            // Maximum two-level category tree, matching the original URL contract.
            if(!$parent || !empty($parent['parent_legacy_id'])){
                flash('error','Geçerli bir üst kategori seçin.');
                redirect('/admin/netvera-kategoriler');return;
            }
        }
        $record=[
            'parent_legacy_id'=>$parentId?:null,'name'=>$name,'slug'=>$slug,
            'description'=>trim((string)($_POST['description']??'')),
            'meta_title'=>mb_substr(trim((string)($_POST['meta_title']??'')),0,255,'UTF-8'),
            'meta_description'=>trim((string)($_POST['meta_description']??'')),
            'sort_order'=>(int)($_POST['sort_order']??0),
            'active'=>isset($_POST['active'])?1:0
        ];
        try{
            if($original){
                $this->db->update('nv_legacy_script_categories',$record,'legacy_id=?',[$id]);
            }else{
                // The legacy category key intentionally is not AUTO_INCREMENT;
                // preserve all imported IDs and allocate a new non-overlapping ID.
                $last=$this->db->fetch('SELECT COALESCE(MAX(legacy_id),0) AS max_id FROM nv_legacy_script_categories');
                $record['legacy_id']=(int)$last['max_id']+1;
                $this->db->insert('nv_legacy_script_categories',$record);
            }
            logActivity('netvera_category_save','Netvera kategori kaydı: '.$name);
            flash('success','Kategori kaydedildi; eski kategori slug ve ürün adresleri korunuyor.');
        }catch(\Throwable $e){
            error_log('Netvera category save: '.$e->getMessage());
            flash('error','Kategori kaydedilemedi. Sunucu kayıtlarını kontrol edin.');
        }
        redirect('/admin/netvera-kategoriler');
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
        catch(\Throwable $e){$image=null;}
        if(!$image){
            flash('error','Galeri görseli yüklenemedi. Orijinal galeri korunuyor.');
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
