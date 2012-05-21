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
    public function getLink($type = 'default') {
        switch ($type) {
            case 'edit':
                $uri = new Stack(array('name' => 'post.link.edit'));
                $uri->append('post');
                $uri->append('edit');
                break;
            case 'delete':
                $uri = new Stack(array('name' => 'post.link.edit'));
                $uri->append('post');
                $uri->append('delete');
                break;
            case 'hide':
                $uri = new Stack(array('name' => 'post.link.hide'));
                $uri->append('post');
                $uri->append('hide');
                break;
            default:
                $uri = new Stack(array('name' => 'post.link'));
                $uri->append('post');
        }
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
        $data['ip']= cogear()->session->get('ip');
        $data['last_update'] = time();
        $data['aid'] = cogear()->user->id;
        if ($result = parent::insert($data)) {
            event('post.insert', $data);
            cogear()->user->recalculate('posts');
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
        isset($data['body']) && $data['last_update'] = time();
        if ($result = parent::update($data)) {
            event('post.update', $data);
            cogear()->user->recalculate('posts');
        }
        return $result;
    }

    /**
     * Delete post
     */
    public function delete() {
        $uid = $this->aid;
        if ($result = parent::delete()) {
            event('post.delete', array(), $uid);
            cogear()->user->recalculate('posts');
        }
        return $result;
    }

    /**
     * Render post
     */
    public function render($template = NULL) {
        event('post.render', $this);
        if (!$this->teaser) {
            $this->views++;
            $this->update(array('views' => $this->views));
        }
        return parent::render($template);
    }

    /**
     * Recalculate params
     *
     * @param type $type
     */
    public function recalculate($type) {
        event('post.recalculate', $this, $type);
    }

}