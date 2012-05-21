<?php

/**
 * List of comments
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Comments_List extends Object {

    public $options = array(
        'page' => 0,
        'per_page' => 50,
        'base' => '',
        'page_suffix' => 'page',
        'where' => array(
        ),
        'render' => 'content',
        'pager' => TRUE,
        'flat' => FALSE,
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
        $comments = new Comments();
        $this->where && cogear()->db->where($this->where->toArray());
        if ($this->options->pager) {
            $pager = new Pager(array(
                        'current' => $this->page ? $this->page : NULL,
                        'count' => $comments->count(),
                        'per_page' => $this->per_page,
                        'base' => '/' . cogear()->router->getUri(),
                        'prefix' => 'comments-page',
                        'method' => Pager::GET,
                    ));
        }
        $this->flat && $comments->order('comments.id','DESC');
        if ($result = $comments->findAll()) {
            $output = new Core_ArrayObject();
            event('comments.list', $this, $result);
            if ($this->flat) {
                $ids = array();
                foreach($result as $comment){
                    if(!in_array($comment->post_id, $ids)){
                        $ids[] = $comment->post_id;
                    }
                }
                $post = new Post();
                $this->db->where_in('posts.id',$ids);
                if($presult = $post->findAll()){
                    $posts = array();
                    foreach($presult as $post){
                        $posts[$post->id] = $post;
                    }
                }
            }
            foreach ($result as $comment) {
                if ($this->object->aid == $comment->aid) {
                    $comment->by_post_author = TRUE;
                }
                $comment->flat = $this->flat;
                if($this->flat){
                    $comment->post = $posts[$comment->post_id];
                }
                $output->append($comment->render());
            }
            $this->options->pager && $output->append($pager->render());
            return $output->toString();
        } else {
            return FALSE;
        }
    }

}