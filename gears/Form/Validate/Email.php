<?php
/**
 * Email validator
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 *         Form

 */
class Form_Validate_Email extends Form_Validate_Abstract{
        /**
         * Validate email address
         */
        public function validate($value){
            if(!$value) return TRUE;
           return filter_var($value, FILTER_VALIDATE_EMAIL) ? TRUE : $this->element->error(t('Укажите корректный адрес электронной почты.'));
        }
}