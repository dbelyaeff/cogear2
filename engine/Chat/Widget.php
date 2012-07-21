<?php

/**
 * Chat widget
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Chat_Widget extends Widgets_Widget {

    public $options = array(
        'class' => 'well chat-widget',
        'limit' => 10,
        'render' => 'sidebar',
        'order' => 3,
    );

    /**
     * Render
     */
    public function render() {
        if ($chat = cogear()->chat->current) {
            $tpl = new Template('Chat.widget');
            $tpl->chat = $chat;
            $this->code = $tpl->render();
            return parent::render();
        }
    }

}