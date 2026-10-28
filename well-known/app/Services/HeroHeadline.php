<?php
namespace App\Services;

/**
 * Readable hero headline breaks, without duplicating or changing the H1 text.
 * Three words or fewer stay on one line; longer headings break once after
 * the third word. SEO text remains regular HTML, never an image.
 */
final class HeroHeadline
{
    public static function render(string $title, string $accent='', string $accentClass='nv-category-hero-accent'):string
    {
        $lead=trim(preg_replace('/\s+/u',' ',trim($title)));
        $accent=trim(preg_replace('/\s+/u',' ',trim($accent)));
        $leadWords=$lead!==''?preg_split('/\s+/u',$lead):[];
        $accentWords=$accent!==''?preg_split('/\s+/u',$accent):[];
        $words=[];
        foreach($leadWords as $word)$words[]=['text'=>$word,'accent'=>false];
        foreach($accentWords as $word)$words[]=['text'=>$word,'accent'=>true];
        if(!$words)return '';
        $lines=count($words)<=3?[$words]:[array_slice($words,0,3),array_slice($words,3)];
        $html='';
        foreach($lines as $lineIndex=>$line){
            $tokens=[];
            foreach($line as $word){
                $token=htmlspecialchars($word['text'],ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');
                if($word['accent'])$token='<span class="'.htmlspecialchars($accentClass,ENT_QUOTES,'UTF-8').'">'.$token.'</span>';
                $tokens[]=$token;
            }
            // Keep a real text separator in the DOM between visual lines.
            // CSS turns each span into a row, but crawlers and screen readers
            // must still read the full H1 with the original word spacing.
            if($lineIndex>0)$html.=' ';
            $html.='<span class="nv-headline-line">'.implode(' ',$tokens).'</span>';
        }
        return $html;
    }
}
