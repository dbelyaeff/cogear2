<?php

/**
 * Abstract database list
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
abstract class Db_List_Abstract extends Cogearable {

    protected $class;
    public $options = array(
        'name' => 'list',
        'page' => 0,
        'per_page' => 5,
        'base' => '',
        'page_suffix' => 'page',
        'where' => array(
        ),
        'where_in' => array(
        ),
        'or_where' => array(
        ),
        'like' => array(),
        'in_set' => array(),
        'fields' => array(),
        'order' => array('login', 'ASC'),
        'render' => 'content',
        'showEmpty' => TRUE,
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
        $item = new $this->class();
        $this->where && $this->db->where((array) $this->where);
        $this->or_where && $this->db->or_where((array) $this->or_where);
        $this->order && $this->db->order($this->order[0], $this->order[1]);
        if ($this->where_in) {
            foreach ($this->where_in->toArray() as $key => $value) {
                $this->db->where_in($key, $value);
            }
        }
        if ($this->in_set) {
            foreach ($this->in_set as $key => $value) {
                $this->db->in_set($key,$value);
            }
        }
        if ($this->like) {
            $i = 0;
            foreach ($this->like as $like) {
                $func = $i ? 'or_like' : 'like';
                cogear()->db->$func($like[0], $like[1], isset($like[2]) ? $like[2] : 'after');
                $i++;
            }
        }
        $pager = new Pager(array(
                    'current' => $this->page ? $this->page : NULL,
                    'count' => $item->count(),
                    'per_page' => $this->per_page,
                    'base' => $this->base,
                ));
        if ($items = $item->findAll()) {
            return $this->process($items, $pager);
        } else {
            $this->options->showEmpty !== FALSE && event('empty');
            return FALSE;
        }
    }

    /**
     * Render process
     *
     * @return string
     */
    abstract function process($items, $pager);
}