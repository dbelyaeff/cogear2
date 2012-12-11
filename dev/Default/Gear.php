<?php

/**
 *  gear
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 *         

 */
class Default_Gear extends Gear {

    protected $name = 'Default Gear';
    protected $description = 'Simple example of Gear';
    protected $order = 100;
    protected $routes = array(
        ':index' => 'index',
    );

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