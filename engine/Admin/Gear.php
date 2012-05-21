<?php

/**
 * Admin gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Admin_Gear extends Gear {

    protected $name = 'Admin gear';
    protected $description = 'Site control panel';
    protected $required = array('Access');
    protected $order = -100;
    protected $access = array(
        'index' => array(1),
    );

    /**
     * Initializer
     */
    public function init() {
        parent::init();
        if (check_route('admin', Router::STARTS)) {
            $menu = new Menu_Auto(array(
                        'name' => 'admin',
                        'template' => 'Admin.menu',
                        'render' => 'info',
                    ));
        }
    }

    /**
     * Load assets - do not load everytime
     */
    public function loadAssets() {}

    /**
     * Load assets only if requested
     */
    public function request() {
        parent::request();
        parent::loadAssets();
    }

    /**
     * Add Control Panel to user panel
     */
    public function menu($name, $menu) {
        switch ($name) {
            case 'navbar':
                $menu->register(array(
                    'link' => l('/admin'),
                    'label' => icon('cog icon-white') . ' ' . t('Control Panel', 'Admin'),
                    'place' => 'right',
                    'access' => access('admin'),
                ));
                break;
            case 'admin':
                $menu->register(array(
                    'link' => l('/admin'),
                    'label' => icon('home') .' '. t('Dashboard', 'Admin'),
                    'active' => check_route('admin', Router::ENDS) OR check_route('admin/dashboard', Router::STARTS),
                ));
                $menu->register(array(
                    'link' => l('/admin/site'),
                    'label' => icon('inbox') .' '. t('Site', 'Admin'),
                    'order' => 1000,
                ));
                break;
        }
    }

    /**
     * Dispatch request
     */
    public function index_action() {
        if (!access('admin'))
            return event('403');
        if ($args = $this->router->getArgs()) {
            $gear = ucfirst($args[0]);
            $args = array_slice($args, 1);
            $callback = array(cogear()->$gear, 'admin');
            $this->router->exec($callback, $args);
        }
    }

    /**
     * Site config
     */
    public function site_action(){
        $form = new Form('Admin.site');
        $form->setValues(array(
            'name' => config('site.name'),
            'url' => config('site.url'),
            'dev' => config('site.development'),
            'date_format' => config('date.format'),
        ));
        if($result = $form->result()){
            $result->name && cogear()->site->set('site.name',$result->name);
            $result->url && cogear()->site->set('site.url',$result->url);
            $result->dev && cogear()->site->set('site.development',$result->dev);
            $result->date_format && cogear()->site->set('date.format',$result->date_format);
            success(t('Data is saved!','Form'));
        }
        $form->show();
    }

}
