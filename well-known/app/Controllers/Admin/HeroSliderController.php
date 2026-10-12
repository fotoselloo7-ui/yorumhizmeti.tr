<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Database;
use App\Core\Upload;
use App\Services\HeroSliderService;
use App\Services\IconService;

class HeroSliderController extends Controller
{
    public function index(): void
    {
        $this->renderAdmin('admin/hero-slider/index', [
            'pageTitle'=>'Ana Sayfa Slider',
            'slides'=>HeroSliderService::all(false),
        ]);
    }

    public function create(): void
    {
        HeroSliderService::ensureSchema();
        $this->renderAdmin('admin/hero-slider/form', [
            'pageTitle'=>'Yeni Slider Ekle',
            'slide'=>null,
            'icons'=>IconService::list(),
        ]);
    }

    public function edit(string $id): void
    {
        $slide=HeroSliderService::find((int)$id);
        if(!$slide){
            flash('error','Slider bulunamadı.');
            redirect('/admin/sliderlar');
        }
        $this->renderAdmin('admin/hero-slider/form', [
            'pageTitle'=>'Slider Düzenle',
            'slide'=>$slide,
            'icons'=>IconService::list(),
        ]);
    }

    public function store(): void
    {
        Csrf::check();
        try{
            $data=$this->fields();
            Database::getInstance()->insert('hero_slides',$data);
            flash('success','Slider eklendi.');
        }catch(\Throwable $e){
            flash('error',$e instanceof \InvalidArgumentException?$e->getMessage():'Slider eklenemedi.');
        }
        redirect('/admin/sliderlar');
    }

    public function update(string $id): void
    {
        Csrf::check();
        $existing=HeroSliderService::find((int)$id);
        if(!$existing){
            flash('error','Slider bulunamadı.');
            redirect('/admin/sliderlar');
        }
        try{
            $data=$this->fields($existing);
            Database::getInstance()->update('hero_slides',$data,'id=?',[(int)$id]);
            flash('success','Slider güncellendi.');
        }catch(\Throwable $e){
            flash('error',$e instanceof \InvalidArgumentException?$e->getMessage():'Slider güncellenemedi.');
        }
        redirect('/admin/sliderlar/'.(int)$id.'/duzenle');
    }

    public function toggle(string $id): void
    {
        Csrf::check();
        $slide=HeroSliderService::find((int)$id);
        if(!$slide){
            flash('error','Slider bulunamadı.');
        }else{
            $next=$slide['status']==='active'?'inactive':'active';
            Database::getInstance()->update('hero_slides',['status'=>$next],'id=?',[(int)$id]);
            flash('success',$next==='active'?'Slider aktifleştirildi.':'Slider pasifleştirildi.');
        }
        redirect('/admin/sliderlar');
    }

