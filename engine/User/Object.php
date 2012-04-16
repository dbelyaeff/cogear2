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

    public $dir;
    protected $template = 'User.list';
    public $avatar;

    /**
     * Constructor
     * 
     * @param   boolean $autoinit
     */
    public function __construct() {
        parent::__construct('users');
    }

    /**
     * Init user as current
     */
    public function init() {
        if ($this->autologin()) {
            event('user.autologin', $this);
            $this->dir = $this->dir();
            $this->avatar = $this->getAvatar();
            if ($this->last_visit < time() - config('User.last_visit.period', 86400)) {
                $this->last_visit = time();
                $this->update();
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
        cogear()->session->set('user', $this->object);
        return TRUE;
    }

    /**
     * Activate user
     */
    public function login() {
        return $this->find() && $this->store();
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
     * Deactivate user
     */
    public function logout() {
        if (!$this->object)
            return;
        $cogear = cogear();
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
    }

    /**
     * Remember user
     */
    public function forget() {
        Cookie::delete('id');
        Cookie::delete('hash');
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
     * Generate hash for user
     *
     * @param object $user
     */
    public function genHash() {
        return md5($this->password . cogear()->secure->key());
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
            $this->link = $this->login;
            event('User.link', $this);
            return Url::gear('user') . $this->link . '/';
        }
        return NULL;
    }

    /**
     * Get HTML link to user profile
     */
    public function getProfileLink() {
        return HTML::a($this->getLink(), $this->getName());
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
        return $this->getAvatarImage() . ' ' . $this->getLink();
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
