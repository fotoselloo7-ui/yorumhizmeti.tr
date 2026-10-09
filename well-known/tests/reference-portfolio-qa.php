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
// Harici barındırma: site sunucusunda MP4 saklamadan iframe veya CDN oynatma.
verify(Refs::externalPlayer('https://youtu.be/dQw4w9WgXcQ')['url']
    ==='https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ?rel=0&playsinline=1&autoplay=1',
    'YouTube share URL converts to privacy-enhanced on-site embed');
verify(Refs::externalPlayer('https://www.youtube.com/shorts/dQw4w9WgXcQ')['provider']==='YouTube',
    'YouTube Shorts URL supported');
verify(Refs::externalPlayer('https://vimeo.com/123456789')['type']==='iframe',
    'Vimeo share URL supported');
verify(Refs::externalPlayer('https://vimeo.com/123456789/abcde12345')['url']
    ==='https://player.vimeo.com/video/123456789?h=abcde12345&autoplay=1',
    'unlisted Vimeo private share hash preserved');
verify(Refs::externalPlayer('https://player.mediadelivery.net/embed/197133/dc48a09e-d9bb-420a-83d7-72dc2304c034')
    ===['type'=>'iframe','url'=>'https://player.mediadelivery.net/embed/197133/dc48a09e-d9bb-420a-83d7-72dc2304c034?autoplay=true','provider'=>'Bunny Stream'],
    'Bunny Stream video URL canonicalized');
verify(Refs::externalPlayer('https://customer-f33zs165nr7gyfy4.cloudflarestream.com/6b9e68b07dfee8cc2d116e4c51d6a957/iframe')['provider']==='Cloudflare Stream',
    'Cloudflare Stream player link supported');
verify(Refs::externalPlayer('https://cdn.example.com/references/demo.mp4?token=abc')['type']==='video',
    'HTTPS CDN media supported with query parameters');
verify(Refs::externalPlayer('https://cdn.example.com/references/demo.webm')['type']==='video',
    'HTTPS WebM supported without local storage');
verify(Refs::externalPlayer('https://evil.example.com/any-script.html')===null,
    'arbitrary external iframe URLs rejected');
verify(Refs::externalPlayer('https://youtube.com.evil.test/watch?v=dQw4w9WgXcQ')===null,
    'lookalike video domains rejected');
verify(Refs::externalPlayer('http://cdn.example.com/references/demo.mp4')===null,
    'unencrypted external video rejected');
verify(Refs::externalPlayer('https://localhost/references/demo.mp4')===null,
    'local network host rejected');
echo "PASS: dual category, Instagram and on-site externally hosted streaming provider safety.\n";