    public function duplicate(string $id): void
    {
        Csrf::check();
        $slide=HeroSliderService::find((int)$id);
        if(!$slide){
            flash('error','Slider bulunamadı.');
            redirect('/admin/sliderlar');
        }
        unset($slide['id'],$slide['created_at'],$slide['updated_at']);
        $slide['title']=mb_substr((string)$slide['title'].' - Kopya',0,220);
        $slide['sort_order']=(int)$slide['sort_order']+5;
        $slide['status']='inactive';
        $slide['quick_links']=json_encode($slide['quick_links']??[],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        Database::getInstance()->insert('hero_slides',$slide);
        flash('success','Slider kopyalandı; yayınlamadan önce düzenleyebilirsiniz.');
        redirect('/admin/sliderlar');
    }

    public function delete(string $id): void
    {
        Csrf::check();
        $slide=HeroSliderService::find((int)$id);
        if($slide){
            Database::getInstance()->delete('hero_slides','id=?',[(int)$id]);
            flash('success','Slider silindi.');
        }else{
            flash('error','Slider bulunamadı.');
        }
        redirect('/admin/sliderlar');
    }

    private function fields(?array $existing=null): array
    {
        HeroSliderService::ensureSchema();
        $title=mb_substr(trim((string)($_POST['title']??'')),0,220);
        if($title==='') throw new \InvalidArgumentException('Slider başlığı zorunludur.');

        $row=[
            'eyebrow'=>mb_substr(trim((string)($_POST['eyebrow']??'')),0,120),
            'title'=>$title,
            'description'=>mb_substr(trim((string)($_POST['description']??'')),0,700),
            'category_label'=>mb_substr(trim((string)($_POST['category_label']??'')),0,120),
            'category_url'=>$this->url((string)($_POST['category_url']??'')),
            'category_icon'=>$this->icon((string)($_POST['category_icon']??'sparkles'),'sparkles'),
            'primary_button_text'=>mb_substr(trim((string)($_POST['primary_button_text']??'')),0,100),
            'primary_button_url'=>$this->url((string)($_POST['primary_button_url']??'')),
            'primary_button_icon'=>$this->icon((string)($_POST['primary_button_icon']??'arrow-right'),'arrow-right'),
            'primary_button_bg'=>$this->color((string)($_POST['primary_button_bg']??'#ffffff'),'#ffffff'),
            'primary_button_text_color'=>$this->color((string)($_POST['primary_button_text_color']??'#14224a'),'#14224a'),
            'secondary_button_text'=>mb_substr(trim((string)($_POST['secondary_button_text']??'')),0,100),
            'secondary_button_url'=>$this->url((string)($_POST['secondary_button_url']??'')),
            'secondary_button_icon'=>$this->icon((string)($_POST['secondary_button_icon']??'play-circle'),'play-circle'),
            'secondary_button_bg'=>$this->color((string)($_POST['secondary_button_bg']??'rgba(255,255,255,.12)'),'rgba(255,255,255,.12)',true),
            'secondary_button_text_color'=>$this->color((string)($_POST['secondary_button_text_color']??'#ffffff'),'#ffffff'),
            'desktop_image'=>$existing['desktop_image']??'',
            'mobile_image'=>$existing['mobile_image']??'',
            'image_alt'=>mb_substr(trim((string)($_POST['image_alt']??'')),0,255),
            'background_start'=>$this->color((string)($_POST['background_start']??'#13255b'),'#13255b'),
            'background_end'=>$this->color((string)($_POST['background_end']??'#6d28d9'),'#6d28d9'),
            'overlay_color'=>$this->color((string)($_POST['overlay_color']??'rgba(7,16,48,.34)'),'rgba(7,16,48,.34)',true),
            'text_color'=>$this->color((string)($_POST['text_color']??'#ffffff'),'#ffffff'),
            'accent_color'=>$this->color((string)($_POST['accent_color']??'#7c5cff'),'#7c5cff'),
            'stat_text'=>mb_substr(trim((string)($_POST['stat_text']??'')),0,120),
            'quick_links'=>json_encode($this->quickLinks(),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
            'autoplay_delay'=>max(2500,min(30000,(int)($_POST['autoplay_delay']??6500))),
            'sort_order'=>max(0,min(9999,(int)($_POST['sort_order']??100))),
            'status'=>($_POST['status']??'')==='active'?'active':'inactive',
            'starts_at'=>$this->dateTime((string)($_POST['starts_at']??'')),
            'ends_at'=>$this->dateTime((string)($_POST['ends_at']??'')),
        ];

        foreach(['desktop_image'=>'slider/desktop','mobile_image'=>'slider/mobile'] as $field=>$folder){
            if(!empty($_POST['remove_'.$field])) $row[$field]='';
            if(!empty($_FILES[$field]['name'])){
                $this->validateSliderImage($_FILES[$field],$field);
                $upload=Upload::image($_FILES[$field],$folder);
                if(!$upload) throw new \InvalidArgumentException(($field==='desktop_image'?'Masaüstü':'Mobil').' görsel yüklenemedi. PNG, JPG veya WebP kullanın.');
                $row[$field]=$upload;
            }
        }
        return $row;
    }

    /**
     * Slider uploads are stored as the original file; they are never silently
     * downscaled or recompressed. This guard only blocks source files that are
     * too small to stay sharp at the site's rendered sizes.
     */
    private function validateSliderImage(array $file,string $field): void
    {
        if(($file['error']??UPLOAD_ERR_NO_FILE)!==UPLOAD_ERR_OK || empty($file['tmp_name'])) return;
        $size=@getimagesize((string)$file['tmp_name']);
        if(!$size || empty($size[0]) || empty($size[1]))
            throw new \InvalidArgumentException('Slider görselinin ölçüleri okunamadı.');

        $width=(int)$size[0];
        $height=(int)$size[1];

        if($field==='desktop_image'){
            if($width<1440 || $height<600){
                throw new \InvalidArgumentException(
                    'Masaüstü slider görseli en az 1440 × 600 px olmalıdır. Önerilen 1920 × 820 px. Yüklenen: '.$width.' × '.$height.' px.'
                );
            }
        }else{
            if($width<900 || $height<1125){
                throw new \InvalidArgumentException(
                    'Mobil slider görseli en az 900 × 1125 px olmalıdır. Önerilen 1080 × 1350 px. Yüklenen: '.$width.' × '.$height.' px.'
                );
            }
        }
    }

    private function quickLinks(): array
    {
        $labels=$_POST['quick_label']??[];
        $urls=$_POST['quick_url']??[];
        $icons=$_POST['quick_icon']??[];
        $colors=$_POST['quick_color']??[];
        $rows=[];
        foreach($labels as $i=>$label){
            $label=mb_substr(trim((string)$label),0,60);
            if($label==='') continue;
            $rows[]=[
                'label'=>$label,
                'url'=>$this->url((string)($urls[$i]??'')),
                'icon'=>$this->icon((string)($icons[$i]??'link'),'link'),
                'color'=>$this->color((string)($colors[$i]??'#6d5dfc'),'#6d5dfc'),
            ];
            if(count($rows)>=8) break;
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
        throw new \InvalidArgumentException('Bağlantılar / ile başlayan site yolu veya http/https adresi olmalıdır.');
    }

    private function color(string $value,string $fallback,bool $alpha=false): string
    {
        $value=trim($value);
        if(preg_match('/^#[0-9a-f]{6}$/i',$value)) return $value;
        if($alpha&&preg_match('/^rgba?\([0-9., %]+\)$/i',$value)) return mb_substr($value,0,32);
        return $fallback;
    }

    private function dateTime(string $value): ?string
    {
        $value=trim($value);
        if($value==='') return null;
        $time=strtotime($value);
        return $time?date('Y-m-d H:i:s',$time):null;
    }
}
