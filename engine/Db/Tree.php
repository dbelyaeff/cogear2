<?php

/**
 * Database Tree
 *
 * Provided by matherialized path
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          Db
 * @version		$Id$
 */
class Db_Tree extends Db_Item {

    protected $thread_field = 'thread';
    protected $level_field = 'level';
    protected $parent_field = 'pid';
    protected $object_field = 'post_id';
    public $order = self::FORWARD;
    const FORWARD = 1;
    const BACKWARD = -1;
    const DELIM = '.';

    /**
     * Find all
     */
    public function findAll() {
        switch ($this->order) {
            case self::FORWARD:
                $order = 'ASC';
                break;
            case self::BACKWARD:
                $order = 'DESC';
                break;
        }
        $this->order && $this->order($this->thread_field, $order);
        return parent::findAll();
    }

    /**
     * Insert data
     *
     * @param type $data
     * @return  int
     */
    public function insert($data = NULL) {
        if ($id = parent::insert($data)) {
            $this->branching();
            parent::update(array($this->thread_field => $this->{$this->thread_field},$this->level_field => $this->{$this->level_field}));
        }
        return $id;
    }

    /**
     * Delete
     *
     * @return boolean
     */
    public function delete() {
        if ($result = parent::delete()) {
            if ($childs = $this->getChilds()) {
                foreach ($childs as $child) {
                    $child->delete();
                }
            }
        }
        return $result;
    }

    /**
     * Form materialized path
     *
     * @return string
     */
    public function branching() {
        if ($this->{$this->parent_field}) {
            $parent = new $this->class($this->table,$this->primary,$this->db);
            $parent->{$this->primary} = $this->{$this->parent_field};
            if ($parent->find()) {
                $obj = new $this->class;
                $obj->{$this->object_field} = $this->{$this->object_field};
                $obj->{$this->parent_field} = $this->{$this->parent_field};
                switch ($this->order) {
                    case self::FORWARD:
                        $data = $parent->{$this->thread_field};
                        $count = $obj->count(TRUE);
                        break;
                    case self::BACKWARD:
                        $data = str_replace('/', '', $parent->{$this->thread_field});
                        $count = 1000 - $obj->count(TRUE);
                        break;
                }
                $this->{$this->thread_field} = $data . self::DELIM . str_pad($count, 3, 0, STR_PAD_LEFT);
                $this->{$this->level_field} = 1 + $parent->{$this->level_field};
            }
        } else {
            $obj = new $this->class;
            $obj->{$this->object_field} = $this->{$this->object_field};
            $obj->{$this->parent_field} = 0;
            $this->{$this->level_field} = 0;
            $this->{$this->thread_field} = str_pad($obj->count(TRUE), 3, 0, STR_PAD_LEFT);
            $this->{$this->thread_field} = str_pad($this->{$this->thread_field}, 25, ' ', STR_PAD_LEFT);
        }
        if ($this->order == self::BACKWARD) {
            $this->{$this->thread_field} .= '/';
        }
    }

    /**
     * Get childs of current item
     *
     * @return  array
     */
    public function getChilds() {
        $obj = new $this->class;
        $obj->{$this->object_field} = $this->{$this->object_field};
        switch ($this->order) {
            case self::FORWARD:
                $data = $this->{$this->thread_field};
                break;
            case self::BACKWARD:
                $data = str_replace('/', '', $this->{$this->thread_field});
                break;
        }
        cogear()->db->like($this->thread_field, $data, 'after');
        if ($result = $obj->findAll()) {
            return $result;
        }
        return $result;
    }

    /**
     * Get parents of current item
     *
     * @return  array
     */
    public function getParents() {
        $obj = new $this->class;
        $obj->{$this->object_field} = $this->{$this->object_field};
        $threads = explode(self::DELIM,$this->{$this->thread_field});
        $parents = array();
        if(sizeof($threads) > 0){
            $current = '';
            foreach($threads as $thread){
                if(!$current){
                    $current = $thread;
                }
                else {
                    $current .= self::DELIM.$thread;
                }
                if($current == $this->{$this->thread_field}){
                    continue;
                }
                $parent = new $this->class;
                $parent->{$this->thread_field} = $current;
                $parent->{$this->object_field} = $this->{$this->object_field};
                if($parent->find()){
                    $parents[] = $parent;
                }
            }
        }
        return $parents;
    }

}