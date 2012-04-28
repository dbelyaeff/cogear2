<?php

/**
 * Comments object.
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Comments
 * @subpackage
 */
class Comments_Object extends Db_Tree {

    protected $table = 'comments';
    protected $primary = 'id';
    protected $template = 'Comments.comment';
    protected $fields_select = TRUE;

    /**
     * Get blog Uri
     *
     * @return string
     */
    public function getLink() {
        $uri = new Stack(array('name' => 'comments.link'));
        $uri->append('comment');
        $uri->append($this->id);
        return '/' . $uri->render('/');
    }

    /**
     * Get blog Uri
     *
     * @return string
     */
    public function getEditLink() {
        $uri = new Stack(array('name' => 'comment.edit.link'));
        $uri->append('comments');
        $uri->append('edit');
        $uri->append($this->id);
        return '/' . $uri->render('/');
    }

    /**
     * Create new blog
     *
     * @param type $data
     */
    public function insert($data = NULL) {
        $data OR $data = $this->object->toArray();
        $data['created_date'] = time();
        $this->aid OR $data['aid'] = cogear()->user->id;
        $data['ip'] = cogear('session')->get('ip');
        if ($result = parent::insert($data)) {
            event('comments.insert', $this);
            $user = new User();
            $user->id = $data['aid'];
            if ($user->find()) {
                $user->comments++;
                $user->save();
            }
        }
        return $result;
    }

    /**
     * Update blog
     *
     * @param type $data
     */
    public function update($data = NULL) {
        $data OR $data = $this->object->toArray();
        isset($data['body']) && $data['last_update'] = time();
        if ($result = parent::update($data)) {
            event('comments.update', $this, $data);
            if ($this->published != $data['published']) {
                $user = new User();
                $user->id = $this->aid;
                if ($user->find()) {
                    $this->published ? $user->comment++ : $user->comments--;
                    $user->save();
                }
            }
        }
        return $result;
    }

    /**
     * Delete blog
     */
    public function delete() {
        $uid = $this->aid;
        if ($result = parent::delete()) {
            $user = new User();
            $user->id = $this->aid;
            if ($user->find()) {
                $user->comments--;
                $user->save();
            }
        }
        return $result;
    }

}