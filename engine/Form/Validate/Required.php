<?php
/**
 * Required Validate
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage	Form
 * @version		$Id$
 */
class Form_Validate_Required extends Form_Validate_Abstract {
	/**
	 * Validation
	 *
	 * @param	string	$value
	 */
	 public function validate($value){
		 if($this->element->form->request && trim($value) == ''){
			 return $this->element->addError(t('This field is required.'));
		 }
		 return TRUE;
	 }
} 