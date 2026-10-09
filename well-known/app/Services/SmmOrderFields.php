<?php
namespace App\Services;

use App\Core\Database;

/**
 * Customer-facing target fields. Never expose supplier credentials or IDs.
 * Existing manual/custom package fields take precedence.
 */
final class SmmOrderFields
{
    private const PLATFORMS = [
        'instagram'=>'instagram.com', 'tiktok'=>'tiktok.com', 'youtube'=>'youtube.com',
        'twitter'=>'x.com','facebook'=>'facebook.com','threads'=>'threads.net',
        'telegram'=>'t.me','twitch'=>'twitch.tv','pinterest'=>'pinterest.com',
        'linkedin'=>'linkedin.com','spotify'=>'open.spotify.com'
    ];

    public static function mode(string $title): string
    {
        $s=mb_strtolower($title,'UTF-8');
        if (preg_match('/takipçi|takipci|followers?|abone|subscribers?|profil|hesap.?takip/u',$s)) return 'username';
        return 'url';
    }

    public static function platform(string $title): string
    {
        $s=mb_strtolower($title,'UTF-8');
        foreach (array_keys(self::PLATFORMS) as $platform) {
            if (str_contains($s,$platform)) return $platform;
        }
        if (preg_match('/\bx[\s\-]twitter\b/u',$s)) return 'twitter';
        return '';
    }

    public static function fields(array $package): array
    {
        $db=Database::getInstance();
        $fields=$db->fetchAll('SELECT * FROM package_fields WHERE package_id=? ORDER BY sort_order ASC,id ASC',[(int)$package['id']]);
        if ($fields) return self::displayFields($fields,$package);
        $m=SmmCatalogService::mapping((int)$package['id']);
        $title=(string)($package['name']??'').' '.(string)($package['category_name']??'');
        $social=($m!==null || self::platform($title)!=='');
        if (!$social) return [];
        $mode=self::mode($title);
        return [[
            'package_id'=>(int)$package['id'],'field_key'=>$m['field_key']??'smm_target',
            'field_label'=>$mode==='username'?'Profil Kullanıcı Adı':'Profil / Gönderi / Video Bağlantısı',
            'field_type'=>$mode==='username'?'text':'url',
            'is_required'=>1,'placeholder'=>$mode==='username'?'@kullaniciadi':'https://...',
            'sort_order'=>0
        ]];
    }

    public static function displayFields(array $fields, array $package): array
    {
        $m=SmmCatalogService::mapping((int)$package['id']);
        if (!$m) return $fields;
        $title=(string)($package['name']??'').' '.(string)($package['category_name']??'');
        $mode=self::mode($title);
        foreach ($fields as &$field) {
            if (($field['field_key']??'')!==$m['field_key']) continue;
            $field['field_label']=$mode==='username'?'Profil Kullanıcı Adı':'Profil / Gönderi / Video Bağlantısı';
            $field['field_type']=$mode==='username'?'text':'url';
            $field['is_required']=1;
            $field['placeholder']=$mode==='username'?'@kullaniciadi':'https://...';
        }
        unset($field);
        return $fields;
    }

    public static function target(string $value, string $title): string
    {
        $value=trim($value);
        if ($value==='' || strlen($value)>2048) throw new \RuntimeException('Lütfen kullanıcı adını veya gönderi bağlantısını girin.');
        $mode=self::mode($title);
        $platform=self::platform($title);
        if ($mode==='username' && !str_contains($value,'/')) {
            $username=ltrim($value,'@');
            if ($platform!=='' && preg_match('/^[a-zA-Z0-9_.-]{1,80}$/D',$username)) {
                $prefix=in_array($platform,['tiktok','threads','youtube'],true)?'@':'';
                return 'https://'.self::PLATFORMS[$platform].'/'.$prefix.rawurlencode($username);
            }
        }
        $parsed=parse_url($value);
        if (!filter_var($value,FILTER_VALIDATE_URL) || !$parsed
            || strtolower((string)($parsed['scheme']??''))!=='https'
            || empty($parsed['host']) || isset($parsed['user']) || isset($parsed['pass'])
            || preg_match('/\s/',$value)) {
            throw new \RuntimeException('Geçerli kullanıcı adı veya HTTPS profil/video bağlantısı girin.');
        }
        return $value;
    }

    /** No DB mutation, usable by manual social packages as well. */
    public static function validateCustomerFields(array $package,array $values): void
    {
        foreach (self::fields($package) as $field) {
            if (empty($field['is_required'])) continue;
            $found='';
            foreach ($values as $value) {
                if (($value['field_key']??'')===($field['field_key']??'')) { $found=trim((string)($value['value']??''));break; }
            }
            if ($found==='') throw new \RuntimeException('Zorunlu hizmet alanını doldurun: '.$field['field_label']);
            if ($field['field_type']==='url' || preg_match('/smm_target|smm_link|^link$/',(string)$field['field_key'])) {
                self::target($found,(string)($package['name']??'').' '.(string)($package['category_name']??''));
            }
        }
    }
}
