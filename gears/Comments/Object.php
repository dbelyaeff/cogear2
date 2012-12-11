<?php

/**
 * Comments object.
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Comments
 * @subpackage
 */
class Comments_Object extends Db_Tree {

    protected $table = 'comments';
    protected $primary = 'id';
    protected $template = 'Comments/templates/comment';
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
        return l('/' . $uri->render('/'));
    }

    /**
     * Create new blog
     *
     * @param type $data
     */
    public function insert($data = NULL) {
        $data OR $data = $this->getData();
        isset($data['created_date']) OR $data['created_date'] = time();
        isset($data['aid']) OR $data['aid'] = cogear()->user->id;
        isset($data['ip']) OR $data['ip'] = session('ip');
        if ($result = parent::insert($data)) {
            event('comment.insert', $this, $data, $result);
        }
        return $result;
    }

    /**
     * Update blog
     *
     * @param type $data
     */
    public function update($data = NULL) {
        $data OR $data = $this->getData();
        isset($data['body']) && $data['last_update'] = time();
        isset($data['ip']) OR $data['ip'] = session('ip');
        if ($result = parent::update($data)) {
            event('comment.update', $this, $data, $result);
        }
        return $result;
    }

    /**
     * Delete blog
     */
    public function delete() {
        $uid = $this->aid;
        if ($result = parent::delete()) {
            event('comment.delete', $this);
        }
        return $result;
    }

    /**
     * Render comment
     *
     * @param type $type
     * @return type
     */
    public function render($type = 'full') {
        switch ($type) {
            case 'widget':
                $comment = new Stack(array('name' => 'comment.widget'));
                if ($author = user($this->aid)) {
                    $comment->append($author->getLink('avatar', 'avatar.tiny'));
                    $comment->append($author->getLink('profile'));
                    $comment->append(' &rarr; ');
                    $post = post($this->post_id);
                    $blog = blog($post->bid);
                    $comment->append($blog->getLink('profile'));
                    $comment->append(' / ');
                    $comment->append($post->getLink('full', '#comment-' . $this->id));
                    $comment->append('<a class="comments-counter" href="' . $post->getLink() . '#comment-' . $this->id . '">' . $post->comments . '</a>');
                }
                return '<div class="comment-widget">' . $comment->render() . '</div>';
                break;
            default :
                return parent::render();
        }
    }

}