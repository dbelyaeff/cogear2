<?php

/**
 * List of posts
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage

 */
class Post_List extends Db_List_Abstract {

    protected $class = 'Post';
    protected $options = array(
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