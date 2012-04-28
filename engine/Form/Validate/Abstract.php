<?php
/**
 * Abstract form validator class
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage	Form
 * @version		$Id$
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