<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Database;
use App\Core\Upload;
use App\Services\MarketingPopupService;
use App\Services\IconService;

class MarketingPopupController extends Controller
{
    public function index(): void
    {
        $this->renderAdmin('admin/popups/index',[
            'pageTitle'=>'Popup & Kampanya Yönetimi',
            'popups'=>MarketingPopupService::all(false),
        ]);
    }

    public function create(): void
    {
        MarketingPopupService::ensureSchema();
        $this->renderAdmin('admin/popups/form',[
            'pageTitle'=>'Yeni Popup Oluştur',
            'popup'=>null,
            'icons'=>IconService::list(),
        ]);
    }

    public function edit(string $id): void
    {
        $popup=MarketingPopupService::find((int)$id);
        if(!$popup){ flash('error','Popup bulunamadı.'); redirect('/admin/popuplar'); }
        $this->renderAdmin('admin/popups/form',[
            'pageTitle'=>'Popup Düzenle',
            'popup'=>$popup,
            'icons'=>IconService::list(),
        ]);
    }

    public function store(): void
    {
        Csrf::check();
        try{
            $id=Database::getInstance()->insert('marketing_popups',$this->fields());
            flash('success','Popup oluşturuldu.');
            redirect('/admin/popuplar/'.$id.'/duzenle');
        }catch(\Throwable $e){
            flash('error',$e instanceof \InvalidArgumentException?$e->getMessage():'Popup oluşturulamadı.');
            redirect('/admin/popuplar/yeni');
        }
    }

    public function update(string $id): void
    {
        Csrf::check();
        $existing=MarketingPopupService::find((int)$id);
        if(!$existing){ flash('error','Popup bulunamadı.'); redirect('/admin/popuplar'); }
        try{
            Database::getInstance()->update('marketing_popups',$this->fields($existing),'id=?',[(int)$id]);
            flash('success','Popup güncellendi.');
        }catch(\Throwable $e){
            flash('error',$e instanceof \InvalidArgumentException?$e->getMessage():'Popup güncellenemedi.');
        }
        redirect('/admin/popuplar/'.(int)$id.'/duzenle');
    }

    public function toggle(string $id): void
    {
        Csrf::check();
        $popup=MarketingPopupService::find((int)$id);
        if($popup){
            $next=$popup['status']==='active'?'inactive':'active';
            Database::getInstance()->update('marketing_popups',['status'=>$next],'id=?',[(int)$id]);
            flash('success',$next==='active'?'Popup yayına alındı.':'Popup pasife alındı.');
        }
        redirect('/admin/popuplar');
    }

