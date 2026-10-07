<?php
if (!function_exists('yvCategoryMeta')) {
    function yvCategoryMeta(array $cat): array {
        $s = mb_strtolower(($cat['slug'] ?? '') . ' ' . ($cat['name'] ?? ''));
        if (str_contains($s,'instagram')) return ['instagram','instagram','Instagram'];
        if (str_contains($s,'tiktok')) return ['tiktok','tiktok','TikTok'];
        if (str_contains($s,'youtube')) return ['youtube','youtube','YouTube'];
        if (str_contains($s,'facebook')) return ['facebook','facebook','Facebook'];
        if (str_contains($s,'twitter') || str_contains($s,' x ')) return ['twitter','twitter','X'];
        if (str_contains($s,'google')) return ['google','google','Google'];
        if (str_contains($s,'seo')) return ['seo','bar-chart','SEO'];
        if (str_contains($s,'web') || str_contains($s,'site')) return ['web','globe','Web Site'];
        if (str_contains($s,'spotify')) return ['spotify','music','Spotify'];
        if (str_contains($s,'threads')) return ['threads','message-circle','Threads'];
        return ['default',$cat['icon_key'] ?? 'package',$cat['name'] ?? 'Hizmet'];
    }
}
?>
<div class="yv-services-page">
<section class="yv-services-hero">
    <div class="container">
        <div class="breadcrumb"><a href="/">Ana Sayfa</a><span class="separator">/</span><span>Tüm Hizmetler</span></div>
        <div class="yv-services-hero-grid">
            <div class="yv-services-hero-copy">
                <div class="yv-kicker"><?= icon('grid',12) ?> Dijital hizmet kataloğu</div>
                <h1>Tüm <span>Hizmetler</span></h1>
                <p>Sosyal medya ve web siteniz için ihtiyacınız olan tüm hizmetleri tek yerde keşfedin. Güvenli, hızlı ve kaliteli çözümlerle markanızı bir adım öne taşıyın.</p>
                <div class="yv-services-stats">
                    <div><?= icon('layers',17) ?><span><strong><?= count($categories) ?>+</strong><small>Platform Desteği</small></span></div>
                    <div><?= icon('users',17) ?><span><strong>250.000+</strong><small>Mutlu Müşteri</small></span></div>
                    <div><?= icon('headphones',17) ?><span><strong>7/24</strong><small>Canlı Destek</small></span></div>
                    <div><?= icon('shield',17) ?><span><strong>%100</strong><small>Güvenli Ödeme</small></span></div>
                </div>
            </div>
            <div class="yv-services-hero-art">
                <div class="yv-services-woman"></div>
                <span class="yv-service-float f1"><?= icon('star-fill',13) ?> 4.9/5 memnuniyet</span>
                <span class="yv-service-float f2"><?= icon('trending-up',13) ?> Markanı büyüt</span>
                <span class="yv-service-float f3"><?= icon('check-circle',13) ?> Güvenli hizmet</span>
                <span class="yv-services-orbit o1"><?= icon('instagram',22) ?></span>
                <span class="yv-services-orbit o2"><?= icon('google',21) ?></span>
                <span class="yv-services-orbit o3"><?= icon('youtube',21) ?></span>
            </div>
        </div>

        <div class="yv-services-filter">
            <div class="yv-services-search"><?= icon('search',14) ?><input id="categorySearch" value="<?= e($searchQuery ?? '') ?>" placeholder="Hizmet, platform veya kategori ara..."></div>
            <select aria-label="Kategori"><option>Tüm Kategoriler</option></select>
            <select aria-label="Hizmet türü"><option>Tüm Hizmet Türleri</option></select>
            <a href="#allServices" class="yv-btn yv-btn-primary"><?= icon('filter',12) ?> Filtrele</a>
        </div>
    </div>
</section>

