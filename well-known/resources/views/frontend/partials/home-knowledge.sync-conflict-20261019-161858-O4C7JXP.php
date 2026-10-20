<?php use App\Services\LandingContentService as Editorial; $knowledge=\App\Services\HomeKnowledgeService::get(); ?>
<?php if($knowledge['enabled']): ?>
<section class="home-knowledge" id="hizmet-rehberi" aria-labelledby="knowledge-title"><div class="container">
 <header class="knowledge-heading"><span><?= icon('file-text',17) ?> NETVERA BİLGİ NOTLARI</span><h2 id="knowledge-title"><?= e($knowledge['title']) ?></h2><p><?= e($knowledge['summary']) ?></p></header>
 <div class="knowledge-grid"><?php foreach($knowledge['cards']as $n=>$card): if(!$card['enabled'])continue; ?>
  <article class="knowledge-card knowledge-theme-<?= e($card['theme']) ?>">
   <div class="knowledge-card-top"><span><?= icon($card['icon'],25) ?></span><small>0<?= $n+1 ?> / REHBER</small></div>
   <h3><?= e($card['title']) ?></h3><div class="knowledge-copy"><?php foreach(preg_split('/\R\s*\R/u',$card['body'])?:[]as $p): ?><p><?= Editorial::rich($p) ?></p><?php endforeach; ?></div>
   <?php if($card['question']!==''): ?><details class="knowledge-question"><summary><?= e($card['question']) ?><?= icon('plus',15) ?></summary><p><?= Editorial::rich($card['answer']) ?></p></details><?php endif; ?>
   <?php foreach($card['faqs']as $faq): ?><details class="knowledge-question"><summary><?= e($faq['question']) ?><?= icon('plus',15) ?></summary><p><?= Editorial::rich($faq['answer']) ?></p></details><?php endforeach; ?>
   <?php if(!empty($card['seo']['geo_summary'])): ?><p class="knowledge-note"><?= e($card['seo']['geo_summary']) ?></p><?php endif; ?>
   <?php if(!empty($card['seo']['direct_answer'])): ?><p class="knowledge-note"><?= Editorial::rich($card['seo']['direct_answer']) ?></p><?php endif; ?>
   <nav class="knowledge-links" aria-label="<?= e($card['title']) ?> bağlantıları"><?php foreach($card['links']as $link): ?><a href="<?= e($link['path']) ?>"><?= e($link['label']) ?><?= icon('arrow-up-right',15) ?></a><?php endforeach; ?></nav>
   <?php if(!empty($card['seo']['sources'])): ?><details class="knowledge-sources"><summary>Kaynaklar</summary><?php foreach(explode("\n",$card['seo']['sources'])as $source): ?><a href="<?= e($source) ?>" rel="noopener noreferrer" target="_blank"><?= e(parse_url($source,PHP_URL_HOST)) ?><?= icon('arrow-up-right',12) ?></a><?php endforeach; ?></details><?php endif; ?>
  </article>
 <?php endforeach; ?></div>
</div></section>
<?php endif; ?>
