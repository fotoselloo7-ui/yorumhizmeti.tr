<?php
namespace App\Services;

use App\Core\Database;

/**
 * Imports ONLY public demo access fields from an original NetVera SQL dump.
 * It never executes source SQL and never reads customer/order/payment tables.
 */
final class NetveraPublicDemoImportService
{
    public static function importSql(string $file): array
    {
        if(!is_file($file) || !is_readable($file)){
            throw new \RuntimeException('Kaynak SQL dosyası okunamadı.');
        }
        $size=filesize($file);
        if($size===false || $size<=0 || $size>50*1024*1024){
            throw new \RuntimeException('Kaynak SQL dosyası geçersiz veya çok büyük.');
        }

        $sql=file_get_contents($file);
        if($sql===false)throw new \RuntimeException('Kaynak SQL açılamadı.');

        $rows=self::extractPublicDemoRows($sql);
        if(!$rows)throw new \RuntimeException('Herkese açık demo ürünü bulunamadı.');

        return self::applyRows($rows);
    }

    public static function importLegacyTable(): array
    {
        $db=Database::getInstance();
        $exists=$db->fetch(
            "SELECT 1 AS ok FROM information_schema.tables
             WHERE table_schema=DATABASE() AND table_name='script_products' LIMIT 1"
        );
        if(!$exists)throw new \RuntimeException('Bu veritabanında eski script_products tablosu yok. Üstteki SQL dosyası alanından eski NetVera yedeğini seçip tekrar aktarın.');

        $rows=$db->fetchAll(
            "SELECT id,slug,demo_url,demo_admin_url,demo_user_url,demo_video_url,
                    demo_username,demo_password,demo_admin_username,demo_admin_password,
                    demo_note,demo_accounts_json,demo_display_mode,
                    demo_is_active,demo_is_public,is_active
             FROM script_products
             WHERE is_active=1 AND demo_is_active=1 AND demo_is_public=1"
        );
        if(!$rows)throw new \RuntimeException('Eski tabloda herkese açık demo ürünü bulunamadı.');

        return self::applyRows($rows);
    }

    private static function applyRows(array $rows): array
    {
        $db=Database::getInstance();
        $matched=0;$skipped=0;

        foreach($rows as $source){
            $id=(int)($source['id']??0);
            $slug=trim((string)($source['slug']??''));
            if($id<=0 || $slug===''){ $skipped++; continue; }

            $target=$db->fetch(
                'SELECT legacy_id,slug,public_json FROM nv_legacy_script_products WHERE legacy_id=? AND slug=? LIMIT 1',
                [$id,$slug]
            );
            if(!$target){$skipped++;continue;}

            $data=json_decode((string)($target['public_json']??'{}'),true);
            if(!is_array($data))$data=[];

            foreach([
                'demo_url','demo_admin_url','demo_user_url','demo_video_url',
                'demo_username','demo_password',
                'demo_admin_username','demo_admin_password',
                'demo_note','demo_accounts_json','demo_display_mode'
            ] as $key){
                if(array_key_exists($key,$source))$data[$key]=(string)($source[$key]??'');
            }

            $data['demo_is_active']=1;
            $data['demo_is_public']=1;
            $data['demo_display_mode']='public';

            $extra=json_decode((string)($source['demo_accounts_json']??'[]'),true);
            if(!is_array($extra)||!array_is_list($extra))$extra=[];
            $data['demo_credentials_public']=(
                trim((string)($source['demo_username']??''))!=='' ||
                trim((string)($source['demo_admin_username']??''))!=='' ||
                !empty($extra)
            ) ? 1 : 0;

            $db->update(
                'nv_legacy_script_products',
                ['public_json'=>json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_THROW_ON_ERROR)],
                'legacy_id=? AND slug=?',
                [$id,$slug]
            );
            $matched++;
        }

        return [
            'source_public_demos'=>count($rows),
            'matched'=>$matched,
            'skipped'=>$skipped,
        ];
    }

    private static function extractPublicDemoRows(string $sql): array
    {
        $tick=chr(96);
        $table=$tick.'script_products'.$tick;
        $pattern='/INSERT\s+INTO\s+'.preg_quote($table,'/').'\s*\((.*?)\)\s*VALUES\s*(.*?);/si';
        if(!preg_match_all($pattern,$sql,$blocks,PREG_SET_ORDER))return [];

        $out=[];
        foreach($blocks as $block){
            preg_match_all('/'.preg_quote($tick,'/').'([^'.preg_quote($tick,'/').']+)'.preg_quote($tick,'/').'/',$block[1],$cm);
            $columns=$cm[1]??[];
            if(!$columns)continue;

            foreach(self::splitRows($block[2]) as $row){
                $parts=self::splitValues($row);
                if(count($parts)!==count($columns))continue;

                $item=[];
                foreach($columns as $i=>$column){
                    $item[$column]=self::decodeValue($parts[$i]);
                }

                if((int)($item['is_active']??0)!==1)continue;
                if((int)($item['demo_is_active']??0)!==1)continue;
                if((int)($item['demo_is_public']??0)!==1)continue;
                if(trim((string)($item['slug']??''))==='')continue;
                $out[]=$item;
            }
        }
        return $out;
    }

    private static function splitRows(string $sql): array
    {
        $rows=[];$buf='';$depth=0;$quoted=false;$escaped=false;
        $len=strlen($sql);

        for($i=0;$i<$len;$i++){
            $ch=$sql[$i];

            if($escaped){$buf.=$ch;$escaped=false;continue;}
            if($quoted&&$ch==='\\'){$buf.=$ch;$escaped=true;continue;}
            if($ch==="'"){$quoted=!$quoted;$buf.=$ch;continue;}

            if(!$quoted){
                if($ch==='(')$depth++;
                elseif($ch===')')$depth--;

                if($ch===','&&$depth===0){
                    if(trim($buf)!=='')$rows[]=trim($buf);
                    $buf='';
                    continue;
                }
            }

            $buf.=$ch;
        }

        if(trim($buf)!=='')$rows[]=trim($buf);
        return $rows;
    }

    private static function splitValues(string $row): array
    {
        $row=trim($row);
        if($row!==''&&$row[0]==='(')$row=substr($row,1);
        if($row!==''&&substr($row,-1)===')')$row=substr($row,0,-1);

        $values=[];$buf='';$quoted=false;$escaped=false;
        $len=strlen($row);

        for($i=0;$i<$len;$i++){
            $ch=$row[$i];

            if($escaped){$buf.=$ch;$escaped=false;continue;}
            if($quoted&&$ch==='\\'){$buf.=$ch;$escaped=true;continue;}
            if($ch==="'"){$quoted=!$quoted;$buf.=$ch;continue;}
            if($ch===','&&!$quoted){$values[]=trim($buf);$buf='';continue;}

            $buf.=$ch;
        }

        $values[]=trim($buf);
        return $values;
    }

    private static function decodeValue(string $token)
    {
        $token=trim($token);
        if(strcasecmp($token,'NULL')===0)return null;

        if(strlen($token)>=2 && $token[0]==="'" && $token[strlen($token)-1]==="'"){
            $value=substr($token,1,-1);
            return strtr($value,[
                "\\0"=>"\0",
                "\\n"=>"\n",
                "\\r"=>"\r",
                "\\t"=>"\t",
                "\\Z"=>chr(26),
                "\\'"=>"'",
                '\\"'=>'"',
                "\\\\"=>"\\",
            ]);
        }

        return $token;
    }
}
