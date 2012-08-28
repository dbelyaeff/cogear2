<?php

/**
 * Blog object.
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Blog
 * @subpackage
 */
class Blog_Object extends Db_Item {

    protected $table = 'blogs';
    protected $primary = 'id';
    protected $template = 'Blog.blog';
    public static $types = array(
        'personal' => 0,
        'private' => 1,
        'public' => 2,
    );
    public $where = array(
        'published' => 1,
    );
    public $followers = array();

    /**
     * Get blog link
     */
    public function getLink($type = 'default', $param = NULL) {
        switch ($type) {
            case 'profile':
                $uri = new Stack(array('name' => 'blog.link.profile'));
                $name = $this->name;
                if (cogear()->blog->check_status($this->id) >= Blog_Gear::APPROVED) {
                    $name = '<b>' . $this->name . '</b>';
                }
                $uri->append($this->getLink());
                return HTML::a($uri->render('/'), $name);
                break;
            case 'avatar':
                $uri = new Stack(array('name' => 'blog.link.avatar'));
                $uri->append($this->getLink());
                return HTML::a($uri->render('/'), $this->getAvatarImage($param ? $param : 'blog.profile'));
                break;
            case 'edit':
                $uri = new Stack(array('name' => 'blog.link.edit'));
                $uri->append('blog');
                $uri->append('edit');
                $uri->append($this->id);
                break;
            case 'users':
                return $this->getLink().'/users';
                break;
            default:
                $uri = new Stack(array('name' => 'blog.link'));
                $uri->append('blog');
                $uri->append($this->login);
        }
        return l('/' . $uri->render('/'));
    }

    /**
     * Create new blog
     *
     * @param type $data
     */
    public function insert($data = NULL) {
        $data OR $data = $this->getData();
        $this->created_date OR $data['created_date'] = time();
        $this->aid OR $data['aid'] = cogear()->user->id;
        if ($result = parent::insert($data)) {
            event('blog.insert', $this);
        }
        return $result;
    }

    /**
     * Update blog
     *
     * @param type $data
     */
    public function update($data = NULL) {
        if ($result = parent::update($data)) {
            event('blog.update', $this, $data);
        }
        return $result;
    }

    /**
     * Delete blog
     */
    public function delete() {
        $uid = $this->aid;
        if ($result = parent::delete()) {

        }
        return $result;
    }

    /**
     * Get HTML image avatar
     *
     * @param string $preset
     * @return string
     */
    public function getAvatarImage($preset = 'avatar.small') {
        return HTML::img(image_preset($preset, $this->getAvatar()->getFile(), TRUE), $this->login, array('class' => 'blog-avatar'));
    }

    /**
     * Get user avatar
     *
     * @return  Blog_Avatar
     */
    public function getAvatar() {
        if (!($this->avatar instanceof User_Avatar)) {
            $this->avatar = new User_Avatar($this->object->avatar);
        }
        $this->avatar->attach($this);
        return $this->avatar;
    }

    /**
     * User navbar
     */
    public function navbar() {
        if (!$this->navbar) {
            $this->navbar = new Blog_Navbar();
            $this->navbar->attach($this);
        }
        return $this->navbar;
    }

    /**
     * Get user upload directory
     */
    public function dir() {
        return UPLOADS . DS . 'blogs' . DS . $this->id;
    }

    /**
     * Render blog
     */
    public function render($type = NULL, $param = NULL) {
        switch ($type) {
            case 'navbar':
                $name = 'blog.navbar';
                if ($param) {
                    $name .= '.' . $param;
                }
                $navbar = new Stack(array('name' => $name));
                $navbar->attach($this);
                $navbar->avatar = $this->getAvatarImage('blog.tiny');
                $navbar->name = '<strong><a href="' . $this->getLink() . '">' . $this->name . '</a></strong>';
                if (access('Blog.edit', $this)) {
                    $navbar->edit = '<a class="blog-edit" title="' . t('Settings') . '" href="' . $this->getLink('edit') . '"><i class="icon-cog"></i></a>';
                }
                if (access('Blog.status', $this) && user()->id != $this->aid) {
                    $status = cogear()->blog->check_status($this->id);
                    switch ($status) {
                        case 0:
                        default :
                            $navbar->join = '<a href="' . l('/blog/status/' . $this->id) . '" class="sh ajax" title="' . t('Follow', 'Blog') . '">' . icon('check') . '</a>';
                            break;
                        case 1:
                            $navbar->join = '<a href="' . l('/blog/status/' . $this->id) . '" class="sh active ajax" title="' . t('You\'ve already send a request. Wait for moderation.', 'Blog') . '">' . icon('check') . '</a>';
                            break;
                        case 2:
                            $navbar->join = '<a href="' . l('/blog/status/' . $this->id) . '" class="sh active ajax" title="' . t('Unfollow', 'Blog') . '">' . icon('check') . '</a>';
                            break;
                    }
                }
                return '<span class="blog-navbar">' . $navbar->render() . '</span>';
                break;
            case 'post':
                return $this->getLink('avatar','blog.tiny').' '.$this->getLink('profile');
                break;
            default:
                event('blog.render', $this);
                $this->where['bid'] = $this->id;
                $posts = new Post_List(array(
                            'name' => 'blog',
                            'base' => $this->getLink() . '/',
                            'per_page' => $this->per_page,
                            'where' => $this->where,
                            'render' => FALSE,
                        ));
                return $posts->render();
        }
    }

    /**
     * Get blog followers
     *
     * @param   int $role
     * @return  array
     */
    public function getFollowers($role = NULL) {
        $followers = new Blog_Followers();
        $followers->bid = $this->id;
        $this->order('blogs_followers.role', 'DESC');
        $role && $this->where('role',$role);
        if ($result = $followers->findAll()) {
            $followers = array();
            foreach ($result as $follower) {
                $followers[$follower->uid] = $follower->role;
            }
        } else {
            $followers = array();
        }
        return $this->followers = $followers;
    }

}