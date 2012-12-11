<?php
/**
 * Strip tags filter
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage	Form

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