<?php

/**
 * blog navbar
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Blog_Navbar extends Object {

    public $options = array(
        'render' => 'info',
    );

    /**
     * Render
     *
     * @return type
     */
    public function render() {
        $tpl = new Template('Blog.navbar');
        if (!$this->object) {
            return;
        }
        $blog = $this->object;

        $tpl->navbar = $blog->render('list');
        $tabs = new Menu_Auto(array(
                    'name' => 'blog.tabs',
                    'template' => 'Twitter_Bootstrap.tabs',
                    'render' => FALSE,
                    'elements' => array(
                        'profile' => array(
                            'label' => t('Posts', 'Blog') . ' <sup>' . $blog->posts . '</sup>',
                            'link' => $blog->getLink(),
                            'active' => !check_route('info', Router::ENDS) && !check_route('users', Router::ENDS),
                        ),
                        'edit' => array(
                            'label' => t('Edit'),
                            'link' => $blog->getLink('edit'),
                            'access' => cogear()->router->check('blog/edit'),
                        ),
                        'info' => array(
                            'label' => t('Info', 'Blog'),
                            'link' => $blog->getLink() . '/info/',
                        ),
                        'users' => array(
                            'label' => t('Users', 'Blog'),
                            'link' => $blog->getLink() . '/users/',
                        ),
                    ),
                ));
        $tabs->attach($blog);
        $tpl->tabs = $tabs;
        event('blog.navbar.render', $blog,$this);
        return $tpl->render();
    }

}
