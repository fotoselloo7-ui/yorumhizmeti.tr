<?php
namespace App\Services;
use App\Core\Database;

/** Shared support writes: ownership, bounded content and atomic ticket/message updates. */
final class MemberSupportService
{
    public static function message(mixed $value):string
    {
        if(!is_string($value)||mb_strlen(trim($value))<3||mb_strlen($value)>10000)throw new \InvalidArgumentException('Mesajınız 3–10.000 karakter olmalıdır.');
        return trim($value);
    }
    public static function input(array $in,int $userId):array
    {
        $subject=MemberPanelService::text($in['subject']??'',301);
        if(mb_strlen($subject)<3||mb_strlen($subject)>300)throw new \InvalidArgumentException('Konu 3–300 karakter olmalıdır.');
        $priority=$in['priority']??'medium';
        if(!is_string($priority)||!in_array($priority,['low','medium','high'],true))throw new \InvalidArgumentException('Geçerli bir öncelik seçin.');
        $raw=$in['order_id']??'';
        if(!is_scalar($raw)||($raw!==''&&$raw!==null&&!ctype_digit((string)$raw)))throw new \InvalidArgumentException('İlgili siparişi kontrol edin.');
        $orderId=(int)$raw;
        if($orderId>0&&!Database::getInstance()->fetch('SELECT id FROM orders WHERE id=? AND user_id=?',[$orderId,$userId]))throw new \InvalidArgumentException('Yalnızca hesabınıza ait bir siparişi seçebilirsiniz.');
        return ['subject'=>$subject,'priority'=>$priority,'order_id'=>$orderId?:null,'message'=>self::message($in['message']??null)];
    }
    public static function create(int $userId,array $data,?string $attachment):int
    {
        $db=Database::getInstance();$pdo=$db->getPdo();$pdo->beginTransaction();
        try{
            if(!empty($data['order_id'])&&!$db->fetch('SELECT id FROM orders WHERE id=? AND user_id=? FOR UPDATE',[$data['order_id'],$userId]))throw new \InvalidArgumentException('İlgili siparişe erişilemiyor.');
            $id=$db->insert('support_tickets',['user_id'=>$userId,'order_id'=>$data['order_id'],'ticket_number'=>'DT'.date('ymd').strtoupper(bin2hex(random_bytes(5))),'subject'=>$data['subject'],'priority'=>$data['priority'],'status'=>'open']);
            $db->insert('support_messages',['ticket_id'=>$id,'sender_type'=>'user','sender_id'=>$userId,'message'=>$data['message'],'attachment'=>$attachment]);
            $pdo->commit();return $id;
        }catch(\Throwable $e){if($pdo->inTransaction())$pdo->rollBack();throw $e;}
    }
    public static function reply(int $ticketId,int $actorId,string $message,?string $attachment=null,bool $admin=false,string $status='answered'):void
    {
        $db=Database::getInstance();$pdo=$db->getPdo();$pdo->beginTransaction();
        try{
            $ticket=$db->fetch('SELECT id,status FROM support_tickets WHERE id=?'.($admin?'':' AND user_id=?').' FOR UPDATE',$admin?[$ticketId]:[$ticketId,$actorId]);
            if(!$ticket||$ticket['status']==='closed')throw new \InvalidArgumentException('Talep kapalı veya erişilemiyor.');
            $message=self::message($message);
            $db->insert('support_messages',['ticket_id'=>$ticketId,'sender_type'=>$admin?'admin':'user','sender_id'=>$actorId,'message'=>$message,'attachment'=>$attachment]);
            $next=$admin?($status==='closed'?'closed':'answered'):'customer_reply';
            $db->update('support_tickets',['status'=>$next,'updated_at'=>date('Y-m-d H:i:s')],'id=?',[$ticketId]);
            $pdo->commit();
        }catch(\Throwable $e){if($pdo->inTransaction())$pdo->rollBack();throw $e;}
    }
    public static function close(int $id,int $userId):void
    {
        Database::getInstance()->query("UPDATE support_tickets SET status='closed',updated_at=NOW() WHERE id=? AND user_id=? AND status!='closed'",[$id,$userId]);
    }
    public static function upload(array $file):?string
    {
        if(($file['error']??UPLOAD_ERR_NO_FILE)===UPLOAD_ERR_NO_FILE)return null;
        if(($file['error']??null)!==UPLOAD_ERR_OK||($file['size']??0)<1||$file['size']>10*1024*1024||!is_uploaded_file($file['tmp_name']??''))throw new \InvalidArgumentException('Dosya yüklenemedi. En fazla 10 MB dosya seçin.');
        $mime=(new \finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        $types=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','image/gif'=>'gif','application/pdf'=>'pdf'];
        if(!isset($types[$mime]))throw new \InvalidArgumentException('Yalnızca JPG, PNG, WebP, GIF veya PDF yükleyebilirsiniz.');
        $dir=BASE_PATH.'/storage/member-support';if(!is_dir($dir)&&!mkdir($dir,0750,true)&&!is_dir($dir))throw new \RuntimeException('Support storage unavailable');
        $name=bin2hex(random_bytes(20)).'.'.$types[$mime];
        if(!move_uploaded_file($file['tmp_name'],$dir.'/'.$name))throw new \RuntimeException('Support upload failed');
        return 'member-support:'.$name;
    }
    /** Only newly-created private files may be cleaned after a rolled-back submission. */
    public static function discard(?string $path):void
    {
        if($path&&preg_match('/^member-support:([a-f0-9]{40}\.(?:jpg|png|webp|gif|pdf))$/D',$path,$m)){
            $file=BASE_PATH.'/storage/member-support/'.$m[1];if(is_file($file))unlink($file);
        }
    }
    public static function file(string $stored):?string
    {
        if(preg_match('/^member-support:([a-f0-9]{40}\.(?:jpg|png|webp|gif|pdf))$/D',$stored,$m))$root=BASE_PATH.'/storage/member-support';
        elseif(preg_match('~^/uploads/support/([a-zA-Z0-9_.-]+\.(?:jpg|jpeg|png|webp|gif|pdf|doc|docx))$~D',$stored,$m))$root=PUBLIC_PATH.'/uploads/support';
        else return null;
        $base=realpath($root);$file=realpath($root.'/'.$m[1]);
        return $base&&$file&&is_file($file)&&str_starts_with($file,$base.DIRECTORY_SEPARATOR)?$file:null;
    }
}
