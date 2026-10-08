<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Services\NetveraInquiryService as Inbox;
use App\Services\NetveraBridgeService as Catalog;

final class NetveraInquiryController extends Controller
{
    private function respondJson(int $code,array $data):void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=UTF-8');
        header('Cache-Control: no-store, private');
        header('X-Content-Type-Options: nosniff');
        echo json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|
            JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT);
    }

    public function send():void
    {
        Csrf::check();
        $isAjax=($_POST['transport']??'')==='json';
        if(!Inbox::ready()){
            if($isAjax){$this->respondJson(503,['ok'=>false,'error'=>'Destek sistemi hazırlanıyor.']);return;}
            flash('error','Destek sistemi hazırlanıyor.');redirect('/iletisim');return;
        }
        if(!empty($_POST['website'])){ // Honeypot: no persistent data
            if($isAjax){$this->respondJson(200,['ok'=>true,'message'=>'Talebiniz alındı.']);return;}
            redirect('/iletisim');return;
        }
        $message=mb_substr(trim((string)($_POST['message']??'')),0,3000,'UTF-8');
        $name=mb_substr(trim((string)($_POST['name']??'')),0,140,'UTF-8');
        $contact=mb_substr(trim((string)($_POST['contact']??'')),0,190,'UTF-8');
        $type=($_POST['source_type']??'chat')==='offer'?'offer':'chat';
        $slug=mb_substr(trim((string)($_POST['product_slug']??'')),0,250,'UTF-8');
        $id=max(0,(int)($_POST['inquiry_id']??0));
        $now=time();
        $log=is_array($_SESSION['nv_inbox_times']??null)?$_SESSION['nv_inbox_times']:[];
        $log=array_values(array_filter($log,static fn($stamp)=>(int)$stamp>$now-3600));
        $error='';
        if(count($log)>=12)$error='Çok fazla istek gönderildi. Daha sonra tekrar deneyin.';
        elseif(mb_strlen($message,'UTF-8')<5)$error='Mesaj en az 5 karakter olmalıdır.';
        elseif($id===0 && (mb_strlen($name,'UTF-8')<2 || mb_strlen($contact,'UTF-8')<5))
            $error='Adınızı ve e-posta veya telefonunuzu yazın.';
        elseif($id!==0 && !Inbox::visibleToVisitor($id))
            $error='Bu sohbet oturumuna erişilemiyor.';
        elseif($id!==0 && $type==='offer')
            $error='Teklif talebine yeni sohbet yanıtı eklenemez.';
        if($id===0 && $type==='offer' && ($slug===''||!Catalog::find($slug)))
            $error='Geçerli bir yazılım seçin.';
        if($error!==''){
            if($isAjax){$this->respondJson(422,['ok'=>false,'error'=>$error]);return;}
            flash('error',$error);redirect('/iletisim');return;
        }
        try{
            if($id){
                Inbox::reply($id,'visitor',$message);
            }else{
                $id=Inbox::create($type,$name,$contact,$message,$slug?:null);
                if($type==='chat')$_SESSION['nv_active_chat_id']=$id;
            }
            $log[]=$now;$_SESSION['nv_inbox_times']=$log;
            if($isAjax){
                $this->respondJson(200,['ok'=>true,'inquiry_id'=>$id,'message'=>'Mesajınız kaydedildi.']);
                return;
            }
            flash('success',$type==='offer'?'Yazılım teklif talebiniz alındı.':'Mesajınız alındı.');
            redirect($slug?'/hazir-scriptler/'.rawurlencode($slug):'/iletisim');
        }catch(\Throwable $e){
            error_log('Netvera inquiry send failed: '.get_class($e));
            if($isAjax){$this->respondJson(500,['ok'=>false,'error'=>'Mesaj şu an kaydedilemedi.']);return;}
            flash('error','Talep kaydedilemedi.');redirect('/iletisim');
        }
    }

    public function messages():void
    {
        $id=max(0,(int)($_GET['id']??0));
        $inquiry=Inbox::visibleToVisitor($id);
        if(!$inquiry){$this->respondJson(404,['ok'=>false,'error'=>'Sohbet bulunamadı.']);return;}
        $this->respondJson(200,[
            'ok'=>true,'id'=>$id,'status'=>$inquiry['status'],
            'messages'=>Inbox::replies($id)
        ]);
    }
}
