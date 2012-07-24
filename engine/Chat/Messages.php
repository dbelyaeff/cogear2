<?php

/**
 * Chat object
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Chat_Messages extends Db_Item {

    protected $table = 'chats_msgs';
    protected $primary = 'id';
    protected $template = 'Chat.msg';
    public $chat;

    /**
     * Find
     */
    public function find(){
        cogear()->db->select('chats_msgs.*, chats_views.viewed')->join('chats_views',array('chats_msgs.id'=>'chats_views.mid','chats_views.uid' => user()->id),'LEFT');
        return parent::find();
    }
    /**
     * Find all
     */
    public function findAll(){
        cogear()->db->select('chats_msgs.*, chats_views.viewed')->join('chats_views',array('chats_msgs.id'=>'chats_views.mid','chats_views.uid' => user()->id),'LEFT');
        return parent::findAll();
    }
    /**
     * Create new chat message
     *
     * @param type $data
     */
    public function insert($data = NULL) {
        $data OR $data = $this->object->toArray();
        $data['created_date'] = time();
        $data['aid'] = user()->id;
        if ($result = parent::insert($data)) {
            event('chat_msg.insert', $this, $data, $result);
        }
        return $result;
    }

    /**
     * Update chat message
     *
     * @param type $data
     */
    public function update($data = NULL) {
        $data OR $data = $this->object->toArray();
        $data['last_update'] = time();
        $data['ip'] = cogear()->session->get('ip');
        if ($result = parent::update($data)) {
            event('chat_msg.update', $this, $data, $result);
        }
        return $result;
    }

    /**
     * Delete chat message
     */
    public function delete() {
        if ($result = parent::delete()) {
            event('chat_msg.delete', $this);
        }
        return $result;
    }
    /**
     * Render message
     */
    public function render($type = NULL){
        switch($type){
            case 'text':
                $this->body = strip_tags($this->body);
                break;
        }
        return parent::render();
    }
}