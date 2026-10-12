<?php
namespace App\Services;

use App\Core\Database;

final class DiscountService
{
    private static bool $ready=false;

    public static function ensureSchema(): void
    {
        if(self::$ready) return;
        $db=Database::getInstance();

        $db->query("
            CREATE TABLE IF NOT EXISTS discount_campaigns (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(160) NOT NULL,
                code VARCHAR(64) DEFAULT NULL,
                kind ENUM('coupon','automatic') NOT NULL DEFAULT 'coupon',
                discount_type ENUM('percentage','fixed') NOT NULL,
                discount_value DECIMAL(10,2) NOT NULL,
                starts_at DATETIME DEFAULT NULL,
                ends_at DATETIME DEFAULT NULL,
                min_subtotal DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                max_discount DECIMAL(10,2) DEFAULT NULL,
                total_usage_limit INT UNSIGNED DEFAULT NULL,
                per_customer_limit INT UNSIGNED DEFAULT NULL,
                customer_id INT UNSIGNED DEFAULT NULL,
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                deleted_at DATETIME DEFAULT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uq_discount_campaign_code (code),
                KEY idx_discount_active (kind,is_active,starts_at,ends_at,deleted_at),
                KEY idx_discount_customer (customer_id,is_active)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        $db->query("
            CREATE TABLE IF NOT EXISTS discount_targets (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                campaign_id BIGINT UNSIGNED NOT NULL,
                target_type ENUM('package','category','script','script_category') NOT NULL,
                target_id INT UNSIGNED NOT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY uq_discount_target (campaign_id,target_type,target_id),
                KEY idx_discount_target_lookup (target_type,target_id,campaign_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        $db->query("
            CREATE TABLE IF NOT EXISTS discount_usages (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                campaign_id BIGINT UNSIGNED NOT NULL,
                order_id BIGINT UNSIGNED NOT NULL,
                customer_id INT UNSIGNED DEFAULT NULL,
                customer_email VARCHAR(190) DEFAULT NULL,
                discount_amount DECIMAL(10,2) NOT NULL,
                final_amount DECIMAL(10,2) NOT NULL,
                used_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY uq_discount_usage_order (order_id),
                KEY idx_discount_usage_campaign (campaign_id,used_at),
                KEY idx_discount_usage_customer (campaign_id,customer_id,customer_email)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        self::$ready=true;
    }

    public static function all(bool $includeDeleted=false): array
    {
        self::ensureSchema();
        $where=$includeDeleted?'1=1':'c.deleted_at IS NULL';
        $rows=Database::getInstance()->fetchAll(
            "SELECT c.*,
                    (SELECT COUNT(*) FROM discount_usages u WHERE u.campaign_id=c.id) AS usage_count
             FROM discount_campaigns c
             WHERE {$where}
             ORDER BY c.is_active DESC,c.id DESC"
        );
        foreach($rows as &$row){
            $row['targets']=self::targets((int)$row['id']);
            $row['target_labels']=self::targetLabels($row['targets']);
        }
        unset($row);
        return $rows;
    }

    public static function find(int $id): ?array
    {
        self::ensureSchema();
        $row=Database::getInstance()->fetch(
            "SELECT * FROM discount_campaigns WHERE id=? AND deleted_at IS NULL",[$id]
        );
        if(!$row) return null;
        $row['targets']=self::targets($id);
        return $row;
    }

    public static function targets(int $campaignId): array
    {
        self::ensureSchema();
        return Database::getInstance()->fetchAll(
            "SELECT target_type,target_id FROM discount_targets WHERE campaign_id=? ORDER BY id",[$campaignId]
        );
    }

    public static function save(array $data,array $targets,?int $id=null): int
    {
        self::ensureSchema();
        $db=Database::getInstance();
        $code=strtoupper(trim((string)($data['code']??'')));
        $kind=in_array($data['kind']??'',['coupon','automatic'],true)?$data['kind']:'coupon';
        if($kind==='coupon' && $code==='') throw new \InvalidArgumentException('Kupon kodu zorunludur.');
        if($kind==='automatic') $code=null;
        if($code!==null && !preg_match('/^[A-Z0-9_-]{3,64}$/',$code))
            throw new \InvalidArgumentException('Kupon kodu 3-64 karakter, harf/rakam/_/- biçiminde olmalıdır.');

        if($code!==null){
            $exists=$db->fetch("SELECT id FROM discount_campaigns WHERE code=? AND deleted_at IS NULL AND id<>? LIMIT 1",[$code,$id??0]);
            if($exists) throw new \InvalidArgumentException('Bu kupon kodu zaten kullanılıyor.');
        }

        $record=[
            'name'=>mb_substr(trim((string)($data['name']??'')),0,160),
            'code'=>$code,
            'kind'=>$kind,
            'discount_type'=>in_array($data['discount_type']??'',['percentage','fixed'],true)?$data['discount_type']:'fixed',
            'discount_value'=>max(0,round((float)($data['discount_value']??0),2)),
            'starts_at'=>self::dateTime($data['starts_at']??null),
            'ends_at'=>self::dateTime($data['ends_at']??null),
            'min_subtotal'=>max(0,round((float)($data['min_subtotal']??0),2)),
            'max_discount'=>($data['max_discount']??'')===''?null:max(0,round((float)$data['max_discount'],2)),
            'total_usage_limit'=>($data['total_usage_limit']??'')===''?null:max(1,(int)$data['total_usage_limit']),
            'per_customer_limit'=>($data['per_customer_limit']??'')===''?null:max(1,(int)$data['per_customer_limit']),
            'customer_id'=>($data['customer_id']??'')===''?null:max(1,(int)$data['customer_id']),
            'is_active'=>!empty($data['is_active'])?1:0,
            'deleted_at'=>null,
        ];
        if($record['name']==='') throw new \InvalidArgumentException('Kampanya adı zorunludur.');
        if($record['discount_value']<=0) throw new \InvalidArgumentException('İndirim değeri sıfırdan büyük olmalıdır.');
        if($record['discount_type']==='percentage' && $record['discount_value']>100)
            throw new \InvalidArgumentException('Yüzde indirim 100 değerini aşamaz.');
        if($record['starts_at'] && $record['ends_at'] && $record['starts_at']>$record['ends_at'])
            throw new \InvalidArgumentException('Bitiş tarihi başlangıç tarihinden önce olamaz.');

        $pdo=$db->getPdo();
        $pdo->beginTransaction();
        try{
            if($id){
                if(!self::find($id)) throw new \InvalidArgumentException('Kupon bulunamadı.');
                $db->update('discount_campaigns',$record,'id=?',[$id]);
            }else{
                $id=$db->insert('discount_campaigns',$record);
            }
            $db->delete('discount_targets','campaign_id=?',[$id]);
            foreach(self::sanitizeTargets($targets) as $target){
                $db->insert('discount_targets',[
                    'campaign_id'=>$id,
                    'target_type'=>$target['type'],
                    'target_id'=>$target['id'],
                ]);
            }
            $pdo->commit();
            return $id;
        }catch(\Throwable $e){
            if($pdo->inTransaction()) $pdo->rollBack();
            throw $e;
        }
    }

    public static function toggle(int $id): void
    {
        self::ensureSchema();
        $row=self::find($id);
        if(!$row) return;
        Database::getInstance()->update(
            'discount_campaigns',['is_active'=>(int)!((int)$row['is_active'])],'id=?',[$id]
        );
    }

    public static function delete(int $id): void
    {
        self::ensureSchema();
        Database::getInstance()->update(
            'discount_campaigns',['is_active'=>0,'deleted_at'=>date('Y-m-d H:i:s')],'id=?',[$id]
        );
    }

    public static function quotePackages(array $items,?array $user=null,string $code=''): array
    {
        self::ensureSchema();
        $subtotal=self::subtotal($items);
        $code=strtoupper(trim($code));

        if($code!==''){
            $campaign=Database::getInstance()->fetch(
                "SELECT * FROM discount_campaigns
                 WHERE code=? AND kind='coupon' AND deleted_at IS NULL LIMIT 1",[$code]
            );
            if(!$campaign) return self::emptyQuote($subtotal,$code,'Kupon kodu bulunamadı.');
            return self::evaluate($campaign,$items,$user,$code,false);
        }

        $best=self::emptyQuote($subtotal,'',null);
        $campaigns=Database::getInstance()->fetchAll(
            "SELECT * FROM discount_campaigns
             WHERE kind='automatic' AND is_active=1 AND deleted_at IS NULL
               AND (starts_at IS NULL OR starts_at<=NOW())
               AND (ends_at IS NULL OR ends_at>=NOW())
             ORDER BY id DESC"
        );
        foreach($campaigns as $campaign){
            $quote=self::evaluate($campaign,$items,$user,'',false);
            if(!empty($quote['valid']) && $quote['discount_amount']>$best['discount_amount']) $best=$quote;
        }
        return $best;
    }

    /**
     * Re-validates the selected campaign under a row lock inside OrderService's transaction.
     * This prevents a last-usage coupon from being consumed twice during concurrent checkout.
     */
    public static function lockedQuoteForOrder(
        array $items,?array $user,?int $campaignId,string $code=''
    ): array {
        self::ensureSchema();
        $subtotal=self::subtotal($items);
        if(!$campaignId) return self::emptyQuote($subtotal,'',null);

        $campaign=Database::getInstance()->fetch(
            "SELECT * FROM discount_campaigns WHERE id=? AND deleted_at IS NULL FOR UPDATE",[$campaignId]
        );
        if(!$campaign) throw new \RuntimeException('İndirim kampanyası artık kullanılamıyor.');

        $expected=strtoupper(trim($code));
        if(($campaign['kind']??'')==='coupon' && strtoupper((string)$campaign['code'])!==$expected)
            throw new \RuntimeException('Kupon kodu doğrulanamadı.');

        $quote=self::evaluate($campaign,$items,$user,$expected,true);
        if(empty($quote['valid'])) throw new \RuntimeException($quote['error']?:'Kupon artık kullanılamıyor.');
        return $quote;
    }

    public static function recordUsage(int $orderId,?array $user,array $quote): void
    {
        if(empty($quote['campaign_id']) || ($quote['discount_amount']??0)<=0) return;
        Database::getInstance()->insert('discount_usages',[
            'campaign_id'=>(int)$quote['campaign_id'],
            'order_id'=>$orderId,
            'customer_id'=>isset($user['id'])?(int)$user['id']:null,
            'customer_email'=>mb_substr(trim((string)($user['email']??'')),0,190),
            'discount_amount'=>round((float)$quote['discount_amount'],2),
            'final_amount'=>round((float)$quote['final_total'],2),
        ]);
    }

    public static function publicCoupons(): array
    {
        self::ensureSchema();
        $rows=Database::getInstance()->fetchAll(
            "SELECT c.* FROM discount_campaigns c
             WHERE c.kind='coupon' AND c.code IS NOT NULL AND c.code<>''
               AND c.customer_id IS NULL AND c.is_active=1 AND c.deleted_at IS NULL
               AND (c.starts_at IS NULL OR c.starts_at<=NOW())
               AND (c.ends_at IS NULL OR c.ends_at>=NOW())
               AND (c.total_usage_limit IS NULL OR
                    (SELECT COUNT(*) FROM discount_usages u WHERE u.campaign_id=c.id)<c.total_usage_limit)
             ORDER BY c.id DESC"
        );
        $out=[];
        foreach($rows as $row){
            $targets=self::targets((int)$row['id']);
            $out[]=[
                'id'=>(int)$row['id'],
                'title'=>$row['name'],
                'code'=>$row['code'],
                'discount'=>self::discountLabel($row),
                'description'=>self::targetSummary($targets),
            ];
        }
        return $out;
    }

    public static function targetOptions(): array
    {
        self::ensureSchema();
        $db=Database::getInstance();
        $out=['package'=>[],'category'=>[],'script'=>[],'script_category'=>[]];

        try{
            $out['package']=$db->fetchAll(
                "SELECT id,name,slug FROM packages ORDER BY name ASC"
            );
            $out['category']=$db->fetchAll(
                "SELECT id,name,slug FROM categories ORDER BY name ASC"
            );
        }catch(\Throwable $e){}

        try{
            if(NetveraBridgeService::ready()){
                $out['script']=$db->fetchAll(
                    "SELECT legacy_id AS id,name,slug FROM nv_legacy_script_products ORDER BY name ASC"
                );
                $out['script_category']=$db->fetchAll(
                    "SELECT legacy_id AS id,name,slug FROM nv_legacy_script_categories ORDER BY name ASC"
                );
            }else{
                foreach(NetveraBridgeService::all() as $row)
                    $out['script'][]=['id'=>$row['legacy_id'],'name'=>$row['name'],'slug'=>$row['slug']];
                foreach(NetveraBridgeService::categories() as $row)
                    $out['script_category'][]=['id'=>$row['legacy_id'],'name'=>$row['name'],'slug'=>$row['slug']];
            }
        }catch(\Throwable $e){}

        return $out;
    }

    private static function evaluate(
        array $campaign,array $items,?array $user,string $code,bool $locked
    ): array {
        $subtotal=self::subtotal($items);
        $base=self::emptyQuote($subtotal,$code,null);
        $base['campaign_id']=(int)$campaign['id'];
        $base['campaign_name']=$campaign['name'];
        $base['kind']=$campaign['kind'];
        $base['code']=$campaign['code']??$code;
        $base['discount_type']=$campaign['discount_type'];
        $base['discount_value']=(float)$campaign['discount_value'];

        $now=date('Y-m-d H:i:s');
        if(!(int)$campaign['is_active']) return self::invalid($base,'Bu kupon pasif.');
        if(!empty($campaign['starts_at']) && $campaign['starts_at']>$now) return self::invalid($base,'Bu kampanya henüz başlamadı.');
        if(!empty($campaign['ends_at']) && $campaign['ends_at']<$now) return self::invalid($base,'Bu kampanyanın süresi dolmuş.');

        $userId=(int)($user['id']??0);
        $email=mb_substr(trim((string)($user['email']??'')),0,190);
        if(!empty($campaign['customer_id']) && (int)$campaign['customer_id']!==$userId)
            return self::invalid($base,'Bu kupon farklı bir müşteri hesabına tanımlı.');

        $db=Database::getInstance();
        if(!empty($campaign['total_usage_limit'])){
            $used=$db->fetch("SELECT COUNT(*) AS c FROM discount_usages WHERE campaign_id=?",[(int)$campaign['id']]);
            if((int)($used['c']??0)>=(int)$campaign['total_usage_limit'])
                return self::invalid($base,'Bu kuponun toplam kullanım limiti dolmuş.');
        }
        if(!empty($campaign['per_customer_limit']) && ($userId>0 || $email!=='')){
            $where=$userId>0?'customer_id=?':'customer_email=?';
            $who=$userId>0?$userId:$email;
            $used=$db->fetch(
                "SELECT COUNT(*) AS c FROM discount_usages WHERE campaign_id=? AND {$where}",
                [(int)$campaign['id'],$who]
            );
            if((int)($used['c']??0)>=(int)$campaign['per_customer_limit'])
                return self::invalid($base,'Bu kuponu hesabınız için kullanma limitine ulaştınız.');
        }

        $targets=self::targets((int)$campaign['id']);
        $eligible=0.0;
        foreach($items as $item){
            if(self::matchesPackageItem($item,$targets))
                $eligible+=max(0,(float)($item['line_total']??0));
        }
        $eligible=round($eligible,2);
        $base['eligible_subtotal']=$eligible;
        if($eligible<=0) return self::invalid($base,'Bu kupon sepetinizdeki ürünlere uygulanmıyor.');
        if($eligible<(float)$campaign['min_subtotal'])
            return self::invalid($base,'Kupon için minimum uygun ürün tutarı '.money((float)$campaign['min_subtotal']).'.');

        $discount=$campaign['discount_type']==='percentage'
            ? $eligible*((float)$campaign['discount_value']/100)
            : min($eligible,(float)$campaign['discount_value']);
        if($campaign['max_discount']!==null && $campaign['max_discount']!=='')
            $discount=min($discount,(float)$campaign['max_discount']);
        $discount=round(max(0,min($eligible,$discount)),2);

        $base['valid']=$discount>0;
        $base['discount_amount']=$discount;
        $base['final_total']=round(max(0,$subtotal-$discount),2);
        $base['error']=$discount>0?null:'Bu kampanya için indirim hesaplanamadı.';
        return $base;
    }

    private static function matchesPackageItem(array $item,array $targets): bool
    {
        if(!$targets) return true;
        foreach($targets as $target){
            $type=$target['target_type'];
            $id=(int)$target['target_id'];
            if($type==='package' && (int)($item['id']??0)===$id) return true;
            if($type==='category' && (int)($item['category_id']??0)===$id) return true;
        }
        return false;
    }

    private static function subtotal(array $items): float
    {
        $sum=0.0;
        foreach($items as $item) $sum+=max(0,(float)($item['line_total']??0));
        return round($sum,2);
    }

    private static function emptyQuote(float $subtotal,string $code,?string $error): array
    {
        return [
            'valid'=>$error===null && $code==='',
            'campaign_id'=>null,'campaign_name'=>null,'kind'=>null,
            'code'=>$code,'discount_type'=>null,'discount_value'=>0,
            'eligible_subtotal'=>0,'subtotal'=>$subtotal,
            'discount_amount'=>0,'final_total'=>$subtotal,'error'=>$error,
        ];
    }

    private static function invalid(array $quote,string $message): array
    {
        $quote['valid']=false;
        $quote['discount_amount']=0;
        $quote['final_total']=$quote['subtotal'];
        $quote['error']=$message;
        return $quote;
    }

    private static function sanitizeTargets(array $targets): array
    {
        $allowed=['package','category','script','script_category'];
        $out=[];$seen=[];
        foreach($targets as $raw){
            if(is_string($raw) && str_contains($raw,':')) [$type,$id]=explode(':',$raw,2);
            elseif(is_array($raw)){ $type=$raw['type']??'';$id=$raw['id']??0; }
            else continue;
            $type=(string)$type;$id=(int)$id;
            if(!in_array($type,$allowed,true)||$id<=0) continue;
            $key=$type.':'.$id;
            if(isset($seen[$key])) continue;
            $seen[$key]=true;
            $out[]=['type'=>$type,'id'=>$id];
        }
        return $out;
    }

    private static function targetLabels(array $targets): array
    {
        $options=self::targetOptions();
        $maps=[];
        foreach($options as $type=>$rows) foreach($rows as $r)
            $maps[$type][(int)$r['id']]=$r['name'];
        $labels=[];
        foreach($targets as $target){
            $type=$target['target_type'];$id=(int)$target['target_id'];
            $prefix=['package'=>'Paket','category'=>'Kategori','script'=>'Yazılım','script_category'=>'Yazılım Kategorisi'][$type]??$type;
            $labels[]=$prefix.': '.($maps[$type][$id]??('#'.$id));
        }
        return $labels;
    }

    private static function targetSummary(array $targets): string
    {
        if(!$targets) return 'Tüm uygun ürünlerde geçerli';
        $labels=self::targetLabels($targets);
        return implode(' • ',array_slice($labels,0,3)).(count($labels)>3?' +'.(count($labels)-3):'');
    }

    public static function discountLabel(array $campaign): string
    {
        if(($campaign['discount_type']??'')==='percentage')
            return '%'.rtrim(rtrim(number_format((float)$campaign['discount_value'],2,'.',''),'0'),'.').' indirim';
        return money((float)$campaign['discount_value']).' indirim';
    }

    private static function dateTime($value): ?string
    {
        $value=trim((string)$value);
        if($value==='') return null;
        $time=strtotime($value);
        return $time?date('Y-m-d H:i:s',$time):null;
    }
}
