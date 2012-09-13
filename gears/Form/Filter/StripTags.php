<?php
/**
 * Strip tags filter
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage	Form
 * @version		$Id$
 */
class Form_Filter_StripTags extends Form_Filter_Abstract {
	/**
	 * Filter
	 *
	 * @value
	 */
	public function filter($value,$tags=''){
		return strip_tags($value,$tags);
	}
}