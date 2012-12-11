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

    /**
     * Load assets
     */
    public function loadAssets() {
        $scripts_dir = $this->dir . DS . 'bootstrap' . DS . 'js';
        $styles_dir = $this->dir . DS . 'bootstrap' . DS . 'css';
        $scripts[] = $scripts_dir.DS.'bootstrap.min.js';
        cogear()->assets->addScript($scripts);
        cogear()->assets->addStyle($styles_dir . DS . 'bootstrap.min.css');
        cogear()->assets->addStyle($styles_dir . DS . 'bootstrap-responsive.min.css');
    }

}

function badge($count,$class = 'default'){
    return '<span class="badge badge-'.$class.'">'.$count.'</span>';
}