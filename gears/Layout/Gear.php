<?php

/**
 * Layout gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Layout
 * @subpackage          
 * @version		$Id$
 */
class Layout_Gear extends Gear {

    protected $name = 'Layout';
    protected $description = 'Provide layout management';
    protected $package = 'Theme';
    protected $type = Gear::MODULE;
    protected $order = -99;

    /**
     * Init
     */
    public function init() {
        parent::init();
        hook('menu.admin.sidebar', array($this, 'adminMenuLink'));
    }

    public function adminMenuLink($menu) {
        $root = Url::gear('admin');
        $menu['10.1'] = new Menu_Item($root . 'layout', icon('application_view_gallery') . t('Layout'));
    }

    /**
     * Default dispatcher
     * 
     * @param string $action
     * @param string $subaction 
     */
    public function index($action = '', $subaction = NULL) {
        
    }

}