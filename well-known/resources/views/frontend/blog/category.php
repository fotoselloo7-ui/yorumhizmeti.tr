<div class="breadcrumb container" style="padding-top: var(--space-4);"><a href="/">Ana Sayfa</a> <span class="separator">/</span> <a href="/blog">Blog</a> <span class="separator">/</span> <span><?= e($category['name']) ?></span></div>
<section class="section">
    <div class="container">
        <div class="section-header"><h1 style="font-size: var(--font-size-2xl);"><?= e($category['name']) ?></h1></div>
        <?php if (!empty($posts)): ?>
        <div class="blog-grid">
            <?php foreach ($posts as $post): ?>
            <div class="blog-card">
                <div class="blog-card-image"><?php if ($post['image']): ?><img src="<?= e(upload_url($post['image'])) ?>" alt="<?= e($post['title']) ?>" loading="lazy"><?php else: ?><?= icon('file-text', 40) ?><?php endif; ?></div>
                <div class="blog-card-body">
                    <h3><a href="/blog/<?= e($post['slug']) ?>"><?= e($post['title']) ?></a></h3>
                    <p><?= e(excerpt($post['excerpt'] ?? '', 120)) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state"><?= icon('file-text', 48) ?><h3>Bu kategoride henüz yazı yok</h3></div>
        <?php endif; ?>
    </div>
</section>
