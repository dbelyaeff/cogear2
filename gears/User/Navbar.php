<?php

/**
 * User navbar
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage

 */
class User_Navbar extends Object {

    public $options = array(
        'render' => 'info',
    );

    /**
     * Render
     *
     * @return type
     */
    public function render() {
        $tpl = new Template('User/templates/navbar');
        if (!$this->object) {
            return;
        }
        $user = $this->object();
        $tpl->navbar = $user->render('list');
        $tabs = new Menu_Tabs(array(
                    'name' => 'profile.tabs',
                    'render' => FALSE,
                    'title' => TRUE,
                    'elements' => array(
 
                        'edit' => array(
                            'label' => t('Редактировать'),
                            'link' => l('/user/edit/' . $user->id),
                            'access' => cogear()->router->check('profile/edit'),
                        ),
                    ),
                ));
        $tabs->object($user);
        $tpl->tabs = $tabs;
        return $tpl->render();
    }

}
