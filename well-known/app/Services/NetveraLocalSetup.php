<?php
namespace App\Services;

use App\Core\Database;
use PDO;

/**
 * Localhost-only, public-content bootstrap. No production, payment, user,
 * order, license or gateway writes. Existing Netvera edits never overwritten.
 */
final class NetveraLocalSetup
{
    private static bool $attempted=false;

    public static function enabled():bool
    {
        if(($_ENV['NETVERA_LOCAL_AUTO_SETUP']??'')==='0')return false;
        if(!in_array(strtolower((string)($_ENV['APP_ENV']??'production')),['local','development'],true))return false;
        $host=strtolower(trim((string)($_SERVER['HTTP_HOST']??'')));
        if(!preg_match('/^(?:localhost|127\.0\.0\.1|\[::1\])(?::[0-9]{1,5})?$/',$host))return false;
        return in_array(strtolower(trim((string)($_ENV['DB_HOST']??''))),
            ['localhost','127.0.0.1','::1'],true);
    }

    public static function boot():void
    {
        if(self::$attempted||!self::enabled())return;
        self::$attempted=true;
        try{
            $pdo=Database::getInstance()->getPdo();
            $pdo->exec("CREATE TABLE IF NOT EXISTS nv_local_setup_state (
                setup_key VARCHAR(80) NOT NULL PRIMARY KEY,
                completed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            $check=$pdo->prepare('SELECT setup_key FROM nv_local_setup_state WHERE setup_key=? LIMIT 1');
            $check->execute(['netvera-public-v1']);
            if($check->fetchColumn())return;
            self::schemas($pdo);
            self::seed($pdo);
            $pdo->prepare('INSERT IGNORE INTO nv_local_setup_state (setup_key) VALUES (?)')
                ->execute(['netvera-public-v1']);
        }catch(\Throwable $e){
            error_log('Local Netvera bootstrap failed: '.get_class($e).' '.$e->getMessage());
        }
    }

    private static function schemas(PDO $pdo):void
    {
        $allow=[
            'nv_legacy_script_categories','nv_legacy_script_products',
            'nv_legacy_script_images','nv_legacy_public_reviews',
            'nv_legacy_blog_meta','nv_public_inquiries',
            'nv_public_inquiry_replies','nv_editorial_seo'
        ];
        foreach(['netvera-legacy-bridge-v1.sql','netvera-inquiries-v1.sql','netvera-editorial-seo-v1.sql'] as $name){
            $sql=file_get_contents(BASE_PATH.'/database/migrations/'.$name);
            if($sql===false)throw new \RuntimeException('Local Netvera migration missing');
            $sql=preg_replace('/^\s*--[^\r\n]*(?:\r?\n|$)/m','',$sql);
            foreach(explode(';',(string)$sql) as $statement){
                $statement=trim($statement);
                if($statement==='')continue;
                if(!preg_match('/^CREATE\s+TABLE\s+IF\s+NOT\s+EXISTS\s+(nv_[a-z0-9_]+)\s*\(/i',
                    $statement,$matches)||!in_array(strtolower($matches[1]),$allow,true))
                    throw new \RuntimeException('Non-Netvera SQL refused in local bootstrap');
                $pdo->exec($statement);
            }
        }
    }

    private static function insertMissing(PDO $pdo,string $table,array $values):void
    {
        // Table and column names originate from fixed source literals only.
        $columns=array_keys($values);
        $sql='INSERT IGNORE INTO '.$table.' ('.implode(',',$columns).') VALUES ('.
             implode(',',array_fill(0,count($columns),'?')).')';
        $pdo->prepare($sql)->execute(array_values($values));
    }

    private static function seed(PDO $pdo):void
    {
        $data=NetveraBridgeService::snapshot();
        if(count($data['products']??[])<1||count($data['categories']??[])<1)
            throw new \RuntimeException('Public Netvera snapshot not available');
        // An already-edited catalogue is authoritative. No resurrecting deleted records.
        if((int)$pdo->query('SELECT COUNT(*) FROM nv_legacy_script_products')->fetchColumn()>0)return;
        $pdo->beginTransaction();
        try{
            foreach($data['categories'] as $r)self::insertMissing($pdo,'nv_legacy_script_categories',[
                'legacy_id'=>(int)$r['id'],'parent_legacy_id'=>($r['parent_id']??null)?:null,
                'slug'=>$r['slug'],'name'=>$r['name'],'description'=>$r['description']??'',
                'meta_title'=>$r['meta_title']??'','meta_description'=>$r['meta_description']??'',
                'sort_order'=>(int)($r['sort_order']??0),'active'=>(int)($r['is_active']??0)
            ]);
            foreach($data['products'] as $r)self::insertMissing($pdo,'nv_legacy_script_products',[
                'legacy_id'=>(int)$r['id'],'category_legacy_id'=>(int)$r['category_id'],
                'slug'=>$r['slug'],'name'=>$r['name'],'short_desc'=>$r['short_desc']??'',
                'description'=>$r['description']??'','cover_image'=>$r['image']??null,
                'price'=>(float)($r['price']??0),'old_price'=>$r['old_price']??null,
                'badge'=>$r['badge']??'','meta_title'=>$r['meta_title']??'',
                'meta_description'=>$r['meta_description']??'',
                'focus_keyword'=>$r['focus_keyword']??'',
                'public_json'=>json_encode($r,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_THROW_ON_ERROR),
                'sort_order'=>(int)($r['sort_order']??0),'active'=>(int)($r['is_active']??0)
            ]);
            foreach($data['images'] as $r)self::insertMissing($pdo,'nv_legacy_script_images',[
                'legacy_id'=>(int)$r['id'],'product_legacy_id'=>(int)$r['script_product_id'],
                'image_path'=>$r['image_path'],'alt_text'=>$r['alt_text']??'',
                'caption'=>$r['caption']??'','sort_order'=>(int)($r['sort_order']??0),
                'active'=>(int)($r['is_active']??0)
            ]);
            foreach($data['approved_reviews'] as $r){
                if(($r['status']??'')!=='approved'||($r['target_type']??'')!=='script')continue;
                self::insertMissing($pdo,'nv_legacy_public_reviews',[
                    'legacy_id'=>(int)$r['id'],'product_legacy_id'=>(int)$r['target_id'],
                    'rating'=>max(1,min(5,(int)$r['rating'])),'comment'=>$r['comment']??'',
                    'created_at'=>$r['created_at']??null
                ]);
            }
            $categoryIds=[];
            foreach($data['blog_categories'] as $r){
                $slug=(string)$r['slug'];
                self::insertMissing($pdo,'blog_categories',[
                    'name'=>$r['name'],'slug'=>$slug,'sort_order'=>(int)($r['sort_order']??0),
                    'status'=>(int)($r['is_active']??0)?'active':'inactive'
                ]);
                $select=$pdo->prepare('SELECT id FROM blog_categories WHERE slug=? LIMIT 1');
                $select->execute([$slug]);
                $categoryIds[(int)$r['id']]=(int)$select->fetchColumn();
            }
            foreach($data['blog_posts'] as $r){
                $slug=(string)$r['slug'];
                self::insertMissing($pdo,'blog_posts',[
                    'blog_category_id'=>$categoryIds[(int)$r['category_id']]??null,
                    'title'=>$r['title'],'slug'=>$slug,'excerpt'=>$r['excerpt']??'',
                    'content'=>$r['content']??'',
                    'image'=>preg_replace('~^/?uploads/~','',(string)($r['image']??'')),
                    'status'=>'active','published_at'=>$r['published_at']??null,
                    'seo_title'=>$r['seo_title']??'','seo_description'=>$r['seo_description']??'',
                    'seo_focus_keyword'=>$r['focus_keyword']??'','canonical_url'=>'',
                    'og_title'=>$r['og_title']??'','og_description'=>$r['og_description']??'',
                    'og_image'=>preg_replace('~^/?uploads/~','',(string)($r['og_image']??''))
                ]);
                $select=$pdo->prepare('SELECT id FROM blog_posts WHERE slug=? LIMIT 1');
                $select->execute([$slug]);
                $postId=(int)$select->fetchColumn();
                if($postId>0)self::insertMissing($pdo,'nv_legacy_blog_meta',[
                    'blog_post_id'=>$postId,'original_slug'=>$slug,
                    'original_author'=>$r['author_name']??'','robots'=>$r['robots']??'',
                    'focus_keyword'=>$r['focus_keyword']??'',
                    'secondary_keywords'=>$r['secondary_keywords']??'',
                    'source_json'=>json_encode($r,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_THROW_ON_ERROR)
                ]);
            }
            $pdo->commit();
        }catch(\Throwable $e){
            if($pdo->inTransaction())$pdo->rollBack();
            throw $e;
        }
    }
}
