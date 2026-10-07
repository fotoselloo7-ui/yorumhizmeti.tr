<?php
$featured = $posts[0] ?? null;
$gridPosts = $featured ? array_slice($posts, 1) : $posts;
$recentComments = $testimonialSection['extra'] ?? [];
if (!function_exists('yvBlogPlatform')) {
    function yvBlogPlatform(string $name=''): array {
        $s = mb_strtolower($name);
        if (str_contains($s,'google')) return ['google','google'];
        if (str_contains($s,'instagram')) return ['instagram','instagram'];
        if (str_contains($s,'tiktok')) return ['tiktok','tiktok'];
        if (str_contains($s,'youtube')) return ['youtube','youtube'];
        if (str_contains($s,'seo') || str_contains($s,'web')) return ['web','globe'];
        return ['default','file-text'];
    }
}
?>
<div class="yv-blog-v8">
<section class="yv-blog-hero-v8">
  <div class="container">
    <div class="breadcrumb"><a href="/">Anasayfa</a><span class="separator">/</span><span>Blog / Bilgi Merkezi</span></div>
    <div class="yv-blog-hero-grid-v8">
      <div class="yv-blog-hero-copy-v8">
        <div class="yv-kicker"><?= icon('book-open',12) ?> Blog / Bilgi Merkezi</div>
        <h1>Dijital Başarınız İçin<br><span>Güncel Bilgiler, Rehberler ve İpuçları!</span></h1>
        <p>Sosyal medya, Google, web siteniz ve daha fazlası için uzman bilgileri, stratejileri ve pratik rehberleri keşfedin. YorumHizmeti.tr blogu ile dijital dünyada her zaman bir adım önde olun.</p>
        <form class="yv-blog-search-v8" method="GET" action="/blog">
          <?= icon('search',15) ?>
          <input name="q" value="<?= e($_GET['q']??'') ?>" placeholder="Hangi konuda bilgi arıyorsunuz?">
          <button type="submit">Ara <?= icon('arrow-right',11) ?></button>
        </form>
        <div class="yv-blog-hot-v8"><small>En çok aranan konular:</small><span>Google Yorum</span><span>Instagram Etkileşim</span><span>TikTok Takipçi</span><span>SEO</span></div>
      </div>
      <div class="yv-blog-hero-art-v8">
        <div class="yv-blog-woman-v8"></div>
        <span class="yv-blog-float-v8 google"><?= icon('google',25) ?></span>
        <span class="yv-blog-float-v8 instagram"><?= icon('instagram',24) ?></span>
        <span class="yv-blog-float-v8 tiktok"><?= icon('tiktok',23) ?></span>
        <span class="yv-blog-float-v8 youtube"><?= icon('youtube',23) ?></span>
        <span class="yv-blog-float-v8 chart"><?= icon('bar-chart',22) ?></span>
        <span class="yv-blog-float-v8 bulb"><?= icon('zap',20) ?></span>
      </div>
    </div>
  </div>
</section>

