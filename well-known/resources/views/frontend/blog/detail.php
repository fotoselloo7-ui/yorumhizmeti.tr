<style>
/* Premium Blog Detail Layout */
:root {
    --blog-primary: #3b82f6;
    --blog-primary-light: #eff6ff;
    --blog-dark: #0f172a;
    --blog-text: #334155;
    --blog-muted: #64748b;
    --blog-border: #e2e8f0;
    --blog-bg: #f8fafc;
    --radius-lg: 24px;
    --radius-md: 16px;
    --shadow-soft: 0 10px 40px -10px rgba(0,0,0,0.08);
}

body { background-color: var(--blog-bg); }

.blog-hero {
    position: relative;
    background: #fff;
    padding: 60px 0 40px;
    border-bottom: 1px solid var(--blog-border);
}

.breadcrumb {
    font-size: 14px;
    font-weight: 500;
    color: var(--blog-muted);
    margin-bottom: 30px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.breadcrumb a { color: var(--blog-primary); text-decoration: none; transition: 0.2s; }
.breadcrumb a:hover { opacity: 0.8; }
.breadcrumb .separator { color: #cbd5e1; font-size: 12px; }

.blog-hero-meta {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 16px;
    margin-bottom: 24px;
}
.blog-hero-meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 14px;
    font-weight: 600;
    color: var(--blog-muted);
    background: var(--blog-primary-light);
    padding: 6px 14px;
    border-radius: 30px;
}
.blog-hero-meta-item i { color: var(--blog-primary); font-size: 16px; }

.blog-hero-title {
    font-size: 42px;
    font-weight: 900;
    color: var(--blog-dark);
    line-height: 1.25;
    margin-bottom: 30px;
    letter-spacing: -0.5px;
}
@media(max-width: 768px) { .blog-hero-title { font-size: 32px; } }

.blog-author-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 24px;
    border-top: 1px solid var(--blog-border);
}
.blog-author-info {
    display: flex;
    align-items: center;
    gap: 15px;
}
.blog-author-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--blog-primary), #8b5cf6);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    font-weight: 700;
}
.blog-author-text h4 { margin: 0 0 4px; font-size: 16px; font-weight: 700; color: var(--blog-dark); }
.blog-author-text p { margin: 0; font-size: 13px; color: var(--blog-muted); }

.blog-social-share {
    display: flex;
    align-items: center;
    gap: 10px;
}
.blog-social-share a {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--blog-bg);
    color: var(--blog-muted);
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    font-size: 18px;
    transition: 0.3s;
    border: 1px solid var(--blog-border);
}
.blog-social-share a:hover {
    background: var(--blog-primary);
    color: #fff;
    border-color: var(--blog-primary);
    transform: translateY(-2px);
}

.blog-content-wrapper {
    padding: 60px 0 80px;
}
.blog-layout {
    display: grid;
    grid-template-columns: 1fr 360px;
    gap: 50px;
}
@media(max-width: 1024px) {
    .blog-layout { grid-template-columns: 1fr; }
}

.blog-cover {
    width: 100%;
    border-radius: var(--radius-lg);
    margin-bottom: 40px;
    box-shadow: var(--shadow-soft);
    object-fit: cover;
    max-height: 500px;
}

/* Internal TOC */
.blog-toc {
    background: #fff;
    border: 1px solid var(--blog-border);
    border-radius: var(--radius-md);
    padding: 30px;
    margin-bottom: 40px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.02);
}
.blog-toc-title {
    font-size: 20px;
    font-weight: 800;
    color: var(--blog-dark);
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 12px;
}
.blog-toc-title i { color: var(--blog-primary); font-size: 24px; }
.blog-toc ul { list-style: none; padding: 0; margin: 0; }
.blog-toc ul li { margin-bottom: 12px; }
.blog-toc ul li:last-child { margin-bottom: 0; }
.blog-toc ul li a {
    color: var(--blog-text);
    text-decoration: none;
    font-size: 15px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 10px;
    transition: 0.2s;
}
.blog-toc ul li a::before {
    content: '';
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #cbd5e1;
    transition: 0.2s;
}
.blog-toc ul li a:hover { color: var(--blog-primary); }
.blog-toc ul li a:hover::before { background: var(--blog-primary); transform: scale(1.5); }
.blog-toc-level-3 { padding-left: 24px; }

