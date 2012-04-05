<?php
/**
 * Factory interface
 *
 * 
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2010, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          Interface
 * @version		$Id$
 */
interface Interface_Factory {
	public static function factory($name,$options = array());
} 