<section class="yv-blog-categories-v8">
  <div class="container">
    <div class="yv-blog-chip-grid-v8">
      <a class="<?= empty($_GET['category'])?'active':'' ?>" href="/blog"><?= icon('grid',14) ?><span>Tümü</span></a>
      <?php foreach(array_slice($categories,0,9) as $cat): [$cls,$ico]=yvBlogPlatform($cat['name']??''); ?>
      <a class="<?= (($_GET['category']??'')===$cat['slug'])?'active':'' ?>" href="/blog?category=<?= e($cat['slug']) ?>"><?= icon($ico,14) ?><span><?= e($cat['name']) ?></span></a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="yv-blog-main-v8">
  <div class="container">
    <div class="yv-blog-layout-v8">
      <main>
        <?php if($featured): ?>
        <article class="yv-blog-featured-v8">
          <div class="yv-blog-featured-copy-v8">
            <span>ÖNE ÇIKAN YAZI</span>
            <h2><?= e($featured['title']) ?></h2>
            <p><?= e(excerpt(strip_tags($featured['excerpt']??$featured['content']??''),170)) ?></p>
            <div class="yv-blog-featured-meta-v8"><small><?= !empty($featured['published_at'])?formatDate($featured['published_at'],'d M Y'):'Güncel' ?></small><small><?= (int)($featured['views']??0) ?> okunma</small><small>24 yorum</small></div>
            <a href="/blog/<?= e($featured['slug']) ?>">Yazıyı Oku <?= icon('arrow-right',11) ?></a>
          </div>
          <div class="yv-blog-featured-media-v8">
            <?php if(!empty($featured['image'])): ?><img src="<?= e(upload_url($featured['image'])) ?>" alt="<?= e($featured['image_alt']??$featured['title']) ?>"><?php else: ?><div class="yv-blog-phone-art-v8"><span><?= icon('google',42) ?></span><strong>4.8</strong><em>★★★★★</em></div><?php endif; ?>
          </div>
        </article>
        <?php endif; ?>

        <div class="yv-blog-head-v8"><div><h2>Son Yazılar</h2><p>Güncel rehberler, stratejiler ve uzman içerikleri ile dijital dünyada fark yaratın.</p></div><a href="/blog">Tüm Yazılar <?= icon('arrow-right',10) ?></a></div>

        <?php if(!empty($gridPosts)): ?>
        <div class="yv-blog-post-grid-v8">
          <?php foreach($gridPosts as $post): [$cls,$ico]=yvBlogPlatform($post['category_name']??$post['title']); ?>
          <a class="yv-blog-post-card-v8 <?= e($cls) ?>" href="/blog/<?= e($post['slug']) ?>">
            <div class="yv-blog-post-image-v8"><?php if(!empty($post['image'])): ?><img src="<?= e(upload_url($post['image'])) ?>" alt="<?= e($post['image_alt']??$post['title']) ?>"><?php else: ?><span><?= icon($ico,34) ?></span><?php endif; ?><b><?= e($post['category_name']??'Rehber') ?></b></div>
            <div class="yv-blog-post-body-v8"><h3><?= e($post['title']) ?></h3><p><?= e(excerpt(strip_tags($post['excerpt']??$post['content']??''),105)) ?></p><div><span><?= icon('calendar',10) ?> <?= !empty($post['published_at'])?formatDate($post['published_at'],'d M Y'):'Güncel' ?></span><span><?= icon('eye',10) ?> <?= number_format((int)($post['views']??0)) ?> okunma</span></div></div>
          </a>
          <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="yv-empty"><div class="yv-empty-icon"><?= icon('search',30) ?></div><h2>İçerik bulunamadı</h2><p>Arama veya filtre kriterlerine uygun yazı bulunamadı.</p></div>
        <?php endif; ?>

        <?php if($totalPages>1): ?><div class="pagination yv-blog-pagination-v8"><?php for($i=1;$i<=$totalPages;$i++): ?><a class="<?= $i==$page?'active':'' ?>" href="?<?= http_build_query(array_merge($_GET,['page'=>$i])) ?>"><?= $i ?></a><?php endfor; ?></div><?php endif; ?>
      </main>

      <aside class="yv-blog-sidebar-v8">
        <section class="yv-blog-side-card-v8"><div class="yv-blog-side-title-v8"><h3><?= icon('trending-up',14) ?> Popüler Yazılar</h3><a href="/blog">Tümünü Gör</a></div><?php foreach($popularPosts as $i=>$post): ?><a class="yv-blog-popular-v8" href="/blog/<?= e($post['slug']) ?>"><b><?= $i+1 ?></b><span><?php if(!empty($post['image'])): ?><img src="<?= e(upload_url($post['image'])) ?>" alt=""><?php else: ?><?= icon('file-text',18) ?><?php endif; ?></span><div><strong><?= e(excerpt($post['title'],48)) ?></strong><small><?= number_format((int)($post['views']??0)) ?> okunma</small></div></a><?php endforeach; ?></section>

        <?php if(!empty($recentComments)): ?><section class="yv-blog-side-card-v8"><div class="yv-blog-side-title-v8"><h3><?= icon('message-circle',14) ?> Son Yorumlar</h3><a href="#">Tümünü Gör</a></div><?php foreach(array_slice($recentComments,0,5) as $i=>$review): ?><div class="yv-blog-comment-v8"><span><?= mb_strtoupper(mb_substr($review['name']??'M',0,1)) ?></span><div><strong><?= e($review['name']??'Müşteri') ?></strong><p><?= e(excerpt($review['text']??'',52)) ?></p></div><small><?= $i<2?'2 saat önce':'1 gün önce' ?></small></div><?php endforeach; ?></section><?php endif; ?>

        <section class="yv-blog-news-v8"><div class="yv-blog-news-icon-v8"><?= icon('mail',22) ?></div><h3>E-Bülten'e Abone Olun</h3><p>En yeni içerikler, özel ipuçları ve kampanyalardan ilk siz haberdar olun.</p><input type="email" placeholder="E-posta adresinizi girin"><button type="button">Abone Ol <?= icon('arrow-right',10) ?></button><small><?= icon('check-circle',10) ?> Kişisel verilerimin işlenmesine izin veriyorum.</small></section>
      </aside>
    </div>
  </div>
