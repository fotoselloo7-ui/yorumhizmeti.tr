<?php
namespace App\Services;

use App\Core\Database;
use PDO;
use RuntimeException;

/**
 * Dealer v2 – explicit approvals, audited changes and unique referral tracking.
 * Historic NetVera commissions are READ ONLY. No payment or order changes here.
 */
final class DealerProgramService
{
    private static function db(): PDO { return Database::getInstance()->getPdo(); }

    public static function ready(): bool
    {
        try {
            foreach (['nv_dealer_accounts','nv_dealer_audit','nv_dealer_referral_events'] as $table) {
                $q = self::db()->prepare('SHOW TABLES LIKE ?');
                $q->execute([$table]);
                if (!$q->fetchColumn()) return false;
            }
            return true;
        } catch (\Throwable $e) { return false; }
    }

    public static function account(int $userId): ?array
    {
        if ($userId < 1 || !self::ready()) return null;
        $q=self::db()->prepare('SELECT * FROM nv_dealer_accounts WHERE user_id=? LIMIT 1');
        $q->execute([$userId]);
        return $q->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public static function legacy(int $userId): ?array
    {
        if ($userId < 1) return null;
        try {
            $q=self::db()->prepare('SELECT old_affiliate_id,referral_code,status,commission_rate FROM nv_private_affiliates WHERE new_user_id=? LIMIT 1');
            $q->execute([$userId]);return $q->fetch(PDO::FETCH_ASSOC)?:null;
        } catch (\Throwable $e) { return null; }
    }

    public static function apply(int $userId): string
    {
        if (!self::ready()) throw new RuntimeException('Bayilik veritabanı henüz kurulu değil.');
        if ($userId < 1) throw new RuntimeException('Önce müşteri hesabınıza giriş yapın.');
        $db=self::db();
        $db->beginTransaction();
        try {
            $q=$db->prepare('SELECT id,status FROM nv_dealer_accounts WHERE user_id=? FOR UPDATE');
            $q->execute([$userId]);$prior=$q->fetch(PDO::FETCH_ASSOC);
            if($prior){
                $db->commit();
                return $prior['status']==='pending'?'Başvurunuz zaten değerlendirmede.':'Bu hesap için bayilik kaydı bulunmaktadır.';
            }
            $legacy=self::legacy($userId);
            for($attempt=0;$attempt<4;$attempt++){
                $code='NV'.strtoupper(bin2hex(random_bytes(6)));
                $exists=$db->prepare('SELECT id FROM nv_dealer_accounts WHERE referral_code=?');
                $exists->execute([$code]);
                if(!$exists->fetchColumn())break;
            }
            if($attempt>=4)throw new RuntimeException('Referans kodu oluşturulamadı.');
            $q=$db->prepare('INSERT INTO nv_dealer_accounts (user_id,legacy_affiliate_id,referral_code,status,tier,commission_rate) VALUES (?,?,?,\'pending\',\'starter\',0)');
            $q->execute([$userId,$legacy? (int)$legacy['old_affiliate_id'] : null,$code]);
            $id=(int)$db->lastInsertId();
            $q=$db->prepare('INSERT INTO nv_dealer_audit (dealer_id,actor_type,actor_id,action,new_status,details) VALUES (?,\'customer\',?,\'apply\',\'pending\',\'Bayilik basvurusu\')');
            $q->execute([$id,$userId]);
            $db->commit();
            return 'Bayilik başvurunuz alındı. Yönetici incelemesinden sonra etkinleşir.';
        }catch(\Throwable $e){
            if($db->inTransaction())$db->rollBack();
            if($e instanceof RuntimeException) throw $e;
            throw new RuntimeException('Bayilik başvurusu kaydedilemedi.');
        }
    }

    public static function review(int $dealerId,int $adminId,string $status,string $tier,float $rate): void
    {
        if(!self::ready())throw new RuntimeException('Bayilik tabloları kurulu değil.');
        if($dealerId<1||$adminId<1||!in_array($status,['pending','approved','suspended','rejected'],true)
           ||!in_array($tier,['starter','pro','agency'],true)||!is_finite($rate)||$rate<0||$rate>30)
            throw new RuntimeException('Geçersiz bayilik yönetim bilgisi.');
        $db=self::db();$db->beginTransaction();
        try{
            $q=$db->prepare('SELECT id,status FROM nv_dealer_accounts WHERE id=? FOR UPDATE');
            $q->execute([$dealerId]);$before=$q->fetch(PDO::FETCH_ASSOC);
            if(!$before)throw new RuntimeException('Bayilik hesabı bulunamadı.');
            $effectiveRate=$status==='approved'?$rate:0.0;
            $q=$db->prepare('UPDATE nv_dealer_accounts SET status=?,tier=?,commission_rate=?,reviewed_by=?,reviewed_at=NOW() WHERE id=?');
            $q->execute([$status,$tier,$effectiveRate,$adminId,$dealerId]);
            $q=$db->prepare('INSERT INTO nv_dealer_audit (dealer_id,actor_type,actor_id,action,old_status,new_status,details) VALUES (?,\'admin\',?,\'review\',?,?,?)');
            $q->execute([$dealerId,$adminId,$before['status'],$status,'tier='.$tier.' rate='.number_format($effectiveRate,2,'.','')]);
            $db->commit();
        }catch(\Throwable $e){
            if($db->inTransaction())$db->rollBack();
            if($e instanceof RuntimeException)throw $e;
            throw new RuntimeException('Bayilik kararı kaydedilemedi.');
        }
    }

    public static function referrals(int $dealerId): int
    {
        if($dealerId<1||!self::ready())return 0;
        $q=self::db()->prepare('SELECT COUNT(*) FROM nv_dealer_referral_events WHERE dealer_id=?');
        $q->execute([$dealerId]);return (int)$q->fetchColumn();
    }

    public static function track(string $code): bool
    {
        if(!self::ready()||!preg_match('/^NV[A-F0-9]{12}$/D',$code))return false;
        $db=self::db();
        $q=$db->prepare("SELECT id FROM nv_dealer_accounts WHERE referral_code=? AND status='approved' LIMIT 1");
        $q->execute([$code]);$id=(int)$q->fetchColumn();
        if($id<1)return false;
        // Daily deduplication of visits via opaque session identifier.
        // No IP, user-agent, customer email or purchase information is stored.
        if(session_status()!==PHP_SESSION_ACTIVE) return true;
        $visitor=hash('sha256',session_id().':'.$id.':'.date('Y-m-d'));
        $q=$db->prepare('INSERT IGNORE INTO nv_dealer_referral_events (dealer_id,day,visitor_hash) VALUES (?,CURRENT_DATE(),?)');
        $q->execute([$id,$visitor]);
        return true;
    }

    public static function auditTrail(): array
    {
        if(!self::ready())return [];
        return Database::getInstance()->fetchAll(
            'SELECT a.dealer_id,a.actor_type,a.action,a.old_status,a.new_status,
                    a.details,a.created_at,u.name AS dealer_name
             FROM nv_dealer_audit a
             JOIN nv_dealer_accounts d ON d.id=a.dealer_id
             JOIN users u ON u.id=d.user_id
             ORDER BY a.id DESC LIMIT 40'
        );
    }

    public static function adminList(): array
    {
        if(!self::ready())return [];
        return Database::getInstance()->fetchAll(
          'SELECT d.*,u.name,u.email,(SELECT COUNT(*) FROM nv_dealer_referral_events e WHERE e.dealer_id=d.id) AS referral_visits
           FROM nv_dealer_accounts d INNER JOIN users u ON u.id=d.user_id ORDER BY FIELD(d.status,\'pending\',\'approved\',\'suspended\',\'rejected\'),d.created_at DESC LIMIT 200'
        );
    }
}
