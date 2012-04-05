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

    /**
     * Initializer
     */
    public function init() {
        parent::init();
    }

    /**
     * Add Control Panel to user panel
     */
    public function menu($name, &$menu) {
        switch ($name) {
            case 'user':
                if ($this->user->id && access('admin')) {
                    $menu->{Url::gear('admin')} = t('Control Panel');
                    $menu->{Url::gear('admin')}->order = 99;
                }
                break;
            case 'admin':
                $menu->{'dashboard'} = t('Dashboard');
                break;
        }
    }

    /**
     * Dispatch request
     */
    public function index() {
        if(!access('admin')) return _403();
        new Admin_Menu();
        $args = $this->router->getArgs();
        $rev_args = array_reverse($args);
        $class = array();
        while ($piece = array_pop($rev_args)) {
            $class[] = $piece;
            $gear = implode('_', $class);
            if ($this->gears->$gear) {
                $callback = array($this->gears->$gear, 'admin');
                if (is_callable($callback)) {
                    event('admin.gear.request', $this->gears->$gear);
                    Template::setGlobal('title', $gear);
                    $this->router->exec($callback, $rev_args);
                    break;
                }
            }
        }
    }

}
