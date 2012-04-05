<?php
/**
 * Widget class
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Widgets {
    public static $widgets = array();
    public static function factory($name,$options){
        $path = Gear::preparePath('*'.EXT, 'Widgets');
    }
}