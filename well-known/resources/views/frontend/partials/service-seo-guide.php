<?php
// $nvSeoGuide is a plain-text content array, derived from the current real
// category or package. Structured data is intentionally NOT added here:
 // a FAQ rich-result promise would be inappropriate for this corporate site.
?>
<section class="nv-service-guide" aria-labelledby="nv-service-guide-title">
 <div class="container">
  <div class="nv-service-guide-head">
   <span class="nv-service-guide-kicker">NETVERA / HİZMET REHBERİ</span>
   <h2 id="nv-service-guide-title"><?= e($nvSeoGuide['title']) ?></h2>
   <p><?= e($nvSeoGuide['lead']) ?></p>
  </div>
  <div class="nv-service-guide-layout">
   <article class="nv-service-guide-copy">
    <?php foreach($nvSeoGuide['paragraphs'] as $paragraph): ?><p><?= e($paragraph) ?></p><?php endforeach; ?>
    <p><a href="/iletisim"><?= icon('arrow-up-right',15) ?> Projeniz hakkında bilgi ve teklif alın</a></p>
   </article>
   <div class="nv-service-guide-faq" aria-label="Bu hizmet için sık sorulan sorular">
    <h3>Sıkça sorulan sorular</h3>
    <?php foreach($nvSeoGuide['faq'] as $qa): ?>
    <details>
     <summary><?= e($qa['question']) ?> <?= icon('plus',15) ?></summary>
     <p><?= e($qa['answer']) ?></p>
    </details>
    <?php endforeach; ?>
   </div>
  </div>
 </div>
</section>
