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
        'comment.insert' => 'hookCommentsRecount',
        'comment.update' => 'hookCommentsRecount',
        'comment.delete' => 'hookCommentsRecount',
        'user.delete' => 'hookUserDelete',
    );
    protected $access = array(
        'create' => 'access',
        'edit' => 'access',
        'delete' => 'access',
        'drafts' => 'access',
        'hide' => 'access',
        'menu' => 'access',
        'ajax' => 'access',
    );
    protected $routes = array(
        ':index' => 'front_action',
    );
    protected $is_core = TRUE;

    /**
     * Access
     *
     * @param type $rule
     * @param type $data
     */
    public function access($rule, $data = NULL) {
        switch ($rule) {
            case 'create':
                $event = event('access.post.create', $data);
                if ($event->check()) {
                    // Allow to write post to reg every user
                    return role();
                } else {
                    return $event->result();
                }
                break;
            case 'edit':
                if (role() == 1) {
                    return TRUE;
                }
                if ($data) {
                    if (event('access.post.edit', $data)->check()) {
                        
                    }
                }
                break;
            case 'drafts':
                if ($data && $user = user($data, 'login')) {
                    if ($user->id == $this->user->id) {
                        return TRUE;
                    }
                }
                break;
            case 'delete':
                if (role() == 1) {
                    return TRUE;
                }
                break;
            case 'hide':
                if ($data instanceof Post_Object && $data->aid == user()->id OR role() == 1) {
                    return TRUE;
                }
                break;
            case 'menu':
                return TRUE;
                break;
            case 'ajax':
                if (Ajax::is()) {
                    return TRUE;
                }
                break;
        }
        return FALSE;
    }

    /**
     * Recalculate post comments count
     *
     * @param type $Comment
     */
    public function hookCommentsRecount($Comment) {
        $post = new Post();
        $post->id = $Comment->post_id;
        if ($post->find()) {
            $post->update(array('comments' => $this->db->where(array('post_id' => $post->id, 'published' => 1))->count('comments', 'id', TRUE)));
        }
    }

    /**
     * Hook user delete
     *
     * @param object $User
     */
    public function hookUserDelete($User) {
        $post = post();
        $post->aid = $User->id;
        if ($posts = $post->findAll()) {
            foreach ($posts as $post) {
                $post->delete();
            }
        }
    }

    /**
     * Constructor
     */
    public function init() {
        parent::init();
        route('user/([^/]+)/posts:maybe', array($this, 'list_action'), TRUE);
        route('user/([^/]+)/drafts:maybe', array($this, 'drafts_action'), TRUE);
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
            case 'user':
                access('Post.create') && $menu->register(array(
                            'label' => icon('pencil icon-white'),
                            'link' => l('/post/create/'),
                            'title' => t('Create post', 'Post'),
                            'place' => 'left',
                            'access' => access('Post.create'),
                        ));
                break;
            case 'user.profile.tabs':
                $menu->register(array(
                    'label' => t('Posts') . ' <sup>' . $menu->object()->posts . '</sup>',
                    'link' => $menu->object()->getLink() . '/posts/',
                    'order' => 2,
                ));
                if ($menu->object()->id == $this->user->id) {
                    $menu->register(array(
                        'label' => t('Drafts') . ' <sup>' . $this->user->drafts . '</sup>',
                        'link' => $menu->object()->getLink() . '/drafts/',
                        'order' => 2.1,
                    ));
                }
                break;
        }
        d();
    }

    /**
     * Show front page
     */
    public function front_action($page = 0) {
        $posts = new Post_List(array(
                    'name' => 'front.posts',
                    'base' => l(),
                    'per_page' => config('User.posts.per_page', 5),
                    'where' => array('published' => 1),
                ));
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
     * List posts
     *
     * @param type $login
     */
    public function list_action($login = NULL) {
        if ($login == user()->login) {
            $user = user();
        } elseif (!$user = user($login, 'login')) {
            return event('404');
        }
        $user->navbar()->show();
        $posts = new Post_List(array(
                    'name' => 'user.posts',
                    'base' => $user->getLink() . '/posts/',
                    'per_page' => config('User.posts.per_page', 5),
                    'where' => array('aid' => $user->id, 'published' => 1),
                ));
    }

    /**
     * Show drafts
     *
     * @param type $page
     */
    public function drafts_action($login = NULL) {
        if ($login == user()->login) {
            $user = user();
        } elseif (!$user = user($login, 'login')) {
            return event('404');
        }
        $user->navbar()->show();
        $posts = new Post_List(array(
                    'name' => 'user.posts',
                    'base' => user()->getLink() . '/posts/',
                    'per_page' => config('User.posts.per_page', 5),
                    'where' => array('aid' => $user->id, 'published' => 0),
                ));
    }

    /**
     * Add action
     */
    public function create_action() {
        $post = new Post();
//        if ($pid = $this->session->get('draft')) {
//            $post->id = $pid;
//            $post->find();
//        } else {
//            $post->aid = $this->user->id;
//            $post->created_date = time();
//            $post->insert();
//            $this->session->set('draft', $post->id);
//        }
        $form = new Form('Post/forms/post');
        if ($result = $form->result()) {
            $post->object()->extend($result);
            if ($result->preview) {
                $post->preview = TRUE;
                $post->show();
            } else {
//                if (Ajax::is() && $this->input->get('autosave')) {
//                    $post->update();
//                    $ajax = new Ajax();
//                    $ajax->message(t('Post saved!', 'Post'));
//                    $ajax->send();
//                }
                $post->last_update = time();
                if ($result->draft) {
                    $post->published = 0;
                } elseif ($result->publish) {
                    $post->published = 1;
                }
                if ($post->save()) {
                    $this->session->remove('draft');
                    flash_success(t($post->published ? 'Post published!' : 'Post saved to drafts!'), NULL, 'growl');
                    redirect($post->getLink());
                }
            }
        } else {
//            $form->object($post);
        }
        // Remove 'delete' button from create post form
        $form->elements->offsetUnset('delete');
        $form->show();
//        js($this->folder . '/js/inline/autosave.js','footer');
    }

    /**
     * Edit action
     */
    public function edit_action($id = NULL) {
        if (!$post = post($id)) {
            return event('404');
        }
        $this->widgets = NULL;
        $form = new Form('Post/forms/post');
        $form->object($post);
        $form->elements->title->options->label = t('Edit post');
        if ($result = $form->result()) {
            $post->object()->extend($result);
            if ($result->delete && access('Post.delete', $post)) {
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
                if (Ajax::is() && $this->input->get('autosave')) {
                    $post->update();
                    $ajax = new Ajax();
                    $ajax->message(t('Post saved!', 'Post'));
                    $ajax->send();
                }
                if ($result->draft) {
                    $post->published = 0;
                } elseif ($result->publish) {
                    $post->published = 1;
                }
                if ($post->save()) {
                    if ($post->published) {
                        $link = l($post->getLink());
                        flash_success(t('Post is published!', 'Post'), NULL, 'growl');
                    } else {
                        $link = l($post->getLink());
                        flash_success(t('Post is saved to drafts! %s', 'Post'), NULL, 'growl');
                    }
                    redirect($post->getLink());
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
        if ($post->find() && (access('Post.hide.all') OR $post->aid == $this->user->id)) {
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
        if ($post->find() && access('Post.delete.all')) {
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

/**
 * Shortcut for post
 *
 * @param int $id
 * @param string    $param
 */
function post($id = NULL, $param = 'id') {
    if ($id) {
        $post = new Post();
        $post->$param = $id;
        if ($post->find()) {
            return $post;
        }
    }
    return new Post();
}