<?php
namespace App\Services;

use App\Core\Database;

/** Private catalog, provider and product-to-fulfillment mapping. */
final class SmmCatalogService
{
    public static function installed(): bool
    {
        try {
            $row = Database::getInstance()->fetch(
                "SELECT COUNT(*) AS cnt FROM information_schema.tables
                 WHERE table_schema=DATABASE() AND table_name IN
                 ('smm_providers','smm_services','smm_package_links','smm_order_jobs','smm_job_events')"
            );
            return (int)($row['cnt'] ?? 0) === 5;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public static function install(): void
    {
        $sql = file_get_contents(BASE_PATH . '/database/migrations/smm-multi-provider-v1.sql');
        if ($sql === false) throw new \RuntimeException('Veritabanı kurulum dosyası okunamadı.');
        $db = Database::getInstance();
        // Strip SQL comments BEFORE splitting: comments may themselves contain semicolons.
        $sql = preg_replace('/^\s*--[^\r\n]*(?:\r?\n|$)/m', '', $sql);
        foreach (explode(';', $sql) as $statement) {
            if (trim($statement) !== '') $db->getPdo()->exec(trim($statement));
        }
    }

    public static function providers(): array
    {
        return self::installed() ? Database::getInstance()->fetchAll(
            'SELECT id,name,endpoint,currency,is_active,last_synced_at,last_error,created_at FROM smm_providers ORDER BY id DESC'
        ) : [];
    }

    public static function saveProvider(array $input): int
    {
        if (!self::installed()) throw new \RuntimeException('Önce SMM modülü veritabanını kurun.');
        $db = Database::getInstance();
        $id = (int)($input['id'] ?? 0);
        $name = trim((string)($input['name'] ?? ''));
        $endpoint = SmmApiClient::validateEndpoint((string)($input['endpoint'] ?? ''));
        $key = trim((string)($input['api_key'] ?? ''));
        $currency = strtoupper(trim((string)($input['currency'] ?? 'USD')));
        if (mb_strlen($name) < 2 || mb_strlen($name) > 120) throw new \RuntimeException('Tedarikçi adı 2-120 karakter olmalı.');
        if (!in_array($currency, ['USD','TRY','EUR','GBP'], true)) throw new \RuntimeException('Desteklenmeyen tedarikçi para birimi.');
        $data = ['name'=>$name,'endpoint'=>$endpoint,'currency'=>$currency,'is_active'=>!empty($input['is_active']) ? 1 : 0];
        if ($key !== '') $data['api_key_enc'] = SmmApiClient::encrypt($key);
        if ($id > 0) {
            if (!$db->fetch('SELECT id FROM smm_providers WHERE id=?', [$id])) throw new \RuntimeException('Tedarikçi bulunamadı.');
            $db->update('smm_providers', $data, 'id=?', [$id]);
            return $id;
        }
        if ($key === '') throw new \RuntimeException('Yeni tedarikçi için API anahtarı gerekli.');
        return $db->insert('smm_providers', $data);
    }

    public static function api(int $providerId): SmmApiClient
    {
        $p = Database::getInstance()->fetch('SELECT * FROM smm_providers WHERE id=? AND is_active=1', [$providerId]);
        if (!$p) throw new \RuntimeException('Tedarikçi bağlantısı pasif veya bulunamadı.');
        return new SmmApiClient($p['endpoint'], SmmApiClient::decrypt($p['api_key_enc']));
    }

    public static function sync(int $providerId): int
    {
        $db = Database::getInstance();
        $client = self::api($providerId);
        $list = $client->call('services');
        if (!array_is_list($list) || $list === []) throw new \RuntimeException('Servis listesi boş veya API formatı beklenenden farklı.');
        $pdo = $db->getPdo();
        $count = 0;
        try {
            $pdo->beginTransaction();
            // Only a successful, non-empty fetch can mark unseen services unavailable.
            $db->query('UPDATE smm_services SET is_available=0 WHERE provider_id=?', [$providerId]);
            foreach ($list as $row) {
                if (!is_array($row) || !isset($row['service'])) continue;
                $externalId = trim((string)$row['service']);
                if ($externalId === '' || strlen($externalId) > 80 || !preg_match('/^[a-zA-Z0-9_-]+$/', $externalId)) continue;
                $minimum = max(1, (int)($row['min'] ?? 1));
                $maximum = max($minimum, (int)($row['max'] ?? $minimum));
                $rate = max(0, min(9999999999.0, (float)($row['rate'] ?? 0)));
                $db->query(
                    'INSERT INTO smm_services
                    (provider_id,external_service_id,category,name,service_type,rate_per_1000,min_quantity,max_quantity,can_refill,can_cancel,is_available,last_seen_at)
                    VALUES (?,?,?,?,?,?,?,?,?,?,1,NOW())
                    ON DUPLICATE KEY UPDATE category=VALUES(category),name=VALUES(name),service_type=VALUES(service_type),
                      rate_per_1000=VALUES(rate_per_1000),min_quantity=VALUES(min_quantity),
                      max_quantity=VALUES(max_quantity),can_refill=VALUES(can_refill),
                      can_cancel=VALUES(can_cancel),is_available=1,last_seen_at=NOW()',
                    [$providerId,$externalId,mb_substr((string)($row['category'] ?? ''),0,200),
                     mb_substr((string)($row['name'] ?? ''),0,300),
                     mb_substr((string)($row['type'] ?? 'Default'),0,70),$rate,$minimum,$maximum,
                     !empty($row['refill'])?1:0,!empty($row['cancel'])?1:0]
                );
                $count++;
            }
            if ($count === 0) throw new \RuntimeException('API geçerli servis numarası döndürmedi.');
            $db->update('smm_providers',['last_synced_at'=>date('Y-m-d H:i:s'),'last_error'=>null],'id=?',[$providerId]);
            $pdo->commit();
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            throw $e;
        }
        return $count;
    }

    public static function services(int $providerId=0, string $search='', int $limit=250): array
    {
        if (!self::installed()) return [];
        $where = ' WHERE 1=1';
        $params = [];
        if ($providerId > 0) { $where .= ' AND s.provider_id=?'; $params[] = $providerId; }
        if (trim($search) !== '') {
            $where .= ' AND (s.name LIKE ? OR s.category LIKE ? OR s.external_service_id=?)';
            $params[] = '%' . mb_substr(trim($search),0,80) . '%';
            $params[] = '%' . mb_substr(trim($search),0,80) . '%';
            $params[] = trim($search);
        }
        $limit = max(10,min(500,$limit));
        return Database::getInstance()->fetchAll(
            'SELECT s.*,p.name AS provider_name,p.currency,p.is_active AS provider_active
             FROM smm_services s INNER JOIN smm_providers p ON p.id=s.provider_id'
            .$where.' ORDER BY s.is_available DESC,s.category,s.name LIMIT '.$limit, $params
        );
    }

    public static function socialCategories(): array
    {
        return Database::getInstance()->fetchAll(
            "SELECT c.id,c.name,c.slug,c.parent_id FROM categories c
             LEFT JOIN categories parent ON parent.id=c.parent_id
             WHERE c.status='active' AND (c.name REGEXP 'Instagram|TikTok|YouTube|Facebook|Twitter|Threads|Telegram|Spotify|Twitch|Discord|LinkedIn|Sosyal'
                 OR parent.slug='sosyal-medya-hizmetleri')
             ORDER BY c.parent_id,c.name"
        );
    }

    public static function createCategory(string $name): int
    {
        $name = trim($name);
        if (mb_strlen($name) < 3 || mb_strlen($name) > 120) throw new \RuntimeException('Kategori adı 3-120 karakter olmalı.');
        $db = Database::getInstance();
        $parent = $db->fetch("SELECT id FROM categories WHERE slug='sosyal-medya-hizmetleri' LIMIT 1");
        if (!$parent) {
            $root = $db->insert('categories', [
                'name'=>'Sosyal Medya Hizmetleri','slug'=>'sosyal-medya-hizmetleri',
                'description'=>'Sosyal medya platformları için dijital hizmetler',
                'icon_key'=>'share-2','status'=>'active','sort_order'=>10
            ]);
            $parent = ['id'=>$root];
        }
        $slug = slugify($name);
        if ($slug === '') throw new \RuntimeException('Kategori adresi üretilemedi.');
        $existing = $db->fetch('SELECT id FROM categories WHERE slug=?', [$slug]);
        if ($existing) throw new \RuntimeException('Bu isimde kategori zaten mevcut; listeden seçebilirsiniz.');
        return $db->insert('categories', [
            'parent_id'=>$parent['id'],'name'=>$name,'slug'=>$slug,'description'=>'',
            'icon_key'=>'share-2','status'=>'active','sort_order'=>10
        ]);
    }

    public static function linkedPackages(): array
    {
        if (!self::installed()) return [];
        return Database::getInstance()->fetchAll(
            'SELECT m.*,p.name AS package_name,p.slug,p.price,p.status AS package_status,
             s.external_service_id,s.name AS source_service,pv.name AS provider_name
             FROM smm_package_links m JOIN packages p ON p.id=m.package_id
             JOIN smm_services s ON s.id=m.service_id JOIN smm_providers pv ON pv.id=s.provider_id
             ORDER BY m.updated_at DESC LIMIT 120'
        );
    }

    public static function publish(array $input): int
    {
        if (!self::installed()) throw new \RuntimeException('Önce SMM modülünü kurun.');
        $db = Database::getInstance();
        $serviceId = (int)($input['service_id'] ?? 0);
        $categoryId = (int)($input['category_id'] ?? 0);
        $service = $db->fetch(
            'SELECT s.*,p.is_active FROM smm_services s JOIN smm_providers p ON p.id=s.provider_id WHERE s.id=?', [$serviceId]
        );
        if (!$service || !$service['is_available'] || !$service['is_active'] || strcasecmp($service['service_type'],'Default') !== 0) {
            throw new \RuntimeException('Yalnızca açık ve Default türündeki servisler otomatik paketleştirilebilir.');
        }
        $categories = array_column(self::socialCategories(), 'id');
        if (!in_array($categoryId, array_map('intval', $categories), true)) {
            throw new \RuntimeException('Sosyal medya hizmetlerine ait bir kategori seçin.');
        }
        $qty = (int)($input['fulfillment_quantity'] ?? 0);
        if ($qty < (int)$service['min_quantity'] || $qty > (int)$service['max_quantity']) {
            throw new \RuntimeException('Tedarikçinin min/max aralığında bir teslim adedi girin.');
        }
        $name = trim((string)($input['name'] ?? ''));
        $short = trim((string)($input['short_description'] ?? ''));
        $description = trim((string)($input['description'] ?? ''));
        $price = (float)($input['price'] ?? 0);
        if (mb_strlen($name) < 5 || mb_strlen($name) > 300 || mb_strlen($short) < 10 || $description === '') {
            throw new \RuntimeException('Özgün paket adı, kısa açıklama ve detay açıklama zorunlu.');
        }
        if (!is_finite($price) || $price < 1 || $price > 99999999) throw new \RuntimeException('Geçerli bir TL satış fiyatı girin.');
        $slug = slugify($name);
        if (!$slug || $db->fetch('SELECT id FROM packages WHERE slug=?', [$slug])) {
            throw new \RuntimeException('Bu paket adı/adresi zaten kullanımda. Özgün başlık seçin.');
        }
        $data = [
            'category_id'=>$categoryId,'name'=>$name,'slug'=>$slug,
            'short_description'=>mb_substr($short,0,500),
            'description'=>$description,'price'=>round($price,2),
            'delivery_time'=>mb_substr(trim((string)($input['delivery_time'] ?? '')),0,100),
            'min_quantity'=>1,'max_quantity'=>1,
            'status'=>!empty($input['publish_now'])?'active':'inactive',
            'is_featured'=>0,
            'seo_title'=>mb_substr(trim((string)($input['seo_title'] ?? $name)),0,200),
            'seo_description'=>mb_substr(trim((string)($input['seo_description'] ?? $short)),0,500),
            'seo_focus_keyword'=>mb_substr(trim((string)($input['seo_focus_keyword'] ?? '')),0,100),
        ];
        $pdo = $db->getPdo();
        $pdo->beginTransaction();
        try {
            $id = $db->insert('packages',$data);
            $db->insert('package_fields',[
                'package_id'=>$id,'field_key'=>'smm_link','field_label'=>'Profil / Gönderi Bağlantısı',
                'field_type'=>'url','is_required'=>1,'placeholder'=>'https://','sort_order'=>0
            ]);
            $db->insert('smm_package_links',[
                'package_id'=>$id,'service_id'=>$serviceId,
                'fulfillment_quantity'=>$qty,'field_key'=>'smm_link','enabled'=>1
            ]);
            $pdo->commit();
            return $id;
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            throw $e;
        }
    }

    /** Import selected supplier services as separate private-linked PUBLIC brand packages. */
    public static function bulkPublish(array $data): array
    {
        if (!self::installed()) throw new \RuntimeException('SMM tablolarını önce kurun.');
        $ids=$data['service_ids']??[];
        if (!is_array($ids)) throw new \RuntimeException('Servis seçimi geçersiz.');
        $ids=array_values(array_unique(array_filter(array_map('intval',$ids),static fn($x)=>$x>0)));
        if (!$ids || count($ids)>80) throw new \RuntimeException('Toplu işlemde 1–80 servis seçebilirsiniz.');
        $short=trim((string)($data['short_description']??''));
        $detail=trim((string)($data['description']??''));
        if (mb_strlen($short)<10 || $detail==='') throw new \RuntimeException('Tüm paketler için özgün kısa ve detay açıklama zorunludur.');
        $features=trim((string)($data['highlight_lines']??''));
        $bullets=array_values(array_filter(array_map('trim',preg_split('/\r\n|\r|\n/',$features)?:[])));
        if (count($bullets)>12) throw new \RuntimeException('En fazla 12 kart özelliği kullanılabilir.');
        foreach($bullets as $b)if(mb_strlen(strip_tags($b))>115) throw new \RuntimeException('Özellik satırı 115 karakteri geçemez.');
        $label=trim((string)($data['label_prefix']??''));
        if ($label!=='' && !preg_match('/^[\pL\pN \-]{3,60}$/u',$label)) throw new \RuntimeException('Paket başlık öneki yalnızca harf, rakam ve boşluk içerebilir.');
        $published=0;$skipped=[];
        $quantity=(int)($data['fulfillment_quantity']??0);
        $catId=(int)($data['category_id']??0);
        $price=(float)($data['price']??0);
        $db=Database::getInstance();
        foreach($ids as $serviceId) {
            $service=$db->fetch('SELECT s.*,p.is_active FROM smm_services s JOIN smm_providers p ON p.id=s.provider_id WHERE s.id=?',[$serviceId]);
            if(!$service || !$service['is_available'] || !$service['is_active']
                || strcasecmp((string)$service['service_type'],'Default')!==0) {
                $skipped[]=$serviceId.' (uyumsuz/pasif)';continue;
            }
            // User-authored generic names. NEVER copy raw provider service name to the storefront.
            $platform=SmmOrderFields::platform((string)$service['category'].' '.(string)$service['name']);
            $platformName=$platform!==''?ucfirst($platform):'Sosyal Medya';
            $source=mb_strtolower((string)$service['name'].' '.(string)$service['category'],'UTF-8');
            $type='Hizmet';
            foreach([
                '/takipçi|takipci|follow|subscriber|abone/u'=>'Takipçi',
                '/beğeni|begeni|like/u'=>'Beğeni',
                '/izlenme|view/u'=>'İzlenme',
                '/yorum|comment/u'=>'Yorum',
                '/kaydetme|save|favori/u'=>'Kaydetme',
                '/paylaşım|share/u'=>'Paylaşım'
            ] as $regex=>$value) {
                if(preg_match($regex,$source)){$type=$value;break;}
            }
            $amount=$quantity>0?$quantity:(int)$service['min_quantity'];
            if ($amount<(int)$service['min_quantity'] || $amount>(int)$service['max_quantity']) {
                $skipped[]=$serviceId.' (adet sınırı)';continue;
            }
            $base=($label!==''?$label.' ':'').$platformName.' '.$amount.' '.$type.' Paketi';
            $name=$base;
            for($counter=2;$counter<=150 && $db->fetch('SELECT id FROM packages WHERE slug=?',[slugify($name)]);$counter++) {
                $name=$base.' Seçenek '.$counter;
            }
            try {
                $record=$data;
                $record['service_id']=$serviceId;
                $record['name']=$name;
                $record['fulfillment_quantity']=$amount;
                $record['category_id']=$catId;
                $record['price']=$price;
                $record['seo_title']=mb_substr($name.' | '.(string)setting('site_name','Yorum Hizmeti'),0,200);
                $record['seo_description']=$short;
                $record['seo_focus_keyword']='';
                // Always create inactive first. Supervisor can individually verify supplier and copy.
                $record['publish_now']=null;
                $id=self::publish($record);
                if($features!=='')PackageHighlightsService::save($id,$features);
                $published++;
            } catch (\Throwable $e) {
                $skipped[]=$serviceId.' ('.mb_substr($e->getMessage(),0,80).')';
            }
        }
        return ['created'=>$published,'skipped'=>$skipped];
    }

    public static function syncAll(): array
    {
        if(!self::installed())throw new \RuntimeException('SMM modülü henüz kurulmadı.');
        $providers=self::providers();
        $stats=['providers'=>0,'services'=>0,'errors'=>[]];
        foreach($providers as $p){
            if(!(int)$p['is_active'])continue;
            try{
                $count=self::sync((int)$p['id']);
                $stats['providers']++;
                $stats['services']+=$count;
            }catch(\Throwable $e){
                $stats['errors'][]=$p['name'].': '.mb_substr($e->getMessage(),0,90);
            }
        }
        return $stats;
    }

    public static function updateMapping(int $packageId, int $serviceId, int $qty, bool $enabled): void
    {
        $db = Database::getInstance();
        $service = $db->fetch('SELECT s.*,p.is_active FROM smm_services s JOIN smm_providers p ON p.id=s.provider_id WHERE s.id=?',[$serviceId]);
        $mapped = $db->fetch('SELECT * FROM smm_package_links WHERE package_id=?',[$packageId]);
        if (!$mapped || !$service || strcasecmp($service['service_type'],'Default')!==0) {
            throw new \RuntimeException('Geçersiz paket veya desteklenmeyen servis tipi.');
        }
        if ($qty < (int)$service['min_quantity'] || $qty > (int)$service['max_quantity']) {
            throw new \RuntimeException('Teslim adedi servis sınırları dışında.');
        }
        $db->update('smm_package_links',['service_id'=>$serviceId,'fulfillment_quantity'=>$qty,'enabled'=>$enabled?1:0],
            'package_id=?',[$packageId]);
    }

    public static function mapping(int $packageId): ?array
    {
        if (!self::installed()) return null;
        return Database::getInstance()->fetch(
            'SELECT m.*,s.provider_id,s.external_service_id,s.service_type,s.min_quantity,s.max_quantity,
                    s.is_available,p.is_active AS provider_active
             FROM smm_package_links m JOIN smm_services s ON s.id=m.service_id
             JOIN smm_providers p ON p.id=s.provider_id WHERE m.package_id=?', [$packageId]
        );
    }
}
