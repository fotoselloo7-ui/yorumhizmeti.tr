<?php
namespace App\Services;

use App\Core\Database;

class SitemapService
{
    private array $seen=[];
    private array $excluded=[];
    private array $directives=[];

    public function generate(): string
    {
        $db = Database::getInstance();
        $this->seen=[];$this->excluded=[];$this->directives=[];
        $baseUrl = rtrim(SeoMetadataService::webUrl(setting('site_url','https://netvera.tr'))??url('/'),'/');

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        // One bulk read of editorial metadata, not one query per catalogue URL.
        try{
            foreach($db->fetchAll('SELECT entity_type,entity_id,extra_json FROM nv_editorial_seo')as $row){
                $meta=json_decode((string)$row['extra_json'],true);
                if(!is_array($meta))continue;
                $robots=(string)($meta['robots']??'');
                $this->directives[$row['entity_type']][(int)$row['entity_id']]=$robots;
                if(!SeoMetadataService::indexable($robots))$this->excluded[$row['entity_type']][(int)$row['entity_id']]=true;
            }
        }catch(\Throwable $ignored){} // Older compatible installs retain the existing default.
        try{
            foreach($db->fetchAll('SELECT blog_post_id,robots FROM nv_legacy_blog_meta')as $row){
                $id=(int)$row['blog_post_id'];if(array_key_exists($id,$this->directives['blog']??[]))continue;
                $this->directives['blog'][$id]=(string)$row['robots'];
                if(!SeoMetadataService::indexable((string)$row['robots']))$this->excluded['blog'][$id]=true;
            }
        }catch(\Throwable $ignored){}

        // Ana sayfa
        $xml .= $this->url($baseUrl . '/', '1.0', 'daily');

        // Discovery pages use stable slash URLs, never category query parameters.
        $xml .= $this->url($baseUrl . '/kategoriler', '0.8', 'weekly');
        foreach (CatalogMenuService::groups() as $group) {
            $xml .= $this->url(
                $baseUrl . PublicSeoUrls::path('/kategoriler/grup', (string)$group['key']),
                '0.7', 'weekly'
            );
        }
        $xml .= $this->url($baseUrl . '/hazir-yazilimlar', '0.8', 'weekly');
        $softwareSlugs = array_fill_keys(array_column(SoftwareCatalogService::definitions(), 1), true);
        foreach (SoftwareCatalogService::publicPackages() as $software) {
            $slug = (string)($software['category_slug'] ?? '');
            if (!isset($softwareSlugs[$slug])) continue;
            unset($softwareSlugs[$slug]); // each populated type once
            $xml .= $this->url(
                $baseUrl . PublicSeoUrls::path('/hazir-yazilimlar/tur', $slug),
                '0.6', 'weekly'
            );
        }

        // Kategoriler
        $categories = $db->fetchAll(
            "SELECT c.id, c.slug, c.updated_at, parent.slug AS parent_category_slug
             FROM categories c
             LEFT JOIN categories parent ON parent.id = c.parent_id
             WHERE c.status = 'active' AND (c.parent_id IS NULL OR parent.status='active')"
        );
        foreach ($categories as $cat) {
            if (ServiceCategoryVisibility::isLegacySoftware($cat)) continue;
            if(isset($this->excluded['category'][(int)$cat['id']]))continue;
            $xml .= $this->url($baseUrl . '/kategori/' . $cat['slug'], '0.8', 'weekly', $cat['updated_at'],$this->directives['category'][(int)$cat['id']]??null);
        }

        // Paketler
        $packages = $db->fetchAll("SELECT p.id,p.slug,p.updated_at FROM packages p
            JOIN categories c ON c.id=p.category_id AND c.status='active'
            LEFT JOIN categories parent ON parent.id=c.parent_id
            WHERE p.status='active' AND (c.parent_id IS NULL OR parent.status='active')");
        foreach ($packages as $pkg) {
            if(isset($this->excluded['package'][(int)$pkg['id']]))continue;
            // The imported canonical software product owns this legacy demo path.
            if ($pkg['slug']==='netvera-haber-sitesi-script-yazilimi' &&
                NetveraBridgeService::find('haber-sitesi-scripti')) continue;
            $xml .= $this->url($baseUrl . '/paket/' . $pkg['slug'], '0.7', 'weekly', $pkg['updated_at'],$this->directives['package'][(int)$pkg['id']]??null);
        }

        // Preserve Netvera's indexed software URLs in the exact original structure.
        if (NetveraBridgeService::all()) {
            $xml .= $this->url($baseUrl . '/hazir-scriptler', '0.9', 'weekly');
            foreach (NetveraBridgeService::categories() as $category) {
                if (!empty($category['parent_legacy_id'])) continue;
                $xml .= $this->url(
                    $baseUrl . PublicSeoUrls::path('/hazir-scriptler/kategori', (string)$category['slug']),
                    '0.7', 'weekly'
                );
            }
            foreach (NetveraScriptTypeService::counts(NetveraBridgeService::all()) as $typeSlug=>$count) {
                if ($count < 1) continue;
                $xml .= $this->url(
                    $baseUrl . PublicSeoUrls::path('/hazir-scriptler/tur', (string)$typeSlug),
                    '0.6', 'weekly'
                );
            }
            foreach (NetveraBridgeService::all() as $script) {
                $public=NetveraBridgeService::jsonFields($script);
                $updated=(string)($public['updated_at']??$public['last_updated_on']??'');
                $xml .= $this->url(
                    $baseUrl.'/hazir-scriptler/'.$script['slug'],
                    '0.8','weekly',$updated?:null
                );
            }
            foreach (NetveraBridgeService::categories() as $cat) {
                if (!$cat['parent_legacy_id']) continue;
                $parent=null;
                foreach (NetveraBridgeService::categories() as $root) {
                    if ((int)$root['legacy_id']===(int)$cat['parent_legacy_id']) {
                        $parent=$root;break;
                    }
                }
                if ($parent) $xml .= $this->url(
                    $baseUrl.'/hazir-scriptler/'.$parent['slug'].'/'.$cat['slug'],
                    '0.6','monthly'
                );
            }
        }

        // Blog
        $xml .= $this->url($baseUrl . '/blog', '0.7', 'daily');

        $blogCats = $db->fetchAll("SELECT slug, updated_at FROM blog_categories WHERE status = 'active'");
        $indexedBlogCategories = [];
        foreach ($blogCats as $bc) {
            $indexedBlogCategories[$bc['slug']] = true;
            $xml .= $this->url(
                $baseUrl . PublicSeoUrls::path('/blog/kategori', (string)$bc['slug']),
                '0.6', 'weekly', $bc['updated_at']
            );
        }
        foreach (NetveraBlogSnapshot::categories() as $bc) {
            if (isset($indexedBlogCategories[$bc['slug']])) continue;
            // A deliberately disabled DB category must not be republished by a snapshot.
            if ($db->fetch('SELECT id FROM blog_categories WHERE slug=? LIMIT 1', [$bc['slug']])) continue;
            $xml .= $this->url(
                $baseUrl . PublicSeoUrls::path('/blog/kategori', (string)$bc['slug']),
                '0.6', 'weekly'
            );
        }

        // Only tags with a published article are included; no empty taxonomies.
        $tagRows = $db->fetchAll("SELECT DISTINCT bt.slug FROM blog_tags bt
            JOIN blog_post_tags bpt ON bpt.tag_id = bt.id
            JOIN blog_posts bp ON bp.id = bpt.post_id
            WHERE bp.status = 'active' AND bp.noindex = 0");
        foreach ($tagRows as $tagRow) {
            $xml .= $this->url(
                $baseUrl . PublicSeoUrls::path('/blog/etiket', (string)$tagRow['slug']),
                '0.5', 'weekly'
            );
        }

        $posts = $db->fetchAll("SELECT id,slug,updated_at,canonical_url FROM blog_posts WHERE status = 'active' AND noindex = 0");
        foreach ($posts as $post) {
            if(isset($this->excluded['blog'][(int)$post['id']]))continue;
            $own=$baseUrl . '/blog/' . $post['slug'];
            $canonical=SeoMetadataService::webUrl((string)($post['canonical_url']??''));
            if($canonical&&rtrim($canonical,'/')!==rtrim($own,'/'))continue;
            $xml .= $this->url($baseUrl . '/blog/' . $post['slug'], '0.6', 'weekly', $post['updated_at'],$this->directives['blog'][(int)$post['id']]??null);
        }
        // Preserve original Netvera indexed blog paths even before staging
        // has imported its separate public-content SQL snapshot.
        $indexedSlugs=array_fill_keys(array_column($posts,'slug'),true);
        foreach (NetveraBlogSnapshot::all() as $post) {
            if (isset($indexedSlugs[$post['slug']])) continue;
            $existing=$db->fetch('SELECT id FROM blog_posts WHERE slug=? LIMIT 1',[$post['slug']]);
            if ($existing) continue; // Do not re-publish deliberately hidden articles.
            $xml .= $this->url($baseUrl.'/blog/'.$post['slug'],'0.6','weekly',$post['updated_at']);
        }

        // Sayfalar
        // Read bounded metadata, not every full landing article into the sitemap.
        // JSON_VALID protects ordinary HTML pages from JSON_EXTRACT errors.
        $pages = $db->fetchAll("SELECT id,slug,updated_at,canonical_url,
            (content LIKE ? ESCAPE '!') AS is_regional,
            IF(JSON_VALID(content),JSON_UNQUOTE(JSON_EXTRACT(content,'$.seo.robots')),NULL) AS regional_robots
            FROM pages WHERE status = 'active'",[RegionalLandingService::pattern()]);
        $hasRegional=false;
        foreach ($pages as $page) {
            if($page['is_regional']){
                $own='/'.rawurlencode($page['slug']);$canonical=$page['canonical_url']??'';
                if(!SeoMetadataService::indexable($page['regional_robots']??'noindex,follow'))continue;
                if($canonical!==''&&$canonical!==$own)continue;
                $xml.=$this->url($baseUrl.$own,'0.6','monthly',$page['updated_at'],$page['regional_robots']??'noindex,follow');$hasRegional=true;continue;
            }
            $xml .= $this->url($baseUrl . '/sayfa/' . $page['slug'], '0.5', 'monthly', $page['updated_at']);
        }
        if($hasRegional)$xml.=$this->url($baseUrl.'/hizmet-bolgeleri','0.6','weekly');

        // SSS & İletişim
        $xml .= $this->url($baseUrl . '/sss', '0.5', 'monthly');
        $xml .= $this->url($baseUrl . '/iletisim', '0.5', 'monthly');

        $xml .= '</urlset>';

        return $xml;
    }

    private function url(string $loc, string $priority, string $changefreq, ?string $lastmod = null,?string $robots=null): string
    {
        if(!SeoMetadataService::indexable($robots?:setting('seo_default_robots')))return '';
        $loc=SeoMetadataService::webUrl($loc);
        if(!$loc||isset($this->seen[$loc]))return '';
        $this->seen[$loc]=true;
        $xml = "  <url>\n    <loc>" . htmlspecialchars($loc,ENT_QUOTES|ENT_XML1,'UTF-8') . "</loc>\n";
        if ($lastmod=SeoMetadataService::lastmod($lastmod)) {
            $xml .= "    <lastmod>" . $lastmod . "</lastmod>\n";
        }
        $xml .= "    <changefreq>{$changefreq}</changefreq>\n";
        $xml .= "    <priority>{$priority}</priority>\n";
        $xml .= "  </url>\n";
        return $xml;
    }

    public function robots(): string
    {
        $baseUrl = rtrim(SeoMetadataService::webUrl(setting('site_url','https://netvera.tr'))??url('/'),'/');
        return "User-agent: *\nAllow: /\nDisallow: /admin/\nDisallow: /hesabim\nDisallow: /siparislerim\nDisallow: /siparis/\nDisallow: /destek\nDisallow: /sepet\nDisallow: /odeme\nDisallow: /payment/\nDisallow: /netvera/canli-destek/\n\nSitemap: {$baseUrl}/sitemap.xml\n";
    }
}
