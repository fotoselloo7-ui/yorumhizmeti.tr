<?php
namespace App\Services;
use App\Core\Database;

/** Reviews are edited in admin; legacy testimonials JSON remains the only source of truth. */
final class TestimonialManager
{
    public static function rows(bool $publishedOnly=false): array
    {
        $db=Database::getInstance();
        $row=$db->fetch("SELECT extra_data FROM home_sections WHERE section_key='testimonials' LIMIT 1");
        $list=json_decode((string)($row['extra_data']??'[]'),true);
        if(!is_array($list))return [];
        $out=[];
        foreach($list as $i=>$r) {
            if(!is_array($r))continue;
            $text=trim((string)($r['text']??''));
            if($text==='')continue;
            $r['id']=(string)($r['id']??'legacy-'.$i);
            $r['name']=mb_substr(trim((string)($r['name']??'Müşteri')),0,90);
            $r['role']=mb_substr(trim((string)($r['role']??'')),0,90);
            $r['text']=mb_substr($text,0,850);
            $r['rating']=isset($r['rating'])?max(1,min(5,(int)$r['rating'])):
                (isset($r['stars'])?max(1,min(5,(int)$r['stars'])):null);
            $r['image']=(string)($r['image']??'');
            $r['category_id']=(int)($r['category_id']??0);
            $r['package_id']=(int)($r['package_id']??0);
            $r['category_name']='';
            $r['service_name']='';
            $r['status']=(string)($r['status']??'active');
            if($publishedOnly && $r['status']!=='active')continue;
            $out[]=$r;
        }
        $catIds=array_values(array_unique(array_filter(array_column($out,'category_id'))));
        $pkgIds=array_values(array_unique(array_filter(array_column($out,'package_id'))));
        // One query each for public labels. Never rely on user-supplied label fields.
        $catNames=[];$pkgNames=[];
        if($catIds){
            $ph=implode(',',array_fill(0,count($catIds),'?'));
            foreach($db->fetchAll("SELECT id,name FROM categories WHERE id IN ($ph)",$catIds) as $c)
                $catNames[(int)$c['id']]=$c['name'];
        }
        if($pkgIds){
            $ph=implode(',',array_fill(0,count($pkgIds),'?'));
            foreach($db->fetchAll("SELECT id,name FROM packages WHERE id IN ($ph)",$pkgIds) as $p)
                $pkgNames[(int)$p['id']]=$p['name'];
        }
        foreach($out as &$r){
            $r['category_name']=$catNames[$r['category_id']]??'';
            $r['service_name']=$pkgNames[$r['package_id']]??'';
        }
        unset($r);
        return $out;
    }
    public static function save(array $data,?string $image=null): void
    {
        $name=trim((string)($data['name']??''));
        $text=trim((string)($data['text']??''));
        if(mb_strlen($name)<2 || mb_strlen($name)>90 || mb_strlen($text)<10 || mb_strlen($text)>850)
            throw new \RuntimeException('İsim 2–90, yorum 10–850 karakter olmalıdır.');
        $rating=(int)($data['rating']??0);
        if($rating<1||$rating>5)throw new \RuntimeException('1–5 arası yıldız seçin.');
        $category=(int)($data['category_id']??0);
        $package=(int)($data['package_id']??0);
        $db=Database::getInstance();
        if($category && !$db->fetch('SELECT id FROM categories WHERE id=?',[$category]))
            throw new \RuntimeException('Kategori bulunamadı.');
        if($package) {
            $p=$db->fetch('SELECT id,category_id FROM packages WHERE id=?',[$package]);
            if(!$p || ($category && (int)$p['category_id']!==$category))
                throw new \RuntimeException('Seçilen hizmet kategoriye ait değil.');
            if(!$category)$category=(int)$p['category_id'];
        }
        $pdo=$db->getPdo();$pdo->beginTransaction();
        try{
            $section=$db->fetch("SELECT id,extra_data FROM home_sections WHERE section_key='testimonials' LIMIT 1 FOR UPDATE");
            if(!$section)throw new \RuntimeException('Ana sayfadaki yorum bölümü bulunamadı.');
            $list=json_decode((string)$section['extra_data'],true);
            if(!is_array($list))$list=[];
            $id=trim((string)($data['id']??''));
            $idx=null;
            foreach($list as $n=>$item)if(is_array($item) && (string)($item['id']??'legacy-'.$n)===$id){$idx=$n;break;}
            if($idx===null && count($list)>=250)throw new \RuntimeException('En fazla 250 yorum saklanabilir.');
            $old=$idx!==null?$list[$idx]:[];
            $record=[
                'id'=>$idx!==null?$id:bin2hex(random_bytes(12)),
                'name'=>$name,'role'=>mb_substr(trim((string)($data['role']??'')),0,90),
                'text'=>$text,'rating'=>$rating,
                'category_id'=>$category,'package_id'=>$package,
                'image'=>$image?:($old['image']??''),
                'status'=>($data['status']??'active')==='active'?'active':'inactive'
            ];
            if($idx!==null)$list[$idx]=$record;else $list[]=$record;
            $db->update('home_sections',['extra_data'=>json_encode(array_values($list),JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR)],'id=?',[(int)$section['id']]);
            $pdo->commit();
        }catch(\Throwable $e){if($pdo->inTransaction())$pdo->rollBack();throw $e;}
    }
    public static function remove(string $id): void
    {
        $db=Database::getInstance();$pdo=$db->getPdo();$pdo->beginTransaction();
        try{
            $section=$db->fetch("SELECT id,extra_data FROM home_sections WHERE section_key='testimonials' LIMIT 1 FOR UPDATE");
            if(!$section)throw new \RuntimeException('Yorum bölümü bulunamadı.');
            $list=json_decode((string)$section['extra_data'],true);
            if(!is_array($list))$list=[];
            $new=[];
            foreach($list as $i=>$row){
                if((string)($row['id']??'legacy-'.$i)!==$id)$new[]=$row;
            }
            $db->update('home_sections',['extra_data'=>json_encode($new,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR)],'id=?',[(int)$section['id']]);
            $pdo->commit();
        }catch(\Throwable $e){if($pdo->inTransaction())$pdo->rollBack();throw $e;}
    }
}
