<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Csrf;
use App\Services\NetveraInquiryService as Inbox;

final class NetveraInboxController extends Controller
{
    public function index():void
    {
        $ready=Inbox::ready();
        $rows=$ready?$this->db->fetchAll(
            "SELECT id,source_type,product_slug,visitor_name,visitor_contact,
                    status,created_at,updated_at
             FROM nv_public_inquiries ORDER BY updated_at DESC,id DESC LIMIT 250"
        ):[];
        $this->renderAdmin('admin/netvera-inbox/index',[
            'pageTitle'=>'Netvera Canlı Destek ve Teklifler',
            'ready'=>$ready,'inquiries'=>$rows
        ]);
    }

    public function detail(string $id):void
    {
        if(!Inbox::ready()){redirect('/admin/netvera-gelen-kutusu');return;}
        $row=$this->db->fetch('SELECT * FROM nv_public_inquiries WHERE id=? LIMIT 1',[(int)$id]);
        if(!$row){flash('error','Talep bulunamadı.');redirect('/admin/netvera-gelen-kutusu');return;}
        $this->renderAdmin('admin/netvera-inbox/detail',[
            'pageTitle'=>'Netvera Talep #'.$row['id'],'inquiry'=>$row,
            'messages'=>Inbox::replies((int)$id)
        ]);
    }

    public function reply(string $id):void
    {
        Csrf::check();
        $row=Inbox::ready()?$this->db->fetch(
            'SELECT id FROM nv_public_inquiries WHERE id=?',[(int)$id]
        ):null;
        if(!$row){redirect('/admin/netvera-gelen-kutusu');return;}
        $message=mb_substr(trim((string)($_POST['message']??'')),0,3000,'UTF-8');
        if(mb_strlen($message,'UTF-8')<2){
            flash('error','Yanıt en az 2 karakter olmalıdır.');
            redirect('/admin/netvera-gelen-kutusu/'.$id);return;
        }
        Inbox::reply((int)$id,'admin',$message);
        logActivity('netvera_inbox_reply','Netvera talebine yanıt: #'.$id);
        flash('success','Yanıt kaydedildi; ziyaretçi açık sohbetinden görebilir.');
        redirect('/admin/netvera-gelen-kutusu/'.$id);
    }

    public function state(string $id):void
    {
        Csrf::check();
        if(!Inbox::ready()){redirect('/admin/netvera-gelen-kutusu');return;}
        $allowed=['new','open','replied','closed'];
        $status=(string)($_POST['status']??'');
        if(!in_array($status,$allowed,true)){
            flash('error','Geçersiz durum.');
            redirect('/admin/netvera-gelen-kutusu/'.$id);return;
        }
        $this->db->update('nv_public_inquiries',['status'=>$status],
            'id=?',[(int)$id]);
        logActivity('netvera_inbox_status','Netvera talebi #'.$id.' durumu değişti');
        flash('success','Talep durumu güncellendi.');
        redirect('/admin/netvera-gelen-kutusu/'.$id);
    }
}
