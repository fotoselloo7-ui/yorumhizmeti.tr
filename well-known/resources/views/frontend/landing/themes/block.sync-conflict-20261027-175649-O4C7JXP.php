<?php
use App\Services\CatalogMenuService as BlockCatalog;
use App\Services\IconService as BlockIcons;
use App\Services\LandingContentService as BlockCopy;
use App\Services\RegionalLandingService as BlockLanding;
use App\Services\SeoMetadataService as BlockSeo;
?>
<section class="nvl-section nvl-section-<?= e($blockType) ?>" id="landing-block-<?= e($blockType) ?>" data-component="<?= e($blockType) ?>" data-variant="<?= e($blockVariant) ?>">
    <div class="nvl-container">
    <?php switch ($blockType): case 'overview':
        $locationImage = (string)($d['location_image'] ?? '');
    ?>
        <div class="nvl-overview<?= $locationImage !== '' ? ' nvl-overview-illustrated' : '' ?>">
            <?php if ($locationImage !== ''): ?>
                <figure class="nvl-overview-media nvl-overview-visual">
                    <img src="<?= e($locationImage) ?>" alt="<?= e($d['location_image_alt']??'') ?>" width="1200" height="800" loading="lazy">
                    <figcaption><?= icon('map-pin', 15) ?><span><?= e($d['city']) ?><?php if ($d['province'] !== '' && $d['province'] !== $d['city']): ?> / <?= e($d['province']) ?><?php endif; ?></span></figcaption>
                </figure>
            <?php endif; ?>
            <div class="nvl-overview-copy">
                <header><span class="nvl-eyebrow">YEREL İHTİYAÇ, ORTAK VİZYON</span><h2><?= e($d['city']) ?> için çalışma kapsamı</h2></header>
                <div class="nvl-prose"><?= BlockCopy::rich($d['local_context']) ?></div>
                <p class="nvl-disclosure"><?= icon('map-pin', 15) ?> Hizmet bölgesi bilgisi, yerel ofis veya şube adresi değildir.</p>
            </div>
        </div>
    <?php break; case 'services': ?>
        <header class="nvl-section-heading">
            <div><span class="nvl-eyebrow">HİZMETLERİMİZ</span><h2><?= e($d['services_title'] ?: 'İhtiyacınıza uygun hizmetler') ?></h2></div>
            <a href="/kategoriler">Tüm hizmetler <?= icon('arrow-up-right', 15) ?></a>
        </header>
        <div class="nvl-service-grid">
            <?php foreach ($landingServices as $n => $service):
                // Use the existing category identity and its registered icon only.
                $serviceIcon = BlockCatalog::icon($service);
                if (!BlockIcons::has($serviceIcon)) $serviceIcon = 'layers';
            ?>
                <a class="nvl-service-card" href="/kategori/<?= rawurlencode($service['slug']) ?>">
                    <span class="nvl-service-icon"><?= icon($serviceIcon, 24) ?></span>
                    <small class="nvl-service-index" aria-hidden="true"><?= sprintf('%02d', $n + 1) ?></small>
                    <h3><?= e($service['name']) ?></h3>
                    <?php if (!empty($service['description'])): ?><p><?= e(excerpt(strip_tags($service['description']), 145)) ?></p><?php endif; ?>
                    <strong>Hizmeti incele <?= icon('arrow-up-right', 16) ?></strong>
                </a>
            <?php endforeach; ?>
        </div>
    <?php break; case 'scripts': case 'blog': case 'references':
        $cards = $blockType === 'scripts' ? $landingScripts : ($blockType === 'blog' ? $landingBlogs : $landingReferences);
        $heading = $blockType === 'scripts' ? ($d['scripts_title'] ?: 'Hazır yazılım çözümleri') : ($blockType === 'blog' ? ($d['blog_title'] ?: 'Rehberler ve içgörüler') : ($d['references_title'] ?: 'Seçili çalışmalarımız'));
        $cardKind = ['scripts' => 'script', 'blog' => 'blog', 'references' => 'reference'][$blockType];
        $cardIcon = ['scripts' => 'code', 'blog' => 'file-text', 'references' => 'briefcase'][$blockType];
    ?>
        <header class="nvl-section-heading">
            <div>
                <span class="nvl-eyebrow"><?= ['scripts' => 'HAZIR YAZILIMLAR', 'blog' => 'BİLGİ VE REHBER', 'references' => 'ÇALIŞMALARIMIZDAN'][$blockType] ?></span>
                <h2><?= e($heading) ?></h2>
                <?php if ($blockType === 'references'): ?><p>NetVera portföyünden örnekler; projelerin bu şehirde yapıldığı anlamına gelmez.</p><?php endif; ?>
            </div>
            <?php if ($blockType !== 'references'): ?><a href="<?= $blockType === 'scripts' ? '/hazir-scriptler' : '/blog' ?>"><?= $blockType === 'scripts' ? 'Yazılım kataloğu' : 'Tüm yazılar' ?> <?= icon('arrow-up-right', 15) ?></a><?php endif; ?>
        </header>
        <?php if($blockType==='references'): ?>
        <div class="nvl-reference-filters" data-nvl-reference-filters role="group" aria-label="Referansları kategoriye göre filtrele">
            <button type="button" class="is-active" data-nvl-reference-filter="all" aria-pressed="true">Tüm çalışmalar</button>
            <button type="button" data-nvl-reference-filter="agency" aria-pressed="false"><?= icon('layers',14) ?> Ajans &amp; Yazılım</button>
            <button type="button" data-nvl-reference-filter="marketing" aria-pressed="false"><?= icon('trending-up',14) ?> SEO &amp; Dijital</button>
            <small data-nvl-reference-count aria-live="polite"><?= count($cards) ?> çalışma</small>
        </div>
        <?php endif; ?>
        <div class="nvl-showcase-viewport" data-nvl-rail-viewport>
          <div class="nvl-card-grid nvl-card-grid-<?= e($blockType) ?>" data-nvl-grid>
            <?php foreach ($cards as $card):
                $cardTitle = $card['title'];
                $cardImage = $blockType === 'references' ? BlockLanding::referenceImage($card) : $card['image'];
                $cardUrl = $blockType === 'references' ? BlockSeo::webUrl($card['url'] ?? '') : $card['url'];
                // This landing displays compact equal-height software/blog cards.
                // Reference project summaries remain visible; source records are untouched.
                $cardDescription = $blockType === 'references' ? ($card['description'] ?? '') : '';
                $demoUrl = $blockType === 'scripts' ? ($card['demo_url'] ?? null) : null;
                $externalCard = $blockType === 'references';
                $cardCategory = trim((string)($card['category'] ?? ''));
                if ($cardCategory === '') $cardCategory = ['scripts' => 'HAZIR YAZILIM', 'blog' => 'BİLGİ VE REHBER', 'references' => 'NETVERA PORTFÖYÜ'][$blockType];
            ?>
                <?php
                $nvFilterGroups='';
                if($blockType==='references') {
                    $nvFilterGroups=' '.implode(' ',array_unique(array_column(
                        \App\Services\ReferencesService::normalizedPlacements($card),'group'
                    ))).' ';
                }
                ?>
                <article class="nvl-card nvl-card-<?= e($cardKind) ?>" <?= $blockType==='references'?'data-nvl-groups="'.e($nvFilterGroups).'"':'' ?>>
                    <div class="nvl-card-media<?= $cardImage === '' ? ' nvl-card-media-empty' : '' ?>">
                        <?php if ($cardUrl): ?><a class="nvl-card-visual-link" href="<?= e($cardUrl) ?>" aria-label="<?= e($cardTitle) ?>" <?= $externalCard ? 'target="_blank" rel="noopener noreferrer"' : '' ?>><?php endif; ?>
                        <?php if ($cardImage !== ''): ?>
                            <img src="<?= e($cardImage) ?>" alt="<?= e(($card['image_alt'] ?? '') ?: $cardTitle) ?>" width="800" height="500" loading="lazy">
                        <?php else: ?><span class="nvl-card-empty-icon"><?= icon($cardIcon, 36) ?></span><?php endif; ?>
                        <?php if ($cardUrl): ?></a><?php endif; ?>
                        <span class="nvl-card-media-mark" aria-hidden="true"><?= icon($cardIcon, 16) ?></span>
                    </div>
                    <div class="nvl-card-copy">
                        <div class="nvl-card-tags"><small><?= e($cardCategory) ?></small></div>
                        <h3><?php if ($cardUrl): ?><a href="<?= e($cardUrl) ?>" <?= $externalCard ? 'target="_blank" rel="noopener noreferrer"' : '' ?>><?= e($cardTitle) ?></a><?php else: ?><?= e($cardTitle) ?><?php endif; ?></h3>
                        <?php if (trim(strip_tags($cardDescription)) !== ''): ?><p><?= e(excerpt(strip_tags($cardDescription), 150)) ?></p><?php endif; ?>
                        <?php if ($cardUrl): ?>
                        <div class="nvl-card-footer">
                            <a class="nvl-card-link" href="<?= e($cardUrl) ?>" <?= $externalCard ? 'target="_blank" rel="noopener noreferrer"' : '' ?>><?= ['scripts' => 'Yazılımı incele', 'blog' => 'Yazıyı oku', 'references' => 'Projeyi görüntüle'][$blockType] ?> <?= icon('arrow-up-right', 14) ?></a>
                            <?php if ($blockType === 'scripts' && $demoUrl): ?>
                            <a class="nvl-card-link nvl-demo-link" href="<?= e($demoUrl) ?>" target="_blank" rel="noopener noreferrer" aria-label="<?= e($cardTitle) ?> canlı demosunu incele"><?= icon('external-link', 13) ?> Demo</a>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
          </div>
        </div>
    <?php break; case 'content':
        $processFlow = in_array($blockVariant, ['steps', 'timeline', 'dark-flow'], true);
        $stepIcons = ['search', 'clipboard', 'settings', 'bar-chart'];
    ?>
        <div class="nvl-articles<?= $processFlow ? ' nvl-process-flow' : '' ?>">
            <?php foreach ($d['sections'] as $n => $item): ?>
                <section class="nvl-article<?= $item['image'] !== '' ? ' nvl-article-illustrated' : '' ?>" id="landing-section-<?= $n + 1 ?>">
                    <div class="nvl-step-marker" aria-hidden="true"><span class="nvl-step-number"><?= sprintf('%02d', $n + 1) ?></span><span class="nvl-step-symbol"><?= icon($stepIcons[$n % count($stepIcons)], 24) ?></span></div>
                    <div class="nvl-article-copy">
                        <h2><?= e($item['title']) ?></h2>
                        <div class="nvl-prose"><?= BlockCopy::rich($item['body']) ?></div>
                        <?php if ($item['subheading'] !== ''): ?><h3><?= e($item['subheading']) ?></h3><div class="nvl-prose"><?= BlockCopy::rich($item['detail']) ?></div><?php endif; ?>
                    </div>
                    <?php if ($item['image'] !== ''): ?><img src="<?= e($item['image']) ?>" alt="<?= e($item['alt']) ?>" width="1200" height="800" loading="lazy"><?php endif; ?>
                </section>
            <?php endforeach; ?>
        </div>
    <?php break; case 'answer': ?>
        <div class="nvl-answer">
            <span class="nvl-answer-icon"><?= icon('message-circle', 28) ?></span>
            <div><span class="nvl-eyebrow">KISA VE NET</span><h2><?= e($seo['main_question'] ?: $d['service'] . ' hakkında bilmeniz gerekenler') ?></h2><div class="nvl-prose"><?= BlockCopy::rich($seo['direct_answer'] ?: $seo['geo_summary']) ?></div><?php if ($seo['direct_answer'] !== '' && $seo['geo_summary'] !== ''): ?><p><?= e($seo['geo_summary']) ?></p><?php endif; ?></div>
        </div>
    <?php break; case 'faq': ?>
        <div class="nvl-faq-layout">
            <header class="nvl-faq-intro"><span class="nvl-faq-intro-icon" aria-hidden="true"><?= icon('message-circle', 25) ?></span><span class="nvl-eyebrow">SIK SORULAN SORULAR</span><h2><?= e($d['faq_title'] ?: 'Merak ettikleriniz') ?></h2><p>Hizmet kapsamı ve süreçle ilgili sorularınız için yanınızdayız.</p><a class="nvl-card-link" href="/iletisim">Sorunuzu bize iletin <?= icon('arrow-up-right', 16) ?></a></header>
            <div class="nvl-faq-grid">
                <?php foreach ($d['faqs'] as $faq): ?><details><summary><span><?= e($faq['question']) ?></span><?= icon('plus', 17) ?></summary><div class="nvl-prose"><?= BlockCopy::rich($faq['answer']) ?></div></details><?php endforeach; ?>
            </div>
        </div>
    <?php break; endswitch; ?>
    </div>
</section>
