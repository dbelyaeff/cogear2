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
        $scripts_dir = $this->dir.DS.'bootstrap'.DS.'js';
        $styles_dir = $this->dir.DS.'bootstrap'.DS.'css';
        $scripts[] = $scripts_dir.DS.'bootstrap.min.js';
        $scripts[] = $scripts_dir.DS.'bootstrap-modal.min.js';
        $scripts[] = $scripts_dir.DS.'bootstrap-alert.min.js';
        $scripts[] = $scripts_dir.DS.'bootstrap-button.min.js';
        $scripts[] = $scripts_dir.DS.'bootstrap-tab.min.js';
        $scripts[] = $scripts_dir.DS.'bootstrap-tooltip.min.js';
//        $scripts[] = $scripts_dir.DS.'bootstrap-.js';
        cogear()->assets->addScript($scripts);
        cogear()->assets->addStyle($styles_dir.DS.'bootstrap.css');
    }

}