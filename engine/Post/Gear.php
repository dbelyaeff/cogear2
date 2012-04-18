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

    /**
     * Init
     */
    public function init() {
        parent::init();
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
                    'label' => icon('pencil icon-white') . ' ' . t('Create'),
                    'link' => l('/post/create/'),
                    'place' => 'left',
                    'access' => access('post.create'),
                ));
                break;
            case 'user.profile.tabs':
                if ($menu->object->id == $this->user->id) {
                    $menu->register(array(
                        'label' => t('Drafts') . ' (' . $this->user->drafts . ')',
                        'link' => l('/post/drafts/'),
                        'active' => $this->router->check('post/drafts'),
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
        $this->user->navbar()->show();
        $post = new Post();
        $post->aid = $this->user->id;
        $this->db->where(array(
            'published' => 0
        ));
        $this->db->order('created_date', 'DESC');
        $pager = new Pager(array(
                    'current' => $page ? intval(str_replace('page', '', $page)) : NULL,
                    'count' => $post->count(),
                    'per_page' => config('Post.drafts.per_page', 5),
                    'base_uri' => l('/post/drafts/')
                ));
        if ($posts = $post->findAll()) {
            foreach ($posts as $post) {
                $post->teaser = TRUE;
                $post->show();
            }
            $pager->show();
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
                    flash_success(t($post->published ? 'Post published!' : 'Post saved to drafts!'));
                    redirect($post->getEditLink());
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
                if ($post->delete()) {
                    flash_success(t('Post has been deleted!'));
                    redirect($this->blog->getLink());
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
     * Recalculate user posts count and store it to database
     * 
     * @param type $uid 
     */
    public function recalculateUserPostCount($uid = NULL) {
        if (!$uid) {
            $user = $this->user->adapter;
            $self = TRUE;
        } else {
            $user = new User();
            $user->id = $uid;
            $user->find();
        }
        $user->posts = $this->db->where(array('aid' => $user->id, 'published' => 1))->count('posts', 'id', TRUE);
        $user->drafts = $this->db->where(array('aid' => $user->id, 'published' => 0))->count('posts', 'id', TRUE);
        $user->save();
        $user->store();
    }

}