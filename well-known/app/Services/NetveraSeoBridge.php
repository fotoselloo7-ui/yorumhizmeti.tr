<?php
namespace App\Services;
use App\Core\Database;

/** Netvera editorial metadata on top of the existing service/category/blog schema. */
final class NetveraSeoBridge
{
    private static array $cache = [];

    public static function get(string $type,int $id):array
    {
        if(!in_array($type,['package','category','blog'],true)||$id<1)return [];
        $key=$type.':'.$id;
        if(isset(self::$cache[$key]))return self::$cache[$key];
        try{
            $row=Database::getInstance()->fetch(
                "SELECT extra_json FROM nv_editorial_seo WHERE entity_type=? AND entity_id=?",
                [$type,$id]
            );
            $value=$row?json_decode($row['extra_json'],true):[];
            return self::$cache[$key]=is_array($value)?$value:[];
        }catch(\Throwable $e){return [];}
    }

    public static function save(string $type,int $id,array $input):void
    {
        if(!in_array($type,['package','category','blog'],true)||$id<1)return;
        $data=[];
        foreach([
            'secondary_keywords'=>1000,'robots'=>90,'geo_summary'=>500,
            'entity_topics'=>500,'service_area'=>300,'author_name'=>120,
            'author_type'=>30,'author_url'=>500,'same_as_urls'=>1500
        ] as $field=>$limit){
            $data[$field]=mb_substr(trim((string)($input['nvseo_'.$field]??'')),0,$limit,'UTF-8');
        }
        if(!in_array($data['robots'],[
            '','index,follow','noindex,follow','noindex,nofollow',
            'index,follow,max-image-preview:large'
        ],true))$data['robots']='';
        if(!in_array($data['author_type'],['','Person','Organization'],true))
            $data['author_type']='Organization';
        foreach(['author_url'] as $field){
            if($data[$field]!==''&&!self::urlAllowed($data[$field]))$data[$field]='';
        }
        $profiles=[];
        foreach(preg_split('/\R/', $data['same_as_urls'])?:[] as $candidate){
            $candidate=trim($candidate);
            if(self::urlAllowed($candidate))$profiles[]=$candidate;
        }
        $data['same_as_urls']=implode("\n",array_slice(array_unique($profiles),0,15));
        $pdo=Database::getInstance()->getPdo();
        $pdo->exec("CREATE TABLE IF NOT EXISTS nv_editorial_seo (
            entity_type VARCHAR(30) NOT NULL, entity_id INT UNSIGNED NOT NULL,
            extra_json LONGTEXT NOT NULL, PRIMARY KEY(entity_type,entity_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        $pdo->prepare("INSERT INTO nv_editorial_seo (entity_type,entity_id,extra_json)
            VALUES(?,?,?) ON DUPLICATE KEY UPDATE extra_json=VALUES(extra_json)")->execute([
            $type,$id,json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_THROW_ON_ERROR)
        ]);
        self::$cache[$type.':'.$id]=$data;
    }

    private static function urlAllowed(string $url):bool
    {
        return filter_var($url,FILTER_VALIDATE_URL)!==false
            && in_array(strtolower((string)parse_url($url,PHP_URL_SCHEME)),['http','https'],true);
    }
}
