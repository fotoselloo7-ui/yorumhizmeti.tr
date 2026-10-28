<?php
namespace App\Controllers\Admin;

use App\Core\AdminAuth;
use App\Core\Controller;
use App\Core\Csrf;

/** Secure, explicit admin account editing; no automatic credential migration. */
final class AdminAccountController extends Controller
{
    private function viewer(): array
    {
        $viewer=AdminAuth::admin();
        if(!$viewer){redirect('/admin/giris');exit;}
        return $viewer;
    }

    private function canEdit(array $viewer,int $id):bool
    {
        return (int)$viewer['id']===$id || $viewer['role']==='super_admin';
    }

    public function index():void
    {
        $viewer=$this->viewer();
        $admins=$viewer['role']==='super_admin'
            ?$this->db->fetchAll("SELECT id,name,email,role,status,created_at FROM admins ORDER BY id ASC")
            :$this->db->fetchAll("SELECT id,name,email,role,status,created_at FROM admins WHERE id=?",[(int)$viewer['id']]);
        $this->renderAdmin('admin/settings/admin-users',[
            'pageTitle'=>'Admin Kullanıcıları',
            'admins'=>$admins,'currentAdmin'=>$viewer
        ]);
    }

    public function edit(string $id):void
    {
        $viewer=$this->viewer();
        $targetId=(int)$id;
        if(!$this->canEdit($viewer,$targetId)){http_response_code(403);echo 'Bu yönetici hesabına erişim yetkiniz yok.';return;}
        $target=$this->db->fetch("SELECT id,name,email,role,status FROM admins WHERE id=?",[$targetId]);
        if(!$target){http_response_code(404);echo 'Yönetici bulunamadı.';return;}
        $this->renderAdmin('admin/settings/admin-user-edit',[
            'pageTitle'=>'Yönetici Bilgilerini Düzenle',
            'targetAdmin'=>$target,'currentAdmin'=>$viewer,
            'isSelf'=>(int)$viewer['id']===$targetId
        ]);
    }

    public function update(string $id):void
    {
        Csrf::check();
        $viewer=$this->viewer();
        $targetId=(int)$id;
        if(!$this->canEdit($viewer,$targetId)){http_response_code(403);echo 'Yetersiz yönetici yetkisi.';return;}
        $target=$this->db->fetch("SELECT id,name,email,role,status,password FROM admins WHERE id=?",[$targetId]);
        if(!$target){http_response_code(404);return;}
        $back='/admin/admin-kullanicilari/'.$targetId.'/duzenle';

        // Never accept sensitive profile/role/password changes on the strength
        // of a browser session alone: verify the acting admin's real password.
        $authPassword=(string)($_POST['current_password']??'');
        if($authPassword==='' || !password_verify($authPassword,(string)$viewer['password'])){
            flash('error','İşlemi onaylamak için kendi güncel yönetici şifrenizi girin.');
            redirect($back);return;
        }
        $name=trim((string)($_POST['name']??''));
        $email=mb_strtolower(trim((string)($_POST['email']??'')),'UTF-8');
        if(mb_strlen($name)<2||mb_strlen($name)>100 || !filter_var($email,FILTER_VALIDATE_EMAIL)||strlen($email)>191){
            flash('error','Geçerli bir ad ve e-posta girin.');redirect($back);return;
        }
        $self=(int)$viewer['id']===$targetId;
        $role=$self?(string)$target['role']:(string)($_POST['role']??$target['role']);
        $status=$self?'active':(string)($_POST['status']??$target['status']);
        if(!in_array($role,['super_admin','admin','editor'],true) ||
           !in_array($status,['active','inactive'],true)){
            flash('error','Rol veya hesap durumu geçersiz.');redirect($back);return;
        }
        if(!$self && $viewer['role']!=='super_admin'){http_response_code(403);return;}
        if($target['role']==='super_admin' && ($role!=='super_admin'||$status!=='active')){
            $others=(int)($this->db->fetch("SELECT COUNT(*) AS cnt FROM admins WHERE id<>? AND role='super_admin' AND status='active'",[$targetId])['cnt']??0);
            if(!$others){flash('error','Son aktif süper yönetici devre dışı bırakılamaz.');redirect($back);return;}
        }
        $newPassword=(string)($_POST['new_password']??'');
        $confirm=(string)($_POST['new_password_confirm']??'');
        $data=['name'=>$name,'email'=>$email,'role'=>$role,'status'=>$status];
        if($newPassword!==''){
            if(strlen($newPassword)<12 || strlen($newPassword)>128 ||
               !hash_equals($newPassword,$confirm)){
                flash('error','Yeni şifre en az 12 karakter olmalı ve tekrarıyla eşleşmeli.');
                redirect($back);return;
            }
            $data['password']=password_hash($newPassword,PASSWORD_DEFAULT);
            $data['must_change_password']=0;
        }
        try{
            $exists=$this->db->fetch("SELECT id FROM admins WHERE email=? AND id<>? LIMIT 1",[$email,$targetId]);
            if($exists){flash('error','Bu e-posta başka bir yönetici tarafından kullanılıyor.');redirect($back);return;}
            $this->db->update('admins',$data,'id=?',[$targetId]);
            if($self){
                $_SESSION['admin_name']=$name;
                $_SESSION['admin_email']=$email;
                $_SESSION['admin_role']=$role;
                if($newPassword!=='')session_regenerate_id(true);
            }
            logActivity('admin_account_update','Yönetici hesabı #'.$targetId.' güncellendi; parola bilgisi loglanmadı.');
            flash('success','Yönetici bilgileri güvenle güncellendi.');
        }catch(\Throwable $e){
            flash('error','Güncelleme başarısız. Bilgileri kontrol edin.');
        }
        redirect($back);
    }
}
