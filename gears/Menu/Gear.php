<?php

/**
 * Шестеренка Меню
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Menu_Gear extends Gear {
    protected $hooks = array(
        'menu.admin.theme' => 'hookMenuAdminTheme',
    );
    protected $routes = array(
      'admin/theme/menu' => 'admin_action',
      'admin/theme/menu/(\d+)' => 'admin_action',
    );
    protected $access = array(
        'admin' => array(1),
    );
    /**
     * Добавляем пункт меню на страниу админки «Внешний вид»
     *
     * @param Menu $menu
     */
    public function hookMenuAdminTheme($menu){
        $menu->add(array(
            'label' => t('Меню'),
            'link' => l('/admin/theme/menu'),
            'title' => FALSE,
        ));
    }

    /**
     * Панель управления меню
     *
     * @param type $id
     */
    public function admin_action($id = 0){
        $this->theme->hookAdminMenu();
    }
}
