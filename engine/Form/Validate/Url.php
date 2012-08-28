<?php
/**
 * Url validator
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          Form
 * @version		$Id$
 */
class Form_Validate_Url extends Form_Validate_Abstract{
        /**
         * Validate email address
         */
        public function validate($value){
            if(!$value) return TRUE;
           return filter_var($value, FILTER_VALIDATE_URL) ? TRUE : $this->element->addError(t('Please, provide correct url address.'));
        }
}