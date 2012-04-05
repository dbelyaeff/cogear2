<?php

/**
 *  User gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class User_Gear extends Gear {

    protected $name = 'User';
    protected $description = 'Manage users.';
    protected $order = -10;
    protected $current;
    protected $roles;

    /**
     * Init
     */
    public function init() {
        $this->router->addRoute('users:maybe', array($this, 'users'));
        parent::init();
        $this->current = new User_Object();
        $this->current->init();
        new User_Menu();
        $this->getRoles();
    }

    /**
     * Menu builder
     * 
     * @param string $name
     * @param object $menu 
     */
    public function menu($name, $menu) {
        d('User_CP');
        switch ($name) {
            case 'user':
                $root = Url::gear('user');
                if ($this->id) {
                    $menu->{$root} = t('My Profile');
                    $menu->{'users'} = t('Find users');
                    $menu->{$root . 'logout'} = t('Logout');
                    $menu->{$root . 'logout'}->order = 100;
                } else {
                    $menu->{$root . 'login'} = t('Login');
                    $menu->{$root . 'register'} = t('Register');
                }
                break;
            case 'admin':
                $menu->{'user'} = t('Users');
                $menu->{'user'}->order = 100;
                break;
            case 'tabs_admin_user':
                $menu->{'/'} = t('List');
                $menu->{'add'} = t('Add');
                $menu->{'add'}->class = 'fl_r';
                break;
            case 'tabs_user_login':
                $menu->{'login'} = t('Log in');
                $menu->{'register'} = t('Register');
                $menu->{'lostpassword'} = t('Lost password?');
                break;
        }
        d();
    }

    /**
     * Magic __get method
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
        $parent = parent::__get($name);
        return $parent !== NULL ? $parent : (isset($this->current->$name) ? $this->current->$name : NULL);
    }

    /**
     * Magic set method
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value) {
        $this->current->$name = $value;
    }

    /**
     * Magic __call method
     *
     * @param   string  $name
     * @param   array   $args
     */
    public function __call($name, $args = array()) {
        return method_exists($this->current, $name) ? call_user_func_array(array($this->current, $name), $args) : parent::__call($name, $args);
    }

    /**
     * Dispatcher
     * @param string $action
     */
    public function index($action = 'index', $subaction=NULL) {
        switch ($action) {
            case 'login':
            case 'register':
            case 'lostpassword':
                new Menu_Tabs('user_login', Url::gear('user'));
        }
        switch ($action) {
            case 'login':
                $this->login_action();
                break;
            case 'logout':
                $this->logout_action();
                break;
            case 'lostpassword':
                $this->lostpassword_action();
                break;
            case 'register':
                $this->register_action();
                break;
            case 'find':
                $this->users();
                break;
            case 'index':
            case 'profile':
                $this->show_action();
                break;
            case 'edit':
                $this->edit_action($subaction);
                break;
            default:
                $this->show_action($action);
        }
    }

    /**
     * Users list
     */
    public function users($action = NULL, $subaction = NULL) {
        switch ($action) {
            default:
                $grid = new Grid('users');
                $users = new User_Object();
                $this->db->order('id', 'ASC');
                $pager = new Pager_Pages(array(
                            'count' => $users->count(),
                            'current' => $subaction,
                            'per_page' => config('pages.per_page', 5),
                            'base_uri' => '/users/page/',
                            'target' => 'content',
                        ));
                $grid->adopt($users->findAll());
                $grid->show();
                $pager->show();
        }
    }

    /**
     * Show user profile
     * 
     * @param string $login
     */
    public function show_action($id = NULL) {
        if ($id) {
            $user = new User_Object();
            $this->db->where('id', $id);
            $this->db->or_where('login', $id);
            if (!$user->find()) {
                return _404();
            }
        } else {
            $user = $this->current;
        }
        if ($user->id) {
            $this->renderUserInfo($user);
        } else {
            return _404();
        }
    }

    /**
     * Render user info
     * 
     * @param object $user 
     */
    public function renderUserInfo($user) {
        $tpl = new Template('User.profile');
        $tpl->user = $user;
        append('content', $tpl->render());
    }

    /**
     * Edit action
     * 
     * @param   string  $login
     */
    public function edit_action($id = NULL) {
        $id OR $id = $this->user->id;
        $user = new User_Object();
        $this->db->where('id', $id);
        if (!$user->find()) {
            return _404();
        }
        if (!access('user edit_all') && $this->id != $user->id) {
            return _403();
        }
        $this->renderUserInfo($user);
        $user = new User_Object();
        $user->where('id', $id);
        $user->find();
        $form = new Form('User.profile');
        $user->password = '';
        $form->attach($user->object);
        if ($form->elements->avatar->is_ajaxed && Ajax::get('action') == 'replace') {
            $user->avatar = '';
            $user->update();
        }
        if ($this->user->id == $user->id) {
            unset($form->elements->delete);
        }
        if ($result = $form->result()) {
            if ($user->login != $result['login']) {
                $redirect = Url::gear('user') . $result['login'];
            }
            if ($result->delete && access('users delete_all')) {
                $user->delete();
                flash_success(t('User <b>%s</b> was deleted!'));
                redirect(Url::link('/users'));
            }
            $user->adopt($result);
            if ($result->password) {
                $user->hashPassword();
            } else {
                unset($user->password);
            }
            if ($user->update()) {
                d('User edit');
                flash_success(t('User data saved!'), t('Success'));
                d();
                if ($user->id == $this->id) {
                    $this->store($user->object->toArray());
                }
                redirect(Url::gear('user') . $user->login);
            }
        }
        $form->show();
    }

    /**
     * Login form show
     */
    public function login_action() {
        if ($this->isLogged()) {
            return info('You are already logged in!', 'Authorization');
        }
        $form = new Form('User.login');
        if ($data = $form->result()) {
            $this->attach($data);
            $this->hashPassword();
            if ($this->login()) {
                $data->saveme && $this->remember();
                redirect(Url::gear('user'));
            } else {
                error('Login or password weren\'t found in the database', 'Authentification error');
            }
        }
        $form->show();
    }

    /**
     * Logout
     */
    public function logout_action() {
        $this->logout();
        redirect(Url::link());
    }

    /**
     * Lost password recovery
     */
    public function lostpassword_action() {
        $form = new Form('User.lostpassword');
        if ($data = $form->result()) {
            $this->attach($data);
            if ($this->find()) {

                back();
            } else {
                error('Login or password weren\'t found in the database', 'Authentification error');
            }
        }
        $form->show();
    }

    /**
     * User registration
     */
    public function register_action() {
        if (!config('user.register', TRUE)) {
            return info('Registration is turned off by site admin');
        }
        if ($this->isLogged()) {
            return info('You are already logged in!', 'Authorization');
        }
        $form = new Form('User.register');
        if ($data = $form->result()) {
            $this->attach($data);
            $this->role = config('user.default.user_group', 100);
            $this->hashPassword();
            $this->save();
            info('User was successfully registered! Please, check your email for further instructions.', 'Registration succeed.');
        }
        else
            $form->show();
    }

    /**
     * Get user roles
     * 
     * @return  Core_ArrayObject
     */
    public function getRoles() {
        if ($this->roles) {
            return $this->roles;
        }
        $this->roles = new Core_ArrayObject(array(
                    0 => 'guest',
                    1 => 'admin',
                    100 => 'user'
                ));
        if ($extra_groups = $this->system_cache->read('user_roles', TRUE)) {
            $this->roles->mix($extra_groups);
        }
        return $this->roles;
    }

    /**
     * Get translated roles list
     * 
     * @return array
     */
    public function getRolesList() {
        $roles = array();
        foreach ($this->roles as $id => $role) {
            $roles[$id] = t($role, 'User Roles');
        }
        return $roles;
    }

    /**
     * Administrate users
     * 
     * @param string $action 
     */
    public function admin($action = '') {
        new Menu_Tabs('admin_user', Url::gear('admin') . 'user');
        switch ($action) {
            case 'add':
                $this->admin_add();
                break;
            default:
                $this->users();
        }
    }

    /**
     * Add a new user
     */
    public function admin_add() {
        $form = new Form('User.register');
        if ($data = $form->result()) {
            $user = new User_Object(FALSE);
            $user->attach($data);
            $user->hashPassword();
            $user->save();
            info('User was successfully registered!', 'Registration succeed.');
        }
        else
            $form->show();
    }

}