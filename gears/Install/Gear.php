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
                    $config = new Config(SITE . DS . 'site' . EXT);
                    $config->site->name = $result->sitename;
                    $config->site->url = str_replace(array('http://','www'),'',trim($result->sitehost,'/'));
                    $config->key OR $config->key = md5(md5(time()) + time() + $config->site->name);
                    $result->port OR $result->port = 3306;
                    $config->database = array(
                        'driver' => config('database.driver'),
                        'host' => $result->host,
                        'base' => $result->base,
                        'user' => $result->user,
                        'pass' => $result->pass,
                        'port' => $result->port,
                        'prefix' => $result->prefix,
                    );
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
                        // Важный момент. PHP 5.2 и PHP 5.3 имеют разные взгляды на сериализацию SPL-класса ArrayObject.
                        // Поэтому данные виджетов в базе должны храниться по-разному.
                        if(version_compare(PHP_VERSION, '5.3.0') >= 0){
                            $db->query("INSERT INTO `widgets` (`id`, `callback`, `name`, `options`, `region`, `route`, `position`) VALUES
(6, 'Theme_Widget_HTML', 'Логотип', 0x783a693a303b613a323a7b733a373a22636f6e74656e74223b733a35373a223c6120687265663d222f223e3c696d67207372633d222f7468656d65732f44656661756c742f696d672f6c6f676f2e706e67222f3e3c2f613e223b733a353a227469746c65223b4e3b7d3b6d3a613a303a7b7d, 'header', '.*', 1);
");
                        }
                        else {
                            $db->query("INSERT INTO `widgets` (`id`, `callback`, `name`, `options`, `region`, `route`, `position`) VALUES
(6, 'Theme_Widget_HTML', 'Логотип', 0x4f3a31363a22436f72655f41727261794f626a656374223a323a7b733a373a22636f6e74656e74223b733a35363a223c6120687265663d222f223e3c696d67207372633d222f7468656d65732f44656661756c742f696d672f6c6f676f2e706e67223e3c2f613e223b733a353a227469746c65223b4e3b7d, 'header', '.*', 1);
");
                        }
                        $config->store(TRUE);
                        redirect(l('/install/finish'));
                    } else {
                        error(t("Не удалось установить подключение к базе данных."),'','content');
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
                flash_success(t('Ваш сайт успешно настроен! <p> Данные для входа – логин <b>admin</b> и пароль <b>password</b>.'),'','info');
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