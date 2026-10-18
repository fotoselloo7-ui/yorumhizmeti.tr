<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Auth;
use App\Core\Csrf;
use App\Services\LegacyCustomerService;
use App\Services\MemberPanelService as Panel;

class AccountController extends Controller
{
    public function index(): void
    {
        $id=(int)Auth::id();$list=Panel::orders($id,[]);
        $this->render('frontend/account/dashboard',[
            'pageTitle'=>'Hesap Özeti','user'=>Auth::user(),'summary'=>Panel::summary($id),
            'recentOrders'=>array_slice($list['orders'],0,4),'activity'=>array_slice(Panel::activity($id),0,4),
        ]);
    }
    public function profile():void{$this->render('frontend/account/profile',['pageTitle'=>'Profil Bilgilerim','user'=>Auth::user()]);}
    public function security():void{$this->render('frontend/account/security',['pageTitle'=>'Hesap Güvenliği','user'=>Auth::user()]);}
    public function software():void{$this->render('frontend/account/software',['pageTitle'=>'Yazılımlarım','nativeSoftware'=>Panel::software((int)Auth::id()),'netveraHistory'=>LegacyCustomerService::overview((int)Auth::id())]);}
    public function activity():void{$this->render('frontend/account/activity',['pageTitle'=>'Hesap Hareketleri','activity'=>Panel::activity((int)Auth::id())]);}
    public function payments():void
    {
        $this->render('frontend/account/payments',array_merge(Panel::orders((int)Auth::id(),$_GET),[
            'pageTitle'=>'Ödeme Geçmişim','summary'=>Panel::summary((int)Auth::id()),'listBase'=>'/hesabim/odemeler',
        ]));
    }
    public function update():void
    {
        Csrf::check();
        try{$data=Panel::profile($_POST);}
        catch(\InvalidArgumentException $e){flash('error',$e->getMessage());redirect('/hesabim/profil');return;}
        $this->db->update('users',$data,'id=?',[Auth::id()]);$_SESSION['user_name']=$data['name'];
        flash('success','Profil bilgileriniz güncellendi.');redirect('/hesabim/profil');
    }
    public function changePassword():void
    {
        Csrf::check();
        try{$password=Panel::password($_POST,Auth::user()['password']);}
        catch(\InvalidArgumentException $e){flash('error',$e->getMessage());redirect('/hesabim/guvenlik');return;}
        $this->db->update('users',['password'=>password_hash($password,PASSWORD_DEFAULT)],'id=?',[Auth::id()]);
        session_regenerate_id(true);
        flash('success','Şifreniz güncellendi.');redirect('/hesabim/guvenlik');
    }
    public function orders():void
    {
        $this->render('frontend/account/orders',array_merge(Panel::orders((int)Auth::id(),$_GET),[
            'pageTitle'=>'Siparişlerim','listBase'=>'/siparislerim',
        ]));
    }
    public function orderDetail(string $id):void
    {
        $order=$this->db->fetch('SELECT * FROM orders WHERE id=? AND user_id=?',[(int)$id,Auth::id()]);
        if(!$order){http_response_code(404);$this->render('frontend/404',['pageTitle'=>'Sipariş Bulunamadı']);return;}
        $items=$this->db->fetchAll("SELECT i.*,p.slug,p.status package_status FROM order_items i LEFT JOIN packages p ON p.id=i.package_id WHERE i.order_id=?",[$order['id']]);
        $this->render('frontend/account/order-detail',[
            'pageTitle'=>'Sipariş #'.$order['order_number'],'order'=>$order,'items'=>$items,
            'fields'=>$this->db->fetchAll('SELECT field_label,field_value FROM order_fields WHERE order_id=?',[$order['id']]),
            // Admin's status form explicitly labels its note customer-visible.
            // Provider/system logs and the separate private admin_note remain hidden.
            'logs'=>$this->db->fetchAll("SELECT new_status,created_at,CASE WHEN created_by='admin' THEN note ELSE NULL END public_note FROM order_status_logs WHERE order_id=? ORDER BY created_at DESC,id DESC",[$order['id']]),
            'bankNotifications'=>$this->db->fetchAll('SELECT sender_name,bank_name,amount,status,created_at FROM bank_transfer_notifications WHERE order_id=? ORDER BY created_at DESC',[$order['id']]),
            'bankAccounts'=>$this->db->fetchAll("SELECT bank_name,account_holder,iban FROM bank_accounts WHERE status='active' ORDER BY sort_order"),
            'delivery'=>Panel::delivery((int)$order['id'],(int)Auth::id()),
        ]);
    }
}
