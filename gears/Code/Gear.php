<?php

/**
 * Code gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Code_Gear extends Gear {

    protected $name = 'Code';
    protected $description = 'Helps to deal with programming code';
    protected $version = '1.2';

    public function loadAssets() {
        //parent::loadAssets();

        $this->assets->addStyle($this->dir . DS . 'css' . DS . 'prettify.css');
        $this->assets->addScript($this->dir . DS . 'js' . DS . 'prettify.js');
        $this->assets->addScript($this->dir . DS . 'js' . DS . 'script.js');
    }

}