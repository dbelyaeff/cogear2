<?php

/**
 * Chat gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Chat_Gear extends Gear {

    protected $name = 'Chat';
    protected $description = 'Instant messenger';
    protected $order = 20;
    protected $access = array(
        'index' => array(1,100),
    );

    /**
     * Init
     */
    public function init() {
        parent::init();
    }
    /**
     * Request catcher
     */
    public function request() {
        parent::request();
        title(t('Chats','Chat'));
    }
    public function menu($name, $menu) {
        switch ($name) {
            case 'navbar':
                $menu->register(array(
                    'label' => icon('envelope icon-white') . ' ' . (cogear()->user->pm_new ? badge(cogear()->user->pm_new, 'info') : ''),
                    'link' => l('/chat/'),
                    'title' => t('Chats','Chat'),
                    'place' => 'left',
                    'access' => access('Chat'),
                ));
                break;
        }
    }

    /**
     * Show menu
     */
    public function showMenu() {
        $menu = new Menu_Tabs(array(
                    'name' => 'chat.tabs',
                    'elements' => array(
                        'inbox' => array(
                            'label' => t('Chats', 'Chat').' <sup>'.$this->user->pm.'</sup>',
                            'link' => l('/chat'),
                            'active' => check_route('Chat/create', Router::ENDS) ? FALSE : TRUE,
                        ),
                        'create' => array(
                            'label' => t('Create', 'Chat'),
                            'link' => l('/chat/create'),
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
        $form = new Form('Chat.create');
        $form->show();
    }

}