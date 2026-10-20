<?php
$links=$contentData['links']??[];$questions=$contentData['faqs']??[];
$linksPrefix=$contentHome?$contentPrefix.'[links]':'links';$faqsPrefix=$contentHome?$contentPrefix.'[faqs]':'faqs';
?>
<details class="landing-editor-details"><summary><?= icon('link',18) ?> İç bağlantılar</summary><div class="landing-editor-fields">
<p class="landing-editor-help">Metin ve site içi yolu birlikte yazın. Ana sayfa, hizmet, kategori veya konumsal sayfalara bağlantı verebilirsiniz.</p>
<?php for($i=0;$i<($contentHome?6:12);$i++): ?><div class="landing-editor-grid">
<div class="form-group"><label for="link-label-<?= e($contentId) ?>-<?= $i ?>">Bağlantı <?= $i+1 ?> metni</label><input id="link-label-<?= e($contentId) ?>-<?= $i ?>" class="form-control" name="<?= e($linksPrefix) ?>[<?= $i ?>][label]" maxlength="100" value="<?= e($links[$i]['label']??'') ?>"></div>
<div class="form-group"><label for="link-path-<?= e($contentId) ?>-<?= $i ?>">Site içi yol</label><input id="link-path-<?= e($contentId) ?>-<?= $i ?>" class="form-control" name="<?= e($linksPrefix) ?>[<?= $i ?>][path]" maxlength="500" value="<?= e($links[$i]['path']??'') ?>"></div>
</div><?php endfor; ?></div></details>
<details class="landing-editor-details"><summary><?= icon('help-circle',18) ?> Soru / cevap</summary><div class="landing-editor-fields">
<p class="landing-editor-help">Sorular ve yanıtlar görünür içeriktir. Arama sonucunda FAQ gösterimi garanti edilmez.</p>
<?php for($i=0;$i<($contentHome?4:12);$i++): ?><fieldset class="landing-editor-row"><legend>Soru <?= $i+1 ?></legend>
<div class="form-group"><label for="faq-question-<?= e($contentId) ?>-<?= $i ?>">Soru</label><input id="faq-question-<?= e($contentId) ?>-<?= $i ?>" class="form-control" name="<?= e($faqsPrefix) ?>[<?= $i ?>][question]" maxlength="240" value="<?= e($questions[$i]['question']??'') ?>"></div>
<div class="form-group"><label for="faq-answer-<?= e($contentId) ?>-<?= $i ?>">Yanıt</label><textarea id="faq-answer-<?= e($contentId) ?>-<?= $i ?>" class="form-control" name="<?= e($faqsPrefix) ?>[<?= $i ?>][answer]" rows="3" maxlength="1800"><?= e($questions[$i]['answer']??'') ?></textarea></div>
</fieldset><?php endfor; ?></div></details>