    public function duplicate(string $id): void
    {
        Csrf::check();
        $popup=MarketingPopupService::find((int)$id);
        if(!$popup){ flash('error','Popup bulunamadı.'); redirect('/admin/popuplar'); }
        unset($popup['id'],$popup['created_at'],$popup['updated_at']);
        $popup['internal_name']=mb_substr((string)$popup['internal_name'].' - Kopya',0,140);
        $popup['status']='inactive';
        $popup['sort_order']=(int)$popup['sort_order']+5;
        $popup['coupon_items']=json_encode($popup['coupon_items']??[],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        Database::getInstance()->insert('marketing_popups',$popup);
        flash('success','Popup kopyalandı ve pasif olarak kaydedildi.');
        redirect('/admin/popuplar');
    }

    public function delete(string $id): void
    {
        Csrf::check();
        $popup=MarketingPopupService::find((int)$id);
        if($popup){
            if(!empty($popup['image'])&&str_starts_with((string)$popup['image'],'/uploads/marketing-popup/')) Upload::delete((string)$popup['image']);
            Database::getInstance()->delete('marketing_popups','id=?',[(int)$id]);
            flash('success','Popup silindi.');
        }
        redirect('/admin/popuplar');
    }

    private function fields(?array $existing=null): array
    {
        MarketingPopupService::ensureSchema();
        $internal=mb_substr(trim((string)($_POST['internal_name']??'')),0,140);
        if($internal==='') throw new \InvalidArgumentException('Yönetim adı zorunludur.');

        $type=in_array($_POST['type']??'',['campaign','image','coupons'],true)?$_POST['type']:'campaign';
        $orientation=in_array($_POST['orientation']??'',['horizontal','vertical'],true)?$_POST['orientation']:'horizontal';
        $pageMode=in_array($_POST['page_mode']??'',['all','home','include','exclude'],true)?$_POST['page_mode']:'all';

        $row=[
            'internal_name'=>$internal,
            'type'=>$type,
            'orientation'=>$orientation,
            'eyebrow'=>mb_substr(trim((string)($_POST['eyebrow']??'')),0,120),
            'title'=>mb_substr(trim((string)($_POST['title']??'')),0,220),
            'description'=>mb_substr(trim((string)($_POST['description']??'')),0,1200),
            'image'=>$existing['image']??'',
            'image_alt'=>mb_substr(trim((string)($_POST['image_alt']??'')),0,255),
            'primary_button_text'=>mb_substr(trim((string)($_POST['primary_button_text']??'')),0,100),
            'primary_button_url'=>$this->url((string)($_POST['primary_button_url']??'')),
            'primary_button_icon'=>$this->icon((string)($_POST['primary_button_icon']??'arrow-right'),'arrow-right'),
            'primary_button_bg'=>$this->color((string)($_POST['primary_button_bg']??'#ffffff'),'#ffffff'),
            'primary_button_text_color'=>$this->color((string)($_POST['primary_button_text_color']??'#17264e'),'#17264e'),
            'secondary_button_text'=>mb_substr(trim((string)($_POST['secondary_button_text']??'')),0,100),
            'secondary_button_url'=>$this->url((string)($_POST['secondary_button_url']??'')),
            'secondary_button_icon'=>$this->icon((string)($_POST['secondary_button_icon']??'grid'),'grid'),
            'secondary_button_bg'=>$this->color((string)($_POST['secondary_button_bg']??'#ffffff'),'#ffffff'),
            'secondary_button_text_color'=>$this->color((string)($_POST['secondary_button_text_color']??'#ffffff'),'#ffffff'),
            'background_start'=>$this->color((string)($_POST['background_start']??'#151a45'),'#151a45'),
            'background_end'=>$this->color((string)($_POST['background_end']??'#6b4de8'),'#6b4de8'),
            'text_color'=>$this->color((string)($_POST['text_color']??'#ffffff'),'#ffffff'),
            'accent_color'=>$this->color((string)($_POST['accent_color']??'#d936a1'),'#d936a1'),
            'coupon_items'=>json_encode($this->couponItems(),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
            'open_delay_ms'=>max(0,min(600000,(int)round(((float)($_POST['open_delay_seconds']??1.8))*1000))),
            'reopen_hours'=>max(0,min(8760,(int)($_POST['reopen_hours']??24))),
            'auto_close_seconds'=>max(0,min(3600,(int)($_POST['auto_close_seconds']??0))),
            'closable'=>!empty($_POST['closable'])?1:0,
            'close_on_backdrop'=>!empty($_POST['close_on_backdrop'])?1:0,
            'once_per_session'=>!empty($_POST['once_per_session'])?1:0,
            'page_mode'=>$pageMode,
            'path_rules'=>mb_substr(trim((string)($_POST['path_rules']??'')),0,3000),
            'sort_order'=>max(0,min(9999,(int)($_POST['sort_order']??100))),
            'status'=>($_POST['status']??'')==='active'?'active':'inactive',
            'starts_at'=>$this->dateTime((string)($_POST['starts_at']??'')),
            'ends_at'=>$this->dateTime((string)($_POST['ends_at']??'')),
        ];

        if(!empty($_POST['remove_image'])) $row['image']='';
        if(!empty($_FILES['image']['name'])){
            $upload=Upload::image($_FILES['image'],'marketing-popup');
            if(!$upload) throw new \InvalidArgumentException('Popup görseli yüklenemedi. PNG, JPG veya WebP kullanın.');
            $row['image']=$upload;
        }
        if($type==='image' && $row['image']==='') throw new \InvalidArgumentException('Sadece görsel popup için görsel yüklemelisiniz.');
        return $row;
    }

    private function couponItems(): array
    {
        $titles=$_POST['coupon_title']??[];
        $codes=$_POST['coupon_code']??[];
        $discounts=$_POST['coupon_discount']??[];
        $descriptions=$_POST['coupon_description']??[];
        $rows=[];
        foreach($titles as $i=>$title){
            $title=mb_substr(trim((string)$title),0,100);
            $code=mb_substr(trim((string)($codes[$i]??'')),0,80);
            $discount=mb_substr(trim((string)($discounts[$i]??'')),0,80);
            $description=mb_substr(trim((string)($descriptions[$i]??'')),0,180);
            if($title===''&&$code===''&&$discount==='') continue;
            $rows[]=['title'=>$title,'code'=>$code,'discount'=>$discount,'description'=>$description];
        }
        return $rows;
    }

    private function icon(string $value,string $fallback): string
    {
        $value=trim($value);
        return IconService::has($value)?$value:$fallback;
    }

    private function url(string $value): string
    {
        $value=trim($value);
        if($value==='') return '';
        if(str_starts_with($value,'/')) return mb_substr($value,0,255);
        if(preg_match('~^https?://~i',$value)&&filter_var($value,FILTER_VALIDATE_URL)) return mb_substr($value,0,255);
        throw new \InvalidArgumentException('Buton linkleri / ile başlayan site yolu veya http/https adresi olmalıdır.');
    }

    private function color(string $value,string $fallback): string
    {
        $value=trim($value);
        return preg_match('/^#[0-9a-f]{6}$/i',$value)?$value:$fallback;
    }

    private function dateTime(string $value): ?string
    {
        $value=trim($value);
        if($value==='') return null;
        $time=strtotime($value);
        return $time?date('Y-m-d H:i:s',$time):null;
    }
}
