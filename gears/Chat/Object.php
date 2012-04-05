<?php

/**
 * Chat object
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Chat
 * @subpackage          
 * @version		$Id$
 */
class Chat_Object extends Core_ArrayObject{
    
    /**
     * Render
     * 
     * @return string
     */
    public function render() {
        $output = '';
        if (access('chat post')) {
            $form = new Form('Chat.post');

            $output .= $form->render();
        }
        return $output;
    }
}
