<div class="yv-blog-home">
<section class="yv-page-hero">
 <div class="container">
  <div class="breadcrumb"><a href="/">Ana Sayfa</a> / <a href="/blog">Blog</a><?php if(!empty($post['category_name'])): ?> / <span><?= e($post['category_name']) ?></span><?php endif; ?></div>
  <div class="yv-hero-grid" style="margin-top:16px">
   <div>
    <div class="yv-kicker"><?= icon('file-text',12) ?> <?= e($post['category_name']??'Bilgi Merkezi') ?></div>
    <h1 style="font-size:clamp(34px,4vw,54px)"><?= e($post['title']) ?></h1>
    <p><?= e($post['excerpt'] ?: excerpt(strip_tags($post['content']),180)) ?></p>
    <div class="yv-trust-row">
     <span class="yv-trust-pill"><?= icon('calendar',11) ?> <?= !empty($post['published_at'])?formatDate($post['published_at'],'d.m.Y'):'Güncel' ?></span>
     <span class="yv-trust-pill"><?= icon('eye',11) ?> <?= (int)($post['views']??0) ?> görüntülenme</span>
     <?php if(!empty($post['reading_time'])): ?><span class="yv-trust-pill"><?= icon('clock',11) ?> <?= (int)$post['reading_time'] ?> dk okuma</span><?php endif; ?>
    </div>
   </div>
   <div class="yv-hero-art yv-blog-detail-art-v12">
    <img src="<?= e(!empty($post['image']) ? upload_url($post['image']) : demo_visual_url($post['title'].' '.($post['category_name']??''),'blog detail')) ?>" alt="<?= e($post['image_alt']??$post['title']) ?>"<?= !empty($nvSeoData['image_title']) ? ' title="'.e($nvSeoData['image_title']).'"' : '' ?>>
    <div class="yv-blog-detail-shade-v12"></div>
    <div class="yv-float a"><?= icon('bookmark',13) ?> Kaydet & paylaş</div>
   </div>
  </div>
 </div>
</section>

