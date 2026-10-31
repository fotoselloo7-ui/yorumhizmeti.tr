<?php
use App\Services\LandingContentService as EditorialText;
$nvLongTitle=trim((string)($d['editorial_title']??''));
$nvLongLead=trim((string)($d['editorial_lead']??''));
$nvLongBody=trim((string)($d['editorial_body']??''));
$nvLongQuestions=$d['editorial_qa']??[];
// Complete each missing editorial field independently. An editor may have
// entered only a title: that must not suppress the long guide or its FAQ.
// Real, authored fields always win and nothing is written back to the DB.
$defaultGuide=\App\Services\RegionalEditorialGuideService::fallback($d);
if($nvLongTitle==='')$nvLongTitle=$defaultGuide['title'];
if($nvLongLead==='')$nvLongLead=$defaultGuide['lead'];
if($nvLongBody==='')$nvLongBody=implode("\n\n",$defaultGuide['paragraphs']);
if(!$nvLongQuestions)$nvLongQuestions=$defaultGuide['questions'];
if($nvLongTitle!==''||$nvLongLead!==''||$nvLongBody!==''||$nvLongQuestions):
    // No fabricated location facts, hidden copy, offsite scripts or second H1.
    $nvLongParagraphs=preg_split('/\R\s*\R/u',$nvLongBody)?:[];
?>
<section class="nvl-deep-guide" id="landing-local-guide" data-component="editorial-guide" aria-labelledby="nvl-deep-title">
    <div class="nvl-container">
        <header class="nvl-deep-heading">
            <span class="nvl-eyebrow">NETVERA / YEREL HİZMET REHBERİ</span>
            <h2 id="nvl-deep-title"><?= e($nvLongTitle!==''?$nvLongTitle:$d['city'].' için dijital çözümler rehberi') ?></h2>
            <?php if($nvLongLead!==''): ?><p class="nvl-deep-lead"><?= EditorialText::rich($nvLongLead) ?></p><?php endif; ?>
        </header>
        <div class="nvl-deep-layout">
            <?php if($nvLongBody!==''): ?>
            <article class="nvl-deep-prose" aria-label="Bölgesel hizmet ve çalışma kapsamı">
                <?php foreach($nvLongParagraphs as $nvParagraph): if(trim($nvParagraph)==='')continue; ?>
                <p><?= EditorialText::rich(trim($nvParagraph)) ?></p>
                <?php endforeach; ?>
            </article>
            <?php endif; ?>
            <?php if($nvLongQuestions): ?>
            <aside class="nvl-deep-questions" aria-label="Yerel rehber soruları ve yanıtları">
                <h3>Merak edilenler</h3>
                <?php foreach($nvLongQuestions as $nvQuestion): ?>
                <div class="nvl-deep-qa">
                    <h4><?= e($nvQuestion['question']) ?></h4>
                    <div class="nvl-deep-answer"><?= EditorialText::rich($nvQuestion['answer']) ?></div>
                </div>
                <?php endforeach; ?>
            </aside>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>
