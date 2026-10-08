<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Upload;
use App\Services\SeoScoreService;
use App\Services\SanitizerService;

class BlogController extends Controller
{
    public function index(): void
    {
        $posts = $this->db->fetchAll("SELECT bp.*, bc.name as category_name, (SELECT COUNT(*) FROM blog_post_tags WHERE post_id = bp.id) as tag_count FROM blog_posts bp LEFT JOIN blog_categories bc ON bp.blog_category_id = bc.id ORDER BY bp.created_at DESC");
        $this->renderAdmin('admin/blog/index', ['pageTitle' => 'Blog Yazıları', 'posts' => $posts]);
    }

    public function create(): void
    {
        $categories = $this->db->fetchAll("SELECT * FROM blog_categories WHERE status = 'active' ORDER BY name");
        $tags = $this->db->fetchAll("SELECT * FROM blog_tags ORDER BY name");
        $this->renderAdmin('admin/blog/form', ['pageTitle' => 'Yazı Ekle', 'post' => null, 'categories' => $categories, 'tags' => $tags, 'postTags' => []]);
    }

    public function store(): void
    {
        Csrf::check();
        $data = $this->getPostData();
        if (!empty($_FILES['image']['name'])) { $data['image'] = Upload::image($_FILES['image'], 'blog'); }
        if (!empty($_FILES['og_image']['name'])) { $data['og_image'] = Upload::image($_FILES['og_image'], 'blog'); }
        
        $data['slug'] = $this->generateUniqueSlug($data['slug']);
        $data['seo_score'] = (new SeoScoreService())->calculate($data)['score'];
        
        $postId = $this->db->insert('blog_posts', $data);
        \App\Services\NetveraSeoBridge::save('blog',(int)$postId,$_POST);
        $this->syncTags((int)$postId, $_POST['tags'] ?? []);

        logActivity('blog_create', 'Blog yazısı oluşturuldu: ' . $data['title']);
        flash('success', 'Yazı oluşturuldu.');
        redirect('/admin/blog');
    }

    public function edit(string $id): void
    {
        $post = $this->db->fetch("SELECT * FROM blog_posts WHERE id = ?", [(int) $id]);
        if (!$post) { redirect('/admin/blog'); }
        // Source Netvera articles were imported as public Markdown, whereas
        // this local Markdown editor reads raw_content. Restore the source
        // body for editing without silently replacing it with an empty draft.
        if(trim((string)($post['raw_content']??''))===''){
            $post['raw_content']=$post['content']??'';
            try{
                $legacy=$this->db->fetch("SELECT source_json FROM nv_legacy_blog_meta WHERE blog_post_id=? LIMIT 1",[(int)$id]);
                $source=$legacy?json_decode((string)$legacy['source_json'],true):null;
                if(is_array($source) && trim((string)($source['content']??''))!=='')
                    $post['raw_content']=$source['content'];
            }catch(\Throwable $ignored){}
        }

        $categories = $this->db->fetchAll("SELECT * FROM blog_categories WHERE status = 'active' ORDER BY name");
        $tags = $this->db->fetchAll("SELECT * FROM blog_tags ORDER BY name");
        $postTags = array_column($this->db->fetchAll("SELECT tag_id FROM blog_post_tags WHERE post_id = ?", [(int)$id]), 'tag_id');
        
        $seo = (new SeoScoreService())->calculate($post);
        $this->renderAdmin('admin/blog/form', ['pageTitle' => 'Yazı Düzenle', 'post' => $post, 'categories' => $categories, 'tags' => $tags, 'postTags' => $postTags, 'seoResult' => $seo,
            'nvSeoData' => \App\Services\NetveraSeoBridge::get('blog',(int)$id)]);
    }

