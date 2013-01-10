<?php

/**
 * Шестеренка Панели управления
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Admin_Gear extends Gear {

    protected $routes = array(
        'admin' => 'dashboard_action',
        'admin/clear/(\w+)' => 'clear_action',
        'admin/site' => 'site_action',
        'admin/site/tools' => 'tools_action',
        'admin/site/export' => 'download_action',
    );
    protected $access = array(
        '*' => array(1),
    );
    protected $hooks = array(
        'menu' => 'hookMenu',
        'gear.request' => 'hookGearRequest',
    );
    public $bc;

    /**
     * Хук запроса к шестерёнке
     *
     * @param Object $Gear
     */
    public function hookGearRequest($Gear) {
        if ($Gear !== $this && 'admin' == $this->router->getSegments(0)) {
            $this->bc && $this->bc->add(array(
                        'link' => $this->router->getUri(),
                        'label' => $Gear->name,
                    ));
        }
    }

    /**
     * Выводим меню на странице настроек
     */
    public function hookSiteSettingsMenu() {
        $this->bc->add(array(
            'link' => l('/admin/site'),
            'label' => t('Сайт'),
        ));
        new Menu_Tabs(array(
                    'name' => 'admin.site',
                    'elements' => array(
                        array(
                            'label' => t('Общие'),
                            'link' => l('/admin/site'),
                        ),
                        array(
                            'label' => icon('wrench') . ' ' . ('Инструменты'),
                            'link' => l('/admin/site/tools'),
                            'class' => 'fl_r',
                        )
                    )
                ));
    }

    /**
     * Initializer
     */
    public function init() {
        parent::init();
        if (access('Admin.*')) {
            new Menu_Auto(array(
                        'name' => 'admin',
                        'template' => 'Admin/templates/menu',
                        'render' => 'before',
                    ));
            css($this->folder . '/css/menu.css', 'head');
            js($this->folder . '/js/menu.js', 'head');
            if ('admin' == $this->router->getSegments(0)) {
                $this->bc = new Breadcrumb_Object(
                                array(
                                    'name' => 'admin.breadcrumb',
                                    'title' => TRUE,
                                    'titleActiveOnly' => FALSE,
                                    'elements' => array(
                                        array(
                                            'link' => l('/admin'),
                                            'label' => icon('home') . ' ' . t('Панель управления'),
                                        ),
                                    ),
                        ));
            }
        }
    }

    /**
     * Load assets - do not load everytime
     */
    public function loadAssets() {

    }

    /**
     * Получение запроса
     */
    public function request() {
        parent::request();
    }

    /**
     * Add Control Panel to user panel
     */
    public function hookMenu($name, $menu) {
        switch ($name) {
            case 'admin':
                $menu->add(array(
                    'label' => icon('home'),
                    'link' => l('/admin'),
                    'active' => check_route('admin$') OR check_route('^admin/dashboard'),
                    'order' => 0,
                    'elements' => array(
                        array(
                            'link' => l('/admin'),
                            'label' => icon('home') . ' ' . t('Главная'),
                        ),
                        array(
                            'link' => l('/admin/clear/session'),
                            'label' => icon('remove') . ' ' . t('Сбросить сессию'),
                            'order' => '0.1',
                        ),
                        array(
                            'link' => l('/admin/clear/cache'),
                            'label' => icon('trash') . ' ' . t('Сбросить кеш'),
                            'access' => access('Admin'),
                            'order' => '0.2',
                        )
                    ),
                ));
                $menu->add(array(
                    'link' => l('/admin/site'),
                    'label' => icon('inbox') . ' ' . t('Сайт'),
                    'order' => 1000,
                ));
                break;
        }
    }

    /**
     * Показывает главную страницу панели управления
     */
    public function dashboard_action() {

    }

    /**
     * Ловушка для страницы по умолчанию
     */
    public function index_action() {
        redirect('/admin/gears');
    }

    /**
     * Cleaner
     *
     * @param type $action
     */
    public function clear_action($action) {
        switch ($action) {
            case 'session':
                $this->session->remove();
                flash_success(t('Сессия сброшена'));
                break;
            case 'cache':
                flash_success(t('Системный кеш сброшен.'));
                $this->system_cache->clear();
                break;
        }
        back();
    }

    /**
     * Site config
     */
    public function site_action() {
        $this->hookSiteSettingsMenu();
        $front_values = new Core_ArrayObject(array(
                    'Admin' => t('Панель управления'),
                    'Post' => t('Пост'),
                    'Pages' => t('Страницы'),
                ));
        $config = array(
            '#name' => 'admin-site',
            'name' => array(
                '#type' => 'text',
                '#label' => t('Название сайта'),
                '#validators' => array('Required'),
            ),
            'front_page' => array(
                '#type' => 'select',
                '#label' => t('Шестерёнка главной страницы:'),
                '#values' => $front_values,
                '#value' => config('router.defaults.gear')
            ),
            'dev' => array(
                '#type' => 'checkbox',
                '#label' => t('Режим разработки'),
                '#value' => config('development'),
            ),
            'save' => array(
            )
        );
        $form = new Form($config);
        $form->object(array(
            'name' => config('site.name'),
            'dev' => config('development'),
        ));
        if ($result = $form->result()) {
            $this->set('site.name', $result->name);
            $this->set('development', $result->dev);
            $this->set('router.defaults.gear', $result->front_page);
            flash_success(t('Настройки успешно сохранены!'));
            back();
        }
        $form->show();
    }

    /**
     * Выгрузка файла конфигурации
     */
    public function download_action($themes = array()) {
        $archive_name = 'config.zip';
        $path = TEMP . DS . $archive_name;
        $zip = new Zip(array(
                    'file' => $path,
                    'create' => TRUE,
                ));
        $zip->info(array('type' => 'config'));
        $zip->add(ROOT . DS . 'config' . EXT);
        $zip->close();
        File::download($path, $archive_name, TRUE);
    }

    /**
     * Импорт и экспорт
     */
    public function tools_action() {
        $this->hookSiteSettingsMenu();
        template('Admin/templates/tools')->show();
        $form = new Form('Admin/forms/import');
        if ($result = $form->result()) {
            if ($file = $result->file) {
                $zip = new Zip(array(
                            'file' => $file->path,
                            'check' => array('type' => 'config'),
                        ));

                if ($zip->extract(ROOT)) {
                    success(t('<b>Архив успешно распакован!</b> Новый файл конфигурации установлен.'));
                }
                $zip->close();
                unlink($file->path);
            }
        }
        $form->show();
    }

}
