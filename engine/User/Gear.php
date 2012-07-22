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
    protected $order = -999;
    protected $current;
    protected $hooks = array(
        'post.insert' => 'hookPostCount',
        'post.update' => 'hookPostCount',
        'post.delete' => 'hookPostCount',
        'friends.insert' => 'hookFriends',
        'friends.delete' => 'hookFriends',
        'post.render' => 'hookRenderFilter',
        'chat.msg.render' => 'hookRenderFilter',
        'comment.render' => 'hookRenderFilter',
        'comment.insert' => 'hookCommentsRecount',
        'comment.update' => 'hookCommentsRecount',
        'comment.delete' => 'hookCommentsRecount',
        'assets.js.global' => 'hookGlobalScripts',
        'user.update' => 'hookUserUpdate',
        'done' => 'hookDone',
        'widgets' => 'hookWidgets',
    );
    protected $access = array(
        'edit' => 'access',
        'edit.login' => 'access',
        'edit.email' => 'access',
        'delete' => 'access',
        'login' => array(0),
        'logout' => array(1, 100),
    );

    /**
     * Access
     *
     * @param string $rule
     * @param object $data
     */
    public function access($rule, $data = NULL) {
        switch ($rule) {
            case 'edit':
                if (role() == 1) {
                    return TRUE;
                }
                if ($data instanceof User_Object) {
                    if ($data->id == $this->user->id) {
                        return TRUE;
                    }
                }
                // Catch user_id from uri and compare it with current user
                elseif ($this->user->id == $data[0]) {
                    return TRUE;
                }
                break;
            case 'edit.login':
            case 'edit.email':
                if (role() == 1) {
                    return TRUE;
                }
                break;
            case 'delete':

                break;
        }
        return FALSE;
    }

    /**
     * Init
     */
    public function init() {
        parent::init();
        $this->attach(new User_Object());
        $this->object->init();
        new Twitter_Bootstrap_Navbar(array(
                    'name' => 'navbar',
                    'render' => 'before',
                ));
    }

    /**
     * If you edit smbdy profile — you need him to be updated immedeately
     *
     * @param   object  $User
     */
    public function hookUserUpdate($User) {
        if ($User->getLink('edit') == '/' . $this->router->getUri()) {
            if ($User->id == user()->id) {
                $User->store();
            } else {
                $User->refresh(TRUE);
            }
        }
    }

    /**
     * Hook done
     *
     * If there is a flag to reset user data — reset it
     */
    public function hookDone() {
        $this->object->refresh();
    }

    /**
     * Hook blog reader
     *
     * @param Blog_Followers $Friends
     */
    public function hookFriends($Friends) {
        foreach (array($Friends->u1, $Friends->u2) as $uid) {
            if ($user = user($uid)) {
                $user->update(array('friends' => sizeof(cogear()->friends->getFriends($user->id))));
            }
        }
    }

    /**
     * Recalculate user posts count and store it to database
     *
     * @param type $uid
     */
    public function hookPostCount() {
        $this->user->object->update(
                array(
                    'drafts' => $this->db->where(array('aid' => $this->user->id, 'published' => 0))->count('posts', 'id', TRUE),
                    'posts' => $this->db->where(array('aid' => $this->user->id, 'published' => 1))->count('posts', 'id', TRUE),
        ));
    }

    /**
     * Recalulate user comments
     *
     * @param type $Commment
     */
    public function hookCommentsRecount($Comment) {
        if ($Comment->aid == $this->user->id) {
            $User = $this->user->object;
        } else {
            $User = new User();
            $User->id = $comment->aid;
            if (!$User->find()) {
                return;
            }
        }
        $User->update(array('comments' => $this->db->select('*')->where(array('aid' => $User->id, 'published' => 1))->count('comments', 'id', TRUE)));
    }

    /**
     * Hook global javascript
     *
     * @param object $cogear
     */
    public function hookGlobalScripts($cogear) {
        if ($this->isLogged()) {
            $user = array(
                'id' => $this->object->id,
                'role' => $this->object->role,
                'login' => $this->object->login,
                'name' => $this->object->name,
            );
        } else {
            $user = array(
                'id' => 0,
                'role' => 0,
            );
        }
        $cogear->user = new Core_ArrayObject($user);
    }

    /**
     * Hook widgets
     *
     * @param type $widgets
     */
    public function hookWidgets($widgets) {
        $widgets->append(new User_Widgets_Top());
        $widgets->append(new User_Widgets_Online());
    }

    /**
     * Hook item render
     *
     * @param type $item
     */
    public function hookRenderFilter($item) {
        if ($item->body && strpos($item->body, '[user')) {
            preg_match_all('#\[user=([^\]]+)\]#imsU', $item->body, $matches);
            for ($i = 0; $i < sizeof($matches[0]); $i++) {
                if ($user = user($matches[1][$i], 'login')) {
                    $item->body = str_replace($matches[0][$i],$user->getLink('avatar').' '.$user->getLink('profile'),$item->body);
                }
            }
        }
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
                        'title' => t('Profile', 'User'),
                        'place' => 'left',
                        'active' => TRUE,
                    ));
                    $menu->register(array(
                        'label' => t('Logout'),
                        'link' => s('/user/logout'),
                        'place' => 'right',
                        'order' => 1000,
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
                            'label' => t('Enter'),
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
     * Show admin page
     */
    public function admin() {
        $list = new User_List(array(
                    'name' => 'admin.users',
                    'base' => l('/admin/user/'),
                    'per_page' => config('Admin.user.per_page', 5),
                    'render' => FALSE,
                ));
        $fields = $list->getFields();
        $list->setFields($fields);
        $list->show();
    }

    /**
     * Dispatcher
     * @param string $action
     */
    public function index_action($action = 'index', $subaction=NULL) {
        $this->show_action($action);
    }

    /**
     * Show user profile
     *
     * @param string $login
     */
    public function show_action($login = NULL) {
        if ($login) {
            $user = new User_Object();
            $user->login = $login;
            if ($user->find()) {
                $user->navbar()->show();
                $tpl = new Template('User.profile');
                $tpl->user = $user;
                $tpl->show();
                return;
            }
        }
        page_header(t('Users', 'User'));
        new User_List(array(
                    'name' => 'user',
                ));
    }

    /**
     * Edit action
     *
     * @param   string  $login
     */
    public function edit_action($id = NULL) {
        $id OR $id = $this->user->id;
        $user = new User_Object();
        $user->id = $id;
        if (!$user->find()) {
            return event('404');
        }
        $user->navbar()->show();
        $form = new Form('User.profile');
        $user->password = '';
        $form->attach($user->object);
        if ($user->id == 1) {
            $form->elements->delete->options->render = FALSE;
        }
        $form->init();
        if ($result = $form->result()) {
            if ($user->login != $result['login']) {
                $redirect = Url::gear('user') . $result['login'];
            }
            if ($result->delete && access('User.delete', $user)) {
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
                redirect(l('/user/edit/' . $id));
            }
        }
        $form->show();
    }

    /**
     * Login form show
     */
    public function login_action() {
        $this->showMenu();
        $form = new Form('User.login');
        if ($data = $form->result()) {
            $user = new User();
            $user->attach($data);
            $user->hashPassword();
            if ($user->find() && $user->login()) {
                $data->saveme && $user->remember();
                redirect($user->getLink());
            } else {
                $user->email = $user->login;
                $user->object->offsetUnset('login');
                if ($user->find() && $user->login()) {
                    $data->saveme && $user->remember();
                    redirect($user->getLink());
                }
            }
            error(t('Wrong credentials.', 'User'), t('Authentification error', 'User'));
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
                redirect($user->getLink('edit'));
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
                    error(t('Wrong credentials.', 'User'), t('Authentification error', 'User'), 'growl');
                    $form->show();
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
                    if ($user->save()) {
                        event('user.verified', $user);
                        if ($user->login()) {
                            flash_success(t('Registration is complete!', 'User.register'));
                            redirect($user->getLink());
                        }
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
                        event('user.confirmation', $user);
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
     * Autocompleter
     */
    public function autocomplete_action() {
        if ($query = $this->input->get('query')) {
            $user = new User();
            $this->db->like('login', $query, 'both');
            if ($users = $user->findAll()) {
                $data = array('query' => $query, 'suggestions' => array());
                foreach ($users as $user) {
                    array_push($data['suggestions'], $user->login);
                }
                die(json_encode($data));
            }
        }
    }

}

/**
 * Shortcut for user
 *
 * @param int $id
 * @param string    $param
 */
function user($id = NULL, $param = 'id') {
    if ($id) {
        $user = new User();
        $user->$param = $id;
        if ($user->find()) {
            return $user;
        }
        return NULL;
    } else {
        return cogear()->user->object;
    }
}