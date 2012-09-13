<?php

/**
 * Front gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Front_Gear extends Gear {

    protected $name = 'Front';
    protected $description = 'Front page';
    protected $order = 0;
    protected $routes = array(
        ':index' => 'index',
        'page:digit' => 'index',
    );
    protected $hooks = array(
        'post.save' => 'hookSavePost',
        'post.insert' => 'hookSavePost',
        'form.init.post' => 'hookPostForm',
        'post.title' => 'hookPostTitle',
    );
    protected $access = array(
        'promote' => array(1),
    );

    /**
     * Hook post title
     *
     * @param object $title
     */
    public function hookPostTitle($title) {
        $post = $title->object();
        if (access('Front.promote') && !$title->object()->preview) {
            $title->append('<a class="post-promote' . ($post->front ? ' promoted' : '') . ' sh" data-id="' . $post->id . '" href="/front/promote/' . $post->id . '"><i class="icon-ok"></i></a>');
        }
    }

    /**
     * Hook / Save post
     *
     * @param array $data
     */
    public function hookSavePost($Post) {
        if ($Post->front && !$Post->front_time) {
            $Post->front_time = time();
        }
        // Remove cache to reset counter on front page
        $this->cache->remove('front.counters');
    }

    /**
     * Hook / Overload Post.form
     *
     * @param object $Form
     */
    public function hookPostForm($Form) {
        if (access('Front.promote')) {
            $Form->addElement('front', array(
                'type' => 'checkbox',
                'access' => access('Blog.front'),
                'text' => t('Promote to front page'),
                'value' => 0,
                'order' => 3.5,
            ));
        }
    }

    /**
     * Default dispatcher
     *
     * @param string $action
     * @param string $subaction
     */
    public function index_action($action = 'all', $filter = 'best') {
        if (!$counters = $this->cache->get('front.counters')) {
            $counters = Core_ArrayObject::transform(array(
                        'all' => array(
                            'all' => post()->where('published')->count(TRUE),
                            'best' => post()->where('posts.rating', config('Front.best', 1), ' > ')->where('posts.front')->where('published')->count(TRUE),
                            'new' => post()->where('posts.created_date', time() - config('Front.new', 86400), ' > ')->where('published')->count(TRUE),
                        ),
                        'blogs' => array(
                            'all' => post()->join('blogs', 'blogs.id = posts.bid AND blogs.type = ' . Blog::$types['public'])->where('published')->count(TRUE),
                            'best' => post()->join('blogs', 'blogs.id = posts.bid AND blogs.type = ' . Blog::$types['public'])->where('posts.rating', config('Front.best', 1), ' > ')->where('posts.front')->where('published')->count(TRUE),
                            'new' => post()->join('blogs', 'blogs.id = posts.bid AND blogs.type = ' . Blog::$types['public'])->where('posts.created_date', time() - config('Front.new', 86400), ' > ')->where('published')->count(TRUE),
                        ),
                        'users' => array(
                            'all' => post()->join('blogs', 'blogs.id = posts.bid AND blogs.type = ' . Blog::$types['personal'])->where('published')->count(TRUE),
                            'best' => post()->join('blogs', 'blogs.id = posts.bid AND blogs.type = ' . Blog::$types['personal'])->where('posts.rating', config('Front.best', 1), ' > ')->where('posts.front')->where('published')->count(TRUE),
                            'new' => post()->join('blogs', 'blogs.id = posts.bid AND blogs.type = ' . Blog::$types['personal'])->where('posts.created_date', time() - config('Front.new', 86400), ' > ')->where('published')->count(TRUE),
                        ),
                    ));
            $this->cache->set('front.counters', $counters);
        }
        new Menu_Tabs(array(
                    'name' => 'front',
                    'multiple' => TRUE,
                    'title' => TRUE,
                    'elements' => array(
                        array(
                            'label' => t('All') . '<sup>' . $counters->all->all . '</sup>',
                            'link' => l('/front/all/' . $filter),
                            'active' => $action == 'all',
                        ),
                        array(
                            'label' => t('Blogs') . '<sup>' . $counters->blogs->all . '</sup>',
                            'link' => l('/front/blogs/' . $filter),
                        ),
                        array(
                            'label' => t('Users') . '<sup>' . $counters->users->all . '</sup>',
                            'link' => l('/front/users/' . $filter),
                        ),
                        array(
                            'label' => t('All'), //.($counters->$action->all ? '<sup>+'.$counters->$action->all.'</sup>' : ''),
                            'link' => l('/front/' . $action . '/all/'),
                            'class' => 'fl_r',
                            'active' => $filter == 'all',
                        ),
                        array(
                            'label' => t('New') . ($counters->$action->new > 0 ? '<sup>+' . $counters->$action->new . '</sup>' : ''),
                            'link' => l('/front/' . $action . '/new/'),
                            'class' => 'fl_r',
                            'active' => $filter == 'new',
                        ),
                        array(
                            'label' => t('Best') . ($counters->$action->best > 0 ? '<sup>+' . $counters->$action->best . '</sup>' : ''),
                            'link' => l('/front/' . $action . '/best/'),
                            'class' => 'fl_r',
                            'active' => $filter == 'best',
                        ),
                    )
                ));
        $this->db->select('posts.*');
        switch ($action) {
            case 'blogs':
                $this->db->join('blogs', 'blogs.id = posts.bid AND blogs.type = ' . Blog::$types['public']);
                break;
            case 'users':
                $this->db->join('blogs', 'blogs.id = posts.bid AND blogs.type = ' . Blog::$types['personal']);
                break;
            default:
            case 'all':
        }
        switch ($filter) {
            case 'best':
                $this->db->where('posts.rating', config('Front.best', 1), ' > ');
                $this->db->where('posts.front');
                break;
            case 'new':
                $this->db->where('posts.created_date', time() - config('Front.new', 86400), ' > ');
                break;
        }
        Db_ORM::skipClear();
        $posts = new Post_List(array(
                    'name' => 'front',
                    'base' => '/',
                    'per_page' => config('Front.per_page', 5),
                    'where' => array(
                        'published' => 1,
                    ),
                    'order' => array('front_time', 'DESC'),
                    'render' => FALSE,
                ));
        $posts->show();
    }

    /**
     * Custom dispatcher
     *
     * @param   string  $subaction
     */
    public function promote_action($post_id) {
        if (!$post = post($post_id)) {
            return event('404');
        }
        if ($post->front) {
            $data['action'] = 'unpromote';
            $post->front = 0;
            $message = t('Post has been removed from front page!', 'Front');
        } else {
            $data['action'] = 'promote';
            $post->front = 1;
            $message = t('Post has been promoted to front page!', 'Front');
            if (!$post->front_time) {
                $post->front_time = time();
            }
        }
        if ($post->save()) {
            if (Ajax::is()) {
                $data['messages'] = array(array(
                        'type' => 'success',
                        'body' => $message,
                        ));
                $ajax = new Ajax();
                $ajax->json($data);
            } else {
                flash_success($message);
                redirect(l('/'));
            }
        }
    }

}