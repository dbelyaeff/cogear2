<?php

/**
 * Шестерёнка jQuery
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class jQuery_Gear extends Gear {

}
/**
 * Подключает jQuery UI
 */
function jqueryui(){
    $folder = cogear()->jQuery->folder.'/UI/';
    js($folder.'jquery-ui-1.9.2.custom.min.js');
    css($folder.'jquery-ui-1.9.2.custom.min.css');
}