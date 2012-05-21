<?php

/**
 * Comments gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Comments_Gear extends Gear {

    protected $name = 'Comments';
    protected $description = 'Comments description';
    protected $order = 15;
    protected $hooks = array(
        'post.show.full.after' => 'hookPostComments',
        'form.init.post' => 'hookPostForm',
        'comment.render' => 'hookFormatComment',
        'post.info' => 'hookRenderPostCommentsCount',
        'comments.list' => 'hookUpdateCommentsViews',
    );
    protected $access = array(
        'post' => 'access',
        'reply' => 'access',
        'edit' => 'access',
        'delete' => 'access',
        'update' => array(100),
        'hide' => array(1),
        'ip' => array(1),
        'load' => array(0, 1, 100),
    );

    /**
     * Access function
     *
     * @param type $rule
     * @param type $Comment
     */
    public function access($rule, $Comment = NULL) {
        switch ($rule) {
            case 'reply':
                if (role()) {
                    if (!$item->fronzen OR $item->level < config('comments.max_level', 2)) {
                        return TRUE;
                    }
                }
                break;
            case 'post':
                if (role()) {
                    return TRUE;
                }
                break;
            case 'edit':
                if (is_numeric($Comment)) {
                    if (!$Comment = comment($Comment)) {
                        return FALSE;
                    }
                }
                // User can edit comment only if its been posted x seconds ago
                if (time() - $Comment->created_date > config('Comments.edit.timer', 180) && $Comment->aid == $this->user->id) {
                    return TRUE;
                }
                break;
            case 'delete':

                break;
        }
        return FALSE;
    }

    /**
     * Add comments count to post
     *
     * @param type $info
     */
    public function hookRenderPostCommentsCount($info) {
        $post = $info->object;
        if ($post->allow_comments) {
            $info->comments = icon('comment') . ' <a class="post-comments scrollTo" data-id="' . $post->id . '" href="' . $post->getLink() . '#comments">' . $post->comments . '</a>';
            $new = '';
            if ($this->session->comments_views && isset($this->session->comments_views[$post->id])) {
                $views = $this->session->comments_views[$post->id];
                $anchor = 'comment-' . $views['last'];
                if ($post->comments > $views['count']) {
                    $new = '+' . ($post->comments - $views['count']);
                }
            } else {
                $anchor = 'comments';
            }
            $info->comments_new = ' <a class="comments-new scrollTo" data-id="' . $post->id . '" href="' . $post->getLink() . '#' . $anchor . '">' . $new . '</a>';
        }
    }

    /**
     * Hook comment format
     *
     * @param type $Comment
     */
    public function hookFormatComment($Comment) {
        $Comment->body = nl2br($Comment->body);
    }

    /**
     *
     */
    public function hookPostComments($after) {
        if ($after->object->allow_comments) {
            $after->append('<div class="page-header" id="comment-form-placer"><h2>' . t('Comments', 'Comments') . '</h2></div>');
            $after->append('<div class="comments-handler" data-type="post" data-id="' . $after->object->id . '"><p class="well t_c">' . t('Loading…', 'Ajax') . '</p></div>');
        }
    }


    /**
     * Extend post form
     *
     * @param type $Form
     */
    public function hookPostForm($Form) {
        $Form->addElement('allow_comments', array(
            'type' => 'checkbox',
            'text' => t('Allow comments', 'Comments'),
            'value' => 1,
            'order' => 3.4,
        ));
    }

    /**
     * Remembers last view comments for post
     *
     * @param objcet $List
     * @param array $result
     */
    public function hookUpdateCommentsViews($List, $result) {
        // If user is guest or there is no post
        if (!role() OR empty($List->where['post_id'])) {
            return;
        }
        if ($this->session->comments_views && $this->session->comments_views[$List->where['post_id']]) {
            $views = $this->session->comments_views[$List->where['post_id']];
            foreach ($result as $item) {
                if ($item->id > $views['last']) {
                    $item->class = $item->class ? $item->class . ' new' : 'new';
                }
            }
        }
        $views = new Comments_Views();
        $views->pid = $List->where['post_id'];
        $views->uid = $this->user->id;
        $views->delete();
        $last_id = 0;
        foreach ($result as $comment) {
            if ($comment->id > $last_id) {
                $last_id = $comment->id;
            }
        }
        $views->cid = $last_id;
        $views->cn = sizeof($result);
        $views->save();
    }

    /**
     * Hook menu
     *
     * @param string $name
     * @param object $menu
     */
    public function menu($name, $menu) {
        switch ($name) {
            case 'user.profile.tabs':
                $menu->register(array(
                    'label' => t('Comments', 'Comments') . ' <sup>' . $menu->object->comments . '</sup>',
                    'link' => $menu->object->getLink() . '/comments/',
                    'order' => 2.2,
                ));
                break;
        }
    }

    /**
     * Init
     */
    public function init() {
        // Force prepend route
        route('user/([^/]+)/comments:maybe', array($this, 'index_action'), TRUE);
        parent::init();
        // Only for authorized users
        if (!role()) {
            return;
        }
        // Get comments views
        if ($this->session->get('comments_views') === NULL) {
            $views = new Comments_Views();
            $views->uid = $this->user->id;
            if ($result = $views->findAll()) {
                $comments_views = array();
                foreach ($result as $view) {
                    $comments_views[$view->pid] = array(
                        'last' => $view->cid,
                        'count' => $view->cn,
                    );
                }
                $this->session->set('comments_views', $comments_views);
            } else {
                $this->session->set('comments_views', FALSE);
            }
        }
    }

    /**
     * Main dispatcher
     */
    public function index_action($login = NULL, $page = NULL) {
        $comments = new Comments();
        $where = array();
        if ($login && $user = user($login, 'login')) {
            $where['aid'] = $user->id;
            $user->navbar()->show();
        }
        $comments = new Comments_List(array(
                    'name' => 'comments.list',
                    'where' => $where,
                    'flat' => TRUE,
                ));
    }

    /**
     * Load comments
     *
     * @param string $type
     * @param int $id
     */
    public function load_action($type = NULL, $id = NULL) {
        if (!$type OR !in_array($type, array('post', 'page')) OR !$id) {
            return event('403');
        }
        $post = new Post();
        $post->id = $id;
        if (!$post->find()) {
            return event('404');
        }
        $where = array('post_id' => $post->id);
        if (!access('Comments.hide')) {
            $where['published'] = 1;
        }
        $comments = new Comments_List(array(
                    'name' => 'comments.' . $type,
                    'where' => $where,
                    'render' => FALSE,
                    'pager' => FALSE,
                ));
        $comments->attach($post);
        $handler = new Stack(array('name' => 'comments'));
        $handler->append('<div id="comments">');

        $form = $this->post($id, 0, FALSE);
        $handler->append($comments->render());
        if (access('Comments.post')) {
            $handler->append('<div class="comments-form-holder">');
            $handler->append('<div class="page-header"><h3> ' . t('Post comment') . '</h3></div>');
            $handler->append($form->render());
            $handler->append('</div>');
        }
        $handler->append('</div>');
        $handler->show();
    }

    /**
     * Post comment action
     */
    public function post_action($post_id = 0) {
        $this->post($post_id);
    }

    /**
     * Secure post controller
     *
     * @param type $post_id
     * @param type $pid
     * @return type
     */
    protected function post($post_id, $pid = 0, $render = TRUE) {
        $form = new Form('Comments.form');
        $post = new Post();
        $post->id = $post_id;
        $form->options->action .= $post->id;
        if ($post->find()) {
            if (!$post->allow_comments) {
                return error(t('Comments are disabled for this post.', 'Comment'));
            }
            $form->init();
            $pid = $this->input->post('pid', $pid);
            if ($pid) {
                $form->pid->setValue($pid);
                $parent = new Comments();
                $parent->id = $pid;
                if ($parent->find()) {
                    $form->options->action = l('/comments/reply/' . $pid . '/');
                    $user = new User();
                    $user->id = $parent->aid;
                    if ($user->find()) {
                        $form->title->options->label = t('Reply to %s comment', 'Comments', $user->getLink('avatar') . ' ' . $user->getLink('profile')) . ' ' . t('to <a class="modal-close" href="%s">%s</a>', 'Comment', $post->getLink() . '#comment-' . $parent->id, $post->name);
                    }
                }
            } else {
                $form->title->options->label .= ' ' . t('to <a href="%s">%s</a>', 'Comment', $post->getLink(), $post->name);
            }
            if ($result = $form->result()) {
                $comment = new Comments();
                $comment->attach($result);
                $comment->post_id = $post->id;
                $comment->aid = $this->user->id;
                $comment->created_date = time();
                $comment->last_update = time();
                if ($result->preview) {
                    append('info', '<div class="page-header"><h2>' . t('Preview') . '</h2></div>');
                    $comment->id = 'preview';
                    $comment->preview = TRUE;
                    if ($form->ajaxed) {
                        $ajax = new Ajax();
                        $data['success'] = TRUE;
                        $data['action'] = 'preview';
                        $data['code'] = $comment->render();
                        $ajax->json($data);
                    } else {
                        $comment->show();
                    }
                } else {
                    $comment->pid = $pid ? $pid : 0;
                    $comment->published = 1;
                    if ($comment->save()) {
                        event('comment.published', $comment, $post, $pid ? $parent : NULL, $pid ? $user : NULL);
                        $post->recalculate('comments');
                        if ($form->ajaxed) {
                            $ajax = new Ajax();
                            $comment->class = 'new';
                            $post = new Post();
                            $post->id = $comment->post_id;
                            $post->find();
                            $ajax->json(array(
                                'success' => TRUE,
                                'messages' => array(array(
                                        'type' => 'success',
                                        'body' => t('Your comment has been posted!'),
                                )),
                                'action' => 'publish',
                                'pid' => $comment->pid,
                                'code' => $comment->render(),
                                'post_id' => $post->id,
                                'counter' => $post->comments,
                            ));
                        } else {
                            flash_success(t('Your comment has been posted!'), '', 'growl');
                            redirect($post->getLink() . '#comment-' . $comment->id);
                        }
                    }
                }
            }
            $form->elements->offsetUnset('delete');
            if (Ajax::is()) {
                $form->title && $form->elements->offsetUnset('title');
            }
            if ($render) {
                $form->show();
            } else {
                $form->title && $form->elements->offsetUnset('title');
                return $form;
            }
        } else {
            return event('403');
        }
    }

    /**
     * Replu to comment
     *
     * @param type $cid
     */
    public function reply_action($cid = 0) {
        $comment = new Comments();
        $comment->id = $cid;
        if ($comment->find() && !$comment->frozen) {
            $this->post($comment->post_id, $comment->id);
        } else {
            return event('404');
        }
    }

    /**
     * Edit comment
     *
     * @param int $comment_id
     * @return
     */
    public function edit_action($comment_id = 0) {
        $comment = new Comments();
        $comment->id = $comment_id;
        if ($comment->find()) {
            $post = new Post();
            $post->id = $comment->post_id;
            $user = new User();
            $user->id = $comment->aid;
            $user->find();
            $form = new Form('Comments.form');
            $form->options->action = l('/comments/edit/' . $comment->id);
            $form->attach($comment);
            if ($post->find()) {
                $form->title->options->label = t('Edit %s comment', 'Comments', $user->getLink('avatar') . ' ' . $user->getLink('profile')) . ' ' . t('to <a href="%s">%s</a>', 'Comment', $post->getLink(), $post->name);
            }
            $form->publish->options->label = t('Update');
            $form->init();
            if ($result = $form->result()) {
                if ($result->preview) {
                    append('info', '<div class="page-header"><h2>' . t('Preview') . '</h2></div>');
                    $comment->mix($result);
                    $comment->id = 'preview';
                    $comment->aid = $this->user->id;
                    $comment->created_date = time();
                    $comment->preview = TRUE;
                    if ($form->ajaxed) {
                        $ajax = new Ajax();
                        $data['success'] = TRUE;
                        $data['action'] = 'preview';
                        event('comment.render', $comment);
                        $data['code'] = $comment->body;
                        $ajax->json($data);
                    } else {
                        $comment->show();
                    }
                } elseif ($result->delete) {
                    if ($comment->delete()) {
                        flash_error(t('Your comment has been deleted!'), '', 'growl');
                        redirect($post->getLink() . '#comments');
                    }
                } else {
                    $comment->object->mix($result);
                    if ($comment->save()) {
                        if ($form->ajaxed) {
                            event('comment.render', $comment);
                            $ajax = new Ajax();
                            $ajax->json(array(
                                'success' => TRUE,
                                'messages' => array(array(
                                        'type' => 'success',
                                        'body' => t('Your comment has been updated!'),
                                )),
                                'action' => 'update',
                                'code' => $comment->body,
                            ));
                        } else {
                            flash_success(t('Your comment has been updated!'), '', 'growl');
                            redirect($post->getLink() . '#comment-' . $comment->id);
                        }
                    }
                }
            }
            if (Ajax::is()) {
                $form->elements->offsetUnset('title');
                $form->elements->offsetUnset('delete');
            }
            $form->show();
        } else {
            return event('404');
        }
    }

    /**
     * Hide or show comment
     *
     * @param type $cid
     */
    public function hide_action($cid) {
        $comment = new Comments();
        $comment->id = $cid;
        if ($comment->find() && access('Comments.hide.all')) {
            $data = array();
            if ($comment->published) {
                $comment->published = 0;
                $data['action'] = 'hide';
            } else {
                $comment->published = 1;
                $data['action'] = 'show';
            }
            if ($comment->save()) {
                if ($childs = $comment->getChilds()) {
                    foreach ($childs as $child) {
                        $child->published = $comment->published;
                        $child->save();
                    }
                }
                $data['success'] = TRUE;
            }
            $post = new Post();
            $post->id = $comment->post_id;
            $post->find();
            $data['post_id'] = $post->id;
            $data['counter'] = $post->comments;
            if (Ajax::is()) {
                $ajax = new Ajax();
                $ajax->json($data);
            } else {
                $post = new Post();
                $post->id = $comment->post_id;
                $post->find();
                redirect($post->getLink() . '#comment-' . $comment->id);
            }
        }
    }

    /**
     * Delete comment
     *
     * @param type $cid
     */
    public function delete_action($cid) {
        $comment = new Comments();
        $comment->id = $cid;
        if ($comment->find() && access('Comments.delete', $comment)) {
            if ($comment->delete()) {
                $message = t('Comments has been deleted', 'Comments');
                if (Ajax::is()) {
                    $data['success'] = TRUE;
                    $data['messages'] = array(
                        array(
                            'type' => 'success',
                            'body' => $message,
                        )
                    );
                    $post = new Post();
                    $post->id = $comment->post_id;
                    $post->find();
                    $data['counter'] = $post->comments;
                    $data['post_id'] = $post->id;
                    $ajax = new Ajax();
                    $ajax->json($data);
                }
                $post = new Post();
                $post->id = $comment->post_id;
                flash_success($message);
                redirect($post->getLink() . '#comments');
            }
        }
    }

    /**
     * Update comments
     *
     * @param string $type
     * @param int $id
     */
    public function update_action($type = NULL, $id = 0) {
        $data = array(
            'comments' => array(),
            'counter' => 0,
            // Just Published by current user — must be highlighted first
            'jp' => 0,
        );
        switch ($type) {
            case 'post':
                $views = new Comments_Views();
                $views->pid = $id;
                $views->uid = $this->user->id;
                $post = post($id);
                $comments = new Comments();
                $comments->post_id = $id;
                if ($views->find()) {
                    $this->db->where('id', $views->cid, ' > ');
                }
                $last_id = 0;
                if ($result = $comments->findAll()) {
                    $data['counter'] = sizeof($result);
                    foreach ($result as $comment) {
                        if ($comment->id > $last_id) {
                            $last_id = $comment->id;
                        }
                        if ($comment->aid == $post->aid) {
                            $comment->by_post_author = TRUE;
                        }
                        $comment->class = 'new';
                        if ($comment->aid == $this->user->id) {
                            $data['jp'] = $comment->id;
                        }
                        $data['comments'][] = array(
                            'id' => $comment->id,
                            'pid' => $comment->pid,
                            'body' => $comment->render(),
                        );
                    }
                    // Update viewed comments
                    $updated_views = new Comments_Views();
                    if ($views->object) {
                        $updated_views->pid = $views->pid;
                        $updated_views->delete();
                    } else {
                        $update_views->pid = $post->id;
                    }
                    $updated_views->uid = $this->user->id;
                    $updated_views->cid = $last_id;
                    $updated_views->cn = $post->comments;
                    $updated_views->save();
                }
                break;
        }
        ajax()->json($data);
    }

}

/**
 * Shortcut for comments
 *
 * @param int $id
 * @param string    $param
 */
function comments($id = NULL, $param = 'id') {
    if ($id) {
        $comments = new Comments();
        $comments->$param = $id;
        if ($comments->findAll()) {
            return $comments;
        }
    }
    return new Comments();
}

/**
 * Shortcut for comment
 *
 * @param int $id
 * @param string    $param
 */
function comment($id = NULL, $param = 'id') {
    if ($id) {
        $comment = new Comments();
        $comment->$param = $id;
        if ($comment->find()) {
            return $comment;
        }
    }
    return new FALSE;
}