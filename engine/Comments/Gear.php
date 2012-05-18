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
        'user.recalculate' => 'hookUserRecalculate',
        'post.recalculate' => 'hookPostRecalculate',
        'comment.render' => 'hookFormatComment',
        'post.info' => 'hookRenderPostCommentsCount',
    );

    /**
     * Add comments count to post
     *
     * @param type $info
     */
    public function hookRenderPostCommentsCount($info) {
        $post = $info->object;
        if ($post->allow_comments) {
            $info->comments = icon('comment') . ' <a class="post-comments scrollTo" data-id="' . $post->id . '" href="' . $post->getLink() . '#comments">' . $post->comments . '</a>';
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
            $after->append('<div class="comments-handler" data-type="post" data-id="' . $after->object->id . '"></div>');
        }
    }

    /**
     * Recalcuate user comments
     *
     * @param type $User
     * @param type $type
     */
    public function hookUserRecalculate($User, $type) {
        if ($type == 'comments') {
            $User->comments = $this->db->select('*')->where(array('aid' => $User->id, 'published' => 1))->count('comments', 'id', TRUE);
            $User->update();
            $User->id == $this->user->id && $this->user->store();
        }
    }

    /**
     * Recalcuate post comments
     *
     * @param type $Post
     * @param type $type
     */
    public function hookPostRecalculate($Post, $type) {
        if ($type == 'comments') {
            $Post->comments = $this->db->where(array('post_id' => $Post->id, 'published' => 1))->count('comments', 'id', TRUE);
            $Post->update();
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
        if(!$post->find()){
            return event('404');
        }
        $where = array('post_id' => $post->id);
        if(!access('comments.hidden')){
            $where['published'] = 1;
        }
        $comments = new Comments_List(array(
                    'name' => 'comments.' . $type,
                    'where' => $where,
                    'render' => FALSE,
                    'pager' => FALSE,
                    'post_author_id' => $post->aid,
                ));
        $handler = new Stack(array('name' => 'comments'));
        $handler->append('<div id="comments">');
        $handler->append('<div class="page-header" id="comment-form-placer"><h2>' . t('Comments', 'Comments') . '</h2></div>');
        $form = $this->post($id, 0, FALSE);
        $handler->append($form->render());
        $handler->append($comments->render());
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
                $parent = new Comment();
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
                $comment = new Comment();
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
                $form->elements->offsetUnset('title');
            }
            if ($render) {
                $form->show();
            } else {
                $form->elements->offsetUnset('title');
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
        $comment = new Comment();
        $comment->id = $cid;
        if ($comment->find() && !$comment->frozen) {
            $this->post($comment->post_id, $comment->id);
        } else {
            return event('403');
        }
    }

    /**
     * Edit comment
     *
     * @param int $comment_id
     * @return
     */
    public function edit_action($comment_id = 0) {
        $comment = new Comment();
        $comment->id = $comment_id;
        if ($comment->find()) {
            if (!access('comments.edit.all') OR !(access('comments.edit') && $comment->aid == $this->user->id)) {
                return event('403');
            }
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
        $comment = new Comment();
        $comment->id = $cid;
        if ($comment->find() && access('comments.hide.all')) {
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
        $comment = new Comment();
        $comment->id = $cid;
        if ($comment->find() && access('comments.delete.all')) {
            if ($comment->delete()) {
                if ($childs = $comment->getChilds()) {
                    foreach ($childs as $child) {
                        $child->published = $comment->published;
                        $child->delete();
                    }
                }
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

}