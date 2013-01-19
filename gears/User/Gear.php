<?php

/**
 * Шестерёнка Пользователи
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class User_Gear extends Gear {

    protected $current;
    protected $hooks = array(
        'post.insert' => 'hookPostCount',
        'post.update' => 'hookPostCount',
        'post.delete' => 'hookPostCount',
//        'friends.insert' => 'hookFriends',
//        'friends.delete' => 'hookFriends',
//        'post.render' => 'hookRenderFilter',
//        'chat.msg.render' => 'hookRenderFilter',
//        'comment.render' => 'hookRenderFilter',
//        'comment.insert' => 'hookCommentsRecount',
//        'comment.update' => 'hookCommentsRecount',
//        'comment.delete' => 'hookCommentsRecount',
        'assets.js.global' => 'hookGlobalScripts',
        'user.update' => 'hookUserUpdate',
        'done' => 'hookDone',
        'menu' => 'hookMenu',
        'user.register' => 'hookUserRegister',
        'parse' => 'hookParse',
    );
    protected $access = array(
        'edit' => 'access',
        'edit.login' => 'access',
        'edit.email' => 'access',
        'delete' => 'access',
        'admin' => array(1),
        'admin_create' => array(1),
        'login' => array(0),
        'logout' => array(1, 100),
        'index' => TRUE,
    );
    protected $routes = array(
        'login' => 'login_action',
        'logout' => 'logout_action',
        'lostpassword' => 'lostpassword_action',
        'register' => 'register_action',
        'admin/users:maybe' => 'admin_action',
        'admin/user/create' => 'admin_create_action',
        'admin/user/(\d+)' => 'edit_action',
        'user/edit/(\d+)' => 'edit_action',
        'user/([\w]+)' => 'index_action',
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
                if (role() == 1) {
                    return TRUE;
                }
                break;
        }
        return FALSE;
    }

    /**
     * Init
     */
    public function init() {
        parent::init();
        $this->object(new User_Object());
        $this->object()->init();
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
        $this->object()->refresh();
    }

    /**
     * Хук парсера
     *
     * @param object $item
     */
    public function hookParse($item) {
        if ($item->body && strpos($item->body, '[user')) {
            preg_match_all('#\[user\](.+?)\[/user\]#i', $item->body, $matches);
            for ($i = 0; $i < sizeof($matches[0]); $i++) {
                if ($user = user($matches[1][$i], 'login')) {
                    $link = $user->getLink('avatar','avatar.tiny') . ' ' . $user->getLink('profile');
                    $item->body = str_replace($matches[0][$i], $link, $item->body);
                }
            }
        }
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
        user()->update(
                array(
                    'drafts' => $this->db->where(array('aid' => $this->user->id, 'published' => 0))->countAll('posts', 'id', TRUE),
                    'posts' => $this->db->where(array('aid' => $this->user->id, 'published' => 1))->countAll('posts', 'id', TRUE),
        ));
    }

    /**
     * Recalulate user comments
     *
     * @param type $Commment
     */
    public function hookCommentsRecount($Comment) {
        if ($Comment->aid == $this->user->id) {
            $User = $this->user->object();
        } else {
            $User = new User();
            $User->id = $comment->aid;
            if (!$User->find()) {
                return;
            }
        }
        $User->update(array('comments' => $this->db->select('*')->where(array('aid' => $User->id, 'published' => 1))->countAll('comments', 'id', TRUE)));
    }

    /**
     * Hook global javascript
     *
     * @param object $cogear
     */
    public function hookGlobalScripts($cogear) {
        if ($this->isLogged()) {
            $user = array(
                'id' => $this->object()->id,
                'role' => $this->object()->role,
                'login' => $this->object()->login,
                'name' => $this->object()->name,
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
     * Hook item render
     *
     * @param type $item
     */
    public function hookCodeUser($matches) {
        if ($matches) {
            if ($user = user($matches[1], 'login')) {
                return $user->getLink('avatar', 'avatar.tiny') . ' ' . $user->getLink('profile');
            } else {
                return '';
            }
        }
        return $matches[0];
    }

    /**
     * Создание меню в админке
     */
    public function hookAdminMenu() {
        new Menu_Tabs(array(
                    'name' => 'admin.users',
                    'elements' => array(
                        array(
                            'label' => icon('user') . ' ' . t('Список'),
                            'link' => l('/admin/users'),
                        ),
                        array(
                            'label' => icon('plus') . ' ' . t('Создать'),
                            'link' => l('/admin/user/create'),
                            'class' => 'fl_r',
                        )
                    )
                ));
    }

    /**
     * Создание меню редактирования пользователя в админке
     */
    public function hookUserEditMenu($User) {
        $menu = new Menu_Tabs(array(
                    'name' => 'admin.users',
                    'elements' => array(
                        array(
                            'label' => icon('user') . ' ' . t('Общие'),
                            'link' => l('/user/edit/' . $User->id),
                            'active' => check_route('user/edit/(\d+)') OR check_route('admin/user/(\d+)'),
                        ),
                    )
                ));
        $menu->object($User);
    }

    /**
     * Отправка письма на почту пользователю о регистрации
     *
     * @param object $user
     */
    public function hookUserRegister($user) {
        $mail = new Mail(array(
                    'name' => 'register',
                    'subject' => t('Регистрация на сайте %s', SITE_URL),
                    'body' => t('Вы успешно зарегистрировались на сайте http://%s.
                        <p>Ваш логин: <b>%s</b>
                        <p>Пароль хранится в зашифрованном виде, но вы всегда сможете его сбросить, используя ссылку: <a href="%s">%s</a>
                            ', SITE_URL, $user->login, l('/lostpassword'), l('/lostpassword')),
                ));
        $mail->to($user->email);
        $mail->send();
    }

    /**
     * Menu builder
     *
     * @param string $name
     * @param object $menu
     */
    public function hookMenu($name, $menu) {
        switch ($name) {
            case 'user':
                if ($this->user->id) {
//                    $menu->add(array(
//                        'label' => $this->getAvatarImage('avatar.navbar'),
//                        'link' => $this->getLink(),
//                        'place' => 'left',
//                        'title' => FALSE,
//                    ));
//                    $menu->add(array(
//                        'label' => $this->getName(),
//                        'link' => NULL,//$this->getLink(),
//                        'title' => FALSE,
//                        'place' => 'right',
//                    ));
                    $menu->add(array(
                        'label' => icon('eject'),
                        'link' => s('/logout'),
                        'title' => FALSE,
                        'place' => 'right',
                        'order' => 1000,
                    ));
                } else {
                    $menu->add(array(
                        'label' => icon('lock'),
                        'link' => l('/login'),
                        'place' => 'right',
                    ));
                }
                break;
            case 'admin':
                $menu->add(array(
                    'link' => l('/admin/users'),
                    'label' => icon('user') . ' ' . t('Пользователи'),
                    'order' => 100,
                ));
                break;
        }
    }

    /**
     * Show login page menu
     */
    public function showMenu() {
        new Menu_Auto(array(
                    'name' => 'user.login',
                    'template' => 'Bootstrap/templates/tabs',
                    'elements' => array(
                        'login' => array(
                            'label' => icon('lock') . ' ' . t('Войти'),
                            'link' => l('/login'),
                        ),
                        'lostpassword' => array(
                            'label' => icon('wrench') . ' ' . t('Забыли пароль?'),
                            'link' => l('/lostpassword'),
                            'access' => check_route('lostpassword'),
                        ),
                        'register' => array(
                            'label' => icon('ok') . ' ' . t('Регистрация'),
                            'link' => l('/register'),
                            'access' => config('user.register.active', FALSE),
                        ),
                    ),
                    'render' => 'info',
                ));
        ;
    }
    /**
     * Отображает профиль пользователя
     *
     * @param string $action
     */
    public function index_action($login = '') {
        if ($login && $user = user($login,'login')) {
                $profile = new Profile($user);
                $profile->show();
        } else {
            event('404');
        }
    }
    /**
     * Show admin page
     */
    public function admin_action() {
        $this->hookAdminMenu();
        $q = $this->input->get('q');
        $tpl = new Template('Search/templates/form');
        $tpl->action = l('/admin/users/');
        $q && $tpl->value = $q;
        $tpl->show('info');
        Db_ORM::skipClear();
        $list = new User_List(array(
                    'name' => 'admin.users',
                    'base' => l('/admin/user/'),
                    'per_page' => config('Admin.user.per_page', 20),
                    'render' => FALSE,
                ));
        $fields = $list->getFields();
        $list->setFields($fields);
        $list->show();
    }

    /**
     * Создание нового пользователя
     */
    public function admin_create_action() {
        $this->hookAdminMenu();
        $form = new Form('User/forms/create');
        if ($result = $form->result()) {
            $user = new User();
            $user->object()->extend($result);
            $user->insert();
            event('user.register', $user);
            flash_success(t('Новый пользоватеь успешно создан!'));
            redirect(l('/admin/users'));
        }
        $form->show();
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
        $this->hookUserEditMenu($user);
//        $user->navbar()->show();
        $form = new Form('User/forms/profile');
        $user->password = '';
        $this->input->post('avatar') !== NULL && $user->object()->avatar = '';
        $form->object($user);
        if ($user->id == 1) {
            $form->delete->options->render = FALSE;
        }

        if ($result = $form->result()) {
            if ($user->login != $result['login']) {
                $redirect = Url::gear('user') . $result['login'];
            }
            if ($result->delete && access('User.delete', $user)) {
                if ($user->delete()) {
                    flash_success(t('Пользователь <b>%s</b> был удалён!', $user->login));
                    redirect(l('/admin/users'));
                }
            }
            $user->object()->extend($result);
            if ($result->password) {
                $user->hashPassword();
            } else {
                unset($user->password);
            }
            if ($user->update()) {
                success(t('Изменения сохранены!'));
                redirect(l(TRUE));
            }
        }
        $form->show();
    }

    /**
     * Login form show
     */
    public function login_action() {
        $this->theme->template('User/templates/login');
        $this->showMenu();
        $form = new Form('User/forms/login');
        if ($data = $form->result()) {
            $user = new User();
            $user->object($data);
            $user->hashPassword();
            if ($user->find() && $user->login()) {
                $data->saveme && $user->remember();
                redirect();
            } else {
                $user->email = $user->login;
                $user->object()->offsetUnset('login');
                if ($user->find() && $user->login()) {
                    $data->saveme && $user->remember();
                    redirect();
                }
            }
            $user->password = '';
            $form->object($user);
            error(t('Введены неверные данные.'), t('Ошибка авторизации'));
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
    public function lostpassword_action($code = NULL) {
        $this->theme->template('User/templates/login');
        $this->showMenu();
        if ($code) {
            $user = new User();
            $user->hash = $code;
            if ($user->find()) {
                $user->hash = $this->secure->genHash(date('H d.m.Y') . $this->session->get('ip') . $user->password);
                $user->save();
                $user->login();
                flash_success(t('Вы вошли по временной ссылке. Теперь вы можете поменять пароль.'));
                redirect($user->getLink('edit'));
            } else {
                error(t('Данный код восстановления пароля уже был использован.', 'User.lostpassword'));
            }
        } else {
            $form = new Form('User/forms/lostpassword');
            if ($result = $form->result()) {
                $user = new User();
                $user->login = $result->login;
                if (!$user->find()) {
                    $user->email = $result->login;
                    if (!$user->find()) {
                        error(t('Вы ввели неверные имя пользователя или пароль.'), t('Ошибка авторизации'), 'growl');
                        $form->show();
                        return;
                    }
                }
                $recover = l('/user/lostpassword/' . $user->hash, TRUE);
                $mail = new Mail(array(
                            'name' => 'register.lostpassword',
                            'subject' => t('Восстановление пароля на сайте %s', SITE_URL),
                            'body' => t('Было запрошено восстановление вашего пароля на сайте http://%s с IP-адреса <b>%s</b>.
                                    <p>Если не вы были инициатором этого действия, оставьте письм без внимания или обратитесь к администрации сайта.
                                    <p>Чтобы пройти процедуру восстановления пароля, перейдите по разовой ссылке:<p>
                            <a href="%s">%s</a>', SITE_URL, $this->session->get('ip'), $recover, $recover),
                        ));
                $mail->to($user->email);
                if ($mail->send()) {
                    $user->save();
                    success(t('Следуйте инструкциям, отправленным на ваш почтовый ящик.', $user->email));
                }
            }
            else
                $form->show();
        }
    }

    /**
     * User Регистрация
     */
    public function register_action($code = NULL) {
        $this->theme->template('User/templates/login');
        if (!config('user.register.active', FALSE)) {
            return error(t('Регистрация отключена администрацией сайта.'));
        }
        if ($this->isLogged()) {
            return error('Вы уже авторизированы!');
        }
        $this->showMenu();
        if ($code) {
            $user = new User();
            $user->hash = $code;
            if ($user->find()) {
                $form = new Form('User/forms/verify');

                $form->email->setValue($user->email);
                if ($result = $form->result()) {
                    $user->object()->extend($result);
                    $result->realname && $user->name = $result->realname;
                    $user->hashPassword();
                    $user->hash = $this->secure->genHash($user->password);
                    $user->reg_date = time();
                    if ($user->save()) {
                        event('user.register', $user);
                        if ($user->login()) {
                            flash_success(t('Регистрация завершена!'));
                            redirect($user->getLink());
                        }
                    }
                }
                $form->show();
            } else {
                error(t('Регистрационный код не найден.'));
            }
        } else {
            $form = new Form('User/forms/register');
            if ($result = $form->result()) {
                $user = new User();
                $user->email = $result->email;
                $user->find();
                $user->hash = $this->secure->genHash(date('H d.m.Y') . $this->session->get('ip') . $result->email);
                if (config('user.register.verification', TRUE)) {
                    $verify_link = l('/user/register/' . $user->hash, TRUE);
                    $mail = new Mail(array(
                                'name' => 'register.verify',
                                'subject' => t('Регистрация на сайте %s', SITE_URL),
                                'body' => t('Вы успешно зарегистрировались на сайте http://%s. <br/>
                            Пожалуйста, перейдите по ссылке ниже, для того чтобы подтвердить данный почтовый ящик:<p>
                            <a href="%s">%s</a>', SITE_URL, $verify_link, $verify_link),
                            ));
                    $mail->to($user->email);
                    if ($mail->send()) {
                        $user->save();
                        event('user.confirmation', $user);
                        success(t('Письмо с подтвержденим регистрации было отправлено на почтовый адрес <b>%s</b>. Следуйте инструкциям.', $user->email));
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
            $this->db->like('login', $query);
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
        return cogear()->user->object();
    }
}