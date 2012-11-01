<?php

/**
 * Twitter gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Bootstrap_Gear extends Gear {

    protected $name = 'Twitter Bootstrap';
    protected $description = 'Interface framework based on Twitter Bootstrap';
    protected $is_core = TRUE;
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