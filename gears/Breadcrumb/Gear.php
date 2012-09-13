<?php

/**
 * Breadcrumb gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Breadcrumb_Gear extends Gear {
    protected $name = 'Breadcrumb';
    protected $description = 'Breadcrumb gear';
    protected $package = '';
    protected $order = 0;
    protected $is_core = TRUE;
}

/**
 * Breadcrumb alias
 *
 * @param type $options
 * @return Breadcrumb_Object
 */
function breadcrumb($options = array()) {
    return new Breadcrumb_Object($options);
}