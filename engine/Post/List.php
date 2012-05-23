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
class Post_List extends Options {

    public $options = array(
        'page' => 0,
        'per_page' => 5,
        'base' => '',
        'page_suffix' => 'page',
        'where' => array(
        ),
        'order' => array('posts.created_date', 'DESC'),
        'render' => 'content',
    );

    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct($options = array()) {
        parent::__construct($options);
        $this->render && hook($this->render, array($this, 'show'));
    }

    /**
     * Render list of posts
     */
    public function render() {
        $post = new Post();
        $this->where && cogear()->db->where($this->where->toArray());
        $this->order && cogear()->db->order($this->order[0], $this->order[1]);
        $pager = new Pager(array(
                    'current' => $this->page ? $this->page : NULL,
                    'count' => $post->count(),
                    'per_page' => $this->per_page,
                    'base' => $this->base,
                ));
        if ($posts = $post->findAll()) {
            $output = new Core_ArrayObject();
            foreach ($posts as $post) {
                $post->teaser = TRUE;
                $output->append($post->render());
            }
            $output->append($pager->render());
            return $output->toString();
        } else {
            event('empty');
            return FALSE;
        }
    }

}