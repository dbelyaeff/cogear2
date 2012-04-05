<?php

/**
 * Chat gear @ in dev
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Chat
 * @subpackage          
 * @version		$Id$
 */
class Chat_Gear extends Gear {

    protected $name = 'Chat';
    protected $description = 'Provide chat to communication.';
    protected $type = Gear::MODULE;
    protected $package = 'Chat';
    protected $order = 100;

    /**
     * Index
     */
    public function index() {
        $chat = new Chat_Object('test');
        $chat->show();
    }

}