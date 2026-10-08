<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Database;

class BlogController extends Controller
{
    public function index(): void
    {
        $db = Database::getInstance();
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        $where = ["bp.status = 'active'"];
        $params = [];
        $joins = [];

        // Kategori filtresi
        if (!empty($_GET['category'])) {
            $joins[] = "LEFT JOIN blog_categories bcf ON bp.blog_category_id = bcf.id";
            $where[] = "bcf.slug = ?";
            $params[] = $_GET['category'];
        }

        // Tag filtresi
        if (!empty($_GET['tag'])) {
            $joins[] = "LEFT JOIN blog_post_tags bpt ON bp.id = bpt.post_id LEFT JOIN blog_tags bt ON bpt.tag_id = bt.id";
            $where[] = "bt.slug = ?";
            $params[] = $_GET['tag'];
        }

        // Arama
        if (!empty($_GET['q'])) {
            $where[] = "(bp.title LIKE ? OR bp.content LIKE ?)";
            $q = '%' . $_GET['q'] . '%';
            $params[] = $q;
            $params[] = $q;
        }

        $whereClause = implode(' AND ', $where);
        $joinClause = implode(' ', array_unique($joins));

        $totalSql = "SELECT COUNT(DISTINCT bp.id) as cnt FROM blog_posts bp {$joinClause} WHERE {$whereClause}";
        $total = (int) $db->fetch($totalSql, $params)['cnt'];

        $sql = "SELECT DISTINCT bp.*, bc.name as category_name, bc.slug as category_slug 
                FROM blog_posts bp 
                LEFT JOIN blog_categories bc ON bp.blog_category_id = bc.id 
                {$joinClause} 
                WHERE {$whereClause} 
                ORDER BY bp.published_at DESC 
                LIMIT {$perPage} OFFSET {$offset}";
        
        $posts = $db->fetchAll($sql, $params);
        $categories = $db->fetchAll("SELECT * FROM blog_categories WHERE status = 'active' ORDER BY sort_order ASC");
        $popularPosts = $db->fetchAll("SELECT bp.*, bc.name as category_name, bc.slug as category_slug FROM blog_posts bp LEFT JOIN blog_categories bc ON bp.blog_category_id = bc.id WHERE bp.status = 'active' ORDER BY bp.views DESC, bp.published_at DESC LIMIT 5");
        // Show authentic Netvera posts on first launch before isolated DB import.
        // Existing DB posts always win by their original indexed slug.
        $legacyPosts=\App\Services\NetveraBlogSnapshot::all();
        $knownSlugs=array_fill_keys(array_column($posts,'slug'),true);
        $added=0;
        foreach($legacyPosts as $original){
            $slug=$original['slug'];
            $existing=$db->fetch('SELECT id FROM blog_posts WHERE slug=? LIMIT 1',[$slug]);
            if($existing)continue; // Editor/unpublished item must never be resurrected.
            if(!empty($_GET['tag']))continue;
            if(!empty($_GET['category']) && $original['category_slug']!==(string)$_GET['category'])continue;
            if(!empty($_GET['q']) && mb_stripos($original['title'].' '.$original['excerpt'],
                (string)$_GET['q'],0,'UTF-8')===false)continue;
            $added++;
            if($page===1 && !isset($knownSlugs[$slug])){
                $posts[]=$original;
                $knownSlugs[$slug]=true;
            }
        }
        $total+=$added;
        usort($posts,static fn($a,$b)=>strcmp((string)$b['published_at'],(string)$a['published_at']));
        if($page===1)$posts=array_slice($posts,0,$perPage);
        $popularSlugs=array_fill_keys(array_column($popularPosts,'slug'),true);
        foreach($legacyPosts as $original){
            if(count($popularPosts)>=5)break;
            if(!isset($popularSlugs[$original['slug']])){
                $popularPosts[]=$original;
                $popularSlugs[$original['slug']]=true;
            }
        }
        $existingCategories=array_fill_keys(array_column($categories,'slug'),true);
        foreach(\App\Services\NetveraBlogSnapshot::categories() as $oldCategory){
            if(!isset($existingCategories[$oldCategory['slug']])){
                $categories[]=$oldCategory;
                $existingCategories[$oldCategory['slug']]=true;
            }
        }


        // Demo kurulumunda gerçek içerik azsa tasarımın dolu halini göstermek için
        // sadece filtresiz ilk sayfada sanal kartlarla vitrini tamamla.
        if (!$posts && !$legacyPosts && $page === 1 && empty($_GET['category']) && empty($_GET['tag']) && empty($_GET['q'])) {
            $demoPosts = demo_blog_posts();
            foreach ($demoPosts as $demoPost) {
                if (count($posts) >= 9) break;
                $posts[] = $demoPost;
            }
            foreach ($demoPosts as $demoPost) {
                if (count($popularPosts) >= 5) break;
                $popularPosts[] = $demoPost;
            }
        }
        $faqs = $db->fetchAll("SELECT * FROM faqs WHERE status = 'active' ORDER BY sort_order ASC LIMIT 6");
        $testimonialSection = null;
        try {
            $testimonialSection = $db->fetch("SELECT * FROM home_sections WHERE section_key = 'testimonials' AND status = 'active' LIMIT 1");
            if ($testimonialSection) {
                $testimonialSection['extra'] = !empty($testimonialSection['extra_data']) ? json_decode($testimonialSection['extra_data'], true) : [];
            }
        } catch (\Exception $e) {
            $testimonialSection = null;
        }

        $this->render('frontend/blog/index', [
            'pageTitle' => 'Blog - ' . setting('site_name'),
            'metaDescription' => 'Dijital dünyadan güncel bilgiler, rehberler ve ipuçları.',
            'posts' => $posts,
            'categories' => $categories,
            'popularPosts' => $popularPosts,
            'faqs' => $faqs,
            'testimonialSection' => $testimonialSection,
            'page' => $page,
            'totalPages' => max(1, ceil($total / $perPage)),
        ]);
    }

