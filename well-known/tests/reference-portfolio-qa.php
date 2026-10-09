<?php
declare(strict_types=1);

require __DIR__.'/../app/Services/ReferencesService.php';

use App\Services\ReferencesService as Refs;

function verify(bool $ok,string $label): void {
    if (!$ok) {
        fwrite(STDERR,"FAIL: ".$label."\n");
        exit(1);
    }
}
$groups=Refs::groups();
verify(array_keys($groups)===['agency','marketing'],'two requested groups');
verify(isset($groups['agency']['services']['web-site']),'website reference subtype');
verify(isset($groups['agency']['services']['software']),'software reference subtype');
verify(isset($groups['marketing']['services']['social-management']),'social media management subtype');
verify(Refs::instagramEmbed('https://www.instagram.com/reel/C9mVh6oN8d_/?igsh=somecode','instagram_reel')
    ==='https://www.instagram.com/reel/C9mVh6oN8d_/embed/','Reels embed normalized');
verify(Refs::instagramEmbed('https://instagram.com/p/C8WdZzX3aaB/','instagram_post')
    ==='https://www.instagram.com/p/C8WdZzX3aaB/embed/','post embed normalized');
verify(Refs::instagramEmbed('https://instagram.com/p/C8WdZzX3aaB/','instagram_reel')===null,'cross-content rejected');
verify(Refs::instagramEmbed('https://evil.instagram.com/reel/C9mVh6oN8d_/','instagram_reel')===null,'host allowlist');
verify(Refs::instagramEmbed('http://instagram.com/reel/C9mVh6oN8d_/','instagram_reel')===null,'http blocked');
verify(Refs::instagramEmbed('https://www.instagram.com/reel/../../evil','instagram_reel')===null,'invalid path blocked');
verify(Refs::normalizedGroup(['title'=>'Legacy site'])==='agency','legacy entries default to agency');
verify(Refs::normalizedService(['title'=>'Legacy site'])==='web-site','legacy entries keep website category');
verify(Refs::normalizedMedia(['title'=>'Legacy site'])==='website','legacy entries retain website mode');
$primary = ['group'=>'agency','service'=>'web-site'];
$secondary = ['group'=>'marketing','service'=>'social-management'];
$dual = ['id'=>'same-project','title'=>'Test Site','group'=>'agency','service'=>'web-site',
         'placements'=>[$primary,$secondary]];
verify(Refs::normalizedPlacements($dual)===[$primary,$secondary],
    'one project is assigned to two different main/service categories');
verify(count(Refs::normalizedPlacements(['placements'=>[$primary,$primary]]))===1,
    'duplicate category assignment is deduplicated');
verify(Refs::normalizedPlacements(['placements'=>[$primary,['group'=>'admin','service'=>'fake']]])===[$primary],
    'unknown category cannot enter public filter');
verify(Refs::normalizedPlacements(['placements'=>[$primary,$secondary,$primary]])===[$primary,$secondary],
    'portfolio category assignment has two slots maximum');
$twoAgency = [['group'=>'agency','service'=>'web-site'],['group'=>'agency','service'=>'software']];
verify(Refs::normalizedPlacements(['placements'=>$twoAgency])===$twoAgency,
    'two different subcategories in one main category are supported');
verify(Refs::normalizedPlacements(['group'=>'marketing','service'=>'seo'])===[
    ['group'=>'marketing','service'=>'seo']
], 'existing one-category references retain their exact group/service');
echo "PASS: dual-category reference placement, Instagram embeds, URL safety and legacy records.\n";
