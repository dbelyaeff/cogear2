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
        $navbar = new Stack(array('name' => 'blog.navbar'));
        $navbar->attach($blog);
        $navbar->avatar = $blog->getAvatarImage('avatar.profile');
        $navbar->name = '<strong><a href="' . $blog->getLink() . '">' . $blog->name . '</a></strong>';
        if (access('Blog.edit',$blog)) {
            $navbar->edit = '<a class="blog-edit" href="' . $blog->getLink('edit') . '"><i class="icon-cog"></i></a>';
        }
        if (access('Blog.status',$blog) && $this->user->id != $blog->aid) {
            $status = cogear()->blog->check($blog->id);
            switch ($status) {
                case 0:
                default :
                    $navbar->join = '<a data-type="modal" data-source="form-blog-status" href="' . l('/blog/status/' . $blog->id) . '" class="btn btn-success btn-mini">' . t('Follow', 'Blog') . '</a>';
                    break;
                case 1:
                    $navbar->join = '<a data-type="modal" data-source="form-blog-status" href="' . l('/blog/status/' . $blog->id) . '" class="btn btn-warning btn-mini">' . t('Unfollow', 'Blog') . '</a>';
                    break;
                case 2:
                    $navbar->join = '<a data-type="modal" data-source="form-blog-status" href="' . l('/blog/status/' . $blog->id) . '" class="btn btn-danger btn-mini">' . t('Unfollow', 'Blog') . '</a>';
                    break;
            }
        }
        $tpl->navbar = $navbar;
        $tabs = new Menu_Auto(array(
                    'name' => 'blog.tabs',
                    'template' => 'Twitter_Bootstrap.tabs',
                    'render' => FALSE,
                    'elements' => array(
                        'profile' => array(
                            'label' => t('Posts', 'Blog').' <sup>'.$blog->posts.'</sup>',
                            'link' => $blog->getLink(),
                            'active' =>  !check_route('info', Router::ENDS) && !check_route('users', Router::ENDS),
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
        return $tpl->render();
    }

}