    public function category(string $slug): void
    {
        $_GET['category'] = $slug;
        $this->index();
    }

    public function show(string $slug): void
    {
        $db = Database::getInstance();
        $post = $db->fetch("SELECT bp.*, bc.name as category_name, bc.slug as category_slug FROM blog_posts bp LEFT JOIN blog_categories bc ON bp.blog_category_id = bc.id WHERE bp.slug = ? AND bp.status = 'active'", [$slug]);
        $snapshotPost=false;
        if (!$post) {
            $post=\App\Services\NetveraBlogSnapshot::find($slug);
            $snapshotPost=(bool)$post;
        }
        if (!$post) {
            http_response_code(404);
            $this->render('frontend/404', ['pageTitle' => 'Yazı Bulunamadı']);
            return;
        }

        // Never update real DB for the read-only original Netvera snapshot.
        if (!$snapshotPost)
            $db->query("UPDATE blog_posts SET views = views + 1 WHERE id = ?", [$post['id']]);
        
        // Tags
        $tags = $db->fetchAll("SELECT bt.* FROM blog_tags bt JOIN blog_post_tags bpt ON bt.id = bpt.tag_id WHERE bpt.post_id = ?", [$post['id']]);
        
        // Sidebar data
        $relatedPosts = $db->fetchAll("SELECT * FROM blog_posts WHERE blog_category_id = ? AND id != ? AND status = 'active' ORDER BY published_at DESC LIMIT 4", [$post['blog_category_id'], $post['id']]);
        $latestPosts = $db->fetchAll("SELECT * FROM blog_posts WHERE id != ? AND status = 'active' ORDER BY published_at DESC LIMIT 4", [$post['id']]);
        $categories = $db->fetchAll("SELECT * FROM blog_categories WHERE status = 'active' ORDER BY sort_order ASC");
        $popularPackages = $db->fetchAll("SELECT p.*, bc.name as category_name, bc.slug as category_slug, bc.icon_key FROM packages p JOIN categories bc ON p.category_id = bc.id WHERE p.is_featured = 1 AND p.status = 'active' LIMIT 3");

        // Önceki - Sonraki
        $prevPost = $db->fetch("SELECT slug, title FROM blog_posts WHERE status = 'active' AND id < ? ORDER BY id DESC LIMIT 1", [$post['id']]);
        $nextPost = $db->fetch("SELECT slug, title FROM blog_posts WHERE status = 'active' AND id > ? ORDER BY id ASC LIMIT 1", [$post['id']]);

        // TOC ve HTML parse
        $content = $post['content'];
        $toc = !empty($post['toc']) ? json_decode($post['toc'], true) : [];
        if (!is_array($toc)) {
            $toc = [];
        }

        // Author attribution is sourced from the same verified metadata that
        // appears visibly on the page. Never invent a person or a review.
        $nvBlogSeo=\App\Services\NetveraSeoBridge::get('blog',(int)$post['id']);
        $nvAuthorName=trim((string)($nvBlogSeo['author_name']??''));
        $nvAuthorType=($nvBlogSeo['author_type']??'')==='Person'?'Person':'Organization';
        $nvAuthor=[
            '@type'=>$nvAuthorType,
            'name'=>$nvAuthorName!==''?$nvAuthorName:setting('site_name')
        ];
        $authorUrl=trim((string)($nvBlogSeo['author_url']??''));
        if($authorUrl!=='' && filter_var($authorUrl,FILTER_VALIDATE_URL)
           && in_array(strtolower((string)parse_url($authorUrl,PHP_URL_SCHEME)),['http','https'],true))
            $nvAuthor['url']=$authorUrl;
        $sameAs=[];
        foreach(preg_split('/\R/',(string)($nvBlogSeo['same_as_urls']??''))?:[] as $profile){
            $profile=trim($profile);
            if($profile!=='' && filter_var($profile,FILTER_VALIDATE_URL)
               && in_array(strtolower((string)parse_url($profile,PHP_URL_SCHEME)),['http','https'],true))
                $sameAs[]=$profile;
        }
        if($sameAs)$nvAuthor['sameAs']=array_values(array_unique(array_slice($sameAs,0,15)));
        // Schema JSON-LD
        $schemas = [];
        
        // Article Schema
        $schemaType = $post['schema_type'] ?: 'Article';
        $schemas[] = [
            '@context' => 'https://schema.org',
            '@type' => $schemaType,
            'headline' => $post['seo_title'] ?: $post['title'],
            'description' => $post['seo_description'] ?: excerpt(strip_tags($post['excerpt'] ?? ''), 160),
            'image' => $post['image'] ? url('/uploads/' . $post['image']) : url('/assets/img/logo.png'),
            'datePublished' => date('c', strtotime($post['published_at'])),
            'dateModified' => date('c', strtotime($post['updated_at'])),
            'author' => $nvAuthor,
            'publisher' => [
                '@type' => 'Organization',
                'name' => setting('site_name'),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => url('/assets/img/logo.png')
                ]
            ]
        ];

