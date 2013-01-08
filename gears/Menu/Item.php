<?php

/**
 * Menu Item
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Menu
 * @subpackage

 */
class Menu_Item extends Options{
    protected $options = array(
        'link' => '',
        'label' => '',
        'level' => 0,
        'access' => TRUE,
        'order' => 0,
        'active' => NULL,
        'class' => NULL,
        'title' => TRUE,
    );
}