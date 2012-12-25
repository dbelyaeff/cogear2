<?php

/**
 * Шестеренка популярного фреймворка Twitter Bootstrap
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Bootstrap_Gear extends Gear {

}

function badge($count,$class = 'default'){
    return '<span class="badge badge-'.$class.'">'.$count.'</span>';
}