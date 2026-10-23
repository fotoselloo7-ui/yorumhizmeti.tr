<?php
namespace App\Services;

/** Landing-only presentation registry. No settings singleton or URL identity changes. */
final class LandingThemeService
{
    public const DEFAULT_KEY='landing-01';
    public static function all():array
    {
        return [
            'landing-01'=>['name'=>'Mavi Ufuk','description'=>'Aydınlık ajans, yatay vitrin, kompakt kartlar','hero'=>'city','services'=>'tiles','scripts'=>'showcase','content'=>'timeline','blog'=>'editorial','faq'=>'split','cta'=>'banner'],
            'landing-02'=>['name'=>'Gece Atlası','description'=>'Lacivert teknoloji, arama sahnesi, koyu vitrin','hero'=>'night','services'=>'elevated','scripts'=>'gallery','content'=>'dark-flow','blog'=>'cards','faq'=>'split','cta'=>'wide'],
            'landing-03'=>['name'=>'Yeşil Rota','description'=>'Temiz büyüme, konum ağı, açık süreç','hero'=>'growth','services'=>'compact','scripts'=>'gallery','content'=>'steps','blog'=>'cards','faq'=>'columns','cta'=>'ribbon'],
            'landing-04'=>['name'=>'Glass Studio','description'=>'Mor-mavi cam yüzeyler, panel ve yatay ürünler','hero'=>'glass','services'=>'glass','scripts'=>'horizontal','content'=>'steps','blog'=>'cards','faq'=>'split','cta'=>'glass'],
            'landing-05'=>['name'=>'Editoryal','description'=>'Krem yüzey, geniş tipografi, çerçeveli vitrin','hero'=>'editorial','services'=>'list','scripts'=>'editorial','content'=>'journal','blog'=>'editorial','faq'=>'columns','cta'=>'minimal'],
            'landing-06'=>['name'=>'Yerel Odak','description'=>'Yeşil yerel hizmet, ters yerleşim ve sade satırlar','hero'=>'local','services'=>'rows','scripts'=>'showcase','content'=>'alternating','blog'=>'list','faq'=>'split','cta'=>'ribbon'],
            'landing-07'=>['name'=>'Turuncu İvme','description'=>'Lacivert-turuncu, diyagonal vitrin ve teklif odağı','hero'=>'conversion','services'=>'compact','scripts'=>'horizontal','content'=>'steps','blog'=>'editorial','faq'=>'split','cta'=>'banner'],
            'landing-08'=>['name'=>'SaaS Minimal','description'=>'Antrasit, merkezî ürün sahnesi ve minimal çizgiler','hero'=>'saas','services'=>'list','scripts'=>'gallery','content'=>'timeline','blog'=>'list','faq'=>'columns','cta'=>'minimal'],
            'landing-09'=>['name'=>'Prizma','description'=>'Mavi-mor gradyan, yüzen vitrin ve bento kartlar','hero'=>'prism','services'=>'bento','scripts'=>'showcase','content'=>'cards','blog'=>'cards','faq'=>'split','cta'=>'wide'],
            'landing-10'=>['name'=>'Boyut','description'=>'Lacivert-beyaz, katmanlı illüstrasyon ve ferah vitrin','hero'=>'dimension','services'=>'tiles','scripts'=>'horizontal','content'=>'alternating','blog'=>'editorial','faq'=>'split','cta'=>'glass'],
        ];
    }
    /** Empty/unknown keys preserve the existing Studio/Editorial presentation. */
    public static function key(mixed $value):string
    {return is_string($value)&&isset(self::all()[$value])?$value:'';}
    public static function profile(string $key):array
    {return self::all()[self::key($key)]??self::all()[self::DEFAULT_KEY];}
    public static function variant(array $profile,string $type):string
    {return $profile[$type]??'generic';}
    /** One normalized block stream; every enabled existing block survives all themes. */
    public static function blocks(array $d,array $sources):array
    {
        $available=['overview'=>$d['local_context']!=='','services'=>!empty($sources['services']),'scripts'=>!empty($sources['scripts']),'references'=>!empty($sources['references']),'content'=>!empty($d['sections']),'answer'=>$d['seo']['direct_answer']!==''||$d['seo']['geo_summary']!=='','blog'=>!empty($sources['blog']),'faq'=>!empty($d['faqs'])];
        $blocks=[];
        foreach($d['section_order'] as $key)if(in_array($key,$d['enabled_sections'],true)&&!empty($available[$key]))$blocks[]=['type'=>$key,'title'=>RegionalLandingService::SECTIONS[$key]];
        return $blocks;
    }
    public static function component(string $type):string
    {return in_array($type,array_keys(RegionalLandingService::SECTIONS),true)?'block.php':'generic.php';}
}
