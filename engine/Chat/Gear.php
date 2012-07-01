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
    protected $hooks = array(
        'chat_msg.insert' => 'hookMsg',
        'chat_msg.update' => 'hookMsg',
        'chat_msg.delete' => 'hookMsg',
    );
    protected $access = array(
        'index' => array(1, 100),
        'view' => 'access',
    );

    /**
     * Acccess
     *
     * @param string $rule
     * @param object $data
     */
    public function access($rule, $data = NULL) {
        switch ($rule) {
            case 'view':
                $event = event('access.chat.view');
                if ($event->check()) {
                    if ($data instanceof Chat_Object && (user()->id = $data->aid OR in_array(user()->id, $data->getUsers()))) {
                        return TRUE;
                    } else {
                        return TRUE;
                    }
                } else {
                    return $event->result;
                }
                break;
        }
        return FALSE;
    }

    /**
     * Hook chat message
     *
     * @param type $Msg
     */
    public function hookMsg($Msg) {
        if ($chat = chat($Msg->cid)) {
            $chat->update(array('last_update' => time()));
        }
    }

    /**
     * Request catcher
     */
    public function request() {
        parent::request();
        js($this->folder . '/js/inline/chat.js');
        page_header(t('Chats', 'Chat'));
    }

    public function menu($name, $menu) {
        switch ($name) {
            case 'navbar':
                $menu->register(array(
                    'label' => icon('envelope icon-white') . ' ' . (cogear()->user->pm_new ? badge(cogear()->user->pm_new, 'info') : ''),
                    'link' => l('/chat/'),
                    'title' => t('Chats', 'Chat'),
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
                            'label' => t('List', 'Chat') . ' <sup>' . $this->user->pm . '</sup>',
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
        $chats = new Chat_List(array(
                    'name' => 'chat-list',
                    'in_set' => array(
                        'chat.to' => user()->id,
                    ),
                    'or_where' => array(
                        'aid' => user()->id,
                    ),
                ));
    }

    /**
     * View chat
     *
     * @param type $cid
     */
    public function view_action($cid = NULL) {
        $this->bc = new Breadcrumb_Object(array(
                    'name' => 'chats',
                    'elements' => array(
                        array(
                            'label' => t('List', 'Chat'),
                            'link' => l('/chat'),
                        ),
                    ),
                    'render' => 'content',
                ));
        if ($chat = chat($cid)) {
            $this->bc->register(array(
                'label' => $chat->name,
                'link' => $chat->getLink(),
            ));
            $chat->show();
        } else {
            event('404');
        }
    }

    /**
     * Custom dispatcher
     *
     * @param   string  $subaction
     */
    public function create_action() {
        $this->showMenu();
        if ($friends = $this->friends->getFriends()) {
            $form = new Form('Chat.chat');
            $form->init();
            $values = array();
            foreach ($friends as $uid => $role) {
                $values[$uid] = user($uid)->getName();
            }
            $form->users->setValues($values);
            if ($result = $form->result()) {
                $chat = new Chat_Object();
//                $chat->aid = user()->id;
                $chat->users = $result->users->toString(',');
                $chat->name = $result->name;
                $chat->created_date = time();
                if ($cid = $chat->insert()) {
                    $msg = new Chat_Messages();
                    $msg->cid = $cid;
//                    $msg->aid = user()->id;
                    $msg->created_date = time();
                    $msg->body = $result->body;
                    if ($msg->insert()) {
                        flash_success(t('Chat has been started'));
                        redirect($chat->getLink());
                    }
                }
            }
            $form->show();
        } else {
            info(t('You have no friends to chat with.', 'Chat'));
        }
    }

    /**
     * Delete chat
     */
    public function delete_action($cid) {
        if ($chat = chat($cid)) {
            if (user()->id == $chat->aid) {
                if ($chat->delete()) {
                    $text = t('Chat has been deleted!', 'Chat');
                    if (Ajax::is()) {
                        $ajax = new Ajax();
                        $ajax->success = TRUE;
                        $ajax->message($text);
                        $ajax->json();
                    }
                    flash_success($text);
                    redirect(l('/chat'));
                }
            }
        }
    }

    /**
     * Leave chat
     *
     * @param type $cid
     * @param type $uid
     */
    public function leave_action($cid, $uid = NULL) {
        if ($chat = chat($cid)) {
            if (user()->id == $chat->aid) {
                $this->delete_action($cid);
            }
            else {
                if ($chat->left(user()->id)) {
                    $text = t('You have left this chat!', 'Chat');
                    if (Ajax::is()) {
                        $ajax = new Ajax();
                        $ajax->success = TRUE;
                        $ajax->message($text);
                        $ajax->json();
                    }
                    flash_success($text);
                    redirect(l('/chat'));
                }
            }
        }
    }

}

/**
 * Shortcut for chat
 *
 * @param int $id
 * @param string    $param
 */
function chat($id = NULL, $param = 'id') {
    if ($id) {
        $chats = new Chat_Object();
        $chats->$param = $id;
        if ($chats->find()) {
            return $chats;
        } else {
            return FALSE;
        }
    }
    return new Chat_Object();
}

/**
 * Shortcut for chat
 *
 * @param int $id
 * @param string    $param
 */
function chat_msg($id = NULL, $param = 'id') {
    if ($id) {
        $msg = new Chat_Messages();
        $msg->$param = $id;
        if ($msg->find()) {
            return $msg;
        } else {
            return FALSE;
        }
    }
    return new Chat_Messages();
}