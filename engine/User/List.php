<?php

/**
 * List of users
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class User_List extends Cogearable {

    protected $fields;
    public $options = array(
        'name' => 'user.list',
        'page' => 0,
        'per_page' => 5,
        'base' => '',
        'page_suffix' => 'page',
        'where' => array(
            'login !=' => '',
        ),
        'fields' => array(),
        'order' => array('login', 'ASC'),
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
                                'login' => array(
                                    'label' => t('Login', 'User'),
                                    'callback' => new Callback(array($this, 'prepareFields')),
                                ),
                                'posts' => array(
                                    'label' => t('Posts', 'User'),
                                    'callback' => new Callback(array($this, 'prepareFields')),
                                    'class' => 't_c w10',
                                ),
                                'comments' => array(
                                    'label' => t('Comments', 'User'),
                                    'callback' => new Callback(array($this, 'prepareFields')),
                                    'class' => 't_c w10',
                                ),
                                'reg_date' => array(
                                    'label' => t('Registered', 'User'),
                                    'callback' => new Callback(array($this, 'prepareFields')),
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
     * Render list of users
     */
    public function render() {
        $user = new User();
        $this->where && $this->db->where((array) $this->where);
        $this->order && $this->db->order($this->order[0], $this->order[1]);
        $pager = new Pager(array(
                    'current' => $this->page ? $this->page : NULL,
                    'count' => $user->count(),
                    'per_page' => $this->per_page,
                    'base' => $this->base,
                ));
        if ($users = $user->findAll()) {
            $table = new Table(array(
                        'name' => 'users',
                        'class' => 'table table-bordered table-striped shd',
                        'fields' => $this->getFields(),
                    ));
            $table->attach($users);
            return $table->render();
        } else {
            event('empty');
        }
    }

    /**
     * Prepare fields for table
     *
     * @param type $user
     * @return type
     */
    public function prepareFields($user, $key) {
        switch ($key) {
            case 'login':
                return $user->render('list');
                break;
            case 'reg_date':
                return df($user->reg_date, 'd M Y');
                break;
            case 'posts':
                return '<a href="' . $user->getLink() . '/posts/" class="badge' . ($user->posts > 0 ? ' badge-info' : '') . '">' . $user->posts . '</a>';
                break;
            case 'comments':
                return '<a href="' . $user->getLink() . '/comments/" class="badge' . ($user->comments > 0 ? ' badge-warning' : '') . '">' . $user->comments . '</a>';
                break;
        }
    }

}