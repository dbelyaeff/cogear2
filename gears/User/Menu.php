<?php

/**
 * Меню пользователя
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class User_Menu extends Bootstrap_Navbar {

    public function __construct($options = array()) {
        $defaults = new Core_ArrayObject(array(
                    'name' => 'user',
                    'class' => 'navbar-inverse',
                    'render' => FALSE,
                ));
        $options && $defaults->extend($options);
        parent::__construct($defaults);
    }
}