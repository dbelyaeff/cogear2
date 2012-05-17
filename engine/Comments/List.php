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
        'post_author_id' => 0,
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
        $comments = new Comment();
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
        if ($result = $comments->findAll(TRUE)) {
            $output = new Core_ArrayObject();
            foreach ($result as $comment) {
                $comment->post_author_id = $this->options->post_author_id;
                $output->append($comment->render());
            }
            $this->options->pager && $output->append($pager->render());
            return $output->toString();
        } else {
            $this->render && event('empty');
        }
    }

}