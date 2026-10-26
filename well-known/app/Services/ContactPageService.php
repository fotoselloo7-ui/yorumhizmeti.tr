<?php
namespace App\Services;

/** Contact content and a strict Google-only embed boundary; never render pasted HTML. */
final class ContactPageService
{
    // NetVera's supplied Alanya business embed. Existing saved settings always override.
    public const DEFAULT_MAP_EMBED = 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3207.397587139795!2d32.1572397!3d36.4962636!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xa8dc4c4e36f0eff1%3A0x32f6fb3719151b2f!2zTmV0dmVyYSBUZWtub2xvamkgWWF6xLFsxLFtIC0gQWxhbnlhIFlhesSxbMSxbSBGaXJtYXPEsQ!5e0!3m2!1str!2str!4v1793039322076!5m2!1str!2str';

    public static function defaults(): array
    {
        return [
            'contact_heading'=>'Projenizi birlikte konuşalım.',
            'contact_intro'=>'Yazılım, dijital ajans veya sosyal medya hizmetleri hakkında ihtiyacınızı paylaşın. Size uygun kapsamı birlikte değerlendirelim.',
            'contact_support_hours'=>'Mesajlarınızı ekibimiz inceler ve uygun iletişim kanalı üzerinden yanıtlar.',
            'contact_map_title'=>'Konum ve ulaşım',
            'contact_map_description'=>'Ziyaret öncesinde bizimle iletişime geçerek görüşmenizi planlayabilirsiniz.',
            'contact_map_embed'=>self::DEFAULT_MAP_EMBED,'contact_map_enabled'=>'1',
        ];
    }

    public static function mapUrl(mixed $input): string
    {
        if(!is_string($input)||strlen($input)>12000)throw new \InvalidArgumentException('Google Haritalar embed kodu veya adresi geçersiz.');
        $input=trim($input);if($input==='')return '';
        if(str_starts_with($input,'<')){
            // One iframe only. Dimensions, style, events and srcdoc are discarded.
            if(!preg_match('~^<iframe\b[^<>]*>\s*</iframe\s*>$~isD',$input))throw new \InvalidArgumentException('Yalnızca Google Haritalar tarafından verilen tek iframe kodunu yapıştırın.');
            $doc=new \DOMDocument();$previous=libxml_use_internal_errors(true);
            try{$loaded=$doc->loadHTML($input,LIBXML_NONET|LIBXML_NOERROR|LIBXML_NOWARNING);$frames=$doc->getElementsByTagName('iframe');$input=$loaded&&$frames->length===1?$frames->item(0)->getAttribute('src'):'';}
            finally{libxml_clear_errors();libxml_use_internal_errors($previous);}
        }
        if($input===''||preg_match('/[\x00-\x20\x7f<>"\\\\]/',$input)||preg_match('/%(?:0[0-9a-f]|1[0-9a-f]|7f|5c)/i',$input))throw new \InvalidArgumentException('Geçerli bir HTTPS Google Haritalar embed adresi girin.');
        $p=parse_url($input);
        if(!$p||($p['scheme']??'')!=='https'||!in_array($p['host']??'',['www.google.com','google.com','maps.google.com'],true)||isset($p['user'])||isset($p['pass'])||isset($p['port'])||isset($p['fragment']))throw new \InvalidArgumentException('Yalnızca HTTPS Google Haritalar embed adresleri kabul edilir.');
        $path=$p['path']??'';parse_str($p['query']??'',$query);
        $shared=in_array($path,['/maps/embed','/maps/embed/'],true)&&is_string($query['pb']??null)&&$query['pb']!=='';
        $legacy=in_array($path,['/maps','/maps/'],true)&&($query['output']??'')==='embed'&&is_string($query['q']??null)&&$query['q']!=='';
        $api=preg_match('~^/maps/embed/v1/(?:place|view|directions|streetview|search)$~D',$path)&&is_string($query['key']??null)&&$query['key']!=='';
        if(!$shared&&!$legacy&&!$api)throw new \InvalidArgumentException('Paylaş → Harita yerleştir bölümündeki iframe kodunu kullanın; normal paylaşım bağlantısı yeterli değildir.');
        return $input;
    }

    public static function validate(array $input): array
    {
        $out=[];
        foreach(['contact_heading'=>140,'contact_intro'=>600,'contact_support_hours'=>400,'contact_map_title'=>140,'contact_map_description'=>500,'site_address'=>1000,'site_phone'=>60,'site_email'=>190,'whatsapp_number'=>30]as $key=>$max)$out[$key]=LandingContentService::text($input[$key]??'',$max);
        if($out['contact_heading']==='')throw new \InvalidArgumentException('Sayfa başlığını yazın.');
        if($out['site_email']!==''&&!filter_var($out['site_email'],FILTER_VALIDATE_EMAIL))throw new \InvalidArgumentException('Geçerli bir e-posta adresi girin.');
        if($out['site_phone']!==''&&!preg_match('/^\+?[0-9 ()-]{7,60}$/D',$out['site_phone']))throw new \InvalidArgumentException('Telefon numarasını kontrol edin.');
        if($out['whatsapp_number']!==''&&!preg_match('/^[0-9]{7,16}$/D',$out['whatsapp_number']))throw new \InvalidArgumentException('WhatsApp numarasını ülke koduyla, yalnızca rakam olarak girin.');
        $out['contact_map_enabled']=($input['contact_map_enabled']??'0')==='1'?'1':'0';
        $out['contact_map_embed']=self::mapUrl($input['contact_map_embed']??'');
        if($out['contact_map_enabled']==='1'&&$out['contact_map_embed']==='')throw new \InvalidArgumentException('Haritayı açmak için Google Haritalar embed kodunu ekleyin.');
        return $out;
    }

    public static function content(): array
    {
        $out=[];foreach(self::defaults()as $key=>$default)$out[$key]=setting($key,$default);
        foreach(['site_address','site_phone','site_email','whatsapp_number']as $key)$out[$key]=setting($key);
        try{$out['map_url']=$out['contact_map_enabled']==='1'?self::mapUrl($out['contact_map_embed']):'';}
        catch(\InvalidArgumentException $e){$out['map_url']='';}
        $out['channels']=array_values(array_filter(RegionalLandingService::contacts(),static fn($channel)=>$channel['url']!=='/iletisim'));
        return $out;
    }
}