<section class="yv-services-platform-strip">
    <div class="container">
        <div class="yv-services-platform-grid" id="platformGrid">
            <?php foreach(array_slice($categories,0,10) as $cat): [$cls,$ico,$label]=yvCategoryMeta($cat); ?>
            <a href="/kategori/<?= e($cat['slug']) ?>" class="yv-services-platform <?= e($cls) ?>" data-name="<?= e(mb_strtolower($cat['name'])) ?>">
                <span><?= icon($ico,22) ?></span><strong><?= e($label) ?></strong><small>Tüm Hizmetler →</small>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="yv-services-promo-wrap">
    <div class="container">
        <div class="yv-services-promo">
            <div class="yv-services-promo-copy">
                <span class="yv-sale-pill">SINIRLI SÜRE</span>
                <h2>Sosyal Medyada Daha Güçlü Ol!<br><span>Premium Hizmetlerle Fark Yarat!</span></h2>
                <p>Gerçek etkileşim, hızlı teslimat ve güvenilir hizmetlerle hedeflerinize daha hızlı ulaşın.</p>
                <a href="#allServices" class="yv-btn yv-btn-light">Hemen İncele <?= icon('arrow-right',12) ?></a>
            </div>
            <div class="yv-services-promo-art"><div class="yv-services-promo-woman"></div><div class="yv-promo-note"><?= icon('zap',14) ?> Özel Fiyatlar<br><small>Şimdi Süreli!</small></div></div>
            <div class="yv-services-promo-list">
                <div><?= icon('percent',13) ?> %50'ye Varan İndirimler</div>
                <div><?= icon('clock',13) ?> Hızlı Teslimat Garantisi</div>
                <div><?= icon('grid',13) ?> Tüm Platformlarda Geçerli</div>
                <div><?= icon('headphones',13) ?> 7/24 Canlı Destek</div>
            </div>
        </div>
    </div>
</section>

<?php if(!empty($featuredPackages)): ?>
<section class="yv-services-popular">
    <div class="container">
        <div class="yv-section-head-v5"><div><div class="yv-kicker"><?= icon('zap',12) ?> En popüler hizmetler</div><h2>Kullanıcıların en çok tercih ettikleri.</h2><p>Öne çıkan hizmet paketlerini inceleyin ve ihtiyacınıza uygun çözümü seçin.</p></div><a href="#allServices" class="yv-link-button">Tümünü Gör <?= icon('arrow-right',11) ?></a></div>
        <div class="yv-popular-service-grid">
            <?php foreach($featuredPackages as $pkg):
                [$cls,$ico,$label]=yvCategoryMeta(['slug'=>$pkg['category_slug']??'','name'=>$pkg['category_name']??$pkg['name']]);
                $price=(!empty($pkg['discount_price'])&&$pkg['discount_price']<$pkg['price'])?$pkg['discount_price']:$pkg['price'];
            ?>
            <a class="yv-popular-service-card" href="/paket/<?= e($pkg['slug']) ?>">
                <div class="yv-popular-service-top <?= e($cls) ?>"><span><?= icon($ico,22) ?></span><?php if(!empty($pkg['is_featured'])): ?><b>En Popüler</b><?php endif; ?></div>
                <div class="yv-popular-service-body"><h3><?= e($pkg['name']) ?></h3><p><?= e(excerpt(strip_tags($pkg['short_description']??''),90)) ?></p><div><span><?= (int)($pkg['max_quantity']??0) > 1 ? number_format((int)$pkg['max_quantity']).'+ seçenek' : 'Hızlı teslimat' ?></span><strong><?= money($price) ?></strong></div><em>Hizmeti İncele <?= icon('arrow-right',10) ?></em></div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="yv-services-all" id="allServices">
    <div class="container">
        <div class="yv-section-head-v5"><div><div class="yv-kicker"><?= icon('grid',12) ?> Tüm hizmet grupları</div><h2>İhtiyacınıza göre keşfedin.</h2><p>Markanız için uygun hizmet grubunu seçin.</p></div></div>
        <?php if(!empty($categories)): ?>
        <div class="yv-services-group-grid">
            <?php foreach($categories as $cat): [$cls,$ico,$label]=yvCategoryMeta($cat); ?>
            <a href="/kategori/<?= e($cat['slug']) ?>" class="yv-services-group-card" data-name="<?= e(mb_strtolower($cat['name'])) ?>">
                <span class="yv-services-group-icon <?= e($cls) ?>"><?= icon($ico,19) ?></span>
                <span class="yv-services-group-copy"><strong><?= e($cat['name']) ?></strong><small><?= (int)($cat['package_count']??0) ?>+ Hizmet</small></span>
                <?= icon('chevron-right',11) ?>
            </a>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="yv-empty"><div class="yv-empty-icon"><?= icon('search',30) ?></div><h2>Hizmet bulunamadı</h2><p>"<?= e($searchQuery ?? '') ?>" aramanızla eşleşen kategori bulunamadı.</p><a class="yv-btn yv-btn-primary" href="/kategoriler">Tüm hizmetleri göster</a></div>
        <?php endif; ?>
    </div>
