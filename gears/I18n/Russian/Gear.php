<?php

/**
 * Russian I18n gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class I18n_Russian_Gear extends Gear {

    protected $name = 'Russian language';
    protected $description = 'Suppose valid russian language everywhere.';
    protected $package = 'I18n';
    protected $order = 0;
    protected $hooks = array(
        'i18n.transliterate' => 'transliterate',
    );
    /**
     * Transliteration from Russian language
     *
     * @param   string  $text
     * @return   string
     */
    public function transliterate($data) {
        $LettersFrom = explode(",", "а,б,в,г,д,е,з,и,к,л,м,н,о,п,р,с,т,у,ф,ц,ы");
        $LettersTo = explode(",", "a,b,v,g,d,e,z,i,k,l,m,n,o,p,r,s,t,u,f,c,y");
        $BiLetters = array(
            "й" => "jj", "ё" => "jo", "ж" => "zh", "х" => "kh", "ч" => "ch",
            "ш" => "sh", "щ" => "shh", "э" => "je", "ю" => "ju", "я" => "ja",
            "ъ" => "", "ь" => "",
        );
        $Caps = explode(",", "А,Б,В,Г,Д,Е,Ё,Ж,З,И,Й,К,Л,М,Н,О,П,Р,С,Т,У,Ф,Х,Ц,Ч,Ш,Щ,Ь,Ъ,Ы,Э,Ю,Я");
        $Small = explode(",", "а,б,в,г,д,е,ё,ж,з,и,й,к,л,м,н,о,п,р,с,т,у,ф,х,ц,ч,ш,щ,ь,ъ,ы,э,ю,я");
//        $data->text = preg_replace("/\s+/ms", $separator, $data->text);
        $data->text = str_replace($Caps, $Small, $data->text);
        $data->text = str_replace($LettersFrom, $LettersTo, $data->text);
        $data->text = strtr($data->text, $BiLetters);
    }

}