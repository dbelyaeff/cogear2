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
    protected $hooks = array(
        'post.show.full.before' => 'postShowUserNavbar',
    );

    /**
     * Init
     */
    public function init() {
        parent::init();
        $this->adapter = new User_Object();
        $this->adapter->init();
        new Twitter_Bootstrap_Navbar(array(
                    'name' => 'navbar',
                    'render' => 'before',
                ));
    }

    /**
     * Menu builder
     * 
     * @param string $name
     * @param object $menu 
     */
    public function menu($name, $menu) {
        d('User');
        switch ($name) {
            case 'navbar':
                if ($this->user->id) {
                    $menu->register(array(
                        'label' => $this->getAvatarImage('avatar.navbar'),
                        'link' => $this->getLink(),
                        'place' => 'left',
                    ));
                    $menu->register(array(
                        'label' => $this->getName(),
                        'link' => $this->getLink(),
                        'place' => 'left',
                        'active' => TRUE,
                    ));
                    $menu->register(array(
                        'label' => t('Logout'),
                        'link' => s('/user/logout'),
                        'place' => 'right',
                    ));
                } else {
                    $menu->register(array(
                        'label' => t('Login'),
                        'link' => l('/user/login'),
                        'place' => 'right',
                    ));
                    $menu->register(array(
                        'label' => t('Register'),
                        'link' => l('/user/register'),
                        'place' => 'right',
                    ));
                }
                break;
            case 'admin':
                $menu->register(array(
                    'link' => l('/admin/user'),
                    'label' => icon('user') . ' ' . t('Users', 'User.admin'),
                    'order' => 100,
                ));
                break;
        }
        d();
    }

    /**
     * Show login page menu
     */
    public function showMenu() {
        d('User');
        new Menu_Auto(array(
                    'name' => 'user.login',
                    'template' => 'Twitter_Bootstrap.tabs',
                    'elements' => array(
                        'login' => array(
                            'label' => t('Login'),
                            'link' => l('/user/login'),
                        ),
                        'lostpassword' => array(
                            'label' => t('Lost password'),
                            'link' => l('/user/lostpassword'),
                            'access' => check_route('user/lostpassword'),
                        ),
                        'register' => array(
                            'label' => t('Register'),
                            'link' => l('/user/register'),
                        ),
                    ),
                    'render' => 'info',
                ));
        d();
    }

    /**
     * Dispatcher
     * @param string $action
     */
    public function index_action($action = 'index', $subaction=NULL) {
        $this->show_action($action);
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
    public function show_action($login = NULL) {
        if ($login) {
            $user = new User_Object();
            $this->db->where('login', $login);
            if (!$user->find()) {
                return event('404');
            }
        } else {
            $user = $this->adapter;
        }
        if ($user->id) {
            $user->navbar()->show();
            $tpl = new Template('User.profile');
            $tpl->user = $user;
            $tpl->show();
        } else {
            return event('404');
        }
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
            return event('404');
        }
        if (!access('user.edit.all') && $this->id != $user->id) {
            return event('403');
        }
        $user->navbar()->show();
        $form = new Form('User.profile');
        $user->password = '';
        $form->attach($user->object);
        if ($user->id == 1) {
            $form->elements->delete->options->render = FALSE;
        }
        if ($result = $form->result()) {
            if ($user->login != $result['login']) {
                $redirect = Url::gear('user') . $result['login'];
            }
            if ($result->delete && access('user.delete.all')) {
                if ($user->delete()) {
                    flash_success(t('User <b>%s</b> was deleted!', 'User', $user->login));
                    redirect(l());
                }
            }
            $user->object->adopt($result);
            if ($result->password) {
                $user->hashPassword();
            } else {
                unset($user->password);
            }
            if ($user->update()) {
                flash_success(t('User data saved!', 'User'), t('Success'));
                if ($user->id == $this->id) {
                    $this->store($user->object->toArray());
                }
                redirect(l('/user/edit/' . $id));
            }
        }
        $form->show();
    }

    /**
     * Login form show
     */
    public function login_action() {
        if ($this->isLogged()) {
            return warning('You are already logged in!', 'Authorization');
        }
        $this->showMenu();
        $form = new Form('User.login');
        if ($data = $form->result()) {
            $this->attach($data);
            $this->hashPassword();
            if ($this->find() && $this->login()) {
                $data->saveme && $this->remember();
                redirect($this->getLink());
            } else {
                error(t('Wrong credentials.', 'User'), t('Authentification error', 'User'));
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
    public function lostpassword_action($code= NULL) {
        $this->showMenu();
        if ($code) {
            $user = new User();
            $user->hash = $code;
            if ($user->find()) {
                $user->hash = $this->secure->genHash(date('H d.m.Y') . $this->session->get('ip') . $user->password);
                $user->save();
                $user->login();
                flash_success(t('You have been logged in be temporary link. Now you can change your password.', 'User.lostpassword'));
                redirect($user->getEditLink());
            } else {
                error(t('Password recovery code has been already used.', 'User.lostpassword'));
            }
        } else {
            $form = new Form('User.lostpassword');
            if ($result = $form->result()) {
                $user = new User();
                if ($result->email) {
                    $user->email = $result->email;
                } elseif ($result->login) {
                    $user->login = $result->login;
                }
                if ($user->find()) {
                    $recover = l('/user/lostpassword/' . $user->hash, TRUE);
                    $mail = new Mail(array(
                                'name' => 'register.lostpassword',
                                'subject' => t('Password recovery on %s', 'Mail.lostpassword', config('site.url')),
                                'body' => t('You password recovery has been requeset on http://%s from IP-address <b>%s</b>. 
                                    <p>If you know nothing about this action, just leave it unnoticed or contact site administration.
                                    <p>To recover password, click following link:<p>
                            <a href="%s">%s</a>', 'Mail.registration', config('site.url'), $this->session->get('ip'), $recover, $recover),
                            ));
                    $mail->to($user->email);
                    if ($mail->send()) {
                        $user->save();
                        success(t('Follow the instructions that were send to your email.', 'Mail.lostpassword', $user->email));
                    }
                } else {
                    error(t('Wrong credentials.', 'User'), t('Authentification error', 'User'));
                }
            }
            else
                $form->show();
        }
    }

    /**
     * User registration
     */
    public function register_action($code = NULL) {
        if (!config('user.register.enabled', TRUE)) {
            return warning('Registration is turned off by site admin');
        }
        if ($this->isLogged()) {
            return warning('You are already logged in!', 'Authorization');
        }
        $this->showMenu();
        if ($code) {
            $user = new User();
            $user->hash = $code;
            if ($user->find()) {
                $form = new Form('User.verify');
                $form->init();
                $form->email->setValue($user->email);
                if ($result = $form->result()) {
                    $user->object->mix($result);
                    $result->realname && $user->name = $result->realname;
                    $user->hashPassword();
                    $user->hash = $this->secure->genHash($user->password);
                    $user->reg_date = time();
                    $user->save();
                    if ($user->login()) {
                        flash_success(t('Registration is complete!', 'User.register'));
                        redirect($user->getLink());
                    }
                }
                $form->show();
            } else {
                error(t('Registration code was not found.', 'User.register'));
            }
        } else {
            $form = new Form('User.register');
            if ($result = $form->result()) {
                $user = new User();
                $user->email = $result->email;
                $user->find();
                $user->hash = $this->secure->genHash(date('H d.m.Y') . $this->session->get('ip') . $result->email);
                if (config('user.register.verification', TRUE)) {
                    $verify_link = l('/user/register/' . $user->hash, TRUE);
                    $mail = new Mail(array(
                                'name' => 'register.verify',
                                'subject' => t('Registraion on %s', 'Mail.registration', config('site.url')),
                                'body' => t('You have been successfully registered on http://%s. <br/>
                            Please, click following link to procceed email verification:<p>
                            <a href="%s">%s</a>', 'Mail.registration', config('site.url'), $verify_link, $verify_link),
                            ));
                    $mail->to($user->email);
                    if ($mail->send()) {
                        $user->save();
                        success(t('Confirmation letter has been successfully send to <b>%s</b>. Follow the instructions.', 'Mail.registration', $user->email));
                    }
                } else {
                    $user->save();
                    redirect(l('/user/register/' . $user->hash));
                }
            } else {
                $form->show();
            }
        }
    }

    /**
     * Show user navbar
     *
     * @param object $Stack 
     */
    public function postShowUserNavbar($Stack) {
        return $Stack->object->author->navbar()->show();
    }

}