</section>

<section class="yv-services-proof">
    <div class="container">
        <div class="yv-services-proof-grid">
            <div class="yv-services-proof-copy">
                <div class="yv-kicker">Neden YorumHizmeti.tr?</div>
                <h2>Güvenilir Hizmet, <span>Gerçek Sonuçlar.</span></h2>
                <p>Ön binlerce müşterinin tercih ettiği, kaliteli hizmet anlayışımız ve 7/24 destek ekibimiz ile daima yanınızdayız.</p>
                <div class="yv-hero-actions"><a href="/iletisim" class="yv-btn yv-btn-primary">Bizimle Hemen Başlayın <?= icon('arrow-right',11) ?></a><a href="/sayfa/hakkimizda" class="yv-btn yv-btn-light">Neden Biz?</a></div>
            </div>
            <div class="yv-services-proof-stats">
                <div><?= icon('users',20) ?><span><strong>250.000+</strong><small>Mutlu Müşteri</small></span></div>
                <div><?= icon('layers',20) ?><span><strong><?= count($categories) ?>+</strong><small>Platform Desteği</small></span></div>
                <div><?= icon('shield',20) ?><span><strong>%100</strong><small>Güvenli Ödeme</small></span></div>
                <div><?= icon('headphones',20) ?><span><strong>7/24</strong><small>Canlı Destek</small></span></div>
            </div>
            <div class="yv-services-proof-art"><div></div></div>
        </div>
    </div>
</section>

<?php if(!empty($faqs)): ?>
<section class="yv-services-faq">
    <div class="container">
        <div class="yv-section-head-v5"><div><div class="yv-kicker">Sıkça sorulan sorular</div><h2>Merak Ettikleriniz</h2><p>Hizmetlerimiz hakkında en çok sorulan soruların cevapları.</p></div><a href="/sss" class="yv-link-button">Tüm Soruları Gör <?= icon('arrow-right',11) ?></a></div>
        <div class="yv-services-faq-grid"><?php foreach($faqs as $faq): ?><div class="premium-faq-item"><button type="button" class="premium-faq-question"><span><?= e($faq['question']) ?></span><?= icon('plus',12) ?></button><div class="premium-faq-answer"><?= nl2br(e($faq['answer'])) ?></div></div><?php endforeach; ?></div>
    </div>
</section>
<?php endif; ?>

<section class="yv-services-cta"><div class="container"><div><span><?= icon('zap',20) ?></span><strong>Hemen Başla, Farkı Hisset!</strong><small>Sosyal medya ve web siteniz için ihtiyacınız olan tüm hizmetler burada.</small></div><a href="#allServices" class="yv-btn yv-btn-light">Tüm Hizmetleri Gör <?= icon('arrow-right',11) ?></a></div></section>
</div>
<script>
document.addEventListener('DOMContentLoaded',()=>{
 const input=document.getElementById('categorySearch');
 const cards=[...document.querySelectorAll('[data-name]')];
 if(input){input.addEventListener('input',()=>{const q=input.value.toLocaleLowerCase('tr-TR').trim();cards.forEach(c=>c.style.display=(c.dataset.name||'').includes(q)?'flex':'none')});}
 document.querySelectorAll('.premium-faq-question').forEach(q=>q.addEventListener('click',()=>{const a=q.nextElementSibling;a.style.display=a.style.display==='block'?'none':'block'}));
});
</script>