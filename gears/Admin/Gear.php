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
    protected $is_core = TRUE;
    protected $order = -997;
    protected $access = array(
        'index' => array(1),
    );
    protected $menu;
    public $bc;

    /**
     * Initializer
     */
    public function init() {
        parent::init();
//        if (check_route('admin', Router::STARTS)) {
//            $menu = new Menu_Auto(array(
//                        'name' => 'admin',
//                        'template' => 'Admin/templates/menu',
//                        'render' => 'info',
//                    ));
//        }
        if (access('Admin')) {
            $this->menu = new Menu_Auto(array(
                        'name' => 'admin',
                        'template' => 'Admin/templates/menu',
                        'render' => 'before',
                    ));
            parent::loadAssets();
        }
    }

    /**
     * Load assets - do not load everytime
     */
    public function loadAssets() {
//
    }

    /**
     * Load assets only if requested
     */
    public function request() {
        parent::request();
        title(t('Control Panel', 'Admin'));
        $this->bc = new Breadcrumb_Object(
                        array(
                            'name' => 'admin_breadcrumb',
                            'title' => FALSE,
                            'elements' => array(
                                array(
                                    'link' => l('/admin'),
                                    'label' => icon('home') . ' ' . t('Control Panel', 'Admin'),
                                ),
                            ),
                ));
        $this->menu->attach($this->bc);
    }

    /**
     * Add Control Panel to user panel
     */
    public function menu($name, $menu) {
        switch ($name) {
            case 'admin':
                $menu->register(array(
                    'label' => icon('home'),
                    'link' => l('/admin'),
                    'active' => check_route('admin', Router::ENDS) OR check_route('admin/dashboard', Router::STARTS),
                    'order' => 0,
                    'elements' => array(
                        array(
                            'link' => l('/admin'),
                            'label' => icon('home') . ' ' . t('Dashboard', 'Admin'),
                        ),
//                        array(
//                            'link' => l('/admin/clear/session'),
//                            'label' => icon('remove') . ' ' . t('Clear session', 'Admin'),
//                            'order' => '0.1',
//                        ),
                        array(
                            'link' => l('/admin/clear/cache'),
                            'label' => icon('trash') . ' ' . t('Clear system cache', 'Admin'),
                            'access' => access('Admin'),
                            'order' => '0.2',
                        )
                    ),
                ));
                $menu->register(array(
                    'link' => l('/admin/site'),
                    'label' => icon('inbox') . ' ' . t('Site', 'Admin'),
                    'order' => 1000,
                ));
                break;
        }
    }

    /**
     * Dispatch request
     */
    public function index_action() {
        if ($args = $this->router->getArgs()) {
            $gear = ucfirst($args[0]);
            $args = array_slice($args, 1);
            $callback = array(cogear()->$gear, 'admin');
            $name = t(cogear()->$gear->gear, 'Gears');
//            title($name);
            $this->router->exec($callback, $args);
        }
    }

    /**
     * Cleaner
     *
     * @param type $action
     */
    public function clear_action($action) {
        switch ($action) {
            case 'session':
                $this->session->remove();
                flash_success(t('Session is flushed', 'Admin'));
                break;
            case 'cache':
                flash_success(t('System cache has been reset.', 'Admin'));
                $this->system_cache->clear();
                break;
        }
        back();
    }

    /**
     * Site config
     */
    public function site_action() {
        $form = new Form('Admin/forms/site');
        $form->object(array(
            'name' => config('site.name'),
            'url' => config('site.url'),
            'dev' => config('site.development'),
            'date_format' => config('date.format'),
        ));
        if ($result = $form->result()) {
            $result->name && cogear()->site->set('site.name', $result->name);
            $result->url && cogear()->site->set('site.url', $result->url);
            $result->dev && cogear()->site->set('site.development', $result->dev);
            $result->date_format && cogear()->site->set('date.format', $result->date_format);
            success(t('Data is saved!', 'Form'));
        }
        $form->show();
    }

}