    public function update(string $id): void
    {
        Csrf::check();
        $data = $this->getPostData();
        if (!empty($_FILES['image']['name'])) { $data['image'] = Upload::image($_FILES['image'], 'blog'); }
        if (!empty($_FILES['og_image']['name'])) { $data['og_image'] = Upload::image($_FILES['og_image'], 'blog'); }
        
        $data['slug'] = $this->generateUniqueSlug($data['slug'], (int)$id);
        $data['seo_score'] = (new SeoScoreService())->calculate($data)['score'];
        
        $this->db->update('blog_posts', $data, 'id = ?', [(int) $id]);
        \App\Services\NetveraSeoBridge::save('blog',(int)$id,$_POST);
        $this->syncTags((int)$id, $_POST['tags'] ?? []);

        logActivity('blog_update', 'Blog yazısı güncellendi: ' . $data['title']);
        flash('success', 'Yazı güncellendi.');
        redirect('/admin/blog');
    }

    public function delete(string $id): void
    {
        Csrf::check();
        $this->db->delete('blog_post_tags', 'post_id = ?', [(int) $id]);
        $this->db->delete('blog_posts', 'id = ?', [(int) $id]);
        flash('success', 'Yazı silindi.');
        redirect('/admin/blog');
    }

    // --- Görsel Yükleme Endpoint (Shortcode) ---
    public function uploadImage(): void
    {
        header('Content-Type: application/json');
        try {
            if (empty($_FILES['file']['name'])) {
                throw new \Exception('Dosya seçilmedi.');
            }
            $location = Upload::image($_FILES['file'], 'editor');
            echo json_encode(['location' => $location]);
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    // --- Kategori Yönetimi ---
    public function categories(): void
    {
        $categories = $this->db->fetchAll("SELECT bc.*, (SELECT COUNT(*) FROM blog_posts WHERE blog_category_id = bc.id) as post_count FROM blog_categories bc ORDER BY bc.sort_order");
        $this->renderAdmin('admin/blog/categories', ['pageTitle' => 'Blog Kategorileri', 'categories' => $categories]);
    }

    public function storeCategory(): void
    {
        Csrf::check();
        $this->db->insert('blog_categories', [
            'name' => trim($_POST['name'] ?? ''),
            'slug' => trim($_POST['slug'] ?? '') ?: slugify($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'sort_order' => (int) ($_POST['sort_order'] ?? 0),
            'status' => $_POST['status'] ?? 'active',
            'seo_title' => trim($_POST['seo_title'] ?? ''),
            'seo_description' => trim($_POST['seo_description'] ?? ''),
        ]);
        flash('success', 'Blog kategorisi oluşturuldu.');
        redirect('/admin/blog-kategorileri');
    }

    public function deleteCategory(string $id): void
    {
        Csrf::check();
        $this->db->delete('blog_categories', 'id = ?', [(int) $id]);
        flash('success', 'Blog kategorisi silindi.');
        redirect('/admin/blog-kategorileri');
    }

    // --- Helper Metotlar ---

    public function preview(): void
    {
        $rawContent = $_POST['raw_content'] ?? '';
        
        // 1. FAQ Extract
        $faqData = \App\Services\FaqExtractorService::extract($rawContent);
        $cleanedText = $faqData['cleaned_text'];
        
        // 2. Parse Markdown/Text
        $html = \App\Services\ArticleParserService::parse($cleanedText);
        
        // 3. Render Shortcodes
        $html = \App\Services\ArticleRendererService::render($html);
        
        // 4. Sanitize
        $html = \App\Services\SanitizerService::cleanHtml($html);
        
        // 5. Build TOC
        $tocData = \App\Services\TocBuilderService::build($html);
        
        header('Content-Type: application/json');
        echo json_encode([
            'html' => $tocData['html'],
            'toc' => $tocData['toc'],
            'faqs' => $faqData['faqs']
        ]);
        exit;
    }

    private function getPostData(): array
    {
        $status = $_POST['status'] ?? 'draft';
        $publishedAt = $_POST['published_at'] ?? date('Y-m-d H:i:s');
        
        if ($status !== 'draft' && strtotime($publishedAt) > time()) {
            $status = 'scheduled';
        }

        // New Smart Editor Flow
        $rawContent = $_POST['raw_content'] ?? '';
        
        // 1. Extract FAQs
        $faqData = \App\Services\FaqExtractorService::extract($rawContent);
        $cleanedText = $faqData['cleaned_text'];
        
        // 2. Parse to HTML
        $html = \App\Services\ArticleParserService::parse($cleanedText);
        
        // 3. Render Shortcodes
        $html = \App\Services\ArticleRendererService::render($html);
        
        // 4. Sanitize HTML
        $cleanContent = \App\Services\SanitizerService::cleanHtml($html);
        
        // 5. Build TOC
        $tocData = \App\Services\TocBuilderService::build($cleanContent);
        $finalHtml = $tocData['html'];
        $tocJson = !empty($tocData['toc']) ? json_encode($tocData['toc'], JSON_UNESCAPED_UNICODE) : null;
        
        // Prepare FAQs JSON (if user manually entered via UI, override or merge? The user wants automatic extraction. We will combine manual UI ones with extracted ones if needed, or just use extracted if they typed it in text).
        // Let's combine them: UI first, extracted second.
        $faqs = [];
        if (!empty($_POST['faq_questions']) && is_array($_POST['faq_questions'])) {
            foreach ($_POST['faq_questions'] as $index => $question) {
                $answer = $_POST['faq_answers'][$index] ?? '';
                if (trim($question) !== '' && trim($answer) !== '') {
                    $faqs[] = ['question' => trim($question), 'answer' => trim($answer)];
                }
            }
        }
        $faqs = array_merge($faqs, $faqData['faqs']);
        
        // Calculate reading time based on 200 words per min
        $wordCount = str_word_count(strip_tags($finalHtml));
        $readingTime = (int) ($_POST['reading_time'] ?? 0);
        if ($readingTime <= 0) {
            $readingTime = max(1, ceil($wordCount / 200));
        }

        return [
            'blog_category_id' => !empty($_POST['blog_category_id']) ? (int) $_POST['blog_category_id'] : null,
            'title' => trim($_POST['title'] ?? ''),
            'slug' => trim($_POST['slug'] ?? '') ?: slugify($_POST['title'] ?? ''),
            'excerpt' => trim($_POST['excerpt'] ?? ''),
            'raw_content' => $rawContent,
            'content' => $finalHtml,
            'toc' => $tocJson,
            'faqs' => !empty($faqs) ? json_encode($faqs, JSON_UNESCAPED_UNICODE) : null,
            'image_alt' => trim($_POST['image_alt'] ?? ''),
            'status' => $status,
            'published_at' => $publishedAt,
            
            'schema_type' => $_POST['schema_type'] ?? 'Article',
            'reading_time' => $readingTime,
            'noindex' => isset($_POST['noindex']) ? 1 : 0,
            
            'seo_title' => trim($_POST['seo_title'] ?? ''),
            'seo_description' => trim($_POST['seo_description'] ?? ''),
            'seo_focus_keyword' => trim($_POST['seo_focus_keyword'] ?? ''),
            'canonical_url' => trim($_POST['canonical_url'] ?? ''),
            'og_title' => trim($_POST['og_title'] ?? ''),
            'og_description' => trim($_POST['og_description'] ?? '')
        ];
    }

    private function generateUniqueSlug(string $slug, int $ignoreId = 0): string
    {
        $originalSlug = $slug;
        $counter = 1;
        while (true) {
            $exists = $this->db->fetch("SELECT id FROM blog_posts WHERE slug = ? AND id != ?", [$slug, $ignoreId]);
            if (!$exists) {
                break;
            }
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        return $slug;
    }

    private function syncTags(int $postId, array $tagIds): void
    {
        $this->db->delete('blog_post_tags', 'post_id = ?', [$postId]);
        foreach ($tagIds as $tagId) {
            // Check if tagId is numeric (existing tag) or string (new tag)
            if (!is_numeric($tagId)) {
                // If it's a new tag string, create it
                $slug = slugify($tagId);
                $existing = $this->db->fetch("SELECT id FROM blog_tags WHERE slug = ?", [$slug]);
                if ($existing) {
                    $tagId = $existing['id'];
                } else {
                    $tagId = $this->db->insert('blog_tags', ['name' => trim($tagId), 'slug' => $slug]);
                }
            }
            
            $this->db->insert('blog_post_tags', [
                'post_id' => $postId,
                'tag_id' => (int) $tagId
            ]);
        }
    }
}