</section>

<section class="yv-blog-benefits-v8"><div class="container"><h2>Neden Bilgi Merkezimizi Takip Etmelisiniz?</h2><p>Dijital dünyada doğru bilgi, doğru strateji ve sürdürülebilir başarı için yanınızdayız.</p><div><span><?= icon('award',19) ?><b>Uzman İçerikler</b><small>Alanında uzman ekibimiz tarafından hazırlanan rehberler.</small></span><span><?= icon('zap',19) ?><b>Güncel Bilgiler</b><small>Sürekli güncellenen içeriklerle her zaman güncel kalın.</small></span><span><?= icon('bar-chart',19) ?><b>Uygulanabilir Stratejiler</b><small>Hemen uygulayabileceğiniz pratik ipuçları ve taktikler.</small></span><span><?= icon('shield',19) ?><b>Güvenilir Kaynak</b><small>Deneyim ve gerçek verilere dayalı, şeffaf bilgiler.</small></span></div></div></section>

<?php if(!empty($faqs)): ?><section class="yv-blog-faq-v8"><div class="container"><div class="yv-blog-head-v8"><div><h2>Sıkça Sorulan Sorular</h2></div><a href="/sss">Tüm Soruları Gör <?= icon('arrow-right',10) ?></a></div><div class="yv-blog-faq-grid-v8"><?php foreach($faqs as $faq): ?><div><button type="button"><span><?= e($faq['question']) ?></span><?= icon('plus',11) ?></button><p><?= nl2br(e($faq['answer'])) ?></p></div><?php endforeach; ?></div></div></section><?php endif; ?>

<section class="yv-blog-final-v8"><div class="container"><div><?= icon('book-open',23) ?><span><strong>Bilgi ile Daha Güçlü Olun!</strong><small>Sosyal medya, Google ve dijital pazarlama hakkında en güncel rehberleri keşfedin.</small></span></div><a href="/blog">Tüm Yazıları Keşfet <?= icon('arrow-right',11) ?></a><div class="yv-blog-avatars-v8"><span>A</span><span>E</span><span>M</span><b>+50.000<small>Mutlu Takipçi</small></b></div></div></section>
</div>
<script>document.querySelectorAll('.yv-blog-faq-grid-v8 button').forEach(btn=>btn.addEventListener('click',()=>{const p=btn.nextElementSibling;p.style.display=p.style.display==='block'?'none':'block'}));</script>