<style>
.blog-hero { background: linear-gradient(135deg, var(--color-primary-dark) 0%, var(--color-primary) 100%); color: #fff; padding: 60px 0; text-align: center; border-radius: 16px; margin-bottom: 40px; }
.blog-hero h1 { font-size: 36px; font-weight: 800; margin-bottom: 15px; color: #fff; }
.blog-hero p { font-size: 18px; opacity: 0.9; max-width: 600px; margin: 0 auto 30px; }
.blog-search { max-width: 500px; margin: 0 auto; position: relative; }
.blog-search input { width: 100%; padding: 15px 20px 15px 50px; border-radius: 30px; border: none; font-size: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); outline: none; }
.blog-search i { position: absolute; left: 20px; top: 50%; transform: translateY(-50%); color: var(--color-text-secondary); font-size: 20px; }
.blog-categories-pills { display: flex; flex-wrap: wrap; gap: 10px; justify-content: center; margin-bottom: 40px; }
.blog-pill { padding: 8px 16px; border-radius: 20px; background: #fff; color: var(--color-text); text-decoration: none; font-weight: 500; font-size: 14px; border: 1px solid var(--color-border); transition: all 0.3s ease; }
.blog-pill:hover, .blog-pill.active { background: var(--color-primary); color: #fff; border-color: var(--color-primary); }
.blog-layout { display: grid; grid-template-columns: 1fr 320px; gap: 30px; }
@media(max-width: 991px) { .blog-layout { grid-template-columns: 1fr; } }
.blog-card { background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.03); border: 1px solid var(--color-border); transition: transform 0.3s ease; display: flex; flex-direction: column; }
.blog-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.08); }
.blog-card-img { width: 100%; height: 200px; object-fit: cover; }
.blog-card-img-placeholder { width: 100%; height: 200px; background: var(--color-light); display: flex; align-items: center; justify-content: center; color: var(--color-text-secondary); }
.blog-card-content { padding: 20px; flex: 1; display: flex; flex-direction: column; }
.blog-card-meta { display: flex; align-items: center; gap: 15px; font-size: 13px; color: var(--color-text-secondary); margin-bottom: 10px; }
.blog-card-meta span { display: flex; align-items: center; gap: 5px; }
.blog-card-title { font-size: 18px; font-weight: 700; margin-bottom: 10px; color: var(--color-dark); line-height: 1.4; }
.blog-card-title a { color: inherit; text-decoration: none; }
.blog-card-title a:hover { color: var(--color-primary); }
.blog-card-excerpt { color: var(--color-text-secondary); font-size: 14px; line-height: 1.6; margin-bottom: 20px; flex: 1; }
.blog-sidebar-widget { background: #fff; border-radius: 12px; padding: 25px; border: 1px solid var(--color-border); margin-bottom: 30px; }
.blog-sidebar-widget h3 { font-size: 18px; font-weight: 700; margin-bottom: 20px; color: var(--color-dark); display: flex; align-items: center; gap: 10px; padding-bottom: 15px; border-bottom: 1px solid var(--color-border); }
.popular-post-item { display: flex; gap: 15px; margin-bottom: 15px; }
.popular-post-item:last-child { margin-bottom: 0; }
.popular-post-item img { width: 70px; height: 70px; border-radius: 8px; object-fit: cover; }
.popular-post-item-info h4 { font-size: 14px; font-weight: 600; line-height: 1.3; margin-bottom: 5px; }
.popular-post-item-info h4 a { color: var(--color-dark); text-decoration: none; }
.popular-post-item-info h4 a:hover { color: var(--color-primary); }
.popular-post-meta { font-size: 12px; color: var(--color-text-secondary); display: flex; align-items: center; gap: 5px; }
</style>

<div class="container" style="padding-top: 40px; padding-bottom: 80px;">
    
    <div class="blog-hero">
        <h1>Dijital Rehber & Blog</h1>
        <p>Sosyal medya büyüme stratejileri, SEO taktikleri ve güncel dijital pazarlama haberleri.</p>
        <div class="blog-search">
            <form action="/blog" method="GET">
                <i class="ri-search-line"></i>
                <input type="text" name="q" placeholder="Blogda ara..." value="<?= e($_GET['q'] ?? '') ?>">
            </form>
        </div>
    </div>

    <div class="blog-categories-pills">
        <a href="/blog" class="blog-pill <?= empty($_GET['category']) ? 'active' : '' ?>">Tümü</a>
        <?php foreach($categories as $cat): ?>
        <a href="/blog?category=<?= e($cat['slug']) ?>" class="blog-pill <?= ($_GET['category'] ?? '') === $cat['slug'] ? 'active' : '' ?>">
            <?= e($cat['name']) ?>
        </a>
        <?php endforeach; ?>
    </div>

    <div class="blog-layout">
        <!-- Main Content -->
        <div>
            <?php if (!empty($posts)): ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 30px;">
                <?php foreach ($posts as $post): ?>
                <div class="blog-card">
                    <?php if ($post['image']): ?>
                        <a href="/blog/<?= e($post['slug']) ?>"><img src="<?= e(upload_url($post['image'])) ?>" alt="<?= e($post['image_alt'] ?? $post['title']) ?>" class="blog-card-img" loading="lazy"></a>
                    <?php else: ?>
                        <a href="/blog/<?= e($post['slug']) ?>" class="blog-card-img-placeholder"><?= icon('image', 40) ?></a>
                    <?php endif; ?>
                    <div class="blog-card-content">
                        <div class="blog-card-meta">
                            <span><i class="ri-folder-2-line"></i> <?= e($post['category_name'] ?? 'Kategorisiz') ?></span>
                            <span><i class="ri-calendar-line"></i> <?= date('d M Y', strtotime($post['published_at'])) ?></span>
                            <span><i class="ri-eye-line"></i> <?= number_format($post['views']) ?></span>
                        </div>
                        <h3 class="blog-card-title"><a href="/blog/<?= e($post['slug']) ?>"><?= e($post['title']) ?></a></h3>
                        <p class="blog-card-excerpt"><?= e(excerpt(strip_tags($post['excerpt'] ?: $post['content']), 120)) ?></p>
                        <a href="/blog/<?= e($post['slug']) ?>" style="color: var(--color-primary); font-weight: 600; text-decoration: none; font-size: 14px; display: inline-flex; align-items: center; gap: 5px;">Devamını Oku <i class="ri-arrow-right-line"></i></a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <?php if ($totalPages > 1): ?>
            <div class="pagination" style="margin-top: 40px; display: flex; justify-content: center; gap: 10px;">
                <?php 
                $queryStr = $_GET;
                unset($queryStr['page']);
                $qStr = http_build_query($queryStr);
                $qStr = $qStr ? '&' . $qStr : '';
                ?>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if ($i === $page): ?>
                        <span style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: var(--color-primary); color: #fff; border-radius: 8px; font-weight: 600;"><?= $i ?></span>
                    <?php else: ?>
                        <a href="?page=<?= $i ?><?= $qStr ?>" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: #fff; color: var(--color-dark); border: 1px solid var(--color-border); border-radius: 8px; font-weight: 600; text-decoration: none; transition: all 0.3s ease;" onmouseover="this.style.background='var(--color-primary)'; this.style.color='#fff';" onmouseout="this.style.background='#fff'; this.style.color='var(--color-dark)';"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
            </div>
            <?php endif; ?>

            <?php else: ?>
            <div style="background: #fff; border-radius: 12px; padding: 60px 20px; text-align: center; border: 1px solid var(--color-border);">
                <i class="ri-file-search-line" style="font-size: 64px; color: var(--color-text-secondary); margin-bottom: 20px; display: block;"></i>
                <h3 style="font-size: 20px; font-weight: 600; color: var(--color-dark); margin-bottom: 10px;">Kayıt Bulunamadı</h3>
                <p style="color: var(--color-text-secondary);">Arama kriterlerinize uygun blog yazısı bulunmuyor.</p>
                <a href="/blog" class="btn btn-primary" style="margin-top: 20px;">Tüm Yazıları Gör</a>
            </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <aside>
            <div class="blog-sidebar-widget">
                <h3><i class="ri-fire-line" style="color: #ff9800;"></i> Popüler Yazılar</h3>
                <?php if(!empty($popularPosts)): ?>
                    <?php foreach($popularPosts as $pop): ?>
                    <div class="popular-post-item">
                        <?php if($pop['image']): ?>
                        <a href="/blog/<?= e($pop['slug']) ?>"><img src="<?= e(upload_url($pop['image'])) ?>" alt="<?= e($pop['title']) ?>" loading="lazy"></a>
                        <?php else: ?>
                        <a href="/blog/<?= e($pop['slug']) ?>" style="width: 70px; height: 70px; border-radius: 8px; background: var(--color-light); display: flex; align-items: center; justify-content: center; color: var(--color-text-secondary);"><i class="ri-image-line" style="font-size: 24px;"></i></a>
                        <?php endif; ?>
                        <div class="popular-post-item-info">
                            <h4><a href="/blog/<?= e($pop['slug']) ?>"><?= e($pop['title']) ?></a></h4>
                            <div class="popular-post-meta">
                                <span><i class="ri-eye-line"></i> <?= number_format($pop['views']) ?> hit</span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-sm text-secondary">Popüler yazı bulunmuyor.</p>
                <?php endif; ?>
            </div>
        </aside>
    </div>
</div>
