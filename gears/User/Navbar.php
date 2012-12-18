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
                    'name' => 'user.profile.tabs',
                    'render' => FALSE,
                    'title' => TRUE,
                    'elements' => array(
                        'profile' => array(
                            'label' => t('Профиль'),
                            'link' => $user->getLink(),
                            'title' => t('Профиль'),
                        ),
                        'edit' => array(
                            'label' => t('Редактировать'),
                            'link' => l('/user/edit/' . $user->id),
                            'access' => cogear()->router->check('user/edit'),
                        ),
                    ),
                ));
        $tabs->object($user);
        $tpl->tabs = $tabs;
        event('user.navbar.render', $user, $this);
        return $tpl->render();
    }

}