<section style="padding:42px 0 72px;background:#f8f9fd">
 <div class="container">
  <div class="yv-blog-layout">
   <main>
    <article class="yv-content-card" style="max-width:none;margin:0">
     <?php if(!empty($toc)): ?><div class="blog-toc" style="margin-bottom:22px;padding:16px"><strong style="font-size:11px">İçindekiler</strong><ul style="margin:10px 0 0;padding-left:18px"><?php foreach($toc as $item): ?><li style="font-size:9px;margin:6px 0"><a href="#<?= e($item['id']??'') ?>"><?= e($item['text']??'') ?></a></li><?php endforeach; ?></ul></div><?php endif; ?>
     <?php
       $nvAnswer=trim((string)($nvSeoData['direct_answer']??''));
       $nvQuestion=trim((string)($nvSeoData['main_question']??''));
     ?>
     <?php if($nvAnswer!==''): ?>
     <aside class="nv44-answer-box" aria-label="Kısa cevap">
       <span class="nv44-answer-label"><?= icon('sparkles',15) ?> Kısa Cevap</span>
       <?php if($nvQuestion!==''): ?><h2><?= e($nvQuestion) ?></h2><?php endif; ?>
       <p><?= e($nvAnswer) ?></p>
     </aside>
     <?php endif; ?>
     <div class="blog-body nv42-article-body"><?= \App\Services\BlogContentRenderer::render((string)$post['content']) ?></div>
     <?php
       $nvSourceUrls=preg_split('/\R/',(string)($nvSeoData['sources']??''))?:[];
       $nvSafeSources=[];
       foreach($nvSourceUrls as $nvSource){
          $nvSource=trim($nvSource);
          if($nvSource!==''&&filter_var($nvSource,FILTER_VALIDATE_URL)
             &&in_array(strtolower((string)parse_url($nvSource,PHP_URL_SCHEME)),['http','https'],true))
             $nvSafeSources[]=$nvSource;
       }
       $nvSafeSources=array_slice(array_unique($nvSafeSources),0,15);
     ?>
     <?php if($nvSafeSources): ?>
       <section class="nv44-sources" aria-label="Makale kaynakları">
         <h2>Kaynaklar ve İleri Okuma</h2>
         <ul>
           <?php foreach($nvSafeSources as $nvSource): ?>
             <li><a href="<?= e($nvSource) ?>" rel="noopener noreferrer nofollow" target="_blank"><?= e((string)(parse_url($nvSource,PHP_URL_HOST)?:$nvSource)) ?> <?= icon('external-link',12) ?></a></li>
           <?php endforeach; ?>
         </ul>
       </section>
     <?php endif; ?>
     <?php if(!empty($nvSeoData['author_name']) || !empty($nvSeoData['reviewer_name'])): ?>
       <div class="nv44-byline">
         <?php if(!empty($nvSeoData['author_name'])): ?>
           <span><?= icon('user-round',13) ?> Yazar: <?= e($nvSeoData['author_name']) ?></span>
         <?php endif; ?>
         <?php if(!empty($nvSeoData['reviewer_name'])): ?>
           <span><?= icon('check-circle',13) ?> Kontrol: <?= e($nvSeoData['reviewer_name']) ?></span>
         <?php endif; ?>
         <?php if(!empty($nvSeoData['last_reviewed'])): ?>
           <span><?= e($nvSeoData['last_reviewed']) ?></span>
         <?php endif; ?>
       </div>
     <?php endif; ?>
     <?php if(!empty($tags)): ?><div style="display:flex;gap:6px;flex-wrap:wrap;margin-top:26px;padding-top:18px;border-top:1px solid #edf0f4"><?php foreach($tags as $tag): ?><a class="yv-blog-chip" href="/blog/etiket/<?= rawurlencode((string)$tag['slug']) ?>">#<?= e($tag['name']) ?></a><?php endforeach; ?></div><?php endif; ?>
    </article>

    <?php if(!empty($faqs)): ?><section class="yv-product-section" style="margin-top:14px"><div class="yv-kicker">Merak edilenler</div><h3>Bu yazıyla ilgili sorular</h3><?php foreach($faqs as $faq): ?><div class="premium-faq-item"><button type="button" class="premium-faq-question"><span><?= e($faq['question']) ?></span><?= icon('chevron-down',12) ?></button><div class="premium-faq-answer"><?= e($faq['answer']) ?></div></div><?php endforeach; ?></section><?php endif; ?>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:14px">
     <?php if($prevPost): ?><a class="yv-package-card" href="/blog/<?= e($prevPost['slug']) ?>"><small style="font-size:7px;color:#8d95a7">Önceki yazı</small><h3 style="min-height:0;margin-top:6px"><?= e($prevPost['title']) ?></h3></a><?php else: ?><div></div><?php endif; ?>
     <?php if($nextPost): ?><a class="yv-package-card" href="/blog/<?= e($nextPost['slug']) ?>" style="text-align:right"><small style="font-size:7px;color:#8d95a7">Sonraki yazı</small><h3 style="min-height:0;margin-top:6px"><?= e($nextPost['title']) ?></h3></a><?php endif; ?>
    </div>
   </main>
   <aside>
    <?php if(!empty($latestPosts)): ?><div class="yv-sidebar-card"><h3><?= icon('clock',13) ?> Son Yazılar</h3><?php foreach($latestPosts as $i=>$lp): ?><div class="yv-popular-item"><span class="yv-popular-num"><?= $i+1 ?></span><a href="/blog/<?= e($lp['slug']) ?>"><?= e($lp['title']) ?></a></div><?php endforeach; ?></div><?php endif; ?>
    <?php if(!empty($categories)): ?><div class="yv-sidebar-card"><h3><?= icon('folder',13) ?> Kategoriler</h3><div style="display:flex;gap:6px;flex-wrap:wrap"><?php foreach($categories as $cat): ?><a class="yv-blog-chip" href="/blog/kategori/<?= rawurlencode((string)$cat['slug']) ?>"><?= e($cat['name']) ?></a><?php endforeach; ?></div></div><?php endif; ?>
    <?php if(!empty($popularPackages)): ?><div class="yv-sidebar-card"><h3><?= icon('shopping-cart',13) ?> Popüler Paketler</h3><?php foreach($popularPackages as $pkg): ?><a href="/paket/<?= e($pkg['slug']) ?>" style="display:block;padding:9px 0;border-bottom:1px solid #edf0f4"><strong style="font-size:8px;color:#25304d"><?= e(package_display_name($pkg)) ?></strong><span style="display:block;margin-top:3px;font-size:8px;color:#664be8"><?= money((!empty($pkg['discount_price'])&&$pkg['discount_price']<$pkg['price'])?$pkg['discount_price']:$pkg['price']) ?></span></a><?php endforeach; ?></div><?php endif; ?>
   </aside>
  </div>
 </div>
</section>
</div>
<script>document.querySelectorAll('.premium-faq-question').forEach(q=>q.addEventListener('click',()=>{const a=q.nextElementSibling;a.style.display=a.style.display==='block'?'none':'block'}));</script>