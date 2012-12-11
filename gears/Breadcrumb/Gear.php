<?php

/**
 * Шестеренка «Хлебные крошки»
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Breadcrumb_Gear extends Gear {

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