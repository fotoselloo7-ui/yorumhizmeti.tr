<?php
// Forward-compatible presentation fallback for normalized blocks; never raw HTML.
$genericTitle=is_string($block['title']??null)?$block['title']:'';
$genericBody=is_string($block['body']??null)?$block['body']:'';
if($genericTitle!==''||$genericBody!==''): ?>
<section class="nvl-section" data-component="generic" data-variant="generic"><div class="nvl-container"><h2><?= e($genericTitle) ?></h2><div class="nvl-prose"><?= \App\Services\LandingContentService::rich($genericBody) ?></div></div></section>
<?php endif; ?>
