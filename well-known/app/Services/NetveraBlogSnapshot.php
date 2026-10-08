<?php
namespace App\Services;

/**
 * Original Netvera public blog articles as safe read-only cold-start content.
 * New admin edits in the destination database always win by original slug.
 * No legacy account, message, payment, credential or transaction fields.
 */
final class NetveraBlogSnapshot
{
    public static function categories():array
    {
        $data=NetveraBridgeService::snapshot();
        return array_values(array_filter(array_map(static fn(array $r):array=>[
            'id'=>(int)$r['id'],'name'=>$r['name'],'slug'=>$r['slug'],
            'sort_order'=>(int)($r['sort_order']??0),'status'=>'active'
        ],$data['blog_categories']??[]),static fn($r):bool=>$r['status']==='active'));
    }

    public static function all():array
    {
        $data=NetveraBridgeService::snapshot();
        $cats=[];
        foreach(self::categories() as $r)$cats[$r['id']]=$r;
        $posts=[];
        foreach($data['blog_posts']??[] as $r){
            $cat=$cats[(int)$r['category_id']]??[];
            $image=(string)($r['image']??'');
            $og=(string)($r['og_image']??'');
            $posts[]=[
                // Negative IDs cannot collide with real destination database PKs.
                'id'=>-abs((int)$r['id']),
                'blog_category_id'=>-abs((int)$r['category_id']),
                'category_name'=>$cat['name']??'Blog',
                'category_slug'=>$cat['slug']??'',
                'title'=>$r['title'],
                'slug'=>$r['slug'],
                'excerpt'=>$r['excerpt']??'',
                'content'=>$r['content']??'',
                'raw_content'=>$r['content']??'',
                'image'=>preg_replace('~^/?uploads/~','',$image),
                'image_alt'=>$r['title'],
                'status'=>'active',
                'published_at'=>$r['published_at']?:date('Y-m-d H:i:s'),
                'updated_at'=>$r['published_at']?:date('Y-m-d H:i:s'),
                'seo_title'=>$r['seo_title']??'',
                'seo_description'=>$r['seo_description']??'',
                'seo_focus_keyword'=>$r['focus_keyword']??'',
                'canonical_url'=>'', // destination site canonical, not old host
                'og_title'=>$r['og_title']??'',
                'og_description'=>$r['og_description']??'',
                'og_image'=>preg_replace('~^/?uploads/~','',$og),
                'noindex'=>0,'schema_type'=>'Article',
                'toc'=>'[]','faqs'=>'[]',
                'views'=>0,'reading_time'=>max(1,(int)ceil(mb_strlen(strip_tags((string)($r['content']??'')),'UTF-8')/1000)),
                'source'=>'netvera-public-snapshot'
            ];
        }
        usort($posts,static fn($a,$b)=>strcmp($b['published_at'],$a['published_at']));
        return $posts;
    }

    public static function find(string $slug):?array
    {
        foreach(self::all() as $p)if($p['slug']===$slug)return $p;
        return null;
    }
}
