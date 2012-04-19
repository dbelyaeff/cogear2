<?php

/**
 * IM gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          
 * @version		$Id$
 */
class IM_Gear extends Gear {

    protected $name = 'IM';
    protected $description = 'Instant messenger';
    protected $order = 20;

    /**
     * Init
     */
    public function init() {
        parent::init();
    }

    public function menu($name, $menu) {
        switch ($name) {
            case 'navbar':
                $menu->register(array(
                    'label' => icon('envelope icon-white') . ' ' . (cogear()->user->pm_new ? badge(cogear()->user->pm_new, 'info') : ''),
                    'link' => l('/im/'),
                    'place' => 'left',
                    'access' => access('im'),
                ));
                break;
        }
    }

    /**
     * Show menu
     */
    public function showMenu() {
        $menu = new Menu_Tabs(array(
                    'name' => 'im.tabs',
                    'elements' => array(
                        'inbox' => array(
                            'label' => t('Messages (%s)', 'IM', $this->user->pm),
                            'link' => l('/im'),
                            'active' => check_route('im/create', Router::ENDS) ? FALSE : TRUE,
                        ),
                        'create' => array(
                            'label' => t('New message', 'IM'),
                            'link' => l('/im/create'),
                            'class' => 'fl_r',
                        ),
                    ),
                ));
    }

    /**
     * Default dispatcher
     * 
     * @param string $action
     * @param string $subaction 
     */
    public function index_action($page = NULL) {
        $this->showMenu();
    }

    /**
     * Custom dispatcher
     * 
     * @param   string  $subaction
     */
    public function create_action() {
        $this->showMenu();
        $form = new Form('IM.create');
        $form->show();
    }

}