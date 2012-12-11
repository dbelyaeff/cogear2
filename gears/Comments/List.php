<?php

/**
 * List of comments
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage

 */
class Comments_List extends Db_List_Abstract {

    protected $class = 'Comments_Object';
    public $options = array(
        'page' => 0,
        'per_page' => 50,
        'base' => '',
        'page_suffix' => 'page',
        'where' => array(
        ),
        'like' => array(),
        'render' => 'content',
        'pager' => TRUE,
        'flat' => FALSE,
    );

    /**
     * Конструктор
     *
     * @param array $options
     */
    public function __construct($options = array()) {
        parent::__construct($options);
        $this->options->base OR $this->options->base = '/' . cogear()->router->getUri();
        if ($this->flat) {
            $this->db->order('comments.id', 'DESC');
            Db_ORM::skipClear();
        }
    }

    /**
     * Process render
     *
     * @param type $comments
     * @param type $pager
     * @return type
     */
    public function process($comments, $pager) {
        $output = new Core_ArrayObject();
        if ($this->flat) {
            $ids = array();
            foreach ($comments as $comment) {
                if (!in_array($comment->post_id, $ids)) {
                    $ids[] = $comment->post_id;
                }
            }
            $post = new Post();
            $this->db->where_in('posts.id', $ids);
            if ($presult = $post->findAll()) {
                $posts = array();
                foreach ($presult as $post) {
                    $posts[$post->id] = $post;
                }
            }
        }
        foreach ($comments as &$comment) {
            if ($this->object && $this->object()->aid == $comment->aid) {
                $comment->by_post_author = TRUE;
            }
            $comment->flat = $this->flat;
            if ($this->flat) {
                $comment->post = $posts[$comment->post_id];
            }
            $output->append($comment->render());
        }
        event('comments.list', $this,$comments);
        $this->options->pager && $output->append($pager->render());
        return $output->toString();
    }

}