/* Article Body */
.blog-body {
    font-size: 17px;
    line-height: 1.85;
    color: var(--blog-text);
}
.blog-body h2 { color: var(--blog-dark); font-size: 28px; font-weight: 800; margin: 50px 0 25px; letter-spacing: -0.5px; }
.blog-body h3 { color: var(--blog-dark); font-size: 22px; font-weight: 700; margin: 40px 0 20px; }
.blog-body p { margin-bottom: 24px; }
.blog-body ul, .blog-body ol { margin: 24px 0; padding-left: 20px; }
.blog-body li { margin-bottom: 12px; }
.blog-body blockquote {
    background: var(--blog-primary-light);
    border-left: 4px solid var(--blog-primary);
    padding: 24px 30px;
    border-radius: 0 var(--radius-md) var(--radius-md) 0;
    margin: 40px 0;
    font-size: 18px;
    font-style: italic;
    color: var(--blog-dark);
    font-weight: 500;
}
.blog-body img {
    border-radius: var(--radius-md);
    box-shadow: 0 4px 20px rgba(0,0,0,0.04);
    margin: 30px 0;
    width: 100%;
}

.blog-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin: 40px 0;
    padding-top: 30px;
    border-top: 1px solid var(--blog-border);
}
.blog-tag {
    background: #fff;
    border: 1px solid var(--blog-border);
    color: var(--blog-muted);
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    transition: 0.3s;
}
.blog-tag:hover {
    background: var(--blog-dark);
    color: #fff;
    border-color: var(--blog-dark);
    transform: translateY(-2px);
}

