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
    protected $hooks = array(
        'assets.js.global' => 'hookAssets',
    );
    /**
     * Загружаем Bootstrap с CDN
     */
    public function hookAssets(){
        echo HTML::style('http://netdna.bootstrapcdn.com/twitter-bootstrap/2.2.2/css/bootstrap-combined.min.css');
        echo HTML::script('http://netdna.bootstrapcdn.com/twitter-bootstrap/2.2.2/js/bootstrap.min.js');
    }
    public function loadAssets() {
//        parent::loadAssets();
    }
}

function badge($count,$class = 'default'){
    return '<span class="badge badge-'.$class.'">'.$count.'</span>';
}