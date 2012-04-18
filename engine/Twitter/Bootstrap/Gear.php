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
class Twitter_Bootstrap_Gear extends Gear {

    protected $name = 'Twitter Bootstrap';
    protected $description = 'Interface framework based on Twitter Bootstrap';

    /**
     * Load assets
     */
    public function loadAssets() {
        $scripts_dir = $this->dir . DS . 'bootstrap' . DS . 'js';
        $styles_dir = $this->dir . DS . 'bootstrap' . DS . 'css';
        $scripts[] = $scripts_dir . DS . 'bootstrap-modal.js';
        $scripts[] = $scripts_dir . DS . 'bootstrap-alert.js';
        $scripts[] = $scripts_dir.DS.'bootstrap-tooltip.js';
//        $scripts[] = $scripts_dir.DS.'bootstrap-button.js';
//        $scripts[] = $scripts_dir.DS.'bootstrap-tab.js';
//        $scripts[] = $scripts_dir.DS.'bootstrap-.js';
        cogear()->assets->addScript($scripts);
        cogear()->assets->addStyle($styles_dir . DS . 'bootstrap.css');
    }

}

function badge($count,$class = 'default'){
    return '<span class="badge badge-'.$class.'">'.$count.'</span>';
}