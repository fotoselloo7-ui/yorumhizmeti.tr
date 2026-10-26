<?php
namespace App\Services;

use App\Core\Database;

/** Thin public projections only: no demo JSON, credentials or private article bodies. */
final class LandingCatalogService
{
    /**
     * Product-only software taxonomy is never offered as an agency service.
     * Keep category rows, URLs, products and saved page IDs unchanged.
     * The distinction is scoped solely to regional landing service pickers/cards.
     */
    public static function isSoftwareOnlyCategory(array $category): bool
    {
        static $productSlugs = null;
        if ($productSlugs === null) {
            $productSlugs = [
                SoftwareCatalogService::ROOT_SLUG => true,
                ServiceCategoryVisibility::LEGACY_SOFTWARE_ROOT => true,
            ];
            foreach (SoftwareCatalogService::definitions() as $definition) {
                if (is_string($definition[1] ?? null)) {
                    $productSlugs[$definition[1]] = true;
                }
            }
        }
        $slug = (string)($category['slug'] ?? '');
        $parent = (string)($category['parent_category_slug'] ?? '');
        return isset($productSlugs[$slug]) || ($parent !== '' && isset($productSlugs[$parent]));
    }

    public static function services():array
    {
        $ids=array_map('intval',array_column(Database::getInstance()->fetchAll("SELECT id FROM categories WHERE status='active' ORDER BY name"),'id'));
        return array_map(static fn($row)=>['id'=>(int)$row['id'],'title'=>$row['name'],'slug'=>$row['slug'],'category'=>!empty($row['parent_id'])?'Alt hizmet / kategori':'Ana hizmet / kategori'],RegionalLandingService::selectedServices($ids));
    }
    private static function image(string $value):string
    {
        $image=RegionalLandingService::referenceImage(['image'=>$value]);
        return str_starts_with($image,'https://')||($image!==''&&is_file(PUBLIC_PATH.$image))?$image:'';
    }
    public static function scripts(): array
    {
        static $items;
        if($items!==null)return $items;
        $items=[];
        foreach(NetveraBridgeService::all() as $row){
            $items[]=['id'=>(int)$row['legacy_id'],'title'=>(string)$row['name'],
                'slug'=>(string)$row['slug'],'description'=>(string)($row['short_desc']??''),
                'image'=>self::image((string)($row['cover_image']??'')),
                'category'=>(string)($row['category_name']??''),'url'=>'/hazir-scriptler/'.rawurlencode($row['slug'])];
        }
        return $items;
    }

    public static function blogs(): array
    {
        static $items;
        if($items!==null)return $items;
        $db=Database::getInstance();
        // Any DB row, including a draft, suppresses the old snapshot by slug.
        $known=array_fill_keys(array_column($db->fetchAll('SELECT slug FROM blog_posts'),'slug'),true);
        $snapshot=NetveraBlogSnapshot::all();$originalIds=[];
        foreach($snapshot as $original)$originalIds[$original['slug']]=$original['id'];
        $rows=$db->fetchAll("SELECT p.id,p.title,p.slug,p.excerpt,p.image,p.image_alt,p.published_at,c.name category_name FROM blog_posts p LEFT JOIN blog_categories c ON c.id=p.blog_category_id WHERE p.status='active' ORDER BY p.published_at DESC,p.id DESC");
        foreach($snapshot as $row)if(!isset($known[$row['slug']]))$rows[]=$row;
        $items=[];
        foreach($rows as $row)$items[]=['id'=>(int)($originalIds[$row['slug']]??$row['id']),'title'=>(string)$row['title'],'slug'=>(string)$row['slug'],
            'description'=>(string)($row['excerpt']??''),'image'=>self::image((string)($row['image']??'')),
            'image_alt'=>(string)($row['image_alt']??$row['title']),'category'=>(string)($row['category_name']??'Blog'),
            'url'=>'/blog/'.rawurlencode($row['slug'])];
        return $items;
    }

    public static function selected(array $rows,array $ids): array
    {
        $byId=[];foreach($rows as $row)$byId[(string)$row['id']]=$row;
        $selected=[];foreach($ids as $id)if(isset($byId[(string)$id]))$selected[]=$byId[(string)$id];
        return $selected;
    }
}
