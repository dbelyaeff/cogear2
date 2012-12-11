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
class Form_Validate_String_Ext extends Form_Validate_Abstract {
    /**
     * Validate string path extension
     *
     * @param string $value
     * @param string $ext 
     */
    public function validate($value,$ext = NULL) {
        $real_ext = pathinfo($value,PATHINFO_EXTENSION);
        return $ext == $real_ext ? TRUE : $this->element->addError(t('<b>%s</b> extension is expected.','Form_Validate',$ext));
    }

}