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

    /**
     * Get blog link
     */
    public function getLink($type = 'default', $param = NULL) {
        switch ($type) {
            case 'profile':
                $uri = new Stack(array('name' => 'blog.link.profile'));
                $uri->append($this->getLink());
                return HTML::a($uri->render('/'), $this->name);
                break;
            case 'avatar':
                $uri = new Stack(array('name' => 'blog.link.avatar'));
                $uri->append($this->getLink());
                return HTML::a($uri->render('/'), $this->getAvatarImage($param));
                break;
            case 'edit':
                $uri = new Stack(array('name' => 'blog.link.edit'));
                $uri->append('blog');
                $uri->append('edit');
                $uri->append($this->login);
                break;
            default:
                $uri = new Stack(array('name' => 'blog.link'));
                $uri->append('blog');
                $uri->append($this->login);
        }
        return '/' . $uri->render('/');
    }

    /**
     * Create new blog
     *
     * @param type $data
     */
    public function insert($data = NULL) {
        $data OR $data = $this->object->toArray();
        $data['created_date'] = time();
        $this->aid OR $data['aid'] = cogear()->user->id;
        if ($result = parent::insert($data)) {
            event('blog.insert', $this);
            $role = new Blog_Role();
            $role->uid = $data['aid'];
            $role->bid = $result;
            $role->role = Blog_Gear::ADMIN;
            $role->save();
        }
        return $result;
    }

    /**
     * Update blog
     *
     * @param type $data
     */
    public function update($data = NULL) {
        $data OR $data = $this->object->toArray();
        isset($data['body']) && $data['last_update'] = time();
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
        return HTML::img(image_preset($preset, $this->getAvatar()->getFile(), TRUE), $this->login, array('class' => 'avatar'));
    }

    /**
     * Get view snippet
     *
     * @return string
     */
    public function getListView() {
        return $this->getAvatarImage() . ' ' . $this->getLink('profile');
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
    public function render() {
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

    /**
     * Update counters
     */
    public function recalculate() {
        $users = new Blog_Role();
        $users->bid = $this->id;
        $this->users = $users->count();
        $this->save();
    }

}