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
class Post_Object extends Db_Item {

    protected $template = 'Post.post';

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
    public function getLink() {
        $uri = new Stack(array('name' => 'post.link'));
        $uri->append('post');
        $uri->append($this->id);
        return '/' . $uri->render('/');
    }

    /**
     * Get post Uri
     * 
     * @return string
     */
    public function getEditLink() {
        $uri = new Stack(array('name' => 'post.edit.link'));
        $uri->append('post');
        $uri->append('edit');
        $uri->append($this->id);
        return '/' . $uri->render('/');
    }

    /**
     * Create new post
     * 
     * @param type $data 
     */
    public function insert($data = NULL) {
        $data OR $data = $this->object->toArray();
        $data['created_date'] = time();
        $data['last_update'] = time();
        $data['aid'] = cogear()->user->id;
        if ($result = parent::insert($data)) {
            cogear()->post->recalculateUserPostCount();
        }
        return $result;
    }

    /**
     * Update post
     * 
     * @param type $data 
     */
    public function update($data = NULL) {
        $data OR $data = $this->object->toArray();
        $data['last_update'] = time();
        if ($result = parent::update($data)) {
            cogear()->post->recalculateUserPostCount();
        }
        return $result;
    }

    /**
     * Delete post
     */
    public function delete() {
        $uid = $this->aid;
        if ($result = parent::delete()) {
            cogear()->post->recalculateUserPostCount($uid);
        }
        return $result;
    }

    /**
     * Render post
     */
    public function render($template = NULL) {
        event('post.render', $this);
        return parent::render($template);
    }

}