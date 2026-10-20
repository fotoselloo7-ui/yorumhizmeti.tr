<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Auth;
use App\Core\AdminAuth;
use App\Core\Csrf;
use App\Services\MemberPanelService as Panel;
use App\Services\MemberSupportService as Support;

class SupportController extends Controller
{
    public function index():void
    {
        $q=Panel::text($_GET['q']??'',80);$status=Panel::text($_GET['status']??'');
        if(!in_array($status,['open','answered','customer_reply','closed'],true))$status='';
        $where=['user_id=?'];$args=[Auth::id()];
        if($q!==''){$where[]='(subject LIKE ? OR ticket_number LIKE ?)';$args[]='%'.$q.'%';$args[]='%'.$q.'%';}
        if($status!==''){$where[]=$status==='answered'?"status IN ('answered','admin_reply')":'status=?';if($status!=='answered')$args[]=$status;}
        $sql=implode(' AND ',$where);$total=(int)$this->db->fetch('SELECT COUNT(*) n FROM support_tickets WHERE '.$sql,$args)['n'];
        $pages=max(1,(int)ceil($total/10));$raw=$_GET['page']??1;$page=min($pages,max(1,is_scalar($raw)?(int)$raw:1));$offset=($page-1)*10;
        $tickets=$this->db->fetchAll('SELECT id,ticket_number,subject,order_id,priority,status,updated_at FROM support_tickets WHERE '.$sql." ORDER BY updated_at DESC,id DESC LIMIT 10 OFFSET $offset",$args);
        $this->render('frontend/support/index',['pageTitle'=>'Destek Taleplerim','tickets'=>$tickets,'q'=>$q,'status'=>$status,'total'=>$total,'page'=>$page,'totalPages'=>$pages]);
    }
    public function create():void
    {
        $orders=$this->db->fetchAll('SELECT id,order_number FROM orders WHERE user_id=? ORDER BY created_at DESC LIMIT 200',[Auth::id()]);
        $selected=is_scalar($_GET['order_id']??null)?(int)$_GET['order_id']:0;
        if(!$this->db->fetch('SELECT id FROM orders WHERE id=? AND user_id=?',[$selected,Auth::id()]))$selected=0;
        $old=$_SESSION['member_support_old']??[];unset($_SESSION['member_support_old']);
        if(!empty($old['order_id'])&&!$this->db->fetch('SELECT id FROM orders WHERE id=? AND user_id=?',[(int)$old['order_id'],Auth::id()]))unset($old['order_id']);
        if($selected>0)$old['order_id']=$selected;
        $this->render('frontend/support/create',['pageTitle'=>'Yeni Destek Talebi','orders'=>$orders,'selectedOrder'=>$selected,'supportOld'=>$old]);
    }
    public function store():void
    {
        Csrf::check();$attachment=null;
        try{
            $data=Support::input($_POST,(int)Auth::id());
            if((int)$this->db->fetch("SELECT COUNT(*) n FROM support_tickets WHERE user_id=? AND created_at>DATE_SUB(NOW(),INTERVAL 15 MINUTE)",[Auth::id()])['n']>=10)throw new \InvalidArgumentException('Kısa sürede çok sayıda talep açtınız. Lütfen mevcut talebinizden devam edin.');
            $attachment=Support::upload($_FILES['attachment']??[]);
            $id=Support::create((int)Auth::id(),$data,$attachment);
        }catch(\InvalidArgumentException $e){
            Support::discard($attachment);$_SESSION['member_support_old']=['subject'=>Panel::text($_POST['subject']??'',300),'message'=>Panel::text($_POST['message']??'',10000),'order_id'=>is_scalar($_POST['order_id']??null)?(int)$_POST['order_id']:0];
            flash('error',$e->getMessage());redirect('/destek/yeni');return;
        }catch(\Throwable $e){Support::discard($attachment);error_log('Member support create failed: '.get_class($e));flash('error','Talep kaydedilemedi. Lütfen tekrar deneyin.');redirect('/destek/yeni');return;}
        $ticket=$this->db->fetch('SELECT ticket_number FROM support_tickets WHERE id=?',[$id]);
        \App\Services\MailService::sendTemplate('support_ticket_created',Auth::user()['email'],['user_name'=>Auth::user()['name'],'ticket_number'=>$ticket['ticket_number'],'subject'=>$data['subject']]);
        flash('success','Destek talebiniz oluşturuldu.');redirect('/destek/'.$id);
    }
    public function show(string $id):void
    {
        $ticket=$this->db->fetch('SELECT * FROM support_tickets WHERE id=? AND user_id=?',[(int)$id,Auth::id()]);
        if(!$ticket){http_response_code(404);$this->render('frontend/404',['pageTitle'=>'Talep Bulunamadı']);return;}
        $messages=$this->db->fetchAll('SELECT id,sender_type,message,attachment,created_at FROM support_messages WHERE ticket_id=? ORDER BY created_at,id',[$ticket['id']]);
        $this->render('frontend/support/detail',['pageTitle'=>'Destek #'.$ticket['ticket_number'],'ticket'=>$ticket,'messages'=>$messages,'user'=>Auth::user()]);
    }
    public function reply(string $id):void
    {
        Csrf::check();$attachment=null;
        try{
            $ticket=$this->db->fetch('SELECT id,status FROM support_tickets WHERE id=? AND user_id=?',[(int)$id,Auth::id()]);
            if(!$ticket||$ticket['status']==='closed')throw new \InvalidArgumentException('Talep kapalı veya erişilemiyor.');
            $message=Support::message($_POST['message']??null);
            $attachment=Support::upload($_FILES['attachment']??[]);
            Support::reply((int)$id,(int)Auth::id(),$message,$attachment);
            flash('success','Yanıtınız gönderildi.');
        }catch(\InvalidArgumentException $e){Support::discard($attachment);flash('error',$e->getMessage());}
        catch(\Throwable $e){Support::discard($attachment);error_log('Member support reply failed: '.get_class($e));flash('error','Yanıt kaydedilemedi. Lütfen tekrar deneyin.');}
        redirect('/destek/'.(int)$id);
    }
    public function close(string $id):void
    {
        Csrf::check();Support::close((int)$id,(int)Auth::id());
        flash('success','Talep kapatıldı. Yeni bir konu için yeni talep açabilirsiniz.');redirect('/destek/'.(int)$id);
    }
    public function attachment(string $id):void
    {
        $admin=AdminAuth::admin()!==null;
        $row=$this->db->fetch('SELECT m.attachment FROM support_messages m JOIN support_tickets t ON t.id=m.ticket_id WHERE m.id=?'.($admin?'':' AND t.user_id=?'),$admin?[(int)$id]:[(int)$id,Auth::id()]);
        $file=$row?Support::file((string)$row['attachment']):null;
        header('X-Robots-Tag: noindex, nofollow');header('Cache-Control: private, no-store');header('X-Content-Type-Options: nosniff');
        if(!$file){http_response_code(404);echo 'Dosya bulunamadı.';return;}
        header('Content-Type: application/octet-stream');header('Content-Disposition: attachment; filename="'.basename($file).'"');header('Content-Length: '.filesize($file));readfile($file);
    }
}
