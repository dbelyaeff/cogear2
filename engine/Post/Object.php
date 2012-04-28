<?php

/**
 * Post.
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Post
 * @subpackage
 */
class Post_Object extends Db_Item {
    protected $table = 'posts';
    protected $primary = 'id';
    protected $template = 'Post.post';
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
        $this->ip = cogear('session')->get('ip');
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
        $data['ip'] = cogear('session')->get('ip');
        isset($data['body']) && $data['last_update'] = time();
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
        if(!$this->teaser){
            $this->views++;
            $this->update(array('views'=>$this->views));
        }
        return parent::render($template);
    }

    /**
     * Recalculate params
     *
     * @param type $type
     */
    public function recalculate($type){
        switch ($type){
            case 'comments':
                $this->comments = cogear()->db->where('pid',$this->id)->count('comments_posts',TRUE);
                break;
        }
        $this->update();
    }
}