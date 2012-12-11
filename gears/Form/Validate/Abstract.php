<?php
/**
 * Abstract form validator class
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage	Form

 */
abstract class Form_Validate_Abstract extends Form_Option_Abstract{
	/**
	 * Errors
	 *
	 * @array
	 */
	protected $errors = array();
        /**
	 * Validate
	 *
	 * @param   mixed   $value
         * @return  boolean Is valid or not.
	 */
	 abstract function validate($value);
} 