/* FAQ Accordion Premium */
.blog-faq-section { margin-top: 60px; }
.blog-faq-section h2 { font-size: 28px; font-weight: 800; color: var(--blog-dark); margin-bottom: 30px; }
.faq-accordion .faq-item {
    background: #fff;
    border: 1px solid var(--blog-border);
    border-radius: var(--radius-md);
    margin-bottom: 16px;
    transition: 0.3s;
    box-shadow: 0 2px 10px rgba(0,0,0,0.02);
}
.faq-accordion .faq-item:hover {
    border-color: #cbd5e1;
    box-shadow: var(--shadow-soft);
}
.faq-accordion .faq-question {
    width: 100%;
    text-align: left;
    background: none;
    border: none;
    padding: 24px;
    font-size: 17px;
    font-weight: 700;
    color: var(--blog-dark);
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.faq-accordion .faq-question i {
    width: 32px;
    height: 32px;
    background: var(--blog-primary-light);
    color: var(--blog-primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.3s, background 0.3s;
}
.faq-accordion .faq-question.active i { transform: rotate(180deg); background: var(--blog-primary); color: #fff; }
.faq-accordion .faq-answer {
    padding: 0 24px 24px;
    display: none;
    color: var(--blog-text);
    line-height: 1.7;
    font-size: 16px;
}

/* Sidebar Widgets */
.sidebar {
    position: sticky;
    top: 30px;
}
.sidebar-widget {
    background: #fff;
    border-radius: var(--radius-lg);
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: var(--shadow-soft);
    border: 1px solid rgba(0,0,0,0.03);
}
.sidebar-widget-title {
    font-size: 18px;
    font-weight: 800;
    color: var(--blog-dark);
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.sidebar-widget-title i { color: var(--blog-primary); font-size: 22px; }

.sidebar-search input {
    width: 100%;
    padding: 16px 20px 16px 50px;
    background: var(--blog-bg);
    border: 1px solid var(--blog-border);
    border-radius: 12px;
    font-size: 15px;
    outline: none;
    transition: 0.3s;
}
.sidebar-search input:focus {
    background: #fff;
    border-color: var(--blog-primary);
    box-shadow: 0 0 0 4px var(--blog-primary-light);
}
.sidebar-search { position: relative; }
.sidebar-search i {
    position: absolute;
    left: 20px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--blog-muted);
    font-size: 18px;
}

.sidebar-list { list-style: none; padding: 0; margin: 0; }
.sidebar-list li { margin-bottom: 10px; }
.sidebar-list li:last-child { margin-bottom: 0; }
.sidebar-list a {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 16px;
    background: var(--blog-bg);
    border-radius: 10px;
    color: var(--blog-text);
    text-decoration: none;
    font-weight: 600;
    font-size: 15px;
    transition: 0.2s;
}
.sidebar-list a:hover {
    background: var(--blog-primary);
    color: #fff;
    transform: translateX(5px);
}
.sidebar-list a i { opacity: 0.5; transition: 0.2s; }
.sidebar-list a:hover i { opacity: 1; transform: translateX(3px); }

.package-rec-item {
    display: flex;
    gap: 16px;
    padding: 16px;
    border-radius: 12px;
    background: var(--blog-bg);
    margin-bottom: 12px;
    transition: 0.3s;
    text-decoration: none;
    border: 1px solid transparent;
}
.package-rec-item:hover {
    background: #fff;
    border-color: var(--blog-primary);
    box-shadow: 0 4px 15px rgba(59, 130, 246, 0.1);
    transform: translateY(-2px);
}
.package-rec-item:last-child { margin-bottom: 0; }
.package-rec-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    background: linear-gradient(135deg, var(--blog-primary), #60a5fa);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    flex-shrink: 0;
}
.package-rec-info h4 { font-size: 15px; font-weight: 700; color: var(--blog-dark); margin: 0 0 4px; transition: 0.2s; }
.package-rec-item:hover .package-rec-info h4 { color: var(--blog-primary); }
.package-rec-info p { font-size: 13px; color: var(--blog-muted); margin: 0; }

.latest-posts-list { display: flex; flex-direction: column; gap: 16px; }
.latest-post-item { display: flex; gap: 15px; text-decoration: none; group; align-items: center; }
.latest-post-img { width: 70px; height: 70px; border-radius: 10px; object-fit: cover; flex-shrink: 0; }
.latest-post-info h4 { font-size: 14px; font-weight: 700; color: var(--blog-dark); margin: 0 0 6px; line-height: 1.4; transition: 0.2s; }
.latest-post-item:hover .latest-post-info h4 { color: var(--blog-primary); }
.latest-post-info span { font-size: 12px; color: var(--blog-muted); display: flex; align-items: center; gap: 4px; }
</style>

<div class="blog-hero">
    <div class="container">
        <div class="breadcrumb">
            <a href="/">Ana Sayfa</a>
            <span class="separator"><i class="ri-arrow-right-s-line"></i></span>
            <a href="/blog">Blog</a>
            <span class="separator"><i class="ri-arrow-right-s-line"></i></span>
            <span style="color: var(--blog-dark); font-weight: 600;"><?= e($post['title']) ?></span>
        </div>

        <div class="row">
            <div class="col-lg-10">
                <div class="blog-hero-meta">
                    <span class="blog-hero-meta-item"><i class="ri-folder-open-fill"></i> <?= e($post['category_name'] ?? 'Genel') ?></span>
                    <span class="blog-hero-meta-item"><i class="ri-time-fill"></i> <?= $post['reading_time'] ?> Dk Okuma</span>
                    <span class="blog-hero-meta-item"><i class="ri-eye-fill"></i> <?= number_format($post['views']) ?> Görüntülenme</span>
                </div>
                <h1 class="blog-hero-title"><?= e($post['title']) ?></h1>
                
                <div class="blog-author-bar">
                    <div class="blog-author-info">
                        <div class="blog-author-avatar">
                            <?= strtoupper(substr(setting('site_name'), 0, 1)) ?>
                        </div>
                        <div class="blog-author-text">
                            <h4>Yazar: <?= setting('site_name') ?></h4>
                            <p><?= date('d F Y', strtotime($post['published_at'])) ?> tarihinde yayınlandı.</p>
                        </div>
                    </div>
                    <div class="blog-social-share">
                        <a href="https://twitter.com/intent/tweet?text=<?= urlencode($post['title']) ?>&url=<?= urlencode(url('/blog/'.$post['slug'])) ?>" target="_blank"><i class="ri-twitter-x-line"></i></a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(url('/blog/'.$post['slug'])) ?>" target="_blank"><i class="ri-facebook-circle-fill"></i></a>
                        <a href="https://api.whatsapp.com/send?text=<?= urlencode($post['title'] . ' ' . url('/blog/'.$post['slug'])) ?>" target="_blank"><i class="ri-whatsapp-line"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="blog-content-wrapper">
    <div class="container">
        <div class="blog-layout">
            
            <!-- Main Content -->
            <article class="article-content">
                <?php if ($post['image']): ?>
                    <img src="<?= e(upload_url($post['image'])) ?>" alt="<?= e($post['image_alt'] ?? $post['title']) ?>" class="blog-cover">
                <?php endif; ?>

                <?php if (!empty($toc)): ?>
                <div class="blog-toc">
                    <div class="blog-toc-title"><i class="ri-list-ordered-2"></i> Bu Makalede Neler Var?</div>
                    <ul>
                        <?php foreach ($toc as $item): ?>
                        <li class="blog-toc-level-<?= $item['level'] ?>">
                            <a href="#<?= e($item['id']) ?>"><?= e($item['text']) ?></a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <div class="blog-body">
                    <?= $post['content'] ?>
                </div>

                <?php if (!empty($tags)): ?>
                <div class="blog-tags">
                    <span style="font-size: 14px; font-weight: 700; color: var(--blog-dark); margin-right: 10px; display: flex; align-items: center; gap: 5px;"><i class="ri-price-tag-3-fill"></i> Etiketler:</span>
                    <?php foreach ($tags as $tag): ?>
                    <a href="/blog?tag=<?= e($tag['slug']) ?>" class="blog-tag"><?= e($tag['name']) ?></a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <?php if (!empty($faqs)): ?>
                <div class="blog-faq-section">
                    <h2><i class="ri-question-answer-fill" style="color:var(--blog-primary); margin-right:10px;"></i> Sıkça Sorulan Sorular</h2>
                    <div class="faq-accordion">
                        <?php foreach ($faqs as $faq): ?>
                        <div class="faq-item">
                            <button class="faq-question">
                                <span><?= e($faq['question']) ?></span>
                                <i class="ri-arrow-down-s-line"></i>
                            </button>
                            <div class="faq-answer">
                                <?= nl2br(e($faq['answer'])) ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

            </article>

            <!-- Sidebar -->
            <aside>
                <div class="sidebar">
                    <div class="sidebar-widget">
                        <div class="sidebar-search">
                            <form action="/blog" method="GET">
                                <i class="ri-search-2-line"></i>
                                <input type="text" name="q" placeholder="Makalelerde ara...">
                            </form>
                        </div>
                    </div>

                    <?php if (!empty($categories)): ?>
                    <div class="sidebar-widget">
                        <div class="sidebar-widget-title"><i class="ri-folder-3-fill"></i> Kategoriler</div>
                        <ul class="sidebar-list">
                            <?php foreach ($categories as $cat): ?>
                            <li>
                                <a href="/blog?category=<?= e($cat['slug']) ?>">
                                    <span><?= e($cat['name']) ?></span> 
                                    <i class="ri-arrow-right-line"></i>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($popularPackages)): ?>
                    <div class="sidebar-widget">
                        <div class="sidebar-widget-title"><i class="ri-fire-fill" style="color:#ef4444;"></i> Önerilen Servisler</div>
                        <div>
                            <?php foreach ($popularPackages as $pkg): ?>
                            <a href="/paket/<?= e($pkg['slug']) ?>" class="package-rec-item">
                                <div class="package-rec-icon"><i class="ri-<?= e($pkg['icon_key'] ?? 'box-3-fill') ?>"></i></div>
                                <div class="package-rec-info">
                                    <h4><?= e($pkg['name']) ?></h4>
                                    <p><?= e($pkg['category_name']) ?></p>
                                </div>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($latestPosts)): ?>
                    <div class="sidebar-widget">
                        <div class="sidebar-widget-title"><i class="ri-article-fill"></i> Son Yazılar</div>
                        <div class="latest-posts-list">
                            <?php foreach ($latestPosts as $lp): ?>
                            <a href="/blog/<?= e($lp['slug']) ?>" class="latest-post-item">
                                <?php if($lp['image']): ?>
                                    <img src="<?= e(upload_url($lp['image'])) ?>" alt="" class="latest-post-img">
                                <?php else: ?>
                                    <div class="latest-post-img" style="background:#e2e8f0; display:flex; align-items:center; justify-content:center;"><i class="ri-image-line" style="color:#94a3b8; font-size:24px;"></i></div>
                                <?php endif; ?>
                                <div class="latest-post-info">
                                    <h4><?= e($lp['title']) ?></h4>
                                    <span><i class="ri-calendar-event-line"></i> <?= date('d.m.Y', strtotime($lp['published_at'])) ?></span>
                                </div>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </aside>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Accordion Logic
    const questions = document.querySelectorAll('.faq-question');
    questions.forEach(q => {
        q.addEventListener('click', function() {
            this.classList.toggle('active');
            const answer = this.nextElementSibling;
            if (answer.style.display === 'block') {
                answer.style.display = 'none';
            } else {
                answer.style.display = 'block';
            }
        });
    });

    // TOC Smooth Scroll
    document.querySelectorAll('.blog-toc a').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            if(targetElement) {
                const headerOffset = 100;
                const elementPosition = targetElement.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
  
                window.scrollTo({
                     top: offsetPosition,
                     behavior: "smooth"
                });
            }
        });
    });
});
</script>
