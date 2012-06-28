<?php

/**
 *  Blog gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Blog_Gear extends Gear {

    protected $name = 'Blog';
    protected $description = 'Allow users to have their own blogs';
    protected $package = '';
    protected $order = 0;
    protected $hooks = array(
        'user.profile.fields' => 'hookUserProfile',
        'form.load.post' => 'hookPostForm',
        'form.result.post' => 'hookFormResult',
        'post.full.before' => 'hookShowUserNavbar',
        'user.verified' => 'hookAutoRegUserBlog',
        'post.title' => 'hookPostTitle',
        'post.insert' => 'hookBlogPostCount',
        'post.update' => 'hookBlogPostCount',
        'post.delete' => 'hookBlogPostCount',
        'user.delete' => 'hookUserDelete',
        'blog.insert' => 'hookBlogInsert',
        'blog.follower.insert' => 'hookBlogFollowers',
        'blog.follower.update' => 'hookBlogFollowers',
        'blog.follower.delete' => 'hookBlogFollowers',
        'widgets' => 'hookWidgets',
    );
    protected $routes = array(
        'blogs:maybe' => 'list_action',
    );
    protected $access = array(
        'create' => 'access',
        'edit' => 'access',
        'status' => 'access',
        'delete' => 'access',
        'moderate' => 'access',
        'menu' => array(1, 100),
    );
    public $current;
    const LEFT = 0;
    const JOINED = 1;
    const APPROVED = 2;
    const MODER = 3;
    const ADMIN = 4;

    /**
     * Acccess
     *
     * @param string $rule
     * @param object $data
     */
    public function access($rule, $data = NULL) {
        switch ($rule) {
            case 'create':
                $event = event('access.blog.create');
                if ($event->check()) {
                    if (role() == 1) {
                        return TRUE;
                    }
                } else {
                    return $event->result;
                }
                break;
            case 'edit':
                $event = event('access.blog.edit');
                if ($event->check()) {
                    if (role() == 1) {
                        return TRUE;
                    }
                    if ($data instanceof Blog_Object) {
                        return $data->aid == $this->user->id;
                    } else {
                        if ($blog = blog($data[0])) {
                            if ($blog->aid == $this->user->id) {
                                return TRUE;
                            }
                        }
                    }
                } else {
                    return $event->result;
                }
                break;
            case 'status':
                if (role()) {
                    return TRUE;
                }
                break;
            case 'delete':
                $event = event('access.blog.delete');
                if ($event->check()) {
                    if (role() == 1) {
                        return TRUE;
                    }
                } else {
                    return $event->result;
                }
                break;
            case 'moderate':
                $event = event('access.blog.moderate');
                if ($event->check()) {
                    if (role() == 1) {
                        return TRUE;
                    }
                } else {
                    return $event->result;
                }
                break;
        }
        return FALSE;
    }

    /**
     * Menu hook
     *
     * @param   string  $name
     * @param   object  $menu
     */
    public function menu($name, $menu) {
        switch ($name) {
            case 'navbar':
                $menu->register(array(
                    'label' => icon('inbox icon-white', t('Feed', 'Blog')),
                    'link' => $this->user->getLink() . '/feed',
                    'title' => t('Feed', 'Blog'),
                    'place' => 'left',
                    'order' => 100,
                ));
                break;
            case 'user.profile.tabs':
                $menu->register(array(
                    'label' => t('Feed', 'Blog'),
                    'link' => $menu->object->getLink() . '/feed',
                    'order' => 100,
                ));
                break;
        }
    }

    /**
     * Hook blog insert
     *
     * @param type $Blog
     */
    public function hookBlogInsert($Blog) {
        $reader = new Blog_Followers();
        $reader->uid = $this->user->id;
        $reader->bid = $Blog->id;
        $reader->role = Blog_Gear::ADMIN;
        $reader->save();
    }

    /**
     * Recalculate user posts count and store it to database
     *
     * @param type $uid
     */
    public function hookBlogPostCount($post, $data = array(), $result) {
        if ($blog = blog($post->bid)) {
            $blog->update(array('posts' => $this->db->where(array('bid' => $blog->id, 'published' => 1))->count('posts', 'id', TRUE)));
        }
    }

    /**
     * Hook Post title
     *
     * @param type $title
     */
    public function hookPostTitle($title) {
        if ($title->object->preview) {
            return;
        }
        $blog = new Blog();
        $blog->id = $title->object->bid;
        if ($this->blog->current && $blog->id == $this->blog->current->id) {
            return;
        }
        if ($blog->find() && $title->object->teaser) {
            $title->inject(' &larr; ' . $blog->render('navbar'), 1);
        }
    }

    /**
     * Autoregister user blog
     *
     * @param type $User
     */
    public function hookAutoRegUserBlog($User) {
        $blog = new Blog();
        $blog->aid = $User->id;
        $blog->login = $User->login;
        $blog->type = Blog::$types['personal'];
        if (!$blog->find()) {
            $blog->name = t('%s blog', 'Blog', $User->getName());
            $blog->save();
        }
    }

    /**
     * Show user navbar
     *
     * @param object $Stack
     */
    public function hookShowUserNavbar($Stack) {
        $blog = new Blog();
        $blog->id = $Stack->object->bid;
        if ($blog->find()) {
            return $blog->navbar()->show();
        }
    }

    /**
     * Add blog list to user profile
     *
     * @param type $Profile
     */
    public function hookUserProfile($Profile) {
        $user = $Profile->object;
        $blogs = $this->getBlogs($user->id);
        $admins = new Core_ArrayObject();
        $moderates = new Core_ArrayObject();
        $reads = new Core_ArrayObject();
        foreach ($blogs as $id => $status) {
            if ($blog = blog($id)) {
                switch ($status) {
                    case self::APPROVED:
                        $reads->append($blog->render('navbar'));
                        break;
                    case self::MODER:
                        $moderates->append($blog->render('navbar'));
                        break;
                    case self::ADMIN:
                        $admins->append($blog->render('navbar'));
                        break;
                }
            }
        }
        $admins->count() && $Profile->append(array(
                    'label' => t('Created', 'Blogs.profile'),
                    'value' => implode(' ', $admins->toArray()),
                    'order' => 3,
                ));
        $moderates->count() && $Profile->append(array(
                    'label' => t('Moderate', 'Blogs.profile'),
                    'value' => implode(' ', $moderates->toArray()),
                    'order' => 4,
                ));
        $reads->count() && $Profile->append(array(
                    'label' => t('Reads', 'Blogs.profile'),
                    'value' => implode(' ', $reads->toArray()),
                    'order' => 5,
                ));
    }

    /**
     * Hook add new post form
     *
     * @param type $Form
     */
    public function hookPostForm($Form) {
        $values = $this->getAvailableBlogs();
        $Form->addElement('bid', array(
            'label' => t('Blog', 'Blog'),
            'type' => 'select',
            'values' => $values,
            'order' => 3,
        ));
    }

    /**
     * Hook user delete
     *
     * @param object $User
     */
    public function hookUserDelete($User) {
        $blog = new Blog();
        $blog->aid = $User->id;
        $blog->type = Blog::$types['personal'];
        if ($blog->find()) {
            $blog->delete();
        }
    }

    /**
     * Hook form "post" result
     *
     * @param object $Form
     * @param boolean $is_valid
     * @param array $result
     */
    public function hookFormResult($Form, $is_valid, $result) {
        if (isset($result['bid']) && $blog = blog($result['bid'])) {
            if ($blog->aid != $this->user->id && $blog->type == Blog::$types['personal']) {
                warning(t('You can\'t post to others personal blogs.', 'Blog'));
                return TRUE;
            }
        }
    }
    /**
     * Hook blog followers
     *
     * @param type $Blog_Followers
     */
    public function hookBlogFollowers($Blog_Followers){
        if($blog = blog($Blog_Followers->bid)){
            $followers = new Blog_Followers();
            $followers->bid = $blog->id;
            $blog->update(array('followers' => $followers->count(TRUE)));
        }
    }
    /**
     * Hook widgets
     *
     * @param type $widgets
     */
    public function hookWidgets($widgets){
        $widgets->append(new Blog_Widget());
    }
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        // This hook must be placed earlier, because it deals with User init method which loads before Friends gear init
        hook('user.refresh', array($this, 'clear'));
    }

    /**
     * Init
     */
    public function init() {
        parent::init();
        route('user/([^/]+)/feed:maybe', array($this, 'feed_action'), TRUE);
        role() && ($this->session->get('blogs') !== NULL OR $this->setBlogs());
    }

    /**
     * Get avaliable blogs for user
     */
    public function getAvailableBlogs() {
        $data = array();
        if ($blogs = $this->session->get('blogs')) {
            $keys = array();
            foreach ($blogs as $key => $blog) {
                if ($blog > self::APPROVED) {
                    $keys[] = $key;
                }
            }
            $blog = new Blog();
            $keys && $this->db->where_in('id', $keys)->order('id', 'ASC');
            if ($result = $blog->findAll()) {
                foreach ($result as $blog) {
                    // You can't write to other personal blogs
                    if ($blog->type == Blog::$types['personal'] && $blog->aid != $this->user->id) {
                        continue;
                    }
                    $data[$blog->id] = $blog->name;
                }
            }
        }
        return $data;
    }

    /**
     * Default dispatcher
     *
     * @param string $action
     * @param string $subaction
     */
    public function index_action($login = '', $action = NULL, $type = 'admin') {
        $blog = new Blog();
        $blog->login = $login;
        if ($blog->find()) {
            $this->current = $blog;
            $blog->navbar()->show();
            switch ($action) {
                case 'info':
                    $tpl = new Template('Blog.info');
                    $tpl->blog = $blog;
                    $tpl->show();
                    break;
                case 'users':
                    $followers = new Blog_Followers();
                    $followers->bid = $blog->id;
                    $followers->role = 4;
                    $admin_count = $followers->count();
                    $followers->role = 3;
                    $moders_count = $followers->count();
                    $followers->role = 2;
                    $followers_count = $followers->count();
                    new Menu_Pills(array(
                                'name' => 'blog.users.types',
                                'elements' => array(
                                    'admin' => array(
                                        'label' => t('Admins', 'Blog.roles').' ('.$admin_count.')',
                                        'link' => $blog->getLink() . '/users/admins/',
                                        'active' => TRUE,
                                    ),
                                    'moders' => array(
                                        'label' => t('Moders', 'Blog.roles').' ('.$moders_count.')',
                                        'link' => $blog->getLink() . '/users/moders/',
                                    ),
                                    'followers' => array(
                                        'label' => t('Followers', 'Blog.roles').' ('.$followers_count.')',
                                        'link' => $blog->getLink() . '/users/followers/',
                                    ),
                                    'newbies' => array(
                                        'label' => t('Newbies', 'Blog.roles'),
                                        'link' => $blog->getLink() . '/users/newbies/',
                                        'access' => access('Blog.moderate', $blog) && $blog->type == Blog::$types['private'],
                                    ),
                                )
                            ));
                    switch ($type) {
                        case 'moders':
                            $type = self::MODER;
                            break;
                        case 'followers':
                            $type = self::APPROVED;
                            break;
                        case 'newbies':
                            $type = self::JOINED;
                            break;
                        default:
                        case 'admins':
                            $type = self::ADMIN;
                            break;
                    }
                    if ($followers = $blog->getFollowers($type)) {
                        Db_ORM::skipClear();
                        $this->db->where_in('users.id', array_keys($followers));
                        $list = new User_List(array(
                                    'name' => 'blog.list',
                                    'per_page' => config('Blog.users.per_page', 20),
                                    'base' => $blog->getLink() . '/info/users/',
                                    'render' => FALSE,
                                ));
                        $fields = $list->getFields();
                        $fields->offsetSet('role', new Core_ArrayObject(array(
                            'label' => t('Role', 'Blog'),
                            'callback' => new Callback(array($this, 'prepareFields'), array($blog)),
                            'class' => 't_c',
                        )));
                        $list->setFields($fields);
                        $list->show();
                    } else {
                        event('empty');
                    }
                    break;
                default:
                    $blog->show();
            }
        } else {
            event('404');
        }
    }

    /**
     * Prepare fields for table
     *
     * @param type $user
     * @return type
     */
    public function prepareFields($user, $key, $blog = NULL) {
        switch ($key) {
            case 'role':
                $role = $this->current->followers[$user->id];
                switch ($role) {
                    case self::ADMIN:
                        $label = t('Administrator', 'Blog.roles');
                        break;
                    case self::MODER:
                        $label = t('Moderator', 'Blog.roles');
                        break;
                    case self::APPROVED:
                        $label = t('Follower', 'Blog.roles');
                        break;
                    case self::JOINED:
                        $label = t('Newbie', 'Blog.roles');
                        break;
                }
                return '<a href="/blog/role/' . $blog->id . '/' . $user->id . '" data-type="modal" data-source="form-blog-role">' . $label . '</a>';
                break;
        }
    }

    /**
     * Custom dispatcher
     *
     * @param   string  $subaction
     */
    public function create_action() {
        if (!access('Blog.create')) {
            return event('403');
        }
        $form = new Form('Blog.create');
        if ($result = $form->result()) {
            $blog = new blog();
            $blog->attach($result);

            if ($blog->save()) {
                flash_success(t('Blog is created!'), '', 'growl');
                redirect($blog->getLink());
            }
        }
        // Remove 'delete' button from create blog form
        $form->show();
    }

    /**
     * Custom dispatcher
     *
     * @param   int  $id
     */
    public function edit_action($id = 0) {
        $blog = new Blog();
        $blog->id = $id;
        if (!$blog->find()) {
            return event('404');
        }
        $form = new Form('Blog.edit');
        $form->init();
        $form->avatar->options->rename = $blog->login;
        $form->attach($blog);
        access('Blog.edit.login') OR $form->login->options->disabled = TRUE;
        $form->title->options->label = t('Edit blog %s', 'Blog', $blog->getLink('profile'));
        $form->create->options->label = t('Save');
        if ($result = $form->result()) {
            $blog->object->adopt($result);
            if ($blog->save()) {
                flash_success(t('Blog is updated!'), '', 'growl');
                redirect($blog->getLink());
            }
        }
        $form->show();
    }

    /**
     * Show user feed
     *
     * @param string $login
     * @param string $action
     * @param string $page
     */
    public function feed_action($login = NULL, $action='feed', $page = 'page0') {
        if ($login) {
            if (!$user = user($login, 'login')) {
                return event('404');
            }
        } else {
            $user = user()->object;
        }
        $user->navbar()->show();
        $readers = new Blog_Followers();
        $readers->uid = $user->id;
        if ($result = $readers->findAll()) {
            $user_blog = new Blog();
            $user_blog->aid = $user->id;
            $user_blog->type = Blog::$types['personal'];
            $user_blog->find();
            $where_in = array();
            foreach ($result as $reader) {
                if ($reader->bid != $user_blog->id) {
                    $where_in[] = $reader->bid;
                }
            }
            Db_ORM::skipClear();
            $this->db->where_in('posts.bid', $where_in);
            $posts = new Post_List(array(
                        'name' => 'user.posts',
                        'base' => $user->getLink() . '/feed/',
                        'per_page' => config('User.posts.per_page', 5),
                        'where' => array('published' => 1),
                    ));
        } else {
            event('empty');
        }
    }

    /**
     * List blogs
     */
    public function list_action(){
        page_header(t('Blogs','Blogs'));
        new Blog_List(array(
            'name' => 'main',
        ));
    }

    /**
     * Set user blog
     */
    public function setBlogs() {
        $this->session->set('blogs', $this->getBlogs());
    }

    /**
     * Get user blog
     *
     * @param int $uid
     * @reutnr  array
     */
    public function getBlogs($uid = 0) {
        $uid OR $uid = $this->user->id;
        $readers = new Blog_Followers();
        $readers->uid = $uid;
        $data = new Core_ArrayObject();
        if ($result = $readers->findAll()) {
            foreach ($result as $item) {
                $data[$item->bid] = $item->role;
            }
        }
        return $data;
    }

    /**
     * Reset current user blog
     */
    public function clear() {
        $this->session->delete('blogs');
    }

    /**
     * Check user to be a friend
     *
     * @return int // 0 - left, 1 - joined, 2 - approved
     */
    public function check_status($bid) {
        if ($blog = $this->session->get('blogs')) {
            return isset($blog[$bid]) ? $blog[$bid] : 0;
        }
    }

    /**
     * Change status
     *
     * @param type $bid
      status */
    public function status_action($bid = 0) {
        if ($blog = blog($bid)) {
            if ($blog->aid == $this->user->id) {
                return event('403');
            }
            $data = array();
            $readers = new Blog_Followers();
            $readers->uid = $this->user->id;
            $readers->bid = $blog->id;
            $status = $readers->find() ? $readers->role : 0;
            $readers->aid = $blog->aid;
            $readers->status_date = time();
            switch ($status) {
                case 0:
                    $data['action'] = array(
                        'type' => 'class',
                        'className' => 'active',
                    );
                    switch ($blog->type) {
                        case Blog::$types['private']:
                            $readers->role = self::JOINED;
                            $data['messages'] = array(array(
                                    'type' => 'info',
                                    'body' => t('You send a request to join this blog. Wait for approve.', 'Blog'),
                                    ));
                            $data['action']['title'] = t('You\'ve already send a request. Wait for moderation.', 'Blog');
                            break;
                        case Blog::$types['public']:
                        case Blog::$types['personal']:
                            $readers->role = self::APPROVED;
                            $data['messages'] = array(array(
                                    'type' => 'success',
                                    'body' => t('You start following this blog.', 'Blog'),
                                    ));
                            $data['action']['title'] = t('Unfollow', 'Blog');
                            break;
                    }
                    $readers->save();
                    break;
                case 1:
                    $data['messages'] = array(array(
                            'type' => 'info',
                            'body' => t('You\'ve already send a request. Wait for moderation.', 'Blog'),
                            ));
                    break;
                case 2:
                    $data['action'] = array(
                        'type' => 'class',
                        'className' => 'active',
                        'title' => t('Follow', 'Blog'),
                    );
                    $data['messages'] = array(array(
                            'type' => 'warning',
                            'body' => t('You stopped following this blog.', 'Blog'),
                            ));
                    $readers->delete();
                    break;
            }
            if (Ajax::is()) {
                ajax()->json($data);
            } else {
                flash_success($data['messages'][0]['body']);
                redirect($blog->getLink());
            }
        }
    }

    /**
     * Change role for blog
     *
     * @param int $bid
     * @param int $uid
     */
    public function role_action($bid, $uid) {
        if (!$blog = blog($bid) OR !$user = user($uid)) {
            return event('404');
        } else if ($blog->aid == $user->id) {
            $message = t('You can\'t change role for blog founder.', 'Blog.roles');
            if (Ajax::is()) {
                $error = error($message, NULL, FALSE);
                append('content', '<div id="form-blog-role">' . $error . '</div>');
            } else {
                error($message);
            }
            return;
        }
        $form = new Form('Blog.role');
        $form->init();
        $values = array(
            Blog_Gear::ADMIN => t('Administrator', 'Blog.roles'),
            Blog_Gear::MODER => t('Moderator', 'Blog.roles'),
            Blog_Gear::APPROVED => t('Follower', 'Blog.roles'),
            Blog_Gear::JOINED => t('Newbie', 'Blog.roles'),
            Blog_Gear::LEFT => t('Dismiss', 'Blog.roles'),
        );
        if($blog->type != Blog_Object::$types['private']){
            unset($values[Blog_Gear::JOINED]);
        }
        $form->role->setValues($values);
        $follower = new Blog_Followers();
        $follower->bid = $blog->id;
        $follower->uid = $user->id;
        if (!$follower->find()) {
            return event('404');
        }
        $form->role->setValue($follower->role);

        if ($result = $form->result()) {
            $follower = new Blog_Followers();
            $follower->bid = $blog->id;
            $follower->uid = $user->id;
            switch ($result->role) {
                case self::LEFT:
                    if ($follower->find()) {
                        $follower->delete();
                        $user->refresh();
                    }
                    break;
                default:
                    if ($follower->find()) {
                        $follower->update(array('role' => $result->role));
                    }
            }
            flash_success(t('User role has been changed'));
            redirect($blog->getLink() . '/users/');
        }
        $form->show();
    }

}

/**
 * Shortcut for blog
 *
 * @param int $id
 * @param string    $param
 */
function blog($id = NULL, $param = 'id') {
    if ($id) {
        $blog = new Blog();
        $blog->$param = $id;
        if ($blog->find()) {
            return $blog;
        } else {
            return FALSE;
        }
    }
    return new Blog();
}