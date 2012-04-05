<?php

/**
 * Blog post.
 * 
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Blog
 * @subpackage          
 */
class Blog_Post extends Db_Item {

    protected $template = 'Blog.post';

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct('posts', 'id');
    }

    /**
     * Get post Uri
     * 
     * @return string
     */
    public function getUri() {
        $uri = new Stack(array('name'=>'blog.post.uri'));
        $uri->append('post');
        $uri->append($this->id);
        return '/' . $uri->render('/');
    }
    
    /**
     * Render post
     */
    public function render($template = NULL){
        event('post.render',$this);
        return parent::render($template);
    }
}