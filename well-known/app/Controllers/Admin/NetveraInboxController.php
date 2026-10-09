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
        $importanceReady=$ready && Inbox::importanceReady();
        $importanceColumn=$importanceReady?'is_important':'0 AS is_important';
        $rows=$ready?$this->db->fetchAll(
            "SELECT id,source_type,product_slug,visitor_name,visitor_contact,
                    status,created_at,updated_at,".$importanceColumn."
             FROM nv_public_inquiries ORDER BY updated_at DESC,id DESC LIMIT 250"
        ):[];
        $this->renderAdmin('admin/netvera-inbox/index',[
            'pageTitle'=>'Netvera Canlı Destek ve Teklifler',
            'ready'=>$ready,'inquiries'=>$rows,
            'importanceReady'=>$importanceReady,
            'telegramConfigured'=>Inbox::telegramConfigured()
        ]);
    }

    public function install(): void
    {
        Csrf::check();
        if ((\App\Core\AdminAuth::admin()['role']??'')!=='super_admin') {
            http_response_code(403);
            exit('Bu işlem için süper yönetici yetkisi gerekiyor.');
        }
        try {
            Inbox::install();
            logActivity('netvera_inbox_install','Canlı destek veritabanı etkinleştirildi.');
            flash('success','Gelen kutusu etkinleştirildi.');
        } catch (\Throwable $e) {
            error_log('Netvera inbox schema setup: '.get_class($e));
            flash('error','Gelen kutusu etkinleştirilemedi. Veritabanı yetkilerini kontrol edin.');
        }
        redirect('/admin/netvera-gelen-kutusu');
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
            'SELECT id,source_type FROM nv_public_inquiries WHERE id=?',[(int)$id]
        ):null;
        if(!$row){redirect('/admin/netvera-gelen-kutusu');return;}
        $message=mb_substr(trim((string)($_POST['message']??'')),0,3000,'UTF-8');
        if(mb_strlen($message,'UTF-8')<2){
            flash('error','Yanıt en az 2 karakter olmalıdır.');
            redirect('/admin/netvera-gelen-kutusu/'.$id);return;
        }
        Inbox::reply((int)$id,'admin',$message);
        logActivity('netvera_inbox_reply','Netvera talebine yanıt: #'.$id);
        flash('success',$row['source_type']==='offer'
            ?'Teklif yanıtı kaydedildi. Teklif sahibine iletişim adresi üzerinden ayrıca dönüş yapın.'
            :'Yanıt kaydedildi; ziyaretçi açık sohbetinden görebilir.');
        redirect('/admin/netvera-gelen-kutusu/'.$id);
    }

    /**
     * Admin-managed Telegram event switches; credentials remain server-side.
     * Missing checkboxes are intentionally saved as disabled.
     */
    public function saveTelegramSettings():void
    {
        Csrf::check();
        $config=\App\Services\SiteConfigService::getInstance();
        foreach(['enabled','notify_chat','notify_offer','notify_followup','notify_important'] as $key){
            $config->set('nv_tg_'.$key,isset($_POST['nv_tg_'.$key])?'1':'0','netvera_telegram');
        }
        logActivity('netvera_telegram_settings','Telegram bildirim seçenekleri güncellendi');
        flash('success','Telegram bildirim tercihleri kaydedildi.');
        redirect('/admin/netvera-gelen-kutusu');
    }

    public function testTelegram():void
    {
        Csrf::check();
        if(Inbox::testTelegramConnection()){
            flash('success','Telegram test mesajı gönderildi. Bot sohbetini kontrol edin.');
        } else {
            flash('error','Telegram test mesajı gönderilemedi. Sunucu .env bilgilerini, bot izinlerini ve bağlantıyı kontrol edin.');
        }
        redirect('/admin/netvera-gelen-kutusu');
    }

    public function installImportance():void
    {
        Csrf::check();
        $ok=Inbox::installImportance();
        if($ok)logActivity('netvera_inbox_migration','Önemli talep kolonu hazırlandı');
        flash($ok?'success':'error',$ok
            ?'Önemli talep işaretleme özelliği hazır.'
            :'Kurulum tamamlanamadı. Veritabanı ALTER TABLE yetkisini kontrol edin.');
        redirect('/admin/netvera-gelen-kutusu');
    }

    public function importance(string $id):void
    {
        Csrf::check();
        if(!Inbox::importanceReady()){
            flash('error','Önemli işareti için netvera-inquiries-v2.sql kurulumu gerekiyor.');
            redirect('/admin/netvera-gelen-kutusu');return;
        }
        $row=$this->db->fetch(
            'SELECT id,is_important FROM nv_public_inquiries WHERE id=? LIMIT 1',[(int)$id]
        );
        if(!$row){
            flash('error','Talep bulunamadı.');
            redirect('/admin/netvera-gelen-kutusu');return;
        }
        $marked=empty($row['is_important'])?1:0;
        $this->db->update('nv_public_inquiries',['is_important'=>$marked],'id=?',[(int)$id]);
        if($marked===1)Inbox::notifyTelegram((int)$id,'important');
        logActivity('netvera_inbox_importance','Talep #'.$id.' önem işareti '.($marked?'eklendi':'kaldırıldı'));
        flash('success',$marked?'Talep önemli olarak işaretlendi.':'Talebin önem işareti kaldırıldı.');
        redirect('/admin/netvera-gelen-kutusu/'.(int)$id);
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
