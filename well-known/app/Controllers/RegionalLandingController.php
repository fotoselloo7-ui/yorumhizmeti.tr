<?php
namespace App\Controllers;
use App\Core\AdminAuth;
use App\Core\Controller;
use App\Services\RegionalLandingService as Landing;
use App\Services\PublicSeoUrls;

final class RegionalLandingController extends Controller
{
    public function index():void
    {
        $page=filter_var($_GET['page']??1,FILTER_VALIDATE_INT)?:1;
        $q=is_string($_GET['q']??null)?mb_substr(trim($_GET['q']),0,100):'';
        $list=Landing::listing($page,$q,true);
        $this->render('frontend/landing/index',['pageTitle'=>'Hizmet Bölgeleri | NetVera','metaDescription'=>'NetVera dijital ajans, sosyal medya ve SEO hizmetlerinin bölgesel kapsamını ve çalışma biçimini inceleyin.','canonicalUrl'=>url('/hizmet-bolgeleri'.($list['page']>1?'?page='.$list['page']:'')),'noindex'=>$q!==''||!$list['total'],'landingDirectory'=>$list,'landingQuery'=>$q]);
    }
    public function show(string $slug):void
    {
        $slug=rawurldecode($slug);
        if(!PublicSeoUrls::isSlug($slug)){PublicSeoUrls::notFound();return;}
        $row=Landing::bySlug($slug);
        if(!$row){$target=Landing::redirectTarget($slug);$redirect=$target?Landing::byId($target):null;if($redirect&&$redirect['status']==='active'){header('Location: /'.rawurlencode($redirect['slug']),true,301);return;}PublicSeoUrls::notFound();return;}
        $preview=($_GET['onizleme']??'')==='1'&&AdminAuth::check()&&AdminAuth::admin();
        if($row['status']!=='active'&&!$preview){PublicSeoUrls::notFound();return;}
        if($preview){header('Cache-Control: private, no-store');header('X-Robots-Tag: noindex, nofollow');}
        // The pages slug index is case/accent-insensitive on MySQL. A lookup
        // match is not necessarily the exact canonical URL requested.
        if($slug!==$row['slug']){header('Location: /'.rawurlencode($row['slug']).($preview?'?onizleme=1':''),true,301);return;}
        try{$data=Landing::validate(Landing::decode($row));}catch(\InvalidArgumentException $ignored){PublicSeoUrls::notFound();return;}
        // Design comparison is a private, noindex admin preview. Never alter
        // public rendering, database values, canonical URLs, or SEO content.
        $requestedDesign=$_GET['tasarim']??null;
        if($preview && is_string($requestedDesign)
            && in_array($requestedDesign,['studio','editorial','spotlight','atlas','signal','canvas','prism'],true)){
            $data['layout']=$requestedDesign;
        }
        $seo=$data['seo'];$own='/'.rawurlencode($row['slug']);
        $this->render('frontend/landing/show',['pageTitle'=>$seo['title']?:$data['title'].' | NetVera','metaDescription'=>$seo['description']?:$data['summary'],'canonicalUrl'=>url($seo['canonical']?:$own),'ogTitle'=>$seo['og_title']?:$seo['title'],'ogDescription'=>$seo['og_description']?:$seo['description'],'ogImage'=>($seo['og_image']?:$data['image'])!==''?url($seo['og_image']?:$data['image']):null,'ogImageAlt'=>$data['image_alt'],'noindex'=>(bool)$preview,'schema'=>Landing::schema($row,$data),'nvSeoData'=>$seo,'landing'=>$data,'landingRow'=>$row,'landingPreview'=>(bool)$preview,'landingServices'=>Landing::selectedServices($data['service_ids']),'landingReferences'=>Landing::selectedReferences($data['reference_ids']),'landingContacts'=>Landing::contacts()]);
    }
}
