<?php

/**
 * Шестеренка популярного фреймворка Twitter Bootstrap
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Bootstrap_Gear extends Gear {

    protected $hooks = array(
        'assets.js.global' => 'hookAssets',
        'menu.admin.theme' => 'hookMenuAdminTheme',
    );
    protected $routes = array(
        'admin/theme/bootstrap' => 'admin_action',
    );
    protected $access = array(
        'admin' => array(1),
    );

    /**
     * Загружаем Bootstrap с CDN
     */
    public function hookAssets() {
        $theme = config('bootstrap.theme', 'default');
        switch ($theme) {
            case 'default':
                echo HTML::style('http://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.0/css/bootstrap-combined.min.css');
                break;
            default:
                echo HTML::style('http://netdna.bootstrapcdn.com/bootswatch/2.3.0/' . $theme . '/bootstrap.min.css');
        }
        echo HTML::style('http://netdna.bootstrapcdn.com/font-awesome/3.0.2/css/font-awesome.css');
        echo HTML::script('http://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.0/js/bootstrap.min.js');
    }

    /**
     * Хук меню
     *
     * @param object $menu
     */
    public function hookMenuAdminTheme($menu) {
        $menu->add(array(
            'label' => 'Bootstrap',
            'link' => l('admin/theme/bootstrap'),
            'order' => 100
        ));
    }

    /**
     * Автозагрузка
     */
    public function loadAssets() {
//        parent::loadAssets();
    }

    /**
     * Панель управления
     */
    public function admin_action() {
        $this->theme->hookAdminMenu();
        $form = new Form(array(
            '#name' => 'admin.bootstrap',
            'title' => array(
                'label' => icon('wrench') . ' ' . t('Настройки')
            ),
            'theme' => array(
                'type' => 'select',
                'label' => t('Выберите тему'),
                'values' => array(
                    'default' => t('Стандартная'),
                    'amelia' => 'Amelia',
                    'cerulean' => 'Cerulean',
                    'cosmo' => 'Cosmo',
                    'cyborg' => 'Cyborg',
                    'journal' => 'Journal',
                    'readable' => 'Readable',
                    'simplex' => 'Simplex',
                    'slate' => 'Slate',
                    'spacelab' => 'Spacelab',
                    'spruce' => 'Spruce',
                    'superhero' => 'Superhero',
                    'united' => 'United'
                ),
                'value' => config('bootstrap.theme', 'default'),
            ),
            'save' => array(),
                ));
        if ($result = $form->result()) {
            $this->set('bootstrap.theme', $result->theme);
            flash_success(t('Настройки сохранены успешно!'));
            reload();
        }
        $form->show();
    }

}

function badge($count, $class = 'default') {
    return '<span class="badge badge-' . $class . '">' . $count . '</span>';
}