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
    protected $hooks = array(
        'post.show.full.after' => 'hookPostComments',
        'form.init.post' => 'hookPostForm',
    );

    /**
     * Init
     */
    public function init() {
        parent::init();
    }

    /**
     *
     */
    public function hookPostComments($after) {
        if ($after->object->allow_comments) {
            $after->append('<div class="page-header"><h2>' . t('Comments', 'Comments') . '</h2></div>');
            $comments = new Comments_List(array(
                        'name' => 'comments.post',
                        'join' => array(
                            'table' => 'comments_posts',
                            'on' => array('comments_posts.pid' => $after->object->id, 'comments_posts.cid' => 'comments.id'),
                        ),
                        'render' => FALSE,
                    ));
            $after->append($comments->render());
            if (access('comments.create')) {
                $after->append(template('Comments.add-button', array('post_id' => $after->object->id))->render());
            }
        }
    }

    public function hookPostForm($Form){
        $Form->addElement('allow_comments',array(
            'type' => 'checkbox',
            'text' => t('Allow comments','Comments'),
            'value' => 1,
            'order' => 3.4,
        ));
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
    protected function post($post_id, $pid = 0) {
        $form = new Form('Comments.form');
        $post = new Post();
        $post->id = $post_id;
        $form->options->action .= $post->id;
        if ($post->find()) {
            if (!$post->allow_comments) {
                return error(t('Comments are disabled for this post.', 'Comment'));
            }
            $form->init();
            if ($pid) {
                $parent = new Comment();
                $parent->id = $pid;
                if ($parent->find()) {
                    $form->options->action = l('/comments/reply/' . $pid . '/');
                    $user = new User();
                    $user->id = $parent->aid;
                    if ($user->find()) {
                        $form->title->options->label = t('Reply to %s comment', 'Comments', $user->getAvatarLinked() . ' ' . $user->getProfileLink()) . ' ' . t('to <a class="modal-close" href="%s">%s</a>', 'Comment', $post->getLink() . '#comment-' . $parent->id, $post->name);
                    }
                }
            } else {
                $form->title->options->label .= ' ' . t('to <a href="%s">%s</a>', 'Comment', $post->getLink(), $post->name);
            }
            if ($result = $form->result()) {
                $comment = new Comment();
                $comment->attach($result);
                if ($result->preview) {
                    append('info', '<div class="page-header"><h2>' . t('Preview') . '</h2></div>');
                    $comment->id = 'preview';
                    $comment->aid = $this->user->id;
                    $comment->created_date = time();
                    $comment->preview = TRUE;
                    $comment->show();
                } else {
                    $comment->pid = $pid;
                    if ($comment->save()) {
                        flash_success(t('Your comment has been posted!'), '', 'growl');
                        $link = new Comments_Link('comments_posts');
                        $link->cid = $comment->id;
                        $link->pid = $post->id;
                        $link->save();
                        $post = new Post();
                        $post->id = $link->pid;
                        $post->recalculate('comments');
                        redirect($post->getLink() . '#comment-' . $comment->id);
                    }
                }
            }
            $form->elements->offsetUnset('delete');
            $form->show();
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
            $link = new Comments_Link('comments_posts');
            $link->cid = $comment->id;
            if ($link->find()) {
                $this->post($link->pid, $comment->id);
            }
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
            $link = new Comments_Link('comments_posts');
            $link->cid = $comment->id;
            $link->find();
            $post = new Post();
            $post->id = $link->pid;
            $user = new User();
            $user->id = $comment->aid;
            $user->find();
            $form = new Form('Comments.form');
            $form->options->action = l('/comments/edit/' . $comment->id);
            $form->attach($comment);
            if ($post->find()) {
                $form->title->options->label = t('Edit %s comment', 'Comments', $user->getAvatarLinked() . ' ' . $user->getProfileLink()) . ' ' . t('to <a href="%s">%s</a>', 'Comment', $post->getLink(), $post->name);
            }
            $form->publish->options->label = t('Update');
            $form->init();
            if ($result = $form->result()) {
                if ($result->preview) {
                    append('info', '<div class="page-header"><h2>' . t('Preview') . '</h2></div>');
                    $comment->id = 'preview';
                    $comment->aid = $this->user->id;
                    $comment->created_date = time();
                    $comment->preview = TRUE;
                    $comment->show();
                } elseif ($result->delete) {
                    if ($comment->delete()) {
                        flash_error(t('Your comment has been deleted!'), '', 'growl');
                        redirect($post->getLink() . '#comments');
                    }
                } else {
                    $comment->object->mix($result);
                    if ($comment->save()) {
                        flash_success(t('Your comment has been updated!'), '', 'growl');
                        redirect($post->getLink() . '#comment-' . $comment->id);
                    }
                }
            }
            $form->show();
        } else {
            return event('404');
        }
    }

    /**
     * Default dispatcher
     *
     * @param string $action
     * @param string $subaction
     */
    public function index_action($action = '', $subaction = NULL) {

    }

}