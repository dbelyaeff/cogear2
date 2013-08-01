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
        'admin_add' => array(1),
        'admin_groups' => array(1),
        'admin_settings' => array(1),
        'admin_list' => array(1),
        'login' => array(0),
        'logout' => array(1, 100),
        'lostpassword' => array(0),
        'index' => TRUE,
        'register' => array(0),
    );
    protected $routes = array(
        'login' => 'login_action',
        'logout' => 'logout_action',
        'lostpassword' => 'lostpassword_action',
        'lostpassword/(\w+)' => 'lostpassword_action',
        'register' => 'register_action',
        'admin/users' => 'admin_list',
        'admin/users/settings' => 'admin_settings',
        'admin/users/groups' => 'admin_groups',
        'admin/users/groups/(\d+)' => 'admin_groups',
        'admin/users/groups/(add)' => 'admin_groups',
        'admin/user/add' => 'admin_add_action',
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
                elseif ($this->user->id == $this->router->getSegments(2)) {
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
                    $link = $user->getLink('avatar', 'avatar.tiny') . ' ' . $user->getLink('profile');
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
        $menu = new Menu_Tabs(array(
            'name' => 'admin.users',
            'render' => 'info',
            'multiple' => TRUE,
            'elements' => array(
                array(
                    'label' => icon('user') . ' ' . t('Пользователи'),
                    'link' => l('/admin/users'),
                    'active' => check_route('admin/users$') OR check_route('admin/user/add'),
                ),
                array(
                    'label' => icon('group') . ' ' . t('Группы'),
                    'link' => l('/admin/users/groups'),
                    'active' => check_route('admin/users/groups(.*)'),
                ),
                array(
                    'label' => icon('cogs') . ' ' . t('Настройки'),
                    'link' => l('/admin/users/settings'),
                ),
                array(
                    'label' => icon('plus') . ' ' . t('Добавить'),
                    'link' => check_route('admin/users/groups') ? l('/admin/users/groups/add') : l('/admin/user/add'),
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
            'name' => 'user.edit',
            'elements' => array(
                array(
                    'label' => icon('user') . ' ' . t('Общие'),
                    'link' => $User->getLink('edit'),
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
        // Выставление роли по-умолчанию
        $user->update(array('role' => config('roles.default', 100)));
        $this->hookUserRegisterEmail($user);
    }

    /**
     * Отправление письма о регистрации на почту
     *
     * @param object $user
     */
    public function hookUserRegisterEmail($user) {
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
        if ($login && $user = user($login, 'login')) {
            $profile = new Profile($user);
            $profile->show();
        } else {
            event('404');
        }
    }

    /**
     * Настройки пользователей
     */
    public function admin_settings() {
        $this->hookAdminMenu();
        $form = new Form(array(
            '#name' => 'user.admin.settings',
            'title' => array(
                'label' => icon('user') . ' ' . t('Настройки регистрации'),
            ),
            'registration' => array(
                'type' => 'checkbox',
                'value' => config('user.register.active'),
                'label' => t('Регистрация разрешена'),
            ),
            'verification' => array(
                'type' => 'checkbox',
                'value' => config('user.register.verification'),
                'label' => t('Подтверждение регистрации по электронной почте'),
            ),
            'save' => array()
        ));
        if ($result = $form->result()) {
            $this->set('user.register.active', $result->registration);
            $this->set('user.register.verification', $result->verification);
            flash_success('Настройки сохранены!');
            reload();
        }
        $form->show();
    }

    /**
     * Управление группами
     *
     * @param mixed $action
     */
    public function admin_groups($action = 'list') {
        $this->hookAdminMenu();
        $config = array(
            '#name' => 'admin.user_group',
            'title' => array(
                'label' => icon('group') . ' ' . t('Добавление группы пользователей'),
            ),
            'id' => array(
                'label' => 'ID',
                'type' => 'text',
                'validate' => array('Num', 'Required'),
            ),
            'name' => array(
            ),
            'save' => array(
            )
        );
        switch ($action) {
            case 'list':
                $table = new Table(array(
                    '#name' => 'admin.user_groups',
                    '#class' => 'table table-bordered table-hover',
                    'id' => array(
                        'label' => 'ID',
                        'align' => 'center',
                        'width' => '5%',
                    ),
                    'name' => array(
                        'label' => t('Название группы'),
                        'align' => 'left',
                        'template' => 'User/templates/tables/group/name',
                    ),
                    'actions' => array(
                        'label' => t('Действия'),
                        'template' => 'User/templates/tables/group/actions',
                    ),
                ));
                $role = new User_Role();
                $role->order('id');
                if ($data = $role->findAll()) {
                    $table->object($data);
                }
                $table->show();
                break;
            case 'add':
                $form = new Form($config);
                if ($result = $form->result()) {
                    $user_role = new User_Role();
                    $user_role->object($result);
                    if (FALSE !== $user_role->insert()) {
                        flash_success(t('Группа добавлена!'));
                        redirect(l('/admin/users/groups'));
                    }
                }
                $form->show();
                break;
            default:
                $user_role = new User_Role();
                $user_role->id = $action;
                if (!$user_role->find()) {
                    return event('404');
                }
                $config['title']['label'] = icon('group') . ' ' . t('Редактирование группы пользователей');
                unset($config['id']);
                if (!in_array($user_role->id, array(0, 1, 100))) {
                    $config['delete'] = array();
                }
                $form = new Form($config);
                $form->object($user_role);
                if ($result = $form->result()) {
                    if ($result->delete && $user_role->delete()) {
                        flash_success(t('Группа удалена!'));
                        redirect(l('/admin/users/groups'));
                    }
                    $user_role->object($result);
                    if ($user_role->save()) {
                        flash_success(t('Группа обновлена!'));
                        redirect(l('/admin/users/groups'));
                    }
                }
                $form->show();
        }
    }

    /**
     * Список пользователей в панели управления
     */
    public function admin_list() {
        $this->hookAdminMenu();
        $q = $this->input->get('q');
        $tpl = new Template('Search/templates/form');
        $tpl->action = l('/admin/users/');
        $q && $tpl->value = $q;
        $tpl->show('info');
        Db_ORM::skipClear();
        $table = new Table(array(
            '#name' => 'admin.users',
            '#class' => 'table table-bordered table-hover shd',
            'login' => array(
                'label' => t('Имя пользователя'),
                'callback' => new Callback(array($this, 'prepareUserTableFields')),
            ),
            'posts' => array(
                'label' => t('Публикации'),
                'callback' => new Callback(array($this, 'prepareUserTableFields')),
            ),
            'reg_date' => array(
                'label' => t('Дата регистрации'),
                'callback' => new Callback(array($this, 'prepareUserTableFields')),
            ),
        ));
        $users = new User();
        $users->order('id');
        if ($data = $users->findAll()) {
            $table->object($data);
            $table->show();
            new Pager(array(
                'count' => $users->countAll(),
                'per_page' => 50,
            ));
        } else {
            event('empty');
        }
    }

    /**
     * Обработчик полей таблицы
     *
     * @param type $user
     * @return type
     */
    public function prepareUserTableFields($key, $user) {
        switch ($key) {
            case 'login':
                return $user->render('list', 'avatar.small');
                break;
            case 'reg_date':
                return df($user->reg_date, 'd M Y');
                break;
            case 'posts':
                return '<a href="' . $user->getLink() . '/posts/" class="badge' . ($user->posts > 0 ? ' badge-info' : '') . '">' . $user->posts . '</a>';
                break;
            case 'comments':
                return '<a href="' . $user->getLink() . '/comments/" class="badge' . ($user->comments > 0 ? ' badge-warning' : '') . '">' . $user->comments . '</a>';
                break;
        }
    }

    /**
     * Создание нового пользователя
     */
    public function admin_add_action() {
        $this->hookAdminMenu();
        $form = new Form('User/forms/add');
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
                $recover = l('/lostpassword/' . $user->hash, TRUE);
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
                    $user->last_visit = time();
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