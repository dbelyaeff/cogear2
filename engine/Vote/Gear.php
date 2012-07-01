<?php

/**
 * Vote gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Vote_Gear extends Gear {

    protected $name = 'Vote';
    protected $description = 'Provide voting API';
    protected $package = '';
    protected $order = 0;
    protected $hooks = array(
        'post.before' => 'hookRenderVote',
        'comment.info' => 'hookRenderVote',
        'user.navbar' => 'hookUserNavbar',
        'blog.navbar.profile' => 'hookRenderVote',
//        'table.render.users' => 'hookUsersTableRender',
    );
    protected $routes = array(
    );
    protected $access = array(
        'status' => 'access',
        'add' => 'access',
        'menu' => array(1, 100),
    );

    /**
     * Acccess
     *
     * @param string $rule
     * @param object $Item
     */
    public function access($rule, $Item = NULL) {
        switch ($rule) {
            case 'status':
                if (role()) {
                    return TRUE;
                }
                break;
            case 'add':
                if (role() == 1) {
                    return TRUE;
                }
                break;
        }
        return FALSE;
    }

    /**
     * Hook post before
     *
     * @param type $info
     */
    public function hookRenderVote($info) {
        $vote = new Vote_Object($info->object);
        $info->append($vote->render());
    }

    /**
     * Hook users table render
     *
     * @param type $Table
     */
    public function hookUsersTableRender($Table) {
        $Table->options->fields->append(new Core_ArrayObject(array(
            'label' => t('Rating', 'Vote'),
            'callback' => new Callback(array($this, 'prepareFields')),
            'class' => 't_c w10',
                )));
    }

    /**
     * Add add_votes action to user navbar
     *
     * @param type $Navbar
     */
    public function hookRenderUserNavbar($Navbar) {
        $User = $Navbar->object;
        if (access('Vote.add', $User)) {
            $Navbar->append('<a href="/vote/points/'.$User->id.'" data-type="modal" data-source="form-vote-points" class="sh" title="'.t('Add votes','Vote').'"><i class="icon-volume-up"></i></a>');
        }
    }

    /**
     * Hooks for user navbar
     *
     * @param type $Navbar
     */
    public function hookUserNavbar($Navbar){
        $this->hookRenderVote($Navbar);
        $this->hookRenderUserNavbar($Navbar);
    }

    /**
     * Prepare fields for table
     *
     * @param type $user
     * @return type
     */
    public function prepareFields($user, $key) {
        switch ($key) {
            case 'rating':
                return '<a href="' . $user->getLink() . '/" class="badge' . ($user->rating >= 0 ? ' badge-success' : 'badge-important') . '">' . $user->rating . '</a>';
                break;
        }
    }

    /**
     * Init
     */
    public function init() {
        parent::init();
        $this->session->get('votes') !== NULL OR $this->setVotes();
    }

    /**
     * Add Control Panel to user panel
     */
    public function menu($name, $menu) {
        switch ($name) {
            case 'navbar':
                $badge = '<span class="badge" id="user-votes">';
                $badge .= user()->votes;
                $badge .= '</span>';
                $menu->register(array(
                    'link' => l('/vote/'),
                    'label' => $badge,
                    'title' => t('Votes', 'Vote'),
                    'place' => 'left',
                    'access' => access('admin'),
                    'order' => 100.2,
                ));
                break;
            case 'user.profile.tabs':
                $menu->register(array(
                    'label' => t('Votes', 'Vote'),
                    'link' => l('/vote/'),
                    'order' => 100,
                ));
                break;
        }
    }

    /**
     * Set votes
     */
    public function setVotes() {
        $this->session->set('votes', $this->getVotes());
    }

    /**
     * Get votes for current user
     */
    public function getVotes($user = NULL) {
        $user OR $user = user();
        $votes = new Vote_Object();
        $votes->uid = $user->id;
        $data = array();
        if ($result = $votes->findAll()) {
            foreach ($result as $item) {
                $data[$item->type][$item->tid] = $item->points ? TRUE : FALSE;
            }
        }
        return $data;
    }

    /**
     * Clear session data
     */
    public function clear() {
        $this->session->remove('votes');
    }

    /**
     * Show user voted items
     *
     * @param string $type
     * @param string $page
     */
    public function index_action($type = 'posts', $page = 'page0') {
        $rtype = preg_replace('#(.*)s$#', '$1', $type);
        if (!isset(Vote_Object::$types[$rtype])) {
            return event('404');
        }
        user()->navbar()->show();
        $votes = $this->session->get('votes');
        foreach (Vote_Object::$types as $value => $key) {
            $var = $value . 's';
            $$var = isset($votes[$key]) ? $votes[$key] : array();
        }
        new Menu_Pills(array(
                    'name' => 'vote.tabs',
                    'elements' => array(
                        'posts' => array(
                            'label' => t('Posts', 'Vote') . ' <sup>' . sizeof($posts) . '</sup>',
                            'link' => l('/vote/posts'),
                            'active' => TRUE,
                        ),
                        'blogs' => array(
                            'label' => t('Blogs', 'Vote') . ' <sup>' . sizeof($blogs) . '</sup>',
                            'link' => l('/vote/blogs'),
                        ),
                        'comments' => array(
                            'label' => t('Comments', 'Vote') . ' <sup>' . sizeof($comments) . '</sup>',
                            'link' => l('/vote/comments'),
                        ),
                        'users' => array(
                            'label' => t('Users', 'Vote') . ' <sup>' . sizeof($users) . '</sup>',
                            'link' => l('/vote/users'),
                        ),
                    )
                ));
        if (sizeof($$type) > 0) {
            switch ($type) {
                case 'posts':
                    Db_ORM::skipClear();
                    $this->db->where_in('posts.id', array_keys($posts));
                    new Post_List(array(
                                'name' => 'vote.posts',
                                'base' => l('/vote/posts/'),
                                'per_page' => config('Vote.post.per_page', 5),
                            ));

                    break;
                case 'comments':
                    Db_ORM::skipClear();
                    $this->db->where_in('comments.id', array_keys($comments));
                    new Comments_List(array(
                                'name' => 'vote.comments',
                                'base' => l('/vote/comments/'),
                                'per_page' => config('Vote.comments.per_page', 10),
                                'flat' => TRUE,
                            ));


                    break;
                case 'users':
                    Db_ORM::skipClear();
                    $this->db->where_in('users.id', array_keys($users));
                    new User_List(array(
                                'name' => 'vote.users',
                                'base' => l('/vote/users/'),
                                'per_page' => config('Vote.users.per_page', 10),
                                'flat' => TRUE,
                            ));
                    break;
                case 'blogs':
                    Db_ORM::skipClear();
                    $this->db->where_in('blogs.id', array_keys($blogs));
                    new Blog_List(array(
                                'name' => 'vote.blogs',
                                'base' => l('/vote/blogs/'),
                                'per_page' => config('Vote.blogs.per_page', 10),
                            ));
                    break;
            }
        } else {
            event('empty');
        }
    }

    /**
     * Voting process
     *
     * @param type $type
     * @param type $id
     * @param type $direction
     */
    public function status_action($type, $id, $direction = 'up') {
        switch ($type) {
            case 'post':
                if (!$object = post($id)) {
                    return;
                }
                break;
            case 'user':
                if (!$object = user($id)) {
                    return;
                }
                break;
            case 'comment':
                if (!$object = comment($id)) {
                    return;
                }
                break;
            case 'blog':
                if (!$object = blog($id)) {
                    return;
                }
                break;
            default:
                return;
        }
        $vote = new Vote_Object($object, $type);
        $data = array(
            'messages' => array(
                array(
                    'type' => 'success',
                    'body' => t('You\'ve just voted!', 'Vote'),
                )
            ),
        );
        $msg_method = 'flash_success';
        if ($direction == 'up' && !$vote->vote('up') OR
                $direction == 'down' && !$vote->vote('down')) {
            unset($data['action']);
            $data['messages'] = array(
                array(
                    'type' => 'error',
                    'body' => $vote->error(),
                )
            );
            $msg_method = 'flash_error';
        } else {
            $data['action'] = array(array(
                    'type' => 'replace',
                    'target' => '#vote-' . $type . '-' . $id,
                    'code' => $vote->render(),
                ), array(
                    'type' => 'set',
                    'target' => '#user-votes',
                    'code' => user()->votes,
                    ));
        }
        if (Ajax::is()) {
            ajax()->json($data);
        } else {
            $msg_method($data['messages'][0]['body']);
            redirect($object->getLink());
        }
    }

    /**
     * Add vote points to user
     *
     * @param type $uid
     */
    public function points_action($uid) {
        $user = user($uid);
        if (!$user OR !access('Vote.add',$user)) {
            return event('404');
        }
        $form = new Form('Vote.points');
        if ($result = $form->result()) {
            if ($user->update(array('votes' => $user->votes + $result->votes))) {
                if ($user->id == user()->id) {
                    $user->store();
                }
                redirect($user->getLink());
            }
        }
        $form->show();
    }

}