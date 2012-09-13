<?php

/**
 * Menu Item
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Menu
 * @subpackage
 * @version		$Id$
 */
class Menu_Item extends Options{
    public $options = array(
        'link' => '',
        'label' => '',
        'level' => 0,
        'access' => TRUE,
        'order' => 0,
        'active' => NULL,
        'class' => NULL,
    );
}