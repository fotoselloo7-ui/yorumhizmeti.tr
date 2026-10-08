<?php
namespace App\Services;

use App\Core\Database;

/**
 * Netvera inbox for fresh public chats and quotes. Never imports old contacts,
 * Telegram tokens, customer records or payment data.
 */
final class NetveraInquiryService
{
    public static function ready(): bool
    {
        try {
            return (bool)Database::getInstance()->fetch(
                "SELECT 1 AS yes FROM information_schema.tables
                 WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='nv_public_inquiries'"
            );
        } catch(\Throwable $e){return false;}
    }

    public static function visitorHash(): string
    {
        if(empty($_SESSION['nv_inbox_visitor']) || !is_string($_SESSION['nv_inbox_visitor']))
            $_SESSION['nv_inbox_visitor']=bin2hex(random_bytes(32));
        return hash('sha256',$_SESSION['nv_inbox_visitor']);
    }

    public static function create(
        string $type,string $name,string $contact,string $message,?string $slug
    ): int {
        $db=Database::getInstance();
        $id=$db->insert('nv_public_inquiries',[
            'source_type'=>$type,
            'product_slug'=>$slug,
            'visitor_name'=>$name,
            'visitor_contact'=>$contact,
            'inquiry_text'=>$message,
            'status'=>'new',
            'session_hash'=>self::visitorHash()
        ]);
        $db->insert('nv_public_inquiry_replies',[
            'inquiry_id'=>$id,'sender'=>'visitor','message'=>$message
        ]);
        self::notifyTelegram($id,$type,$name,$slug);
        return $id;
    }

    public static function visibleToVisitor(int $id): ?array
    {
        if($id<1 || !self::ready())return null;
        return Database::getInstance()->fetch(
            "SELECT id,source_type,product_slug,status,created_at
               FROM nv_public_inquiries WHERE id=? AND session_hash=? LIMIT 1",
            [$id,self::visitorHash()]
        );
    }

    public static function replies(int $id): array
    {
        return Database::getInstance()->fetchAll(
            "SELECT sender,message,created_at FROM nv_public_inquiry_replies
             WHERE inquiry_id=? ORDER BY id ASC LIMIT 150",[$id]
        );
    }

    public static function reply(int $id,string $sender,string $message): void
    {
        $db=Database::getInstance();
        $db->insert('nv_public_inquiry_replies',[
            'inquiry_id'=>$id,'sender'=>$sender,'message'=>$message
        ]);
        $db->update('nv_public_inquiries',[
            'status'=>$sender==='admin'?'replied':'open'
        ],'id=?',[$id]);
    }

    /** Safe optional Telegram notification; token stays in environment only. */
    private static function notifyTelegram(int $id,string $type,string $name,?string $slug): void
    {
        $token=trim((string)($_ENV['NETVERA_TELEGRAM_BOT_TOKEN']??getenv('NETVERA_TELEGRAM_BOT_TOKEN')?:''));
        $chat=trim((string)($_ENV['NETVERA_TELEGRAM_CHAT_ID']??getenv('NETVERA_TELEGRAM_CHAT_ID')?:''));
        if(!extension_loaded('curl') || !preg_match('/^[0-9]{6,15}:[A-Za-z0-9_-]{30,}$/',$token)
            || !preg_match('/^-?[0-9]{5,20}$/',$chat))return;
        $label=$type==='offer'?'Teklif talebi':'Yeni sohbet';
        $text=$label.' #'.$id."\n".$name."\n".($slug?:'Genel iletişim');
        $ch=curl_init('https://api.telegram.org/bot'.$token.'/sendMessage');
        if(!$ch)return;
        curl_setopt_array($ch,[
            CURLOPT_POST=>true, CURLOPT_POSTFIELDS=>http_build_query([
                'chat_id'=>$chat,'text'=>$text,'disable_web_page_preview'=>'true'
            ]), CURLOPT_RETURNTRANSFER=>true,CURLOPT_CONNECTTIMEOUT=>2,
            CURLOPT_TIMEOUT=>5,CURLOPT_FOLLOWLOCATION=>false,
            CURLOPT_SSL_VERIFYPEER=>true,CURLOPT_SSL_VERIFYHOST=>2
        ]);
        curl_exec($ch);
        curl_close($ch);
        // Never log tokens, visitor contacts or message bodies.
    }
}
