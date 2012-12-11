<?php

/**
 * Required Validate
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage	Form

 */
class Form_Validate_Required extends Form_Validate_Abstract {

    /**
     * Validation
     *
     * @param	string	$value
     */
    public function validate($value) {
        if ($this->element->form->request) {
            if(is_array($value) && !$value){
                return $this->element->addError(t('Поле обязательно к заполнению.'));
            }
            elseif (is_string($value) && trim($value) == '') {
                return $this->element->addError(t('Поле обязательно к заполнению.'));
            }
        }
        return TRUE;
    }

}

