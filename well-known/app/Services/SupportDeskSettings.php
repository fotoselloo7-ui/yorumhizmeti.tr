<?php
namespace App\Services;
use App\Core\Upload;
/** Staff-facing identity and browser sound presets (no secrets or client tokens). */
final class SupportDeskSettings {
    public static function publicProfile(): array {
        return [
          'name'=>setting('nv_support_agent_name','NetVera Destek'),
          'title'=>setting('nv_support_agent_title','Müşteri Temsilcisi'),
          'photo'=>setting('nv_support_agent_photo',''),
        ];
    }
    public static function sounds(): array {
        return [
          'new'=>self::sound(setting('nv_support_sound_new','chime')),
          'reply'=>self::sound(setting('nv_support_sound_reply','soft'))
        ];
    }
    private static function sound(string $v): string {
        return in_array($v,['chime','soft','digital','off'],true)?$v:'chime';
    }
    public static function save(array $post,array $upload): void {
        $name=trim((string)($post['name']??''));
        $title=trim((string)($post['title']??''));
        if(mb_strlen($name)<2||mb_strlen($name)>90||mb_strlen($title)<2||mb_strlen($title)>90)
            throw new \RuntimeException('Temsilci ismi ve uzmanlık alanı 2–90 karakter olmalıdır.');
        $cfg=SiteConfigService::getInstance();
        $cfg->set('nv_support_agent_name',$name,'support');
        $cfg->set('nv_support_agent_title',$title,'support');
        $cfg->set('nv_support_sound_new',self::sound((string)($post['new_sound']??'off')),'support');
        $cfg->set('nv_support_sound_reply',self::sound((string)($post['reply_sound']??'off')),'support');
        if(!empty($upload['name'])){
            $path=Upload::image($upload,'staff');
            if(!$path)throw new \RuntimeException('Temsilci profil resmi yüklenemedi.');
            $cfg->set('nv_support_agent_photo',$path,'support');
        }
    }
}
