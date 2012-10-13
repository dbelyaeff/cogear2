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
class Chat_Object extends Db_Item {

    protected $table = 'chats';
    protected $primary = 'id';

    /**
     * Get chat link
     */
    public function getLink($type = 'default', $param = NULL) {
        switch ($type) {
            case 'full':
                return '<a href="' . $this->getLink() . '">' . $this->name . '</a>';
                break;
            default:
                $uri = new Stack(array('name' => 'chat.link'));
                $uri->append('chat');
                $uri->append('view');
                $uri->append($this->id);
        }
        return l('/' . $uri->render('/'));
    }

    /**
     * Get users
     *
     * @return array
     */
    public function getUsers() {
        return preg_split('#[,][\s]*#', $this->users, NULL, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * User join chat
     *
     * @param int $uid
     */
    public function join($uid = NULL) {
        if (!$uid) {
            $uid = user()->id;
        } else if (!user($uid)->id) {
            return;
        }
        event('chat.join', $this, $uid);
        $users = $this->getUsers();
        if (in_array($uid, $users)) {
            return FALSE;
        } else {
            array_push($users, $uid);
            $this->update(array('users' => implode(',', $users)));
            return TRUE;
        }
    }

    /**
     * User left chat
     *
     * @param uid $uid
     * @return boolean
     */
    public function left($uid = NULL) {
        if (!$uid) {
            $uid = user()->id;
        } else if (!user($uid)->id) {
            return;
        }
        event('chat.left', $this, $uid);
        $users = $this->getUsers();
        foreach ($users as $key => $user_id) {
            if ($uid == $user_id) {
                unset($users[$key]);
            }
        }
        $this->update(array('users' => implode(',', $users)));
        return TRUE;
    }

    /**
     * Get messages for chat
     */
    public function getMessages($limit = NULL) {
        $msgs = new Chat_Messages();
        $msgs->cid = $this->id;
        $msgs->order('created_date', 'desc');
        $msgs->limit($limit ? $limit : config('Chat.msgs.default_num', 20));
        if ($result = $msgs->findAll()) {
            return $result->reverse();
        }
        return $result;
    }

    /**
     * Render chat
     */
    public function render() {
        if (access('Chat.view', $this)) {
            $output = new Core_ArrayObject();
            title($this->name);
            $tpl = new Template('Chat/templates/chat');
            $tpl->chat = $this;
            $output->append($tpl->render());
            $form = new Form('Chat/forms/msg');
            
            $form->cid->setValue($this->id);
            if ($result = $form->result()) {
                $msg = new Chat_Messages();
                $msg->cid = $this->id;
                $msg->body = $result->body;
                $msg->insert();
                redirect($this->getLink());
            }
            $output->append($form->render());
            return $output->toString();
        }
        return event('empty');
    }

    /**
     * Create new chat
     *
     * @param type $data
     */
    public function insert($data = NULL) {
        if($this->find()){
            return $this->id;
        }
        $data OR $data = $this->getData();
        isset($data['created_date']) OR $data['created_date'] = time();
        isset($data['aid']) OR $data['aid'] = user()->id;
        if ($result = parent::insert($data)) {
            event('chat.insert', $this, $data, $result);
        }
        return $result;
    }

    /**
     * Update chat
     *
     * @param type $data
     */
    public function update($data = NULL) {
        $data OR $data = $this->getData();
        $data['last_update'] = time();
        if ($result = parent::update($data)) {
            event('chat.update', $this, $data, $result);
        }
        return $result;
    }

    /**
     * Delete chat
     */
    public function delete() {
        if ($result = parent::delete()) {
            event('chat.delete', $this);
            $msgs = new Chat_Messages();
            $msgs->cid = $this->id;
            $msgs->delete();
        }
        return $result;
    }

    /**
     * Get last message from chat
     *
     * @return type
     */
    public function getLastMsg() {
        $msg = chat_msg();
        $msg->cid = $this->id;
        $msg->limit(1);
        $msg->order('id', 'DESC');
        foreach($msg->findAll() as $item){
            return $item;
        }
    }

}