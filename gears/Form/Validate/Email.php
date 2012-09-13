<?php
/**
 * Email validator
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          Form
 * @version		$Id$
 */
class Form_Validate_Email extends Form_Validate_Abstract{
        /**
         * Validate email address
         */
        public function validate($value){
            if(!$value) return TRUE;
           return filter_var($value, FILTER_VALIDATE_EMAIL) ? TRUE : $this->element->addError(t('Please, provide correct e-mail address.'));
        }
}