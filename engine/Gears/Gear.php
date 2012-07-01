<?php

/**
 * Gears manager
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          Gears
 * @version		$Id$
 */
class Gears_Gear extends Gear {

    protected $name = 'Gears manager';
    protected $description = 'Manage and download gears.';
    protected $order = 0;
    /**
     * Menu hook
     * 
     * @param string $name
     * @param object $menu 
     */
    public function menu($name, $menu) {
        switch ($name) {
            case 'admin':
                $menu->register(array(
                    'link' => l('/admin/gears'),
                    'label' => icon('cog').' '.t('Gears','Gears.admin'),
                ));
                break;
        }
    }
    /**
     * Admin dispatcher
     * 
     */
    public function admin(){
    
        
    }


}