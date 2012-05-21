<?php

/**
 * User navbar
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class User_Navbar extends Object {
    public $options = array(
        'render' => 'info',
    );

    /**
     * Constructor
     *
     * @param type $options
     * @param type $place
     */
    public function __construct($options = NULL, $place = NULL) {
        parent::__construct($options, $place);
    }
    /**
     * Render
     *
     * @return type
     */
    public function render() {
        $tpl = new Template('User.navbar');
        if (!$this->object) {
            return;
        }
        $user = $this->object;
        $navbar = new Stack(array('name' => 'user.navbar'));
        $navbar->attach($user);
        $navbar->avatar = $user->getAvatarImage('avatar.profile');
        $navbar->name = '<strong><a href="' . $user->getLink() . '">'.$user->login.'</a></strong>';
        if (access('User.edit',$user)) {
            $navbar->edit = '<a href="' . $user->getLink('edit') . '" class="sh no_fl" title="'.t('Edit').'"><i class="icon-pencil"></i></a>';
        }
        $tpl->navbar = $navbar;
        $tabs = new Menu_Auto(array(
                    'name' => 'user.profile.tabs',
                    'template' => 'Twitter_Bootstrap.tabs',
                    'render' => FALSE,
                    'elements' => array(
                        'profile' => array(
                            'label' => t('Profile', 'User'),
                            'link' => $user->getLink(),
                        ),
                        'edit' => array(
                            'label' => t('Edit'),
                            'link' => l('/user/edit/' . $user->id),
                            'access' => cogear()->router->check('user/edit'),
                        ),
                    ),
                ));
        $tabs->attach($user);
        $tpl->tabs = $tabs;
        return $tpl->render();
    }

}
