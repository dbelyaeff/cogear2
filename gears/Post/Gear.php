<?php

/**
 * Шестеренка «Пост»
 *
 * Служит для публиации информации
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Post_Gear extends Gear {

    protected $hooks = array(
        'comment.insert' => 'hookCommentsRecount',
        'comment.update' => 'hookCommentsRecount',
        'comment.delete' => 'hookCommentsRecount',
        'user.delete' => 'hookUserDelete',
        'menu' => 'hookMenu',
    );
    protected $access = array(
        'index' => TRUE,
        'create' => array(1,100),
        'edit' => 'access',
        'delete' => 'access',
        'drafts' => 'access',
        'hide' => 'access',
        'menu' => 'access',
        'ajax' => 'access',
        'front' => TRUE,
    );
    protected $routes = array(
        'blog' => 'index_action',
        'post/(\d+)' => 'index_action',
        'post/create' => 'create_action',
        'post/edit/(\d+)' => 'edit_action',
        'post/delete/(\d+)' => 'delete_action',
        'drafts/?' => 'drafts_action',
    );

    /**
     * Access
     *
     * @param type $rule
     * @param type $data
     */
    public function access($rule, $data = NULL) {
        switch ($rule) {
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
                } else {
                    if (user()->isLogged()) {
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
            $post->update(array('comments' => $this->db->where(array('post_id' => $post->id, 'published' => 1))->countAll('comments', 'id', TRUE)));
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
     * Конструктор
     */
    public function init() {
        parent::init();
//        bind_route('user/([^/]+)/posts:maybe', array($this, 'list_action'), TRUE);
//        bind_route('user/([^/]+)/drafts:maybe', array($this, 'drafts_action'), TRUE);
    }

    /**
     * Menu hook
     *
     * @param   string  $name
     * @param   object  $menu
     */
    public function hookMenu($name, $menu) {
        switch ($name) {
            case 'user':
                $menu->add(array(
                            'label' => icon('pencil') ,
                            'tooltip' => t('Написать'),
                            'link' => l('/post/create/'),
                            'place' => 'left',
                            'access' => access('Post.create'),
                            'title' => FALSE,
                        ));
                $menu->add(array(
                            'label' => icon('eye-close').' <span class="badge">'.user()->drafts.'</span>',
                            'tooltip' => t('Черновики'),
                            'link' => l('/drafts/'),
                            'place' => 'left',
                            'access' => access('Post.create') && user()->drafts > 0,
                            'title' => t('Черновики'),
                        ));
                break;
            case 'user.profile.tabs':
                $menu->add(array(
                    'label' => t('Публикации') . ' <sup>' . $menu->object()->posts . '</sup>',
                    'link' => $menu->object()->getLink() . '/posts/',
                    'order' => 2,
                    'title' => t('Публикации'),
                ));
                if ($menu->object()->id == $this->user->id) {
                    $menu->add(array(
                        'label' => t('Черновики') . ' <sup>' . $this->user->drafts . '</sup>',
                        'link' => $menu->object()->getLink() . '/drafts/',
                        'order' => 2.1,
                        'title' => t('Черновики'),
                    ));
                }
                break;
        }
    }

    /**
     * Default dispatcher
     *
     * @param string $action
     */
    public function index_action($id = NULL) {
        if (!$id) {
            $posts = new Post_List(array(
                        'name' => 'front.posts',
                        'per_page' => config('User.posts.per_page', 5),
                        'where' => array('published' => 1),
                    ));
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
        if (!$login OR $login == user()->login) {
            $user = user();
        } elseif (!$user = user($login, 'login')) {
            return event('404');
        }
//        $user->navbar()->show();
        $posts = new Post_List(array(
                    'name' => 'user.drafts',
                    'base' => l('/drafts'),
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
                $post->aid = user()->id;
                $post->created_date = time();
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
                    flash_success($post->published ? t('Пост опубликован!') : t('Сохранено в черновиках!'), NULL, 'growl');
                    redirect($post->getLink());
                }
            }
        } else {
            $form->object($post);
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
        $post = new Post();
        $post->id = $id;
        $post->cache(FALSE);
        if (!$post->find()) {
            return event('404');
        }
        $form = new Form('Post/forms/post');
        $form->object($post);
        $form->elements->title->options->label = t('Редактирование публикации');
        event('post.edit', $post, $form);
        if ($result = $form->result()) {
            $post->object()->extend($result);
            if ($result->delete && access('Post.delete', $post)) {
                if ($post->delete()) {
                    flash_success(t('Пост удалён!'));
                    redirect();
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
                    $ajax->message(t('Пост сохранён!', 'Post'));
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
                        flash_success(t('Пост опубликован!'), NULL, 'growl');
                    } else {
                        $link = l($post->getLink());
                        flash_success(t('Сохранено в черновиках!'), NULL, 'growl');
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
            if ($post->delete()) {
                $message = t('Пост удалён');
                if (Ajax::is()) {
                    $data['success'] = TRUE;
                    $data['messages'] = array(
                        array(
                            'type' => 'success',
                            'body' => $message,
                        )
                    );
                    $data['redirect'] = server('referer');
                    $ajax = new Ajax();
                    $ajax->json($data);
                }
                $post = new Post();
                $post->id = $post->post_id;
                flash_success($message);
                back(-2);
            }
        }
    }

}

/**
 * Ярлык для поста
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