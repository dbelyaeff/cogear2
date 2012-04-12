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

    /**
     * Initializer
     */
    public function init() {
        parent::init();
        if ($this->router->check('admin', Router::STARTS)) {
            $menu = new Menu_Auto(array(
                        'name' => 'admin.menu',
                        'template' => 'Admin.menu',
                        'render' => 'sidebar',
                    ));
        }
    }

    /**
     * Add Control Panel to user panel
     */
    public function menu($name, &$menu) {
        switch ($name) {
            case 'user':
                if (access('admin')) {
                    $menu->register(array(
                        'link' => Url::link('/admin'),
                        'label' => t('Control Panel', 'Admin'),
                        'place' => 'right',
                    ));
                }
                break;
            case 'admin.menu':
                $menu->register(array(
                    'link' => Url::link('/admin/dashboard'),
                    'label' => icon('home') . t('Dashboard', 'Admin'),
                    'active' => $this->router->check('admin', Router_Object::ENDS) OR $this->router->check('admin/dashboard', Router_Object::ENDS),
                ));
                break;
        }
    }

    /**
     * Dispatch request
     */
    public function index() {
        if (!access('admin'))
            return event('403');
        if ($args = $this->router->getArgs()) {
            $gear = $args[0];
            $args = array_slice($args, 1);
            $callback = array(cogear()->gear($gear), 'admin');
            if (is_callable($callback)) {
                event('admin.gear.request', $this->gear($gear));
                $this->router->exec($callback, $args);
            }
        }
    }

}
