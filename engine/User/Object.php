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
                $this->update();
                event('user.refresh', $this);
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
        if ($cogear->session->get('user')) {
            $this->attach($cogear->session->get('user'));
            return TRUE;
        } elseif ($id = Cookie::get('id') && $hash = Cookie::get('hash')) {
            $this->id = $id;
            if ($this->find() && $this->genHash() == $hash) {
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
            event('user.insert', $this);
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
            event('user.find', $this);
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
        Cookie::set('hash', $this->genHash());
        event('user.remember', $this);
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
    public function getName() {
        if ($this->id) {
            return $this->name ? $this->name : $this->login;
        }
        return NULL;
    }

    /**
     * Get user profile link
     */
    public function getLink() {
        if ($this->id) {
            $link = $this->login;
            return Url::gear('user') . $link . '/';
        }
        return NULL;
    }

    /**
     * Get user profile link
     */
    public function getEditLink() {
        if ($this->id) {
            $link = $this->id;
            event('User.edit.link', $link);
            return Url::gear('user') . 'edit/' . $link . '/';
        }
        return NULL;
    }

    /**
     * Get HTML link to user profile
     */
    public function getProfileLink($useName = FALSE) {
        return HTML::a($this->getLink(), $useName ? $this->getName() : $this->login);
    }

    /**
     * Get HTML image avatar
     *  
     * @param string $preset
     * @return string 
     */
    public function getAvatarImage($preset = 'avatar.small') {
        return HTML::img(image_preset($preset, $this->getAvatar()->getFile(), TRUE), $this->login, array('class' => 'avatar'));
    }

    /**
     * Get HTML avatar linked to profile
     * 
     * @return string
     */
    public function getAvatarLinked() {
        return HTML::a($this->getLink(), $this->getAvatarImage());
    }

    /**
     * Get view snippet
     * 
     * @return string
     */
    public function getListView() {
        return $this->getAvatarImage() . ' ' . $this->getProfileLink();
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
        if (!$this->navbar) {
            $this->navbar = new User_Navbar();
            $this->navbar->attach($this);
        }
        return $this->navbar;
    }

    /**
     * Get user upload directory
     */
    public function dir() {
        return UPLOADS . DS . 'users' . DS . $this->id;
    }

}
