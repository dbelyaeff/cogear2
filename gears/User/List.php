<?php

/**
 * List of users
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage

 */
class User_List extends Db_List_Table {

    protected $class = 'User';
    protected $fields;
    protected $options = array(
        'name' => 'user-list',
        'page' => 0,
        'per_page' => 5,
        'base' => '',
        'page_suffix' => 'page',
        'where' => array(
            'login !=' => '',
        ),
        'like' => array(),
        'where_in' => array(),
        'fields' => array(),
        'order' => array('login', 'ASC'),
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
                        'login' => array(
                            'label' => t('Имя пользователя'),
                            'callback' => new Callback(array($this, 'prepareFields')),
                        ),
                        'posts' => array(
                            'label' => t('Публикаций'),
                            'callback' => new Callback(array($this, 'prepareFields')),
                            'class' => 't_c w10',
                        ),
                        'comments' => array(
                            'label' => t('Комментариев'),
                            'callback' => new Callback(array($this, 'prepareFields')),
                            'class' => 't_c w10',
                            'access' => (boolean)$this->gears->Comments,
                        ),
                        'reg_date' => array(
                            'label' => t('Зарегистрирован'),
                            'callback' => new Callback(array($this, 'prepareFields')),
                        ),
                    ));
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
                return $user->render('list','avatar.small');
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