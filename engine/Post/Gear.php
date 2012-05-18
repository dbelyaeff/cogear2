<?php

/**
 * Post gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Post_Gear extends Gear {

    protected $name = 'Post';
    protected $description = 'Manage posts';
    protected $order = 10;
    protected $hooks = array(
        'user.recalculate' => 'hookUserPostCount',
        'comment.insert' => 'hookPostCommentsCount',
        'comment.update' => 'hookPostCommentsCount',
        'comment.delete' => 'hookPostCommentsCount',
    );

    /**
     * Recalculate user posts count and store it to database
     *
     * @param type $uid
     */
    public function hookUserPostCount($User, $type) {
        if ($type == 'posts') {
            $User->posts = $this->db->where(array('aid' => $User->id, 'published' => 1))->count('posts', 'id', TRUE);
            $User->drafts = $this->db->where(array('aid' => $User->id, 'published' => 0))->count('posts', 'id', TRUE);
            $User->update();
            $this->user->store();
        }
    }

    /**
     * Recalculate post comments count
     *
     * @param type $Comment
     */
    public function hookPostCommentsCount($Comment){
        $post = new Post();
        $post->id = $Comment->post_id;
        if($post->find()){
            $post->recalculate('comments');
        }
    }
    /**
     * Menu hook
     *
     * @param   string  $name
     * @param   object  $menu
     */
    public function menu($name, $menu) {
        d('Post');
        switch ($name) {
            case 'navbar':
                $menu->register(array(
                    'label' => icon('pencil icon-white'),
                    'link' => l('/post/create/'),
                    'place' => 'left',
                    'access' => access('post.create'),
                ));
                break;
            case 'blog.tabs':
                if ($menu->object->aid == $this->user->id && $menu->object->type == 0) {
                    $menu->register(array(
                        'label' => t('Drafts') . ' <sup>' . $this->user->drafts . '</sup>',
                        'link' => l('/post/drafts/'),
                        'active' => check_route('post/drafts'),
                    ));
                }
                break;
        }
        d();
    }

    /**
     * Default dispatcher
     *
     * @param string $action
     * @param string $subaction
     */
    public function index_action($id = '', $subaction = NULL) {
        if (!$id) {
            return $this->create_action();
        } else {
            $post = new Post();
            $post->id = $id;
            if ($post->find()) {
                $post->show();
            } else {
                return event('404');
            }
        }
    }

    /**
     * Show drafts
     *
     * @param type $page
     */
    public function drafts_action($page = NULL) {
        $blog = new Blog();
        $blog->aid = $this->user->id;
        $blog->type = Blog::$types['personal'];
        if ($blog->find()) {
            $blog->navbar()->show();
            $blog->where['published'] = 0;
            $blog->show();

//        $post = new Post();
//        $post->aid = $this->user->id;
//        $this->db->where(array(
//            'published' => 0
//        ));
//        $this->db->order('created_date', 'DESC');
//        $pager = new Pager(array(
//                    'current' => $page ? intval(str_replace('page', '', $page)) : NULL,
//                    'count' => $post->count(),
//                    'per_page' => config('Post.drafts.per_page', 5),
//                    'base' => l('/post/drafts/')
//                ));
//        if ($posts = $post->findAll()) {
//            foreach ($posts as $post) {
//                $post->teaser = TRUE;
//                $post->show();
//            }
//            $pager->show();
        } else {
            event('empty');
        }
    }

    /**
     * Add action
     */
    public function create_action() {
        $form = new Form('Post.post');
        if ($result = $form->result()) {
            $post = new Post();
            $post->attach($result);
            if ($result->preview) {
                $post->created_date = time();
                $post->aid = $this->user->id;
                $post->preview = TRUE;
                $post->show();
            } else {
                if ($result->draft) {
                    $post->published = 0;
                } elseif ($result->publish) {
                    $post->published = 1;
                }
                if ($post->save()) {
                    flash_success(t($post->published ? 'Post published!' : 'Post saved to drafts!') . ' <a class="btn btn-primary btn-mini" href="' . $post->getLink() . '">' . t('View') . '</a>');
                    redirect($post->getLink('edit'));
                }
            }
        }
        // Remove 'delete' button from create post form
        $form->elements->offsetUnset('delete');
        $form->show();
    }

    /**
     * Edit action
     */
    public function edit_action($id = NULL) {
        if (!$id) {
            return event('404');
        }
        $post = new Post();
        $post->id = $id;
        if (!$post->find()) {
            return event('404');
        }
        $form = new Form('Post.post');
        $form->attach($post);
        $form->elements->title->options->label = t('Edit post');
        if ($result = $form->result()) {
            $post->object->mix($result);
            if ($result->delete && (access('post.delete.all') OR access('post.delete') && $this->user->id == $post->aid)) {
                $blog = new Blog();
                $blog->id = $post->bid;
                $blog->find();
                if ($post->delete()) {
                    flash_success(t('Post has been deleted!'));
                    redirect($blog->getLink());
                }
            }
            if ($result->preview) {
                $post->created_date = time();
                $post->aid = $this->user->id;
                $post->preview = TRUE;
                $post->show();
            } else {
                if ($result->draft) {
                    $post->published = 0;
                } elseif ($result->publish) {
                    $post->published = 1;
                }
                if ($post->save()) {
                    if ($post->published) {
                        $link = l($post->getLink());
                        info(t('Post is published! %s', 'Post', '<a class="btn btn-primary btn-mini" href="' . $link . '">' . t('View') . '</a>'));
                    } else {
                        $link = l($post->getLink());
                        success(t('Post is saved to drafts! %s', 'Post', '<a class="btn btn-primary btn-mini" href="' . $link . '">' . t('View') . '</a>'));
                    }
                }
            }
        }
        $form->show();
    }

    /**
     * Hide or show post
     *
     * @param type $post_id
     */
    public function hide_action($post_id) {
        $post = new Post();
        $post->id = $post_id;
        if ($post->find() && (access('post.hide.all') OR $post->aid == $this->user->id)) {
            $data = array();
            if ($post->published) {
                $post->published = 0;
                $data['action'] = 'hide';
            } else {
                $post->published = 1;
                $data['action'] = 'show';
            }
            if ($post->save()) {
                $data['success'] = TRUE;
            }
            if (Ajax::is()) {
                $ajax = new Ajax();
                $ajax->json($data);
            } else {
                redirect($post->getLink());
            }
        }
    }

    /**
     * Delete post
     *
     * @param type $cid
     */
    public function delete_action($post_id) {
        $post = new Post();
        $post->id = $post_id;
        if ($post->find() && access('post.delete.all')) {
            $blog = new Blog();
            $blog->id = $post->bid;
            $blog->find();
            if ($post->delete()) {
                $message = t('Post has been deleted', 'Post');
                if (Ajax::is()) {
                    $data['success'] = TRUE;
                    $data['messages'] = array(
                        array(
                            'type' => 'success',
                            'body' => $message,
                        )
                    );
                    $ref = $this->response->get('referer');
                    $data['redirect'] = $blog->getLink();
                    $ajax = new Ajax();
                    $ajax->json($data);
                }
                $post = new Post();
                $post->id = $post->post_id;
                flash_success($message);
                redirect($blog->getLink());
            }
        }
    }

}