<?php
namespace App\Services;
use App\Core\Database;

/** Member-scoped read models; no payment/provider submission or invented entitlements. */
final class MemberPanelService
{
    public const ORDER_STATUSES=['payment_pending','paid','preparing','processing','completed','cancelled','refunded','waiting_customer_info'];
    public const PAYMENT_STATUSES=['pending','paid','failed','refunded'];
    public static function text(mixed $v,int $limit=100):string{return is_string($v)?mb_substr(trim($v),0,$limit):'';}
    public static function filters(array $in):array
    {
        $s=self::text($in['status']??'');$p=self::text($in['payment']??'');$d=self::text($in['period']??'');
        return ['q'=>self::text($in['q']??'',80),'status'=>in_array($s,array_merge(self::ORDER_STATUSES,['active']),true)?$s:'',
            'payment'=>in_array($p,array_merge(self::PAYMENT_STATUSES,['unpaid']),true)?$p:'','period'=>in_array($d,['30','90','365'],true)?$d:''];
    }
    public static function summary(int $id):array
    {
        $db=Database::getInstance();
        $s=$db->fetch("SELECT COUNT(*) total,
            COALESCE(SUM(order_status IN ('paid','preparing','processing','waiting_customer_info')),0) active,
            COALESCE(SUM(order_status='completed'),0) completed,
            COALESCE(SUM(payment_status='paid'),0) paid_count,
            COALESCE(SUM(CASE WHEN payment_status='paid' THEN total_amount ELSE 0 END),0) paid_total,
            COALESCE(SUM(CASE WHEN payment_status IN ('pending','failed') AND order_status NOT IN ('cancelled','refunded') THEN 1 ELSE 0 END),0) awaiting_payment,
            COALESCE(SUM(CASE WHEN payment_status IN ('pending','failed') AND order_status NOT IN ('cancelled','refunded') THEN total_amount ELSE 0 END),0) awaiting_total,
            COALESCE(SUM(order_status='waiting_customer_info'),0) needs_info FROM orders WHERE user_id=?",[$id]);
        $t=$db->fetch("SELECT COALESCE(SUM(status!='closed'),0) open_tickets,COALESCE(SUM(status IN ('answered','admin_reply')),0) answered_tickets FROM support_tickets WHERE user_id=?",[$id]);
        return array_merge($s??[],$t??[]);
    }
    public static function orders(int $id,array $in):array
    {
        $db=Database::getInstance();$f=self::filters($in);$where=['o.user_id=?'];$args=[$id];
        if($f['q']!==''){$term='%'.str_replace(['!','%','_'],['!!','!%','!_'],$f['q']).'%';$where[]="(o.order_number LIKE ? ESCAPE '!' OR EXISTS (SELECT 1 FROM order_items i WHERE i.order_id=o.id AND i.package_name LIKE ? ESCAPE '!'))";array_push($args,$term,$term);}
        foreach(['status'=>'order_status','payment'=>'payment_status']as $key=>$col)if($f[$key]!==''){
            if($key==='status'&&$f[$key]==='active')$where[]="o.order_status IN ('paid','preparing','processing','waiting_customer_info')";
            elseif($key==='payment'&&$f[$key]==='unpaid')$where[]="o.payment_status IN ('pending','failed') AND o.order_status NOT IN ('cancelled','refunded')";
            else{$where[]='o.'.$col.'=?';$args[]=$f[$key];}
        }
        if($f['period']!==''){$where[]='o.created_at>=?';$args[]=date('Y-m-d H:i:s',strtotime('-'.(int)$f['period'].' days'));}
        $sql=implode(' AND ',$where);$total=(int)($db->fetch('SELECT COUNT(*) n FROM orders o WHERE '.$sql,$args)['n']??0);
        $pages=max(1,(int)ceil($total/10));$p=$in['page']??1;$page=min($pages,max(1,is_scalar($p)?(int)$p:1));$offset=($page-1)*10;
        $rows=$db->fetchAll("SELECT o.id,o.order_number,o.total_amount,o.payment_method,o.payment_gateway,o.payment_status,o.order_status,o.created_at,o.updated_at,
            (SELECT GROUP_CONCAT(i.package_name SEPARATOR ' · ') FROM order_items i WHERE i.order_id=o.id) item_names
            FROM orders o WHERE $sql ORDER BY o.created_at DESC,o.id DESC LIMIT 10 OFFSET $offset",$args);
        return ['orders'=>$rows,'filters'=>$f,'page'=>$page,'totalPages'=>$pages,'total'=>$total];
    }
    public static function link(string $base,array $f,int $page=1):string
    {
        $q=array_filter(self::filters($f),static fn($v)=>$v!=='');if($page>1)$q['page']=$page;
        return $base.($q?'?'.http_build_query($q,'','&',PHP_QUERY_RFC3986):'');
    }
    public static function activity(int $id):array
    {
        $db=Database::getInstance();$events=[];
        foreach($db->fetchAll('SELECT id,order_number,order_status,payment_status,updated_at FROM orders WHERE user_id=? ORDER BY updated_at DESC,id DESC LIMIT 12',[$id])as $o)
            $events[]=['kind'=>'order','title'=>'#'.$o['order_number'],'detail'=>orderStatusLabel($o['order_status']).' · '.paymentStatusLabel($o['payment_status']),'date'=>$o['updated_at'],'url'=>'/siparis/'.$o['id']];
        foreach($db->fetchAll('SELECT id,ticket_number,subject,status,updated_at FROM support_tickets WHERE user_id=? ORDER BY updated_at DESC,id DESC LIMIT 12',[$id])as $t)
            $events[]=['kind'=>'support','title'=>$t['subject'],'detail'=>'#'.$t['ticket_number'].' · '.supportStatusLabel($t['status']),'date'=>$t['updated_at'],'url'=>'/destek/'.$t['id']];
        usort($events,static fn($a,$b)=>strcmp($b['date'],$a['date']));return array_slice($events,0,20);
    }
    public static function delivery(int $orderId,int $userId):array
    {
        $db=Database::getInstance();if(!$db->fetch("SHOW TABLES LIKE 'smm_order_jobs'"))return [];
        // Allowlist projection: never expose provider identity, raw errors or API identifiers.
        $jobs=$db->fetchAll('SELECT j.order_item_id,j.quantity,j.remains,j.state,j.last_checked_at,j.updated_at,i.package_name
            FROM smm_order_jobs j JOIN orders o ON o.id=j.order_id JOIN order_items i ON i.id=j.order_item_id AND i.order_id=o.id
            WHERE o.id=? AND o.user_id=? ORDER BY j.id',[$orderId,$userId]);
        foreach($jobs as &$j)$j['progress']=self::progress($j);unset($j);return $jobs;
    }
    public static function progress(array $j):?int
    {
        if(($j['state']??'')==='completed')return 100;
        $q=(int)($j['quantity']??0);$r=$j['remains']??null;
        if($q<1||$r===null||!is_numeric($r)||(int)$r<0||(int)$r>$q)return null;
        return (int)floor(($q-(int)$r)*100/$q);
    }
    public static function deliveryLabel(string $s):string
    {
        return ['queued'=>'Sırada','sending'=>'İletiliyor','submitted'=>'İşleme alındı','in_progress'=>'Devam ediyor','completed'=>'Tamamlandı','partial'=>'Kısmen tamamlandı','cancelled'=>'İptal edildi','failed'=>'Kontrol gerekiyor','manual_review'=>'Ekip kontrolünde'][$s]??'Durum bekleniyor';
    }
    public static function profile(array $in):array
    {
        if(!is_string($in['name']??null)||(isset($in['phone'])&&!is_string($in['phone'])))throw new \InvalidArgumentException('Profil alanlarını kontrol edin.');
        $name=self::text($in['name']??'',101);$phone=self::text($in['phone']??'',21);
        if(mb_strlen($name)<2||mb_strlen($name)>100||preg_match('/[\x00-\x1F<>]/u',$name))throw new \InvalidArgumentException('Ad soyad 2–100 karakter olmalı ve geçerli metin içermelidir.');
        if($phone!==''&&(strlen($phone)>20||!preg_match('/^\+?[0-9 ()-]{7,20}$/D',$phone)))throw new \InvalidArgumentException('Geçerli bir telefon numarası girin veya alanı boş bırakın.');
        return ['name'=>$name,'phone'=>$phone];
    }
    public static function password(array $in,string $hash):string
    {
        $current=$in['current_password']??null;$new=$in['new_password']??null;$confirm=$in['new_password_confirmation']??null;
        if(!is_string($current)||strlen($current)>4096||!password_verify($current,$hash))throw new \InvalidArgumentException('Mevcut şifre hatalı.');
        // bcrypt must not silently truncate an oversized password.
        if(!is_string($new)||str_contains($new,"\0")||mb_strlen($new)<8||strlen($new)>72)throw new \InvalidArgumentException('Yeni şifre en az 8 karakter, en fazla 72 bayt olmalıdır.');
        if(!is_string($confirm)||!hash_equals($new,$confirm))throw new \InvalidArgumentException('Yeni şifre ve tekrarı eşleşmiyor.');
        if(password_verify($new,$hash))throw new \InvalidArgumentException('Yeni şifre mevcut şifrenizden farklı olmalıdır.');
        return $new;
    }
}
