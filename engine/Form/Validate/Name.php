<?php
/**
 * Alphabet validator
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage	Form
 * @version		$Id$
 */
class Form_Validate_Name extends Form_Validate_Regexp{
	/**
	 * Constructor
	 */
	public function __construct(){
                $regexp = '([a-zа-я\s]+)';
		parent::__construct($regexp,t('Value must contain only alphabetical characters and spaces.'));
	} 
} 