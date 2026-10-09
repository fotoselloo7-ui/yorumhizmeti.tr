<?php
namespace App\Services;

use App\Core\Database;

/**
 * Safe payment-gated fulfillment outbox. An uncertain POST never auto-retries:
 * human reconciliation prevents duplicate upstream charges/deliveries.
 */
final class SmmFulfillmentService
{
    public static function validateLine(int $packageId, array $fields, int $cartQty=1): void
    {
        $m = SmmCatalogService::mapping($packageId);
        if ($m === null) return;
        if (!$m['enabled'] || !$m['is_available'] || !$m['provider_active']
            || (int)$m['fulfillment_quantity'] < (int)$m['min_quantity']
            || (int)$m['fulfillment_quantity'] > (int)$m['max_quantity']
            || max(1,$cartQty) * (int)$m['fulfillment_quantity'] > (int)$m['max_quantity']
            || strcasecmp((string)$m['service_type'],'Default') !== 0) {
            throw new \RuntimeException('Seçtiğiniz hizmet şu an siparişe kapalı. Lütfen sepetinizden çıkarın.');
        }
        $link = self::findLink($m['field_key'], $fields);
        $parts = parse_url($link);
        if (!filter_var($link,FILTER_VALIDATE_URL) || !$parts
            || !in_array(strtolower((string)($parts['scheme'] ?? '')),['https'],true)
            || empty($parts['host']) || isset($parts['user']) || isset($parts['pass'])
            || strlen($link) > 2048) {
            throw new \RuntimeException('Hizmet için geçerli bir HTTPS profil/gönderi bağlantısı girin.');
        }
    }

    private static function findLink(string $fieldKey, array $fields): string
    {
        foreach ($fields as $field) {
            if (($field['field_key'] ?? '') === $fieldKey) {
                return trim((string)($field['value'] ?? $field['field_value'] ?? ''));
            }
        }
        return '';
    }

    /** Snapshot upstream service at order creation; later catalog changes cannot redirect orders. */
    public static function attachOrderItem(int $orderId, int $orderItemId, int $packageId, int $cartQty, array $fields): void
    {
        $m = SmmCatalogService::mapping($packageId);
        if (!$m) return;
        self::validateLine($packageId,$fields,$cartQty);
        Database::getInstance()->insert('smm_order_jobs',[
            'order_id'=>$orderId,'order_item_id'=>$orderItemId,'package_id'=>$packageId,
            'provider_id'=>(int)$m['provider_id'],'external_service_id'=>$m['external_service_id'],
            'quantity'=>(int)$m['fulfillment_quantity'] * max(1,$cartQty),
            'target_link'=>self::findLink($m['field_key'],$fields),'state'=>'queued',
        ]);
    }

    private static function event(int $id, string $type, string $message): void
    {
        Database::getInstance()->insert('smm_job_events',[
            'job_id'=>$id,'event_type'=>$type,'message'=>mb_substr($message,0,255)
        ]);
    }

    public static function jobs(int $orderId=0): array
    {
        if (!SmmCatalogService::installed()) return [];
        $where = $orderId > 0 ? 'WHERE j.order_id=?' : '';
        return Database::getInstance()->fetchAll(
            'SELECT j.*,p.name AS provider_name,o.order_number,o.payment_status
             FROM smm_order_jobs j JOIN smm_providers p ON p.id=j.provider_id
             JOIN orders o ON o.id=j.order_id '.$where.' ORDER BY j.id DESC LIMIT 200',
             $orderId > 0 ? [$orderId] : []
        );
    }

    public static function history(int $jobId): array
    {
        return Database::getInstance()->fetchAll(
            'SELECT * FROM smm_job_events WHERE job_id=? ORDER BY id DESC LIMIT 30', [$jobId]
        );
    }

