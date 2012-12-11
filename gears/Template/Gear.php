<?php

/**
 * Шестерёнка Шаблон
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Template_Gear extends Gear {

}

/**
 * Template object alias
 *
 * @param string $name
 * @param array $args
 * @return Template
 */
function template($name, $args = array()) {
    $tpl = new Template($name);
    $args && $tpl->assign($args);
    return $tpl;
}