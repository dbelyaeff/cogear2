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
    public function getLink($type = 'default') {
        switch ($type) {
            case 'edit':
                $uri = new Stack(array('name' => 'comment.link.edit'));
                $uri->append('comments');
                $uri->append('edit');
                break;
            case 'hide':
                $uri = new Stack(array('name' => 'comment.hide.link'));
                $uri->append('comments');
                $uri->append('hide');
                break;
            case 'delete':
                $uri = new Stack(array('name' => 'comment.delete.link'));
                $uri->append('comments');
                $uri->append('delete');
                break;
            default:
                $uri = new Stack(array('name' => 'comments.link'));
                $uri->append('comment');
        }
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
        $data['ip'] = cogear()->session->get('ip');
        if ($result = parent::insert($data)) {
            event('comment.insert', $this,$data,$result);
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
        $data['ip'] = cogear()->session->get('ip');
        if ($result = parent::update($data)) {
            event('comment.update', $this, $data,$result);
        }
        return $result;
    }

    /**
     * Delete blog
     */
    public function delete() {
        $uid = $this->aid;
        if ($result = parent::delete()) {
            event('comment.delete',$this);
        }
        return $result;
    }

}