        // FAQ Schema
        $faqs = !empty($post['faqs']) ? json_decode($post['faqs'], true) : [];
        if (is_array($faqs) && count($faqs) > 0) {
            $faqItems = [];
            foreach ($faqs as $faq) {
                $faqItems[] = [
                    '@type' => 'Question',
                    'name' => $faq['question'],
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => $faq['answer']
                    ]
                ];
            }
            $schemas[] = [
                '@context' => 'https://schema.org',
                '@type' => 'FAQPage',
                'mainEntity' => $faqItems
            ];
        }

        // Breadcrumb Schema
        $schemas[] = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Anasayfa', 'item' => url('/')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Blog', 'item' => url('/blog')],
                ['@type' => 'ListItem', 'position' => 3, 'name' => $post['title'], 'item' => url('/blog/' . $post['slug'])]
            ]
        ];

        $this->render('frontend/blog/detail', [
            'pageTitle' => $post['seo_title'] ?: $post['title'],
            'metaDescription' => $post['seo_description'] ?: excerpt(strip_tags($post['excerpt'] ?? ''), 160),
            'canonicalUrl' => $post['canonical_url'] ?: url('/blog/' . $post['slug']),
            'ogTitle' => $post['og_title'] ?: ($post['seo_title'] ?: $post['title']),
            'ogDescription' => $post['og_description'] ?: ($post['seo_description'] ?: excerpt(strip_tags($post['excerpt'] ?? ''), 160)),
            'ogImage' => $post['og_image'] ? url('/uploads/' . $post['og_image']) : ($post['image'] ? url('/uploads/' . $post['image']) : url('/assets/img/logo.png')),
            'ogType' => 'article',
            'noindex' => $post['noindex'] == 1,
            'schema' => json_encode($schemas, JSON_UNESCAPED_UNICODE),
            'post' => $post,
            'nvSeoData' => $snapshotPost ? [] : \App\Services\NetveraSeoBridge::get('blog',(int)$post['id']),
            'tags' => $tags,
            'toc' => $toc,
            'faqs' => $faqs,
            'relatedPosts' => $relatedPosts,
            'latestPosts' => $latestPosts,
            'categories' => $categories,
            'popularPackages' => $popularPackages,
            'prevPost' => $prevPost,
            'nextPost' => $nextPost,
        ]);
    }
}
