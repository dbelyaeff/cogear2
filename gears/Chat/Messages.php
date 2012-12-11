<?php

/**
 * Chat object
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage

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
        $data OR $data = $this->getData();
        isset($data['created_date']) OR $data['created_date'] = time();
        isset($data['aid']) OR $data['aid'] = user()->id;
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
        $data OR $data = $this->getData();
        $data['last_update'] = time();
        isset($data['ip']) OR $data['ip'] = cogear()->session->get('ip');
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
            case 'teaser':
                $this->body = mb_substr(strip_tags($this->body),0,125,'UTF-8').'…';
                break;
        }
        return parent::render();
    }
}