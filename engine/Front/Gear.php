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
        'Db_ORM.save' => 'hookSavePost',
        'Form.init.post' => 'hookPostForm',
    );

    /**
     * Hook / Save post
     * 
     * @param array $data 
     */
    public function hookSavePost($ORM) {
        if ($ORM->front && !$ORM->front_time) {
            $ORM->front_time = time();
        }
    }

    /**
     * Hook / Overload Post.form
     * 
     * @param object $Form 
     */
    public function hookPostForm($Form) {
        $Form->options->elements->title->elements->front = array(
            'type' => 'checkbox',
            'access' => access('Blog.front'),
            'text' => t('Promote to front page'),
            'order' => 3.5,
        );
    }

    /**
     * Default dispatcher
     * 
     * @param string $action
     * @param string $subaction 
     */
    public function index($page = 'page1') {
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
    public function action_index($subaction = NULL) {
        
    }

}