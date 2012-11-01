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
        
        $this->assets->addScript($this->dir.DS.'js'.DS.'jquery.chili-2.2.js');
        $this->assets->addScript($this->dir.DS.'js'.DS.'recipes.js');
    }
    
}