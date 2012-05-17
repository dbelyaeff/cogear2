<?php

/**
 * Highlight gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Highlight_Gear extends Gear {

    protected $name = 'Highlight';
    protected $description = 'Syntax highlight';
    protected $package = '';
    protected $order = 0;

    /**
     * Load assets
     */
    public function loadAssets() {
        //parent::loadAssets();
        $this->assets->addScript($this->folder.'/js/highlight.pack.js');
        $this->assets->addScript($this->folder.'/js/load.js');
        $this->assets->addStyle($this->folder.'/css/googlecode.css');
        $this->assets->addStyle($this->folder.'/css/style.css');
    }
}