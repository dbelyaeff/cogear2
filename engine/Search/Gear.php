<?php

/**
 * Search gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Search_Gear extends Gear {

    protected $name = 'Search';
    protected $description = 'Search description';
    protected $package = '';
    protected $hooks = array(
        'widgets' => 'hookWidgets',
    );
    protected $order = 0;

    /**
     * Init
     */
    public function init() {
        parent::init();
    }

    /**
     * Hook widgets
     *
     * @param type $widgets
     */
    public function hookWidgets($widgets) {
        $widgets->append(new Search_Widget());
    }

    /**
     * Default dispatcher
     *
     * @param string $action
     * @param string $subaction
     */
    public function index($type = NULL) {
        $q = $this->input->get('q');

        page_header($q ? t('Search results for «%s»', 'Seach', $q) : t('Search'));
        $tpl = new Template('Search.form');
        $tpl->action = $type;
        $q && $tpl->value = $q;
        $tpl->show('info');
        new Menu_Tabs(array(
                    'name' => 'search',
                    'elements' => array(
                        'posts' => array(
                            'label' => t('Posts', 'Search'),
                            'link' => l('/search' . ($q ? '?q=' . $q : '')),
                            'active' => check_route('search', Router::ENDS),
                        ),
                        'blogs' => array(
                            'label' => t('Blogs', 'Search'),
                            'link' => l('/search/blogs' . ($q ? '?q=' . $q : '')),
                            'active' => check_route('search/blogs'),
                        ),
                        'users' => array(
                            'label' => t('Users', 'Search'),
                            'link' => l('/search/users' . ($q ? '?q=' . $q : '')),
                            'active' => check_route('search/users'),
                        ),
                        'comments' => array(
                            'label' => t('Comments', 'Search'),
                            'link' => l('/search/comments' . ($q ? '?q=' . $q : '')),
                            'active' => check_route('search/comments'),
                        ),
                    ),
                ));
        if (!$q) {
            return event('empty');
        }
        switch ($type) {
            case 'blogs':
                new Blog_List(array(
                            'name' => 'search.blogs',
                            'like' => array(
                                array('name', $q, 'both'),
                                array('login', $q, 'both'),
                                array('body', $q, 'both'),
                            ),
                        ));
                break;
            case 'users':
                new User_List(array(
                            'name' => 'search.users',
                            'where' => array('login !=' => ''),
                            'like' => array(
                                array('name', $q, 'both'),
                                array('login', $q, 'both'),
                            ),
                        ));
                break;
            case 'comments':
                new Comments_List(array(
                            'name' => 'search.comments',
                            'where' => array('published' => 1),
                            'like' => array(
                                array('body', $q, 'both'),
                            ),
                            'per_page' => config('User.comments.per_page', 10),
                            'flat' => TRUE,
                        ));
                break;
            default:
                new Post_List(array(
                            'name' => 'search.posts',
                            'where' => array('published' => 1),
                            'like' => array(
                                array('name', $q, 'both'),
                                array('body', $q, 'both'),
                            ),
                        ));
        }
    }

    /**
     * Custom dispatcher
     *
     * @param   string  $subaction
     */
    public function action_index($subaction = NULL) {

    }

}