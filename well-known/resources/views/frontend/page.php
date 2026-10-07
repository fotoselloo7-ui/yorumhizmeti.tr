<section class="section">
    <div class="container">
        <div class="breadcrumb mb-4"><a href="/">Ana Sayfa</a> <span class="separator">/</span> <span><?= e($page['title']) ?></span></div>
        <div class="blog-content card" style="max-width: 800px; margin: 0 auto;">
            <h1><?= e($page['title']) ?></h1>
            <?= $page['content'] ?>
        </div>
    </div>
</section>
