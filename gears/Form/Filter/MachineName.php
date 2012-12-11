<?php

/**
 * 
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage

 */
class Form_Filter_MachineName extends Form_Filter_Abstract {

    /**
     * Transform value into machine readable
     * 
     * @param string $value
     * @return string
     */
    function filter($value) {
        if ($value == '' && isset($this->element->form->elements->name)) {
            $value = transliterate($this->element->form->elements->name->value);
        }
        else {
            $value = transliterate($value);
        }
        $value = strtolower($value);
        return $value;
    }

}