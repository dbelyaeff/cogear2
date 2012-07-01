<?php

/**
 * List of posts
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Post_List extends Db_List_Abstract {

    protected $class = 'Post';
    public $options = array(
        'page' => 0,
        'per_page' => 5,
        'base' => '',
        'page_suffix' => 'page',
        'where' => array(
        ),
        'like' => array(),
        'where_in' => array(),
        'order' => array('posts.created_date', 'DESC'),
        'render'=>'content',
    );
    /**
     * Process posts render
     *
     * @param type $posts
     * @return type
     */
    public function process($posts,$pager) {
        $output = new Core_ArrayObject();
        foreach ($posts as $post) {
            $post->teaser = TRUE;
            $output->append($post->render());
        }
        event('post.list', $posts);
        $output->append($pager->render());
        return $output->toString();
    }

}