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
        $post = $title->object;
        if (access('Front.promote') && !$title->object->preview) {
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
    }

    /**
     * Hook / Overload Post.form
     *
     * @param object $Form
     */
    public function hookPostForm($Form) {
        $Form->addElement('front', array(
            'type' => 'checkbox',
            'access' => access('Blog.front'),
            'text' => t('Promote to front page'),
            'value' => 0,
            'order' => 3.5,
        ));
    }

    /**
     * Default dispatcher
     *
     * @param string $action
     * @param string $subaction
     */
    public function index_action($page = 'page1') {
        $post = new Post();
        $post->where('published')->where('front');
        $pager = new Pager(array(
                    'current' => $page ? intval(str_replace('page', '', $page)) : NULL,
                    'count' => $post->count(),
                    'per_page' => config('Front.per_page', 5),
                    'base' => l('/page')
                ));
        $post->order('front_time', 'DESC');
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
     * Custom dispatcher
     *
     * @param   string  $subaction
     */
    public function promote_action($post_id) {
        $post = new Post();
        $post->id = $post_id;
        $post->find();
        if ($post->front) {
            $data['action'] = 'unpromote';
            $post->front = 0;
            $message = t('Post has been removed from front page!', 'Front');
        } else {
            $data['action'] = 'promote';
            $post->front = 1;
            $message = t('Post has been promoted to front page!', 'Front');
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