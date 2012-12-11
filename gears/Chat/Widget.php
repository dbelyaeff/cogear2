<?php

/**
 * Chat widget
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage

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
            $tpl = new Template('Chat/templates/widget');
            $tpl->chat = $chat;
            $this->code = $tpl->render();
            return parent::render();
        }
    }

}