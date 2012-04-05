<?php

/**
 *  gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          
 * @version		$Id$
 */
class Default_Gear extends Gear {

    protected $name = 'Default Gear';
    protected $description = 'Simple example of Gear';
    protected $type = Gear::MODULE;
    protected $package = 'Core';
    protected $order = 0;

    /**
     * Init
     */
    public function init() {
        $this->router->addRoute(':index', array($this, 'index'), TRUE);
        parent::init();
    }

    /**
     * Default dispatcher
     * 
     * @param string $action
     * @param string $subaction 
     */
    public function index($action = '', $subaction = NULL) {
        info(t('Simple default gear','Default'));
    }

    /**
     * Custom dispatcher
     * 
     * @param   string  $subaction
     */
    public function action_index($subaction = NULL) {
        
    }

}