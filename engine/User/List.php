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

    public $options = array(
        'name' => 'user.list',
        'page' => 0,
        'per_page' => 5,
        'base' => '',
        'page_suffix' => 'page',
        'where' => array(
            'login !=' => '',
        ),
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
     * Render list of users
     */
    public function render() {
        $user = new User();
        $this->where && $this->db->where((array)$this->where);
        $this->db->order('login', 'ASC');
        $pager = new Pager(array(
                    'current' => $this->page ? $this->page : NULL,
                    'count' => $user->count(),
                    'per_page' => $this->per_page,
                    'base' => $this->base,
                ));
        if ($users = $user->findAll()) {
            $table = new Table(array(
                        'name' => $this->name,
                        'class' => 'table table-bordered table-striped',
                        'fields' => array(
                            'login' => array(
                                'label' => t('Login', 'User'),
                                'callback' => new Callback(array($this,'prepareFields')),
                            ),
                            'posts' => array(
                                'label' => t('Posts', 'User'),
                                'callback' => new Callback(array($this,'prepareFields')),
                            ),
                            'comments' => array(
                                'label' => t('Comments', 'User'),
                                'callback' => new Callback(array($this,'prepareFields')),
                            ),
                            'reg_date' => array(
                                'label' => t('Registered', 'User'),
                                'callback' => new Callback(array($this,'prepareFields')),
                            ),
                        ),
                    ));
            $table->attach($users);
            return $table->render();
        } else {
            event('empty');
        }
    }

    /**
     * Prepare login field for table
     *
     * @param type $user
     * @return type
     */
    public function prepareFields($user,$key){
        switch($key){
            case 'login':
                return $user->getLink('avatar').' '.$user->getLink('profile');
                break;
            case 'reg_date':
                return df($user->reg_date,'d M Y');
                break;
            case 'posts':
                return '<a href="'.l('/blog/'.$user->login).'" class="badge'.($user->posts > 0 ? ' badge-info' : '').'">'.$user->posts.'</a>';
                break;
            case 'comments':
                return '<a href="'.l('/comments/'.$user->login).'" class="badge'.($user->comments > 0 ? ' badge-warning' : '').'">'.$user->comments.'</a>';
                break;
        }
    }

}