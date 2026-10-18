<?php
namespace App\Services;

/** Connects existing truthful schemas; no new ratings, claims, FAQs or credentials. */
final class SeoStructuredDataService
{
    public static function graph(?string $existing,array $page,array $seo=[],string $template=''): ?string
    {
        if(!empty($page['seoPrivate'])||!empty($page['seoError']))return null;
        $canonical=SeoMetadataService::webUrl((string)($page['canonicalUrl']??''));
        if(!$canonical)return null;
        $decoded=json_decode((string)$existing,true);
        $nodes=is_array($decoded)?($decoded['@graph']??(array_is_list($decoded)?$decoded:[$decoded])):[];
        if(!is_array($nodes))$nodes=[];
        $nodes=array_values(array_filter($nodes,static fn($n)=>is_array($n)&&isset($n['@type'])));
        $pageIndex=null;$primary=null;$breadcrumb=null;
        foreach($nodes as $i=>&$node){
            unset($node['@context']);$type=(array)$node['@type'];
            if(array_intersect($type,['WebPage','CollectionPage','ContactPage','AboutPage'])||($template==='frontend/faq'&&in_array('FAQPage',$type,true)))$pageIndex??=$i;
            if(array_intersect($type,['Article','BlogPosting','NewsArticle','Product','SoftwareApplication','Service'])){
                $node['@id']??=$canonical.'#main-entity';$primary=$node['@id'];
            }
            if(in_array('BreadcrumbList',$type,true)){$node['@id']??=$canonical.'#breadcrumbs';$breadcrumb=$node['@id'];}
        }unset($node);
        $site=rtrim((string)setting('site_url','https://netvera.tr'),'/');
        if(!SeoMetadataService::webUrl($site))$site=rtrim(url('/'),'/');
        $base=['@type'=>$template==='frontend/contact'?'ContactPage':'WebPage','@id'=>$canonical.'#page','url'=>$canonical,
            'name'=>(string)($page['pageTitle']??''),'inLanguage'=>'tr-TR',
            'isPartOf'=>['@id'=>$site.'/#website'],'publisher'=>['@id'=>$site.'/#organization']];
        if(!empty($page['metaDescription']))$base['description']=$page['metaDescription'];
        if(!empty($page['ogImage']))$base['primaryImageOfPage']=['@type'=>'ImageObject','url'=>$page['ogImage']];
        if($primary)$base['mainEntity']=['@id'=>$primary];
        if($breadcrumb)$base['breadcrumb']=['@id'=>$breadcrumb];
        $topics=array_slice(array_values(array_filter(array_map('trim',explode(',',(string)($seo['entity_topics']??''))))),0,15);
        if($topics)$base['about']=array_map(static fn($t)=>['@type'=>'Thing','name'=>$t],$topics);
        if(!empty($seo['service_area']))$base['spatialCoverage']=['@type'=>'Place','name'=>(string)$seo['service_area']];
        if(!empty($seo['author_name'])){
            $author=['@type'=>($seo['author_type']??'')==='Person'?'Person':'Organization','name'=>$seo['author_name']];
            if($authorUrl=SeoMetadataService::webUrl((string)($seo['author_url']??'')))$author['url']=$authorUrl;
            $profiles=self::links((string)($seo['same_as_urls']??''));if($profiles)$author['sameAs']=$profiles;
            $base['author']=$author;
        }
        if($template==='frontend/blog/detail'){
            $sources=self::links((string)($seo['sources']??''));if($sources)$base['citation']=$sources;
            if((!empty($seo['author_name'])||!empty($seo['reviewer_name']))&&($reviewed=SeoMetadataService::lastmod((string)($seo['last_reviewed']??''))))$base['lastReviewed']=$reviewed;
        }
        if($pageIndex===null)$nodes[]=$base;
        else{$nodes[$pageIndex]=array_replace($base,$nodes[$pageIndex]);$nodes[$pageIndex]['url']=$canonical;}
        // Preserve the existing editor-managed GEO summary on the one page entity.
        $resolvedPageIndex=$pageIndex??array_key_last($nodes);
        if(trim((string)($seo['geo_summary']??''))!=='')$nodes[$resolvedPageIndex]['description']=(string)$seo['geo_summary'];
        $pageId=$pageIndex===null?$base['@id']:$nodes[$pageIndex]['@id'];
        if($primary)foreach($nodes as &$node)if(($node['@id']??null)===$primary)$node['mainEntityOfPage']=['@id'=>$pageId];unset($node);
        return json_encode(['@context'=>'https://schema.org','@graph'=>$nodes],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_INVALID_UTF8_SUBSTITUTE);
    }

    private static function links(string $value): array
    {
        $out=[];foreach(preg_split('/\R/',$value)?:[]as $url)if($url=SeoMetadataService::webUrl(trim($url)))$out[]=$url;
        return array_slice(array_values(array_unique($out)),0,15);
    }
}
