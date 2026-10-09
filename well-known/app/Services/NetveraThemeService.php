<?php
namespace App\Services;

/** Accessibility color helpers for editor-customized brand themes. */
final class NetveraThemeService
{
    public static function luminance(string $hex):float
    {
        $h=ltrim(trim($hex),'#');
        if(strlen($h)===3)$h=$h[0].$h[0].$h[1].$h[1].$h[2].$h[2];
        if(strlen($h)!==6 || !ctype_xdigit($h))return 1.0;
        $rgb=[];
        foreach([0,2,4] as $i){
            $x=hexdec(substr($h,$i,2))/255;
            $rgb[]=$x<=.04045?$x/12.92:(($x+.055)/1.055)**2.4;
        }
        return $rgb[0]*.2126+$rgb[1]*.7152+$rgb[2]*.0722;
    }

    public static function onColor(string $hex):string
    {
        return self::luminance($hex)>.179?'#17264E':'#FFFFFF';
    }

    public static function onGradient(string $first,string $second):string
    {
        return (self::luminance($first)+self::luminance($second))/2>.179
            ?'#17264E':'#FFFFFF';
    }
}
