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

    /**
     * Init
     */
    public function init() {
        parent::init();
        if ($this->status() == Gears::ENABLED) {
            $this->router->bind(':index', array($this, 'index'), TRUE);
            if (!check_route('install', Router::STARTS)) {
                redirect(l('/install'));
            }
        }
    }

    /**
     * Initializing menu system
     *
     * @param type $name
     * @param type $menu
     */
    public function menu($name, $menu) {
        if ($name == 'install') {

        }
    }

    /**
     * Request
     */
    public function request() {
        parent::request();
        if (config('installed')) {
            redirect('/');
        }
    }

    /**
     * Default dispatcher
     *
     * @param string $action
     * @param string $subaction
     */
    public function index($action = '') {
        new Menu_Tabs(array(
                    'name' => 'install',
                    'render' => 'content',
                    'elements' => array(
                        array(
                            'label' => t('1. Начало'),
                            'link' => '',
                            'active' => check_route('install', Router::ENDS),
                        ),
                        array(
                            'label' => t('2. Проверка'),
                            'link' => '',
                            'active' => check_route('check', Router::ENDS),
                        ),
                        array(
                            'label' => t('3. Настройки'),
                            'link' => '',
                            'active' => check_route('site', Router::ENDS),
                        ),
                        array(
                            'label' => t('4. Завершение'),
                            'link' => '',
                            'active' => check_route('finish', Router::ENDS),
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
                    $config = new Config(SITE . DS . 'site' . EXT);
                    $config->site->name = $result->sitename;
                    $config->key OR $config->key = md5(md5(time()) + time() + $config->site->name);
                    $config->database = Db::parseDSN($result->database);
                    $db = Db::factory('temp', $config->database);
                    if (!$db->connect()) {
                        if ($result->create_db && $db->connect(FALSE)) {
                            $db->query("CREATE DATABASE `{$config->database->base}` DEFAULT  CHARACTER SET utf8 COLLATE utf8_general_ci;
                CREATE USER '{$config->database->user}'@'{$config->database->host}' IDENTIFIED BY '{$config->database->pass}';
                GRANT ALL ON `{$config->database->base}`.* TO '{$config->database->user}'@'localhost';
                FLUSH PRIVILEGES;");
                        }
                        $db->connect();
                    }
                    if ($db->is_connected && $db->import($this->dir . DS . 'cogear.sql')) {
                        $config->store(TRUE);
                        redirect('/install/finish');
                    } else {
                        error(t("Не удалось установить подключение к базе данных."));
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
                $config = new Config(SITE . DS . 'site' . EXT);
                $config->store(TRUE);
                flash_success(t('Ваш сайт успешно настроен!'));
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