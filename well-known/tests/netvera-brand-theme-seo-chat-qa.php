<?php
declare(strict_types=1);
$root=dirname(__DIR__);
$read=static function(string $path)use($root):string{
    $p=$root.'/'.$path;
    if(!is_file($p))throw new RuntimeException('Missing file '.$path);
    return file_get_contents($p);
};
$checks=[
 'resources/views/frontend/home.php'=>['data-review-viewport','data-review-track'],
 'public/assets/css/reviews-v68.css'=>['overflow:hidden','overflow-y:auto','height:328px'],
 'resources/views/admin/netvera-inbox/index.php'=>['data-nv70-chatdesk','data-nv70-chats','data-nv70-form'],
 'public/assets/js/inbox-desktop-v70.js'=>['data-nv70-chatdesk','/mesajlar','/yanit'],
 'app/Controllers/Admin/NetveraInboxController.php'=>['public function conversation(','public function conversationReply(','Csrf::check();'],
 'app/Core/App.php'=>['seo-optimize','NetveraInboxController@conversation','NetveraInboxController@conversationReply'],
 'resources/views/admin/settings/site.php'=>['theme_preset_enabled','NetVera Premium','theme_primary'],
 'app/Controllers/Admin/SettingsController.php'=>["if (\$key === 'theme_preset_enabled')"],
 'resources/views/layouts/app.php'=>['theme-runtime-v71.css','nv-theme-enabled','NetVera Teknoloji Yazılım'],
 'resources/views/admin/categories/index.php'=>['seo-optimize'],
 'app/Controllers/CategoryController.php'=>['CategorySearchBlueprint::decorate','BreadcrumbList','CollectionPage'],
 'resources/views/admin/partials/netvera-seo.php'=>['nvSeoIsCategory','nvseo_main_question','nvseo_direct_answer'],
 'resources/views/frontend/category-detail.php'=>['nv71-category-answer'],
];
$ok=0;
foreach($checks as $path=>$phrases){
    $content=$read($path);
    foreach($phrases as $phrase){
        if(!str_contains($content,$phrase))throw new RuntimeException($path.' missing '.$phrase);
        $ok++;
    }
}
require_once $root.'/app/Services/CategorySearchBlueprint.php';
$profiles=\App\Services\CategorySearchBlueprint::profiles();
if(count($profiles)<12)throw new RuntimeException('Keyword blueprint incomplete.');
foreach($profiles as $slug=>$profile){
    if(preg_match('/yazilim|script|software|cms|tema|web-sitesi/i',$slug))throw new RuntimeException('Software SEO must not be touched.');
    foreach(['title','desc','focus','secondary','question','answer'] as $field){
        if(trim((string)($profile[$field]??''))==='')throw new RuntimeException('Incomplete keyword profile '.$slug.' '.$field);
    }
    $original=['name'=>'Instagram Hizmetleri','slug'=>$slug,'seo_title'=>'','seo_description'=>'','seo_focus_keyword'=>''];
    $decorated=\App\Services\CategorySearchBlueprint::decorate($original);
    if($decorated['slug']!==$slug)throw new RuntimeException('Indexed slug was changed.');
}
$protected=\App\Services\CategorySearchBlueprint::decorate(['name'=>'Hazır Yazılımlar','slug'=>'hazir-yazilimlar','seo_title'=>'Özgün Yazılım Başlığı']);
if($protected['seo_title']!=='Özgün Yazılım Başlığı')throw new RuntimeException('Software SEO mutated.');
$js=$read('public/assets/js/reviews-v68.js');
if(str_contains($js,'nv68-fade-out'))throw new RuntimeException('Old vertical testimonial animation restored.');
if(str_contains($read('public/assets/css/reviews-v68.css'),'-webkit-line-clamp:4'))throw new RuntimeException('Reviews clipped at 4 lines.');
echo "PASS: {$ok} NetVera brand/theme/live-chat/category SEO contracts and ".count($profiles)." category intent profiles\n";
