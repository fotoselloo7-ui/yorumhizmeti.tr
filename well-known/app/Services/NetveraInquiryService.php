<?php
namespace App\Services;

use App\Core\Database;

/**
 * Public chats and offers stay isolated from orders/payment records.
 * Telegram credentials are read from the server environment, never from source control.
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
        self::notifyTelegram($id,$type==='offer'?'offer':'chat');
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
        if($sender==='visitor')self::notifyTelegram($id,'followup');
    }

    /** The importance flag is optional until the one-time v2 migration is applied. */
    public static function importanceReady(): bool
    {
        try {
            return (bool)Database::getInstance()->fetch(
                "SELECT 1 AS present FROM information_schema.columns
                 WHERE table_schema=DATABASE() AND table_name='nv_public_inquiries'
                   AND column_name='is_important' LIMIT 1"
            );
        } catch (\Throwable $e) { return false; }
    }

    /** Credentials from NetVera-compatible environment variable names. */
    private static function telegramCredentials(): array
    {
        $token=trim((string)($_ENV['NETVERA_TELEGRAM_BOT_TOKEN']??getenv('NETVERA_TELEGRAM_BOT_TOKEN')?:''));
        $chat=trim((string)($_ENV['NETVERA_TELEGRAM_CHAT_ID']??getenv('NETVERA_TELEGRAM_CHAT_ID')?:''));
        if(!preg_match('/^[0-9]{6,15}:[A-Za-z0-9_-]{30,}$/D',$token)
            || !preg_match('/^-?[0-9]{5,20}$/D',$chat))return ['',''];
        return [$token,$chat];
    }

    public static function telegramConfigured(): bool
    {
        [$token,$chat]=self::telegramCredentials();
        return $token!=='' && $chat!=='' && extension_loaded('curl');
    }

    /** Send an admin-triggered connection test without disclosing bot credentials. */
    public static function testTelegramConnection(): bool
    {
        if(!self::telegramConfigured())return false;
        [$token,$chat]=self::telegramCredentials();
        $ch=curl_init('https://api.telegram.org/bot'.$token.'/sendMessage');
        if(!$ch)return false;
        curl_setopt_array($ch,[
            CURLOPT_POST=>true,
            CURLOPT_POSTFIELDS=>http_build_query([
                'chat_id'=>$chat,'text'=>'✅ YorumHizmeti.tr Telegram destek bildirimleri test mesajı.',
                'disable_web_page_preview'=>'true'
            ]),
            CURLOPT_RETURNTRANSFER=>true,CURLOPT_CONNECTTIMEOUT=>3,
            CURLOPT_TIMEOUT=>7,CURLOPT_FOLLOWLOCATION=>false,
            CURLOPT_SSL_VERIFYPEER=>true,CURLOPT_SSL_VERIFYHOST=>2
        ]);
        $response=curl_exec($ch);
        $code=(int)curl_getinfo($ch,CURLINFO_HTTP_CODE);
        curl_close($ch);
        $payload=is_string($response)?json_decode($response,true):null;
        return $code===200 && is_array($payload) && ($payload['ok']??false)===true;
    }

    /**
     * Optional Telegram alerts. A failed request never rolls back a saved inquiry.
     * Event switches: chat, offer, followup, important.
     */
    public static function notifyTelegram(int $id,string $event): void
    {
        if(!in_array($event,['chat','offer','followup','important'],true))return;
        if(setting('nv_tg_enabled','1')!=='1'
            || setting('nv_tg_notify_'.$event,'1')!=='1'
            || !self::telegramConfigured())return;
        [$token,$chat]=self::telegramCredentials();
        try {
            $db=Database::getInstance();
            $inquiry=$db->fetch(
                "SELECT id,source_type,product_slug,visitor_name,visitor_contact,inquiry_text
                 FROM nv_public_inquiries WHERE id=? LIMIT 1",[$id]
            );
            if(!$inquiry)return;
            $titles=[
                'chat'=>'💬 Yeni canlı destek mesajı',
                'offer'=>'📦 Yeni yazılım teklif talebi',
                'followup'=>'🔔 Ziyaretçiden yeni yanıt',
                'important'=>'⭐ Önemli olarak işaretlenen talep'
            ];
            $detail=$event==='followup'
                ?$db->fetch(
                    "SELECT message FROM nv_public_inquiry_replies
                     WHERE inquiry_id=? AND sender='visitor' ORDER BY id DESC LIMIT 1",[$id]
                )
                :null;
            $excerpt=trim((string)($detail['message']??$inquiry['inquiry_text']??''));
            $text=$titles[$event]." #".$id."\n"
                ."👤 ".(string)$inquiry['visitor_name']."\n"
                ."📞 / ✉ ".(string)$inquiry['visitor_contact']."\n"
                ."📁 ".((string)($inquiry['product_slug']??'')?:'Genel destek')."\n"
                ."📝 ".mb_substr($excerpt,0,400,'UTF-8');
            $ch=curl_init('https://api.telegram.org/bot'.$token.'/sendMessage');
            if(!$ch)return;
            curl_setopt_array($ch,[
                CURLOPT_POST=>true,
                CURLOPT_POSTFIELDS=>http_build_query([
                    'chat_id'=>$chat,'text'=>$text,'disable_web_page_preview'=>'true'
                ]),
                CURLOPT_RETURNTRANSFER=>true,CURLOPT_CONNECTTIMEOUT=>3,
                CURLOPT_TIMEOUT=>7,CURLOPT_FOLLOWLOCATION=>false,
                CURLOPT_SSL_VERIFYPEER=>true,CURLOPT_SSL_VERIFYHOST=>2
            ]);
            $result=curl_exec($ch);
            $code=(int)curl_getinfo($ch,CURLINFO_HTTP_CODE);
            curl_close($ch);
            if($result===false||$code!==200)
                error_log('NetVera Telegram alert delivery failed for event '.$event);
        } catch (\Throwable $e) {
            error_log('NetVera Telegram alert could not be processed.');
        }
    }
}
