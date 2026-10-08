<?php
namespace App\Services;

/**
 * Escaped, allowlisted Markdown renderer for legacy NetVera articles.
 * Existing saved HTML articles keep their existing CMS rendering untouched.
 */
final class BlogContentRenderer
{
    public static function render(string $content): string
    {
        $content=trim(str_replace(["\r\n","\r"],"\n",$content));
        if($content==='')return '';
        // Do not corrupt existing admin HTML or rich-editor shortcodes.
        if(preg_match('~<(?:p|h[1-6]|ul|ol|div|figure|table|blockquote|section)\b~i',$content))
            return $content;
        if(!preg_match('/(^|\n)\s*(?:#{1,6}\s+|[>*-]\s+|\d+\.\s+|!\[)|\*\*[^*]+\*\*|\[[^\]]+\]\([^)]+\)/u',$content))
            return nl2br(htmlspecialchars($content,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8'));
        return self::markdown($content);
    }

    private static function safeUrl(string $value,bool $image=false): ?string
    {
        $value=trim(html_entity_decode($value,ENT_QUOTES|ENT_HTML5,'UTF-8'));
        if($value===''||strlen($value)>2000||preg_match('/[\x00-\x1f\x7f]/',$value))return null;
        if(str_starts_with($value,'/') && !str_starts_with($value,'//'))return $value;
        if(preg_match('~^https?://~i',$value)&&filter_var($value,FILTER_VALIDATE_URL))return $value;
        if(!$image && str_starts_with($value,'#'))return $value;
        return null;
    }

    private static function inline(string $text): string
    {
        $escaped=htmlspecialchars($text,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');
        // Links are only reintroduced after URL scheme filtering; all labels
        // remain encoded so source Markdown can never inject executable markup.
        $escaped=preg_replace_callback(
            '/(?<!!)\[([^\]\n]+)\]\((\S+?)(?:\s+&quot;[^&]*?&quot;)?\)/u',
            static function(array $m):string{
                $href=self::safeUrl(html_entity_decode($m[2],ENT_QUOTES,'UTF-8'));
                return $href===null?$m[0]:'<a href="'.htmlspecialchars($href,ENT_QUOTES,'UTF-8').
                    '"'.(preg_match('~^https?://~i',$href)?' rel="noopener noreferrer"':'').'>'.$m[1].'</a>';
            },$escaped);
        $escaped=preg_replace('/\*\*(.+?)\*\*/us','<strong>$1</strong>',$escaped);
        $escaped=preg_replace('/(?<!\*)\*([^*\n]+)\*(?!\*)/u','<em>$1</em>',$escaped);
        $escaped=preg_replace('/\x60([^\x60\n]+)\x60/u','<code>$1</code>',$escaped);
        return $escaped;
    }

    private static function markdown(string $input): string
    {
        $lines=explode("\n",$input);
        $out=[];$paragraph=[];$list=null;$quote=[];
        $flushParagraph=static function()use(&$out,&$paragraph):void{
            if($paragraph){
                $out[]='<p>'.self::inline(implode(' ',$paragraph)).'</p>';
                $paragraph=[];
            }
        };
        $closeList=static function()use(&$out,&$list):void{
            if($list!==null){$out[]='</'.$list.'>';$list=null;}
        };
        $flushQuote=static function()use(&$out,&$quote):void{
            if($quote){
                $out[]='<blockquote><p>'.self::inline(implode(' ',$quote)).'</p></blockquote>';
                $quote=[];
            }
        };
        foreach($lines as $raw){
            $line=trim($raw);
            if($line===''){
                $flushParagraph();$closeList();$flushQuote();
                continue;
            }
            if(preg_match('/^!\[([^\]]*)\]\((\/[^\s)]+|https?:\/\/[^\s)]+)(?:\s+"([^"]*)")?\)$/u',$line,$m)){
                $flushParagraph();$closeList();$flushQuote();
                $src=self::safeUrl($m[2],true);
                if($src!==null){
                    $out[]='<figure class="nv-blog-figure"><img loading="lazy" decoding="async" src="'.
                        htmlspecialchars($src,ENT_QUOTES,'UTF-8').'" alt="'.
                        htmlspecialchars($m[1],ENT_QUOTES,'UTF-8').'">'.
                        (!empty($m[3])?'<figcaption>'.htmlspecialchars($m[3],ENT_QUOTES,'UTF-8').'</figcaption>':'').
                        '</figure>';
                }
                continue;
            }
            if(preg_match('/^(#{1,6})\s+(.+)$/u',$line,$m)){
                $flushParagraph();$closeList();$flushQuote();
                $level=max(2,min(6,strlen($m[1]))); // article page already has H1
                $out[]='<h'.$level.'>'.self::inline($m[2]).'</h'.$level.'>';
                continue;
            }
            if(preg_match('/^>\s*(.*)$/u',$line,$m)){
                $flushParagraph();$closeList();$quote[]=$m[1];continue;
            }
            $flushQuote();
            if(preg_match('/^([-*+]|\d+\.)\s+(.+)$/u',$line,$m)){
                $flushParagraph();
                $type=preg_match('/^\d/u',$m[1])?'ol':'ul';
                if($list!==$type){$closeList();$out[]='<'.$type.'>';$list=$type;}
                $out[]='<li>'.self::inline($m[2]).'</li>';
                continue;
            }
            $closeList();
            // Preserve tables as legible text; don't output raw Markdown pipes
            // as executable HTML or interpolate untrusted HTML attributes.
            $paragraph[]=$line;
        }
        $flushParagraph();$closeList();$flushQuote();
        return implode("\n",$out);
    }
}
