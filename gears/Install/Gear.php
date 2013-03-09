<?php

/**
 * Установщик
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Install_Gear extends Gear {

    protected $routes = array(
        'install' => 'index',
        'install/(\w+)' => 'index',
    );

    /**
     * Init
     */
    public function init() {
        parent::init();
        if ($this->status() == Gears::ENABLED) {
            $this->router->bind(':index', array($this, 'index'), TRUE);
            if (!check_route('^install')) {
                redirect(l('/install'));
            }
        }
    }

    /**
     * Request
     */
    public function request() {
        parent::request();
        if (config('installed')) {
            redirect(l('/'));
        }
    }

    /**
     * Default dispatcher
     *
     * @param string $action
     * @param string $subaction
     */
    public function index($action = '') {
        template('Install/templates/header')->show('info');
        new Menu_Tabs(array(
            'name' => 'install',
            'render' => 'content',
            'elements' => array(
                array(
                    'label' => t('1. Начало'),
                    'link' => '',
                    'active' => check_route('install$'),
                ),
                array(
                    'label' => t('2. Проверка'),
                    'link' => '',
                    'active' => check_route('check$'),
                ),
                array(
                    'label' => t('3. Настройки'),
                    'link' => '',
                    'active' => check_route('site$'),
                ),
                array(
                    'label' => t('4. Завершение'),
                    'link' => '',
                    'active' => check_route('finish$'),
                ),
            ),
                ));
        switch ($action) {
            case 'check':
                $tpl = new Template('Install/templates/check');
                $tpl->show();
                break;
            case 'site':
                append('content', '<p class="alert alert-info">' . t('Определите базовые настройки сайта.') . '</p>');
                $form = new Form('Install/forms/site');

                if ($result = $form->result()) {
                    $site = new Config(SITE . DS . 'site' . EXT);
                    $config = new Config(SITE . DS . 'config' . EXT);
                    $config->site->name = $result->sitename;
                    $site->key OR $site->key = md5(md5(time()) + time() + $site->site->name);
                    $result->port OR $result->port = 3306;
                    $site->database = array(
                        'driver' => config('database.driver'),
                        'host' => $result->host,
                        'base' => $result->base,
                        'user' => $result->user,
                        'pass' => $result->pass,
                        'port' => $result->port,
                        'prefix' => $result->prefix,
                    );
                    $db = Db::factory('temp', $site->database);
                    if (!$db->connect()) {
                        if ($result->create_db && $db->connect(FALSE)) {
                            $db->query("CREATE DATABASE `{$site->database->base}` DEFAULT  CHARACTER SET utf8 COLLATE utf8_general_ci;
                CREATE USER '{$site->database->user}'@'{$site->database->host}' IDENTIFIED BY '{$site->database->pass}';
                GRANT ALL ON `{$site->database->base}`.* TO '{$site->database->user}'@'localhost';
                FLUSH PRIVILEGES;");
                        }
                        $db->connect();
                    }
                    if ($db->is_connected) {
                        $site->store(TRUE);
                        $config->store(TRUE);
                        if ($db->import($this->dir . DS . 'cogear.sql',$site->database->prefix)) {
                            redirect(l('/install/finish'));
                        }
                    } else {
                        error(t("Не удалось установить подключение к базе данных."), '', 'content');
                    }
                } else {
                    $form->save->label = t('Попробуйте снова');
                }
                $form->show();
                break;
            case 'finish':
                $tpl = new Template('Install/templates/finish');
                $tpl->show();
                break;
            case 'done':
//                $site = new Config(SITE . DS . 'site' . EXT);
//                $site->store(TRUE);
                flash_success(t('Ваш сайт успешно настроен! <p> Данные для входа – логин <b>admin</b> и пароль <b>password</b>.'), '', 'info');
                $this->disable();
                redirect();
                break;
            default:
            case 'welcome':
                $tpl = new Template('Install/templates/welcome');
                $tpl->show();
        }
    }

}