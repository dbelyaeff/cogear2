<?php

/**
 * User object
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class User_Object extends Db_Item {

    protected $table = 'users';
    public $dir;
    protected $template = 'User.list';
    public $avatar;

    /**
     * Init user as current
     */
    public function init() {
        if ($this->autologin()) {
            event('user.autologin', $this);
            $this->dir = $this->dir();
            $this->avatar = $this->getAvatar();
            if ($this->last_visit < time() - config('user.refresh', 86400)) {
                $this->last_visit = time();
                event('user.refresh', $this);
                $this->update(array('last_visit' => $this->last_visit));
                $this->store();
            }
        }
        // Set data for guest
        else {
            $this->id = 0;
            $this->role = 0;
        }
    }

    /**
     * Autologin
     */
    public function autologin() {
        $cogear = cogear();
        $event = event('user.autologin', $this);
        if (!$event->check()) {
            if ($event->result()) {
                return TRUE;
            }
        } elseif ($cogear->session->get('user')) {
            $this->attach($cogear->session->get('user'));
            $this->store();
            return TRUE;
        } elseif (Cookie::get('id') && $hash = Cookie::get('hash')) {
            $this->id = Cookie::get('id');
            if ($this->find() &&  cogear()->secure->genHash($this->login) == $hash) {
                $this->store();
                return TRUE;
            }
        }
        return FALSE;
    }

    /**
     * Store — save user to session
     */
    public function store() {
        event('user.store', $this);
        cogear()->session->set('user', $this->object);
        return TRUE;
    }

    /**
     * Activate user
     */
    public function login() {
        if (!$this->find()) {
            return FALSE;
        }
        event('user.login', $this);
        return $this->store();
    }

    /**
     * Force login
     *
     * @param   mixed   $value
     * @param   string  $param
     */
    public function forceLogin($value, $param = 'login') {
        $this->clear();
        $this->where($param, $value);
        return $this->login();
    }

    /**
     *
     */
    public function insert($data = NULL) {
        if ($result = parent::insert($data)) {
            event('user.insert', $this, $data, $result);
        }
        return $result;
    }

    /**
     * Update user
     *
     * @param type $data
     */
    public function update($data = NULL) {
        if ($result = parent::update($data)) {
            // Automatically store new data to session
            if ($this->id == user()->id) {
                $this->store();
            }
            event('user.update', $this, $data, $result);
        }
        return $result;
    }

    /**
     * User find method overload
     *
     * @return boolean
     */
    public function find() {
        if ($result = parent::find()) {
            event('user.find', $this, array(), $result);
        }
        return $result;
    }

    /**
     * Delete user
     *
     * @return type
     */
    public function delete() {
        if ($result = parent::delete()) {
            event('user.delete', $this);
        }
        return $result;
    }

    /**
     * Deactivate user
     */
    public function logout() {
        if (!$this->object)
            return;
        $cogear = cogear();
        event('user.logout', $this);
        $cogear->session->remove('user');
        $this->forget();
    }

    /**
     * Check if user is logged
     *
     * @return boolean
     */
    public function isLogged() {
        return $this->id;
    }

    /**
     * Remember user
     */
    public function remember() {
        if (!$this->object)
            return;
        Cookie::set('id', $this->id);
        Cookie::set('hash', cogear()->secure->genHash($this->login));
        event('user.remember', $this);
    }

    /**
     * Make a cache mark to refresh user
     */
    public function refresh($set_flag = FALSE) {
        if (cogear()->cache->read('users/reset/' . $this->id, TRUE)) {
            cogear()->session->remove('user');
            cogear()->cache->remove('users/reset/' . $this->id);
        } elseif($set_flag) {
            cogear()->cache->write('users/reset/' . $this->id, TRUE);
        }
    }

    /**
     * Show user
     */
    public function render($type = NULL, $param = NULL) {
        switch ($type) {
            case 'list':
                $navbar = new Stack(array('name' => 'user.navbar'));
                $navbar->attach($this);
                $navbar->avatar = $this->getAvatarImage('avatar.profile');
                $navbar->name = '<strong><a href="' . $this->getLink() . '">' . $this->getName($param) . '</a></strong>';
                if (access('User.edit', $this)) {
                    $navbar->edit = '<a href="' . $this->getLink('edit') . '" class="sh" title="' . t('Edit') . '"><i class="icon-pencil"></i></a>';
                }
                return $navbar->render();
                break;
            default:
        }
    }

    /**
     * Remember user
     */
    public function forget() {
        Cookie::delete('id');
        Cookie::delete('hash');
        event('user.forget', $this);
    }

    /**
     * Encrypt password
     *
     * @param string $password
     * @return string
     */
    public function hashPassword($password = NULL) {
        $password OR $password = $this->password;
        $this->password = md5(md5($password) . cogear()->secure->key());
        return $this->password;
    }

    /**
     * Get name
     *
     * If name is not provided, login will be used
     *
     * @return string
     */
    public function getName($showName = TRUE) {
        return $showName && $this->name ? $this->name : $this->login;
    }

    /**
     * Get user profile link
     */
    public function getLink($type = 'default', $param = NULL) {
        switch ($type) {
            case 'profile':
                $uri = new Stack(array('name' => 'user.link.profile'));
                $uri->append($this->getLink());
                $name = $this->getName($param);
                if ($param) {
                    $name = '<b>' . $name . '</b>';
                }
                return HTML::a($uri->render('/'), $name);
                break;
            case 'avatar':
                $uri = new Stack(array('name' => 'user.link.avatar'));
                $uri->append($this->getLink());
                $param OR $param = 'avatar.small';
                return HTML::a($uri->render('/'), $this->getAvatarImage($param), array('data-id' => $this->id));
                break;
            case 'edit':
                $uri = new Stack(array('name' => 'user.link.edit'));
                $uri->append('user');
                $uri->append('edit');
                $uri->append($this->id);
                break;
            default:
                $uri = new Stack(array('name' => 'user.link'));
                $uri->append('user');
                $uri->append($this->login);
        }
        return l('/' . $uri->render('/'));
    }

    /**
     * Get HTML image avatar
     *
     * @param string $preset
     * @return string
     */
    public function getAvatarImage($preset = 'avatar.small') {
        return HTML::img(image_preset($preset, $this->getAvatar()->getFile(), TRUE), $this->login, array('class' => 'avatar','title'=>$this->getName()));
    }

    /**
     * Get view snippet
     *
     * @return string
     */
    public function getListView() {
        return $this->getAvatarImage() . ' ' . $this->getLink('profile');
    }

    /**
     * Get user avatar
     *
     * @return  User_Avatar
     */
    public function getAvatar() {
        if (!($this->avatar instanceof User_Avatar)) {
            $this->avatar = new User_Avatar($this->object->avatar);
        }
        $this->avatar->attach($this);
        return $this->avatar;
    }

    /**
     * User navbar
     */
    public function navbar() {
        if (!$this->navbar OR !$this->navbar->count()) {
            $this->navbar = new User_Navbar();
            $this->navbar->attach($this);
        }
        return $this->navbar;
    }

    /**
     * Get user upload directory
     */
    public function dir() {
        return File::mkdir(UPLOADS . DS . 'users' . DS . $this->id);
    }

}