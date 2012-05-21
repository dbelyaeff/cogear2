<?php

/**
 * Favorite gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Fave_Gear extends Gear {

    protected $name = 'Favorite';
    protected $description = 'Store favorite posts and comments';
    protected $package = '';
    protected $order = 0;
    protected $hooks = array(
        'post.title' => 'hookPostTitle',
        'comment.info' => 'hookCommentInfo',
    );
    protected $access = array(
        'status' => array(100),
        'menu' => array(100),
    );

    /**
     * Acccess
     *
     * @param string $rule
     * @param object $Item
     */
    public function access($rule, $Item = NULL) {
        switch ($rule) {
            case 'create':
                return TRUE;
                break;
        }
        return FALSE;
    }

    /**
     * Hook post title
     *
     * @param object $title
     */
    public function hookPostTitle($title) {
        $post = $title->object;
        if (access('Fave.status') && !$title->object->preview) {
            $title->append('<a class="fave ' . ($this->check($post->id) ? ' faved' : '') . ' sh" data-id="' . $post->id . '" href="/fave/status/post/' . $post->id . '"><i class="icon-star"></i></a>');
        }
    }

    public function hookCommentInfo($info) {
        $comment = $info->object;
        if (access('Fave.status') && !$info->object->preview) {
            $info->append('<a class="fave ' . ($this->check($comment->id, 'comment') ? ' faved' : '') . ' sh" data-id="' . $comment->id . '" href="/fave/status/comment/' . $comment->id . '"><i class="icon-star"></i></a>');
        }
    }

    /**
     * Init
     */
    public function init() {
        parent::init();
        // Force prepend route
        route('user/([^/]+)/fave:maybe', array($this, 'index_action'), TRUE);
        if ($this->user->isLogged() && $this->session->get('faves') == FALSE) {
            $faves = array(
                'posts' => array(),
                'comments' => array(),
            );
            $fave = new Fave_Object();
            $fave->uid = user()->id;
            if ($result = $fave->findAll()) {
                foreach ($result as $fave) {
                    if ($fave->pid) {
                        $faves['posts'][$fave->pid] = 1;
                    } elseif ($fave->cid) {
                        $faves['comments'][$fave->cid] = 1;
                    }
                }
            }
            $this->session->set('faves', $faves);
        }
    }

    /**
     * Hook menu
     *
     * @param string $name
     * @param object $menu
     */
    public function menu($name, $menu) {
        switch ($name) {
            case 'navbar':
                $menu->register(array(
                    'label' => icon('star icon-white'),
                    'link' => l($this->user->getLink() . '/fave/'),
                    'place' => 'left',
                    'order' => 20,
                ));
                break;
            case 'user.profile.tabs':
                $menu->register(array(
                    'label' => t('Favorites', 'Fave'),
                    'link' => l($menu->object->getLink() . '/fave/'),
                    'order' => 20,
                ));
                break;
        }
    }

    /**
     * Default dispatcher
     *
     * @param string $action
     * @param string $subaction
     */
    public function index_action($login, $fave, $type = 'posts', $page = 0) {
        if (!$user = user($login, 'login')) {
            return event('404');
        }
        $user->navbar()->show();
        $menu = new Menu_Pills(array(
                    'name' => 'fave',
                    'elements' => array(
                        'posts' => array(
                            'label' => t('Posts', 'Fave') . ' <sup>' . $this->db->join('fave', array(
                                'fave.pid' => 'posts.id',
                                'fave.uid' => $user->id,
                            ))->count('posts', 'posts.id', TRUE) . '</sup>',
                            'link' => l($user->getLink() . '/' . $fave . '/posts/'),
                            'active' => $type == 'posts',
                        ),
                        'comments' => array(
                            'label' => t('Comments', 'Fave') . ' <sup>' . $this->db->join('fave', array(
                                'fave.cid' => 'comments.id',
                                'fave.uid' => $user->id,
                            ))->count('comments', 'comments.id', TRUE) . '</sup>',
                            'link' => l($user->getLink() . '/' . $fave . '/comments/'),
                        )
                    ),
                    'render' => FALSE
                ));
        $menu->show();
        switch ($type) {
            case 'posts':
                Db_ORM::skipClear();
                $this->db->select('posts.*')->join('fave', array(
                    'fave.pid' => 'posts.id',
                    'fave.uid' => $user->id
                ));
                $posts = new Post_List(array(
                            'name' => 'fave',
                            'base' => l($user->getLink() . '/' . $fave . '/posts/'),
                            'per_page' => config('Fave.posts.per_page', 5),
                            'render' => FALSE,
                        ));
                if ($output = $posts->render()) {
                    append('content', $output);
                } else {
                    event('empty');
                }

                break;
            case 'comments':
                Db_ORM::skipClear();
                $this->db->select('comments.*')->join('fave', array(
                    'fave.cid' => 'comments.id',
                    'fave.uid' => $user->id
                ));
                $comments = new Comments_List(array(
                            'name' => 'fave',
                            'base' => l($user->getLink() . '/' . $fave . '/comments/'),
                            'per_page' => config('Fave.comments.per_page', 5),
                            'render' => FALSE,
                            'flat' => TRUE,
                        ));
                if ($output = $comments->render()) {
                    append('content', $output);
                } else {
                    event('empty');
                }
                break;
        }
    }

    /**
     * Check if is faved
     *
     * @param int $id
     * @param string $type
     */
    public function check($id, $type = 'post') {
        if ($this->session->faves) {
            switch ($type) {
                case 'post':
                    return isset($this->session->faves['posts'][$id]);
                    break;
                case 'comment':
                    return isset($this->session->faves['comments'][$id]);
                    break;
            }
        }
        return FALSE;
    }

    /**
     * Custom dispatcher
     *
     * @param   string  $subaction
     */
    public function status_action($type, $id) {
        switch ($type) {
            case 'post':
                // If post doesn't exists
                if (!$post = post($id)) {
                    return;
                }
                $fave = new Fave_Object();
                $fave->pid = $id;
                $fave->uid = $this->user->id;
                if ($fave->find()) {
                    $data['action'] = 'unfave';
                    $fave->delete();
                    $message = t('Post has been removed from favorites!', 'Fave');
                } else {
                    $data['action'] = 'fave';
                    $fave->created_date = time();
                    $fave->save();
                    $message = t('Post has been added to favorites!', 'Fave');
                }
                $this->session->remove('faves');
                if (Ajax::is()) {
                    $data['messages'] = array(array(
                            'type' => 'success',
                            'body' => $message,
                            ));
                    $ajax = new Ajax();
                    $ajax->json($data);
                } else {
                    flash_success($message);
                    redirect($post->getLink());
                }
                break;
            case 'comment':
                // If post doesn't exists
                if (!$comment = comment($id)) {
                    return;
                }
                $fave = new Fave_Object();
                $fave->cid = $id;
                $fave->uid = $this->user->id;
                if ($fave->find()) {
                    $data['action'] = 'unfave';
                    $fave->delete();
                    $message = t('Comment has been removed from favorites!', 'Fave');
                } else {
                    $data['action'] = 'fave';
                    $fave->created_date = time();
                    $fave->save();
                    $message = t('Comment has been added to favorites!', 'Fave');
                }
                $this->session->remove('faves');
                if (Ajax::is()) {
                    $data['messages'] = array(array(
                            'type' => 'success',
                            'body' => $message,
                            ));
                    $ajax = new Ajax();
                    $ajax->json($data);
                } else {
                    flash_success($message);
                    redirect(post($comment->post_id)->getLink() . '#comment-' . $comment->id);
                }
                break;
        }
    }

}