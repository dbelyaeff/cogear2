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
    public function hookGearRequest($Gear){
        if ($Gear !== $this && 'admin' == $this->router->getSegments(0)) {
            $this->bc && $this->bc->register(array(
                'link' => $this->router->getUri(),
                'label' => $Gear->name,
            ));
        }
    }

    /**
     * Выводим меню на странице настроек
     */
    public function hookSiteSettingsMenu(){
        new Menu_Tabs(array(
            'name' => 'admin.site',
            'elements' => array(
                array(
                    'label' => t('Общие'),
                    'link' => l('/admin/site'),
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
            css($this->folder . DS . 'css' . DS . 'menu.css', 'head');
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
                $menu->register(array(
                    'label' => icon('home'),
                    'link' => l('/admin'),
                    'active' => check_route('admin', Router::ENDS) OR check_route('admin/dashboard', Router::STARTS),
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
                $menu->register(array(
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
        $form = new Form('Admin/forms/site');
        $form->object(array(
            'name' => config('site.name'),
            'dev' => config('development'),
        ));
        if ($result = $form->result()) {
            cogear()->set('site.name', $result->name);
            cogear()->set('development', $result->dev);
            success(t('Настройки успешно сохранены!'));
        }
        $form->show();
    }

}
