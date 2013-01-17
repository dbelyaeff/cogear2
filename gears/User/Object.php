<?php

/**
 * Класс пользователя
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class User_Object extends Db_Item {

    protected $table = 'users';
    public $dir;
    protected $template = 'User.list';
    public $avatar;
    protected $navbar;

    /**
     * Init user as current
     */
    public function init() {
        if ($this->autologin()) {
            event('user.autologin', $this);
            $this->dir = $this->getUploadPath();
            $this->avatar = $this->getAvatar();
            if ($this->last_visit < time() - config('user.refresh', 86400)) {
                $this->last_visit = time();
                event('user.refresh', $this);
                $this->update(array('last_visit' => $this->last_visit));
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
        $event = event('user.autologin', $this);
        if (!$event->check()) {
            if ($event->result()) {
                return TRUE;
            }
        }
        if ($id = session('uid')) {
            $this->id = $id;
            if ($this->find()) {
                return TRUE;
            }
        }
        if (Cookie::get('uid') && $hash = Cookie::get('hash')) {
            $this->id = Cookie::get('uid');
            if ($this->find() && cogear()->secure->genHash($this->login) == $hash) {
                session('uid', $this->id);
                return TRUE;
            }
        }
        return FALSE;
    }

    /**
     * Activate user
     */
    public function login() {
        if (!$this->find()) {
            return FALSE;
        }
        session('uid',$this->id);
        event('user.login', $this);
        return TRUE;
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
        Cookie::set('uid', $this->id);
        Cookie::set('hash', cogear()->secure->genHash($this->login));
        event('user.remember', $this);
    }

    /**
     * Make a cache mark to refresh user
     */
    public function refresh($set_flag = FALSE) {
        if (cogear()->cache->read('users/reset/' . $this->id, TRUE)) {
            cogear()->session->remove('uid');
            cogear()->cache->remove('users/reset/' . $this->id);
        } elseif ($set_flag) {
            cogear()->cache->write('users/reset/' . $this->id, TRUE);
        }
    }

    /**
     * Show user
     */
    public function render($type = NULL, $param = 'avatar.profile') {
        switch ($type) {
            case 'list':
                $navbar = new Stack(array('name' => 'user.navbar'));
                $navbar->object($this);
                $navbar->avatar = $this->getAvatarImage($param);
                $navbar->name = '<strong><a href="' . $this->getLink() . '">' . $this->getName(NULL) . '</a></strong>';
                if (access('User.edit', $this)) {
                    $navbar->edit = '<a href="' . $this->getLink('edit') . '" class="sh" title="' . t('Редактировать') . '"><i class="icon-pencil"></i></a>';
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
        Cookie::delete('uid');
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
                $uri->append('admin');
                $uri->append('user');
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
        return HTML::img(image_preset($preset, $this->getAvatar()->getFile(), TRUE), $this->login, array('class' => 'avatar', 'title' => $this->getName()));
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
            $this->avatar = new User_Avatar($this->object()->avatar);
        }
        $this->avatar->object($this);
        return $this->avatar;
    }

    /**
     * User navbar
     */
    public function navbar() {
        if (!$this->navbar) {
            $this->navbar = new User_Navbar();
            $this->navbar->object($this);
        }
        return $this->navbar;
    }

    /**
     * Get user upload directory
     */
    public function getUploadPath() {
        return File::mkdir(UPLOADS . DS . 'users' . DS . $this->id);
    }

}