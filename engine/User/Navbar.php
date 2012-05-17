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
        if (access('user.edit.all') OR $user->id == cogear()->user->id) {
            $navbar->edit = '<a href="' . $user->getLink('edit') . '" class="btn btn-primary btn-mini">' . t('Edit') . '</a>';
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
