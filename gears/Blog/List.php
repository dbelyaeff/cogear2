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
class Blog_List extends Db_List_Table {
    protected $class = 'Blog_Object';
    public $options = array(
        'name' => 'blogs-list',
        'page' => 0,
        'per_page' => 5,
        'base' => '',
        'page_suffix' => 'page',
        'where' => array(
        ),
        'like' => array(),
        'fields' => array(),
        'order' => array('name', 'ASC'),
        'render' => 'content',
    );

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
                return '<a href="' . $blog->getLink() . '/users/" class="badge' . ($blog->followers > 0 ? ' badge-success' : '') . '">' . $blog->object()->followers . '</a>';
                break;
        }
    }

}