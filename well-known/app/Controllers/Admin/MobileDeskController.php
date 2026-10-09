<?php
namespace App\Controllers\Admin;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\AdminAuth;
use App\Services\NetveraInquiryService as Inbox;
use App\Services\SupportDeskSettings;

/**
 * Session-authenticated, same-origin mobile support desk.
 * QR code only contains the app URL, never a login token.
 */
final class MobileDeskController extends Controller
{
    private function protect(): void {
        if(!AdminAuth::admin()){http_response_code(401);header('Cache-Control: no-store');exit('Yönetici girişi gerekli.');}
        header('Cache-Control: no-store, private, max-age=0');
        header('X-Robots-Tag: noindex, nofollow');
    }
    private function send(array $response,int $code=200): void {
        $this->protect();
        $this->json($response,$code);
    }
    public function index():void {
        $this->protect();
        $this->render('admin/mobile/index',[
            'pageTitle'=>'NetVera Cep Paneli',
            'agent'=>SupportDeskSettings::publicProfile(),
            'sound'=>SupportDeskSettings::sounds(),
            'csrf'=>Csrf::token()
        ],'mobile');
    }
    public function feed():void {
        $this->protect();
        $db=$this->db;
        $inbox=Inbox::ready()?$db->fetchAll(
            "SELECT i.id,i.source_type,i.visitor_name AS name,i.status,i.updated_at,
             (SELECT message FROM nv_public_inquiry_replies r WHERE r.inquiry_id=i.id ORDER BY id DESC LIMIT 1) AS preview
             FROM nv_public_inquiries i ORDER BY updated_at DESC LIMIT 70"
        ):[];
        $tickets=$db->fetchAll(
            "SELECT t.id,t.ticket_number,t.subject,t.status,t.updated_at,u.name,
                (SELECT message FROM support_messages m WHERE m.ticket_id=t.id ORDER BY id DESC LIMIT 1) AS preview
             FROM support_tickets t LEFT JOIN users u ON u.id=t.user_id ORDER BY t.updated_at DESC LIMIT 70"
        );
        $orders=$db->fetchAll(
            "SELECT o.id,o.order_number,o.order_status,o.payment_status,o.total_amount,o.created_at,u.name
             FROM orders o LEFT JOIN users u ON u.id=o.user_id ORDER BY o.id DESC LIMIT 70"
        );
        $latestInbox=Inbox::ready()?$db->fetch(
            "SELECT COALESCE(MAX(id),0) AS id FROM nv_public_inquiry_replies WHERE sender='visitor'"):['id'=>0];
        $latestTicket=$db->fetch(
            "SELECT COALESCE(MAX(id),0) AS id FROM support_messages WHERE sender_type='user'");
        $this->send([
            'ok'=>true,'inbox'=>$inbox,'tickets'=>$tickets,'orders'=>$orders,
            'cursor'=>['chat'=>(int)$latestInbox['id'],'ticket'=>(int)($latestTicket['id']??0)],
            'agent'=>SupportDeskSettings::publicProfile(),
            'sounds'=>SupportDeskSettings::sounds()
        ]);
    }
    public function detail(string $type,string $id):void {
        $this->protect();
        $num=ctype_digit($id)?(int)$id:0;
        if($num<1 || !in_array($type,['chat','ticket','order'],true)){$this->send(['error'=>'Kayıt bulunamadı'],404);return;}
        if($type==='chat'){
            if(!Inbox::ready()){$this->send(['error'=>'Gelen kutusu kapalı'],404);return;}
            $row=$this->db->fetch("SELECT id,visitor_name,visitor_contact,source_type,status,created_at FROM nv_public_inquiries WHERE id=?",[$num]);
            $messages=$this->db->fetchAll("SELECT sender,message,created_at FROM nv_public_inquiry_replies WHERE inquiry_id=? ORDER BY id ASC LIMIT 200",[$num]);
        }elseif($type==='ticket'){
            $row=$this->db->fetch("SELECT t.id,t.subject,t.ticket_number,t.status,t.created_at,u.name AS visitor_name
                FROM support_tickets t LEFT JOIN users u ON u.id=t.user_id WHERE t.id=?",[$num]);
            $messages=$this->db->fetchAll("SELECT sender_type AS sender,message,created_at FROM support_messages WHERE ticket_id=? ORDER BY id ASC LIMIT 200",[$num]);
        }else{
            $row=$this->db->fetch("SELECT o.id,o.order_number,o.order_status,o.payment_status,o.total_amount,o.created_at,u.name AS visitor_name
                 FROM orders o LEFT JOIN users u ON u.id=o.user_id WHERE o.id=?",[$num]);
            $messages=$row?$this->db->fetchAll("SELECT package_name,quantity,unit_price,total_price FROM order_items WHERE order_id=? ORDER BY id",[$num]):[];
        }
        if(!$row){$this->send(['error'=>'Kayıt bulunamadı'],404);return;}
        $this->send(['ok'=>true,'item'=>$row,'messages'=>$messages]);
    }
    public function reply(string $type,string $id):void {
        $this->protect();Csrf::check();
        if(!in_array($type,['chat','ticket'],true)||!ctype_digit($id)|| (int)$id<1){
            $this->send(['error'=>'Geçersiz talep'],422);return;
        }
        $message=trim((string)($_POST['message']??''));
        if(mb_strlen($message)<2||mb_strlen($message)>3000){$this->send(['error'=>'Yanıt 2–3000 karakter olmalı'],422);return;}
        $now=microtime(true);
        if(($now-($_SESSION['nv_desk_last_reply']??0))<1.5){$this->send(['error'=>'Lütfen bir an bekleyin'],429);return;}
        $_SESSION['nv_desk_last_reply']=$now;
        $num=(int)$id;
        if($type==='chat'){
            $row=Inbox::ready()?$this->db->fetch('SELECT id FROM nv_public_inquiries WHERE id=?',[$num]):null;
            if(!$row){$this->send(['error'=>'Sohbet bulunamadı'],404);return;}
            Inbox::reply($num,'admin',$message);
        }else{
            $ticket=$this->db->fetch('SELECT id FROM support_tickets WHERE id=?',[$num]);
            if(!$ticket){$this->send(['error'=>'Talep bulunamadı'],404);return;}
            $this->db->insert('support_messages',[
              'ticket_id'=>$num,'sender_type'=>'admin',
              'sender_id'=>AdminAuth::id(),'message'=>$message
            ]);
            $this->db->update('support_tickets',['status'=>'admin_reply'],'id=?',[$num]);
        }
        logActivity('mobile_support_reply',$type.' #'.$num);
        $this->send(['ok'=>true]);
    }
}
