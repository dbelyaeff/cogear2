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
    protected $order = -100;
    protected $hooks = array(
        'user.profile.fields' => 'hookUserProfile',
        'form.load.post' => 'hookPostForm',
        'post.show.full.before' => 'hookShowUserNavbar',
        'user.verified' => 'hookAutoRegUserBlog',
        'post.title' => 'hookPostTitle',
        'post.insert' => 'hookBlogPostCount',
        'post.update' => 'hookBlogPostCount',
        'post.delete' => 'hookBlogPostCount',
        'user.delete' => 'hookUserDelete',
    );
    protected $access = array(
        'create' => 'access',
        'edit' => 'access',
        'status' => 'access',
        'delete' => 'access',
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

                break;
            case 'edit':
                if ($data instanceof Blog_Object) {
                    return $data->aid == $this->user->id;
                }
                else {
                    if($blog = blog($data[0])){
                        if($blog->aid == $this->user->id){
                            return TRUE;
                        }
                    }
                }
                break;
            case 'status':

                break;
            case 'delete':

                break;
        }
        return FALSE;
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
        $this->session->get('blogs') !== NULL OR $this->setBlogs();
    }

    /**
     * Recalculate user posts count and store it to database
     *
     * @param type $uid
     */
    public function hookBlogPostCount($post,$data = array(), $result) {
        if ($blog = blog($post->bid)) {
            $blog->update(array('posts'=>$this->db->where(array('bid' => $blog->id, 'published' => 1))->count('posts', 'id', TRUE)));
            $this->db->where(array('bid' => $blog->id, 'published' => 1))->count('posts', 'id', TRUE);
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
        if ($this->blog->current && $blog->id != $this->blog->current->id) {
            return;
        }
        if ($blog->find() && $title->object->teaser) {
            $title->inject(' &larr; ' . $blog->getAvatarImage() . ' ' . $blog->getLink('profile'), 1);
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
     * Add friends list to user profile
     *
     * @param type $Profile
     */
    public function hookUserProfile($Profile) {
        $blog = $Profile->object;
        $blogs = $this->getBlogs($blog->id);
        $data = new Core_ArrayObject();
        foreach ($blogs as $id => $status) {
            if ($status) {
                $blog = new Blog();
                $blog->id = $id;
                if ($blog->find()) {
                    $data->append($blog->getListView());
                }
            }
        }
        $data->count() && $Profile->append(array(
                    'label' => t('Follow blogs', 'Blogs.profile'),
                    'value' => implode(' ', $data->toArray()),
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
     * Get avaliable blogs for user
     */
    public function getAvailableBlogs() {
        $data = array();
        if ($blogs = $this->session->get('blogs')) {
            $keys = array();
            foreach ($blogs as $key => $blog) {
                if ($blog > 1) {
                    $keys[] = $key;
                }
            }
            $this->db->where_in('id', $keys)->order('id', 'ASC');
            $blog = new Blog();
            if ($result = $blog->findAll()) {
                foreach ($result as $blog) {
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
    public function index_action($login = '', $action = NULL) {
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
                    cogear()->db->join('blogs_users', array('blogs_users.uid' => 'users.id'), 'INNER');
                    cogear()->db->group('users.id');
                    $list = new User_List(array(
                                'name' => 'blog.list',
                                'per_page' => config('blog.users.per_page', 20),
                                'base' => $blog->getLink() . '/info/users/',
                                'page_suffix' => 'page',
                                'where' => array(
                                    'login !=' => '',
                                    'bid' => $blog->id,
                                ),
                            ));
                    break;
                default:
                    $blog->show();
            }
        } else {
            event('404');
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
                flash_success(t('Blog is created!') . ' <a class="btn btn-primary btn-mini" href="' . $blog->getLink() . '">' . t('View') . '</a>');
                redirect($blog->getLink('edit'));
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
                flash_success(t('Blog is updated!') . ' <a class="btn btn-primary btn-mini" href="' . $blog->getLink() . '">' . t('View') . '</a>');
                redirect($blog->getLink('edit'));
            }
        }
        $form->show();
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
        $role = new Blog_Role();
        $role->uid = $uid;
        $data = new Core_ArrayObject();
        if ($result = $role->findAll()) {
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
    public function check($bid) {
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
        $blog = new Blog();
        $blog->id = $bid;
        if ($bid && $blog->find()) {
            if ($blog->aid == $this->user->id) {
                return event('403');
            }
            $status = $this->check($bid);
            $form = new Form('Blog.status');
            $form->init();
            switch ($status) {
                case 1:
                case 2:
                    $form->title->options->label = t('Unfollow %s blog?', 'blog', $blog->getListView());
                    break;
                case 0:
                default:
                    $form->title->options->label = t('Follow %s blog?', 'blog', $blog->getListView());
            }
            $form->body->options->label = $blog->body;
            if ($result = $form->result()) {
                if ($result->yes) {
                    $role = new Blog_Role();
                    $role->uid = $this->user->id;
                    $role->bid = $blog->id;
                    $role->find();
                    switch ($status) {
                        case 0:
                            $role->created_date = time();
                            switch ($blog->type) {
                                case 1:
                                    $role->role = 2;
                                    break;
                                case 0:
                                case 2:
                                    $role->role = 1;
                                    break;
                            }
                            $role->save();
                            flash_success(t('You have started to follow this blog.'));
                            break;
                        case 1:
                        case 2:
                            $role->role = 0;
                            $role->save();
                            flash_error($message = t('You stoped follow this blog.'));
                    }
                }
                $blog->recalculate();
                $this->clear();
                redirect($blog->getLink());
            }
            $form->show();
        } else {
            return event('404');
        }
    }

}


/**
 * Shortcut for blog
 *
 * @param int $id
 * @param string    $param
 */
function blog($id = NULL,$param = 'id'){
    if($id){
        $blog = new Blog();
        $blog->$param = $id;
        if($blog->find()){
            return $blog;
        }
        else {
            return FALSE;
        }
    }
    return new Blog();
}