<?php

/**
 * Профиль пользователя
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Profile_Gear extends Gear {

    protected $hooks = array(
        'menu.user' => 'hookUserMenu',
    );

    /**
     * Добавляем активную иконку в меню
     *
     * @param object $menu
     */
    public function hookUserMenu($menu) {
        if(!user()->id){
            return;
        }
        $menu->add(array(
            'label' => icon('user'),
            'tooltip' => t('Профиль'),
            'link' => user()->getLink(),
            'place' => 'left',
            'title' => FALSE,
            'order' => 3,
        ));
    }

}