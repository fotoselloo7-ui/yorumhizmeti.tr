<?php if($totalPages>1): ?>
<nav class="member-pagination" aria-label="Sayfalama">
 <?php if($page>1): ?><a href="<?= e(\App\Services\MemberPanelService::link($listBase,$filters,$page-1)) ?>" aria-label="Önceki sayfa"><?= icon('chevron-left',18) ?></a><?php endif; ?>
 <?php for($i=max(1,$page-2);$i<=min($totalPages,$page+2);$i++): ?><a href="<?= e(\App\Services\MemberPanelService::link($listBase,$filters,$i)) ?>" <?= $i===$page?'aria-current="page"':'' ?>><?= $i ?></a><?php endfor; ?>
 <span><?= $page ?> / <?= $totalPages ?></span>
 <?php if($page<$totalPages): ?><a href="<?= e(\App\Services\MemberPanelService::link($listBase,$filters,$page+1)) ?>" aria-label="Sonraki sayfa"><?= icon('chevron-right',18) ?></a><?php endif; ?>
</nav><?php endif; ?>
