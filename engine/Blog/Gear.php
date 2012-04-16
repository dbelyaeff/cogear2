<?php

/**
 *  Blog gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          
 * @version		$Id$
 */
class Blog_Gear extends Gear {

    protected $name = 'Blog';
    protected $description = 'Allow users to have their own blogs';
    protected $package = '';
    protected $order = 0;

    /**
     * Menu hook
     * 
     * @param   string  $name
     * @param   object  $menu
     */
    public function menu($name, $menu) {
        d('Blog');
        switch ($name) {
            case 'user.profile.tabs':
                $menu->register(array(
                    'label' => t('Blog').' ('.$menu->object->posts.')',
                    'link' => $this->getLink($menu->object),
                ));
                break;
        }
        d();
    }

    /**
     * Default dispatcher
     * 
     * @param string $action
     * @param string $subaction 
     */
    public function index($login, $page = NULL) {
        $user = new User_Object();
        $user->login = $login;
        if ($user->find()) {
            $user->navbar()->show();
        } else {
            event('404');
        }
        $post = new Post();
        $post->aid = $user->id;
        $this->db->where(array(
            'published' => 1
        ));
        $this->db->order('created_date', 'DESC');
        $pager = new Pager(array(
            'current' => $page ? intval(str_replace('page','',$page)) : NULL,
            'count' => $post->count(),
            'per_page' => config('Blog.per_page',5),
            'base_uri' => l('/blog/'.$login.'/page')
        ));
        if ($posts = $post->findAll()) {
            foreach ($posts as $post) {
                $post->teaser = TRUE;
                $post->show();
            }
            $pager->show();
        } else {
            event('empty');
        }
    }

    /**
     * Get link to blog
     *
     * @param type $User
     * @return  string 
     */
    public function getLink($User = NULL) {
        $User OR $User = $this->user->adapter;
        $link[] = 'blog';
        $link[] = $User->login;
        event('blog.link', $link);
        return l('/' . implode('/', $link) . '/');
    }

}