    public static function process(int $limit=20): array
    {
        if (!SmmCatalogService::installed()) return ['sent'=>0,'updated'=>0,'review'=>0];
        $db = Database::getInstance();
        $limit = max(1,min(50,$limit));
        // A worker crash mid-POST is ambiguous. Never resend that request automatically.
        $stale = $db->fetchAll(
            "SELECT id FROM smm_order_jobs WHERE state='sending' AND updated_at < DATE_SUB(NOW(),INTERVAL 10 MINUTE) LIMIT 100"
        );
        foreach ($stale as $row) {
            $db->query("UPDATE smm_order_jobs SET state='manual_review',
                last_error='Gönderim kesildi; tedarikçide sipariş oluşmuş olabilir.' WHERE id=? AND state='sending'",[(int)$row['id']]);
        }
        $queued = $db->fetchAll(
            "SELECT j.* FROM smm_order_jobs j JOIN orders o ON o.id=j.order_id
             WHERE j.state='queued' AND o.payment_status='paid'
               AND o.order_status NOT IN ('cancelled','refunded')
             ORDER BY j.id ASC LIMIT ".$limit
        );
        $sent=0; $review=0; $updated=0;
        foreach ($queued as $job) {
            $id=(int)$job['id'];
            $claimed=$db->query("UPDATE smm_order_jobs j JOIN orders o ON o.id=j.order_id
                SET j.state='sending',j.attempts=j.attempts+1
                WHERE j.id=? AND j.state='queued' AND o.payment_status='paid'
                  AND o.order_status NOT IN ('cancelled','refunded')",[$id])->rowCount();
            if ($claimed !== 1) continue;
            try {
                $result=SmmCatalogService::api((int)$job['provider_id'])->call('add',[
                    'service'=>$job['external_service_id'],
                    'link'=>$job['target_link'],'quantity'=>(int)$job['quantity'],
                ]);
                $external=trim((string)($result['order'] ?? ''));
                if ($external === '' || strlen($external)>120) throw new \RuntimeException('Sipariş numarası dönmedi. Elle kontrol gerekli.');
                $db->update('smm_order_jobs',[
                    'state'=>'submitted','upstream_order_id'=>$external,'last_error'=>null,
                    'provider_status'=>'Pending','last_checked_at'=>date('Y-m-d H:i:s')
                ],'id=? AND state=?',[$id,'sending']);
                self::event($id,'submitted','Tedarikçiye sipariş başarıyla iletildi.');
                $sent++;
                $order=$db->fetch('SELECT order_status FROM orders WHERE id=?',[(int)$job['order_id']]);
                if ($order && $order['order_status']==='paid') {
                    (new OrderService())->updateStatus((int)$job['order_id'],'processing','Hizmet işleme alındı.','system');
                }
            } catch (\Throwable $e) {
                $db->update('smm_order_jobs',[
                    'state'=>'manual_review','last_error'=>mb_substr($e->getMessage(),0,255)
                ],'id=? AND state=?',[$id,'sending']);
                self::event($id,'review','Gönderim sonucu belirsiz. Otomatik tekrar gönderim kapalı.');
                $review++;
                error_log('SMM job '.$id.' requires reconciliation: '.get_class($e));
            }
        }

        $poll = $db->fetchAll(
            "SELECT id,provider_id,upstream_order_id,order_id FROM smm_order_jobs
             WHERE state IN ('submitted','in_progress') AND upstream_order_id IS NOT NULL
               AND (last_checked_at IS NULL OR last_checked_at < DATE_SUB(NOW(),INTERVAL 10 MINUTE))
             ORDER BY last_checked_at ASC LIMIT ".$limit
        );
        foreach ($poll as $job) {
            $id=(int)$job['id'];
            try {
                $state=SmmCatalogService::api((int)$job['provider_id'])->call('status',['order'=>$job['upstream_order_id']]);
                $upstream=trim((string)($state['status'] ?? ''));
                $normalized=mb_strtolower($upstream);
                $next=match (true) {
                    in_array($normalized,['completed','complete'],true) => 'completed',
                    in_array($normalized,['partial'],true) => 'partial',
                    in_array($normalized,['canceled','cancelled'],true) => 'cancelled',
                    in_array($normalized,['failed','error'],true) => 'failed',
                    default => 'in_progress',
                };
                $db->update('smm_order_jobs',[
                    'state'=>$next,'provider_status'=>mb_substr($upstream,0,80),
                    'remains'=>isset($state['remains'])?max(0,(int)$state['remains']):null,
                    'last_checked_at'=>date('Y-m-d H:i:s'),'last_error'=>null
                ],'id=?',[$id]);
                if (in_array($next,['completed','partial','cancelled','failed'],true)) {
                    self::event($id,'status','Tedarikçi durumu: '.$next);
                }
                self::refreshInternalOrder((int)$job['order_id']);
                $updated++;
            } catch (\Throwable $e) {
                $db->update('smm_order_jobs',[
                    'last_checked_at'=>date('Y-m-d H:i:s'),
                    'last_error'=>mb_substr($e->getMessage(),0,255)
                ],'id=?',[$id]);
            }
        }
        return ['sent'=>$sent,'updated'=>$updated,'review'=>$review];
    }

    private static function refreshInternalOrder(int $orderId): void
    {
        $db=Database::getInstance();
        $row=$db->fetch(
            "SELECT COUNT(*) AS total, SUM(j.state='completed') AS complete
             FROM smm_order_jobs j WHERE j.order_id=?",[$orderId]);
        if (!$row || (int)$row['total']===0 || (int)$row['complete']!==(int)$row['total']) return;
        // A mixed basket may contain manually-delivered items, do not auto-close that order.
        $unmapped=$db->fetch(
            'SELECT COUNT(*) AS cnt FROM order_items i LEFT JOIN smm_order_jobs j ON j.order_item_id=i.id WHERE i.order_id=? AND j.id IS NULL',
            [$orderId]
        );
        $order=$db->fetch('SELECT order_status,payment_status FROM orders WHERE id=?',[$orderId]);
        if ((int)($unmapped['cnt']??0)===0 && ($order['payment_status']??'')==='paid'
            && in_array($order['order_status']??'',['paid','processing','preparing'],true)) {
            (new OrderService())->updateStatus($orderId,'completed','Hizmet tamamlandı.','system');
        }
    }

    /**
     * Only a super-admin can resolve an ambiguous submission after checking the
     * supplier dashboard. Requeue explicitly asserts NO upstream order exists.
     */
    public static function reconcile(int $jobId, string $mode, string $externalId='', bool $verifiedAbsent=false): void
    {
        $db=Database::getInstance();
        $job=$db->fetch('SELECT * FROM smm_order_jobs WHERE id=?',[$jobId]);
        if (!$job || $job['state']!=='manual_review') {
            throw new \RuntimeException('Yalnızca manuel kontroldeki siparişler uzlaştırılabilir.');
        }
        if ($mode==='matched') {
            $externalId=trim($externalId);
            if (!preg_match('/^[a-zA-Z0-9_-]{1,120}$/',$externalId)) {
                throw new \RuntimeException('Tedarikçideki gerçek sipariş numarasını girin.');
            }
            $db->update('smm_order_jobs',[
                'upstream_order_id'=>$externalId,'state'=>'submitted',
                'last_error'=>null,'last_checked_at'=>null
            ],'id=? AND state=?',[$jobId,'manual_review']);
            self::event($jobId,'reconciled','Tedarikçideki sipariş doğrulanıp numarası eşleştirildi.');
            return;
        }
        if ($mode==='retry' && $verifiedAbsent) {
            // Human has confirmed absence at the supplier. Still queued rather than sending in request.
            $db->update('smm_order_jobs',[
                'state'=>'queued','upstream_order_id'=>null,
                'last_error'=>null,'last_checked_at'=>null
            ],'id=? AND state=?',[$jobId,'manual_review']);
            self::event($jobId,'requeued','Yönetici tedarikçide sipariş olmadığını doğrulayıp tekrar kuyruğa aldı.');
            return;
        }
        throw new \RuntimeException('Güvenli işlem için tedarikçi kontrolünü ve seçiminizi doğrulayın.');
    }

    public static function refill(int $jobId): void
    {
        $db=Database::getInstance();
        $job=$db->fetch(
            'SELECT j.*,s.can_refill FROM smm_order_jobs j JOIN smm_services s
             ON s.provider_id=j.provider_id AND s.external_service_id=j.external_service_id WHERE j.id=?',[$jobId]);
        if (!$job || !$job['can_refill'] || !$job['upstream_order_id']
            || !in_array($job['state'],['completed','partial'],true)) {
            throw new \RuntimeException('Bu sipariş yenileme işlemine uygun değil.');
        }
        $result=SmmCatalogService::api((int)$job['provider_id'])->call('refill',['order'=>$job['upstream_order_id']]);
        self::event($jobId,'refill','Yenileme talebi oluşturuldu: '.mb_substr((string)($result['refill']??'Beklemede'),0,80));
    }

    public static function cancel(int $jobId): void
    {
        $db=Database::getInstance();
        $job=$db->fetch(
            'SELECT j.*,s.can_cancel FROM smm_order_jobs j JOIN smm_services s
             ON s.provider_id=j.provider_id AND s.external_service_id=j.external_service_id WHERE j.id=?',[$jobId]);
        if (!$job || !$job['can_cancel'] || !$job['upstream_order_id']
            || !in_array($job['state'],['submitted','in_progress'],true)) {
            throw new \RuntimeException('Bu sipariş tedarikçi tarafından iptale uygun değil.');
        }
        $result=SmmCatalogService::api((int)$job['provider_id'])->call('cancel',['orders'=>$job['upstream_order_id']]);
        $first=$result[0]??[];
        if (!is_array($first) || ($first['cancel']??null)!==1) {
            throw new \RuntimeException('İptal talebi kabul edilmedi; tedarikçi durumunu kontrol edin.');
        }
        self::event($jobId,'cancel_request','Tedarikçiye iptal talebi iletildi; müşteri ödemesi otomatik iade edilmedi.');
        // Do not assume cancellation completed or initiate refunds.
    }
}
