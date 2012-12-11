<?php

/**
 * Friends gear
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage

 */
class Friends_Gear extends Gear {

    protected $hooks = array(
        'user.navbar' => 'hookUserNavbar',
        'blog.follower.insert' => 'clear',
        'blog.follower.update' => 'clear',
        'blog.follower.delete' => 'clear',
        'table.render.users' => 'hookUsersTableRender',
//        'user.profile.fields' => 'hookUserProfile',
    );
    protected $access = array(
        'status' => 'access',
    );

    /**
     * Acccess
     *
     * @param string $rule
     * @param object $Item
     */
    public function access($rule, $data = NULL) {
        switch ($rule) {
            case 'status':
                if ($data instanceof User_Object && user()->id == $data->id) {
                    return FALSE;
                }
                if (role()) {
                    return TRUE;
                }
                break;
        }
        return FALSE;
    }

    /**
     * Hook user navbar
     *
     * @param object  $Navbar
     */
    public function hookUserNavbar($Navbar) {
        if (!access('Friends.status', $Navbar->object))
            return;
        $user = $Navbar->object();
        if ($status = $this->check_status($user)) {
            $code = '<a href="/friends/status/' . $user->id . '" class="ajax sh active"  title="' . t('Unfollow', 'Friends') . '"><i class="icon-user"></i></a>';
        } else {
            $code = '<a href="/friends/status/' . $user->id . '" class="ajax sh" title="' . t('Follow', 'Friends') . '"><i class="icon-user"></i></a>';
        }
        $Navbar->append($code);
    }

    /**
     * Add friends list to user profile
     *
     * @param type $Profile
     */
    public function hookUserProfile($Profile) {
        $user = $Profile->object();
        if ($friends = $this->getFriends($user->id)) {
            $ids = array();
            foreach ($friends as $key => $friend) {
                $ids[] = $key;
            }
            $users = new User();
            if ($ids) {
                $this->db->where_in('users.id', $ids);
                if ($result = $users->findAll()) {
                    $users = new Core_ArrayObject();
                    foreach ($result as $user) {
                        $users->append($user->render('list', $this->check_status($user)));
                    }
                    $Profile->append(array(
                        'label' => t('Friends', 'Friends.profile'),
                        'value' => implode(' ', $users->toArray()),
                        'order' => 5.4,
                    ));
                }
            }
        }
    }

    /**
     * Hook users table render
     *
     * @param type $Table
     */
    public function hookUsersTableRender($Table) {
        $Table->options->fields->prependTo(array(
            'label' => t('Friends', 'Friends'),
            'callback' => new Callback(array($this, 'prepareFields')),
            'class' => 't_c w10',
                ), 'friends', 'reg_date');
    }

    /**
     * Prepare fields for table
     *
     * @param type $user
     * @return type
     */
    public function prepareFields($user, $key) {
        switch ($key) {
            case 'friends':
                return '<a href="' . $user->getLink() . '/friends/" class="badge' . ($user->friends >= 0 ? ' badge-success' : 'badge-important') . '">' . $user->friends . '</a>';
                break;
        }
    }

    /**
     * Menu hook
     *
     * @param   string  $name
     * @param   object  $menu
     */
    public function menu($name, $menu) {
       ;
        switch ($name) {
            case 'user.profile.tabs':
                $menu->register(array(
                    'label' => t('Friends') . ' <sup>' . $menu->object()->friends . '</sup>',
                    'link' => $menu->object()->getLink() . '/friends/',
                    'order' => 3,
                ));
                break;
        }
       ;
    }

    /**
     * Init
     */
    public function init() {
        parent::init();
        route('user/([^/]+)/friends:maybe', array($this, 'index_action'), TRUE);
        if (role() && $this->session->get('friends') == FALSE) {
            $this->setFriends();
        }
    }

    /**
     * Check friendship status
     *
     * @param type $uid
     */
    public function check_status($user) {
        if ($friends = $this->session->get('friends')) {
            $uid = $user instanceof User_Object ? $user->id : $user;
            if (isset($friends[$uid])) {
                return $friends[$uid];
            }
        }
        return FALSE;
    }

    /**
     * Set friends for current user
     */
    public function setFriends() {
        $this->session->set('friends', $this->getFriends());
    }

    /**
     * Get friends for user
     */
    public function getFriends($uid = NULL) {
        $user = $uid ? user($uid) : user();
        $friends = new Friends_Object();
        $friends->where('u1', $user->id)->or_where('u2', $user->id);
        $data = array();
        if ($result = $friends->findAll()) {
            foreach ($result as $item) {
                switch ($user->id) {
                    case $item->u1:
                        $data[$item->u2] = isset($data[$item->u2]) ? 2 : 1;
                        break;
                    case $item->u2:
                        $data[$item->u1] = isset($data[$item->u1]) ? 2 : 1;
                        break;
                }
            }
            foreach ($data as $uid => $value) {
                if ($value < 2) {
                    unset($data[$uid]);
                }
            }
        }
        return $data;
    }

    /**
     * List user friends
     *
     * @param type $login
     */
    public function index_action($login = NULL) {
        if ($login == user()->login) {
            $user = user();
        } elseif (!$user = user($login, 'login')) {
            return event('404');
        }
        $user->navbar()->show();
        $friends = $this->getFriends($user->id);
        $users = new User_List(array(
                    'name' => 'user.friends',
                    'base' => $user->getLink() . '/friends/',
                    'per_page' => config('User.friends.per_page', 10),
                    'where_in' => array('users.id'=>array_keys($friends)),
                    'order' => array('rating','desc'),
                ));
    }

    /**
     * Change friendship status
     *
     * @param   int $uid
     */
    public function status_action($uid) {
        if (!$user = user($uid)) {
            return event('404');
        }
        if (!$blog = blog($user->id, 'aid')) {
            return event('404');
        }
        $data = array();
        $status = $this->check_status($uid);
        $friend = new Friends_Object();
        $friend->u1 = user()->id;
        $friend->u2 = $uid;
        if ($friend->find()) {
            $friend->delete();
            $data['action'] = array(
                'type' => 'class',
                'className' => 'active',
                'title' => t('Follow user', 'Friends'),
            );
            $data['messages'][0] = array(
                'type' => 'success',
                'body' => t('You\'ve stop to follow this user.', 'Friends'),
            );
        } else {
            if ($friend->save()) {
                $data['action'] = array(
                    'type' => 'class',
                    'className' => 'active',
                    'title' => t('You follow this user', 'Friends'),
                );
                $data['messages'][0] = array(
                    'type' => 'success',
                    'body' => t('You\'ve start to follow this user.', 'Friends'),
                );
            }
        }
        $this->clear();
        if (Ajax::is()) {
            ajax()->json($data);
        } else {
            flash_success($data['messages'][0]['body']);
            redirect($user->getLink());
        }
    }

    /**
     * Clear friends session data
     */
    public function clear() {
        $this->session->remove('friends');
    }

}