<?php

/**
 * List of blogs
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Chat_List extends Db_List_Table {

    protected $class = 'Chat_Object';
    public $options = array(
        'name' => 'chats-list',
        'page' => 0,
        'per_page' => 20,
        'base' => '',
        'page_suffix' => 'page',
        'where' => array(
        ),
        'like' => array(),
        'fields' => array(),
        'order' => array('last_update', 'DESC'),
        'render' => 'content',
    );

    /**
     * Get fields
     *
     * @return  Core_ArrayObject
     */
    public function getFields() {
        if ($this->fields) {
            return $this->fields;
        } else {
            return $this->setFields(array(
                        'name' => array(
                            'label' => t('Name', 'Chat'),
                            'callback' => new Callback(array($this, 'prepareFields')),
                        ),
                        'users' => array(
                            'class' => 't_c w30',
                            'label' => t('Users', 'Chat'),
                            'callback' => new Callback(array($this, 'prepareFields')),
                        ),
                        'last_update' => array(
                            'label' => t('Last message', 'Chat'),
                            'callback' => new Callback(array($this, 'prepareFields')),
                            'class' => 't_c w20',
                        ),
                        'controls' => array(
                            'callback' => new Callback(array($this, 'prepareFields')),
                            'class' => 't_c w5',
                        )
                    ));
        }
    }

    /**
     * Prepare fields for table
     *
     * @param type $chat
     * @return type
     */
    public function prepareFields($chat, $key) {
        switch ($key) {
            case 'name':
                return $chat->getLink('full');
                break;
            case 'users':
                $output = new Core_ArrayObject();
                $user = user($chat->aid);
                $output->append($user->getLink('avatar', 'avatar.tiny').' &rarr; ');
                foreach ($chat->getUsers() as $uid) {
                    if ($user = user($uid)) {
                        $output->append($user->getLink('avatar', 'avatar.tiny'));
                    }
                }
                return $output->toString(' ');
                break;
            case 'last_update':
                return df($chat->last_update);
                break;
            case 'controls':
                if ($chat->aid == user()->id) {
                    return '<a href="/chat/delete/'.$chat->id.'" class="chat-action" title="' . t('Delete chat', 'Chat') . '"><i class="icon-remove"></i></a>';
                } else {
                    return '<a href="/chat/left/'.$chat->id.'" class="chat-action" title="' . t('Leave chat', 'Chat') . '"><i class="icon-remove"></i></a>';
                }
                break;
        }
    }

}