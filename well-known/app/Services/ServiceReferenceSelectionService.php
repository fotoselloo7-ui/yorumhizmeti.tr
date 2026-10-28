<?php
namespace App\Services;

/**
 * Explicit admin-selected, active, real references for a package or category.
 * Uses the existing settings table; imported ready software stays independent.
 */
final class ServiceReferenceSelectionService
{
    private static function key(string $type,int $id):string
    {
        if(!in_array($type,['package','category'],true)||$id<1)
            throw new \InvalidArgumentException('Geçersiz referans hedefi.');
        return 'nv_service_reference_'.$type.'_'.$id;
    }

    public static function ids(string $type,int $id):array
    {
        if($id<1)return [];
        $raw=json_decode((string)setting(self::key($type,$id),'[]'),true);
        if(!is_array($raw))return [];
        return array_values(array_unique(array_filter($raw,static fn($v):bool=>
            is_string($v) && preg_match('/^[a-f0-9]{16}$/D',$v)===1)));
    }

    public static function save(string $type,int $id,$chosen):void
    {
        if(!is_array($chosen))$chosen=[];
        $valid=[];
        foreach(ReferencesService::all() as $ref)$valid[(string)$ref['id']]=true;
        $ids=[];
        foreach($chosen as $value){
            if(is_string($value)&&isset($valid[$value]))$ids[$value]=$value;
            if(count($ids)>=12)break;
        }
        SiteConfigService::getInstance()->set(
            self::key($type,$id),
            json_encode(array_values($ids),JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR),
            'references'
        );
    }

    public static function selected(string $type,int $id):array
    {
        $ids=self::ids($type,$id);
        if(!$ids)return [];
        $refs=[];
        foreach(ReferencesService::all(true) as $ref)$refs[(string)$ref['id']]=$ref;
        $output=[];
        foreach($ids as $key)if(isset($refs[$key]))$output[]=$refs[$key];
        return $output;
    }
}
