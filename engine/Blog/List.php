<?php

/**
 * List of blogs
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Blog_List extends Cogearable {

    protected $fields;
    public $options = array(
        'name' => 'blogs.list',
        'page' => 0,
        'per_page' => 5,
        'base' => '',
        'page_suffix' => 'page',
        'where' => array(
        ),
        'fields' => array(),
        'order' => array('name', 'ASC'),
        'render' => 'content',
    );

    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct($options = array()) {
        parent::__construct($options);
        $this->render && hook($this->render, array($this, 'show'));
    }

    /**
     * Get fields
     *
     * @return  Core_ArrayObject
     */
    public function getFields() {
        if ($this->fields) {
            return $this->fields;
        } else {
            return $this->setFields(array(
                                'name' => array(
                                    'label' => t('Name', 'Blog'),
                                    'callback' => new Callback(array($this, 'prepareFields')),
                                ),
                                'admin' => array(
                                    'class' => 't_c w20',
                                    'label' => t('Admin', 'Blog'),
                                    'callback' => new Callback(array($this, 'prepareFields')),
                                ),
                                'posts' => array(
                                    'label' => t('Posts', 'Blog'),
                                    'callback' => new Callback(array($this, 'prepareFields')),
                                    'class' => 't_c w10',
                                ),
                                'followers' => array(
                                    'label' => t('Followers', 'Blog'),
                                    'callback' => new Callback(array($this, 'prepareFields')),
                                    'class' => 't_c w10',
                                ),
                            ));
        }
    }

    /**
     * Set fields for table
     *
     * @param   mixed   $fields
     * @return  Core_ArrayObject
     */
    public function setFields($fields){
        if(is_array($fields)){
            $fields = Core_ArrayObject::transform($fields);
        }
        return $this->fields = $fields;
    }
    /**
     * Render list of blogs
     */
    public function render() {
        $blog = new Blog();
        $this->where && $this->db->where((array) $this->where);
        $this->order && $this->db->order($this->order[0], $this->order[1]);
        $pager = new Pager(array(
                    'current' => $this->page ? $this->page : NULL,
                    'count' => $blog->count(),
                    'per_page' => $this->per_page,
                    'base' => $this->base,
                ));
        if ($blogs = $blog->findAll()) {
            $table = new Table(array(
                        'name' => 'blogs',
                        'class' => 'table table-bordered table-striped shd',
                        'fields' => $this->getFields(),
                    ));
            $table->attach($blogs);
            return $table->render();
        } else {
            event('empty');
        }
    }

    /**
     * Prepare fields for table
     *
     * @param type $blog
     * @return type
     */
    public function prepareFields($blog, $key) {
        switch ($key) {
            case 'name':
                return $blog->render('navbar','profile');
                break;
            case 'admin':
                $author = user($blog->aid);
                return $author->getLink('avatar').' '.$author->getLink('profile');
                break;
            case 'posts':
                return '<a href="' . $blog->getLink() . '/posts/" class="badge' . ($blog->posts > 0 ? ' badge-info' : '') . '">' . $blog->posts . '</a>';
                break;
            case 'followers':
                return '<a href="' . $blog->getLink() . '/comments/" class="badge' . ($blog->followers > 0 ? ' badge-success' : '') . '">' . $blog->object->followers . '</a>';
                break;
        }
    }

}