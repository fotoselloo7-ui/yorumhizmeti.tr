<?php
declare(strict_types=1);
/**
 * Verify original Netvera blog source images really exist IN THIS REPO, are
 * readable WEBP images, and our public Markdown renderer emits working paths.
 * Does not fetch or fabricate artwork. No DB or secrets needed.
 */
require __DIR__.'/../app/Services/BlogContentRenderer.php';
$data=json_decode((string)file_get_contents(__DIR__.'/../database/netvera-public-catalog.json'),true,512,JSON_THROW_ON_ERROR);
if(count($data['blog_posts']??[])!==5)throw new RuntimeException('Original blog snapshot incomplete');
$srcs=[];
foreach($data['blog_posts'] as $article){
    $html=\App\Services\BlogContentRenderer::render((string)$article['content']);
    if(!preg_match_all('~<figure\b[^>]*>\s*<img\b[^>]*src="([^"]+)"~i',$html,$matches))
        throw new RuntimeException('Markdown image missing in rendered article: '.$article['slug']);
    if(count($matches[1])!==2)
        throw new RuntimeException('Expected two authentic inline article images: '.$article['slug']);
    $srcs=array_merge($srcs,$matches[1]);
    foreach(['image','og_image'] as $field)
        if(!empty($article[$field]))$srcs[]=$article[$field];
}
$srcs=array_values(array_unique($srcs));
$publicRoot=realpath(__DIR__.'/../public');
foreach($srcs as $src){
    if(!preg_match('~^/uploads/blog/[a-zA-Z0-9_-]+\.webp$~',$src))
        throw new RuntimeException('Unsafe or moved public blog media URL: '.$src);
    $file=realpath($publicRoot.$src);
    if($file===false || !str_starts_with($file,$publicRoot.DIRECTORY_SEPARATOR) || !is_file($file))
        throw new RuntimeException('Original blog media file missing from versioned public directory: '.$src);
    if(filesize($file)<1024)throw new RuntimeException('Tiny/corrupt blog media file: '.$src);
    $meta=@getimagesize($file);
    if(!$meta || ($meta['mime']??'')!=='image/webp' || $meta[0]<150 || $meta[1]<90)
        throw new RuntimeException('Broken original WebP content: '.$src);
}
if(count($srcs)!==20)throw new RuntimeException('Expected 10 inline + 10 cover/OG files, got '.count($srcs));
echo "PASS: 5 original articles, 10 inline media + 10 cover/OG images; all real WebP files and Markdown image URLs valid.\n";
