<?php
$featured=$posts[0]??null;
$gridPosts=$featured?array_slice($posts,1):$posts;
?>
<div class="yv-blog-home">
<section class="yv-page-hero yv-blog-hero">
 <div class="container">
  <div class="yv-hero-grid">
   <div>
    <div class="yv-kicker"><?= icon('file-text',12) ?> Blog / Bilgi Merkezi</div>
    <h1>Dijital başarınız için <span>rehberler ve ipuçları.</span></h1>
    <p>Sosyal medya, Google, web siteleri ve dijital büyüme hakkında güncel içerikleri keşfedin. Doğru bilgiyle daha güçlü kararlar alın.</p>
    <form class="yv-blog-search" method="GET" action="/blog"><input name="q" value="<?= e($_GET['q']??'') ?>" placeholder="Hangi konuda bilgi arıyorsunuz?"><button type="submit"><?= icon('search',12) ?> Ara</button></form>
   </div>
   <div class="yv-hero-art"><div class="yv-hero-person"></div><div class="yv-float a"><?= icon('google',14) ?> Google</div><div class="yv-float b"><?= icon('instagram',14) ?> Instagram</div><div class="yv-float c"><?= icon('trending-up',14) ?> Büyüme rehberleri</div></div>
  </div>
 </div>
</section>
<div class="container">
 <div class="yv-blog-chips"><a class="yv-blog-chip <?= empty($_GET['category'])?'active':'' ?>" href="/blog">Tümü</a><?php foreach($categories as $cat): ?><a class="yv-blog-chip" href="/blog?category=<?= e($cat['slug']) ?>"><?= e($cat['name']) ?></a><?php endforeach; ?></div>
 <div class="yv-blog-layout">
  <main>
   <?php if($featured): ?><article class="yv-featured-post"><div class="yv-featured-copy"><div class="yv-kicker" style="background:rgba(255,255,255,.12);color:#fff">Öne çıkan yazı</div><h2><?= e($featured['title']) ?></h2><p><?= e(excerpt(strip_tags($featured['excerpt']??$featured['content']??''),180)) ?></p><a class="yv-btn yv-btn-light" href="/blog/<?= e($featured['slug']) ?>">Yazıyı oku <?= icon('arrow-right',11) ?></a></div><div class="yv-featured-media"><?php if(!empty($featured['image'])): ?><img src="<?= e(upload_url($featured['image'])) ?>" alt="<?= e($featured['image_alt']??$featured['title']) ?>" style="width:100%;height:100%;object-fit:cover"><?php endif; ?></div></article><?php endif; ?>
   <div style="display:flex;align-items:end;justify-content:space-between;margin:22px 0 13px"><div><div class="yv-kicker">Güncel içerikler</div><h2 style="margin:0;font-size:27px;letter-spacing:-.045em">Son Yazılar</h2></div></div>
   <?php if(!empty($gridPosts)): ?><div class="yv-post-grid"><?php foreach($gridPosts as $post): ?><a class="yv-post-card" href="/blog/<?= e($post['slug']) ?>"><div class="yv-post-image"><?php if(!empty($post['image'])): ?><img src="<?= e(upload_url($post['image'])) ?>" alt="<?= e($post['image_alt']??$post['title']) ?>"><?php else: ?><div class="yv-post-placeholder"><?= icon('file-text',26) ?></div><?php endif; ?></div><div class="yv-post-body"><small><?= e($post['category_name']??'Rehber') ?></small><h3><?= e($post['title']) ?></h3><p><?= e(excerpt(strip_tags($post['excerpt']??$post['content']??''),95)) ?></p><div class="yv-post-meta"><span><?= !empty($post['published_at'])?formatDate($post['published_at'],'d.m.Y'):'Güncel' ?></span><span><?= (int)($post['views']??0) ?> görüntülenme</span></div></div></a><?php endforeach; ?></div><?php else: ?><div class="yv-empty"><div class="yv-empty-icon"><?= icon('search',30) ?></div><h2>İçerik bulunamadı</h2><p>Arama veya filtre kriterlerine uygun yazı bulunamadı.</p></div><?php endif; ?>
   <?php if($totalPages>1): ?><div class="pagination" style="margin-top:22px"><?php for($i=1;$i<=$totalPages;$i++): ?><a class="<?= $i==$page?'active':'' ?>" href="?<?= http_build_query(array_merge($_GET,['page'=>$i])) ?>"><?= $i ?></a><?php endfor; ?></div><?php endif; ?>
  </main>
  <aside>
   <div class="yv-sidebar-card"><h3><?= icon('trending-up',13) ?> Popüler Yazılar</h3><?php foreach($popularPosts as $i=>$post): ?><div class="yv-popular-item"><span class="yv-popular-num"><?= $i+1 ?></span><a href="/blog/<?= e($post['slug']) ?>"><?= e($post['title']) ?></a></div><?php endforeach; ?></div>
   <div class="yv-sidebar-card"><h3><?= icon('mail',13) ?> E-Bülten</h3><p style="font-size:8px;color:#7e879a;line-height:1.6">Yeni rehber ve içeriklerden haberdar olun.</p><div class="footer-newsletter-form"><input type="email" placeholder="E-posta adresiniz"><button type="button"><?= icon('arrow-right',11) ?></button></div></div>
  </aside>
 </div>
</div>
</div>