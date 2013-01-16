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
    protected $hooks = array(
        'assets.js.global' => 'hookjQuery',
    );

    public function hookjQuery(){
        echo HTML::script('http://code.jquery.com/jquery-1.8.3.js');
    }
//
}
/**
 * Подключает jQuery UI
 */
function jqueryui(){
    css('http://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css');
    js('http://code.jquery.com/ui/1.9.2/jquery-ui.js');
}