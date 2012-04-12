<?php
/**
 * Menu gear 
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Menu
 * @subpackage
 * @version		$Id$
 */
class Menu_Gear extends Gear {
    protected $name = 'Menu';
    protected $description = 'Menu handler';
    protected $menu;
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->menu = new Core_ArrayObject();
    }
    /**
     * Register new Menu
     *  
     * Done automatically when you are creating new Menu_Object
     * 
     * @param string $name
     * @param Menu_Object $menu 
     */
    public function register($name,Menu_Object &$menu){
        $this->menu->$name = $menu;
    }
}
