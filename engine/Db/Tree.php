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
    const DELIM = '.';

    /**
     * Find all
     */
    public function findAll() {
        $this->order($this->thread_field, 'DESC');
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
            $this->{$this->thread_field} = $this->formThread();
            $this->{$this->level_field} = sizeof(explode(self::DELIM, $this->{$this->thread_field}))-1;
            $this->update();
        }
        return $id;
    }

    /**
     * Insert data
     *
     * @param type $data
     * @return  int
     */
    public function update($data = NULL) {
        if ($result = parent::update($data)) {
            parent::update($this->getData());
        }
        return $result;
    }

    /**
     * Delete
     *
     * @return boolean
     */
    public function delete() {
        if ($result = parent::delete()) {
            $item = new self($this->table,$this->primary);
            $this->like($this->thread_field, $result->{$this->thread_field}, 'after');
            $item->delete();
        }
        return $result;
    }

    /**
     * Form materialized path
     *
     * @return string
     */
    public function formThread() {
        if ($this->{$this->parent_field}) {
            $parent = new self($this->table,$this->primary);
            $parent->{$this->primary} = $this->{$this->parent_field};
            if ($parent->find()) {
                $obj = new self($this->table,$this->primary);
                $obj->{$this->object_field} = $this->{$this->object_field};
                $obj->{$this->parent_field} = $this->{$this->parent_field};
                $thread = str_replace('/','',$parent->{$this->thread_field}) .self::DELIM. $obj->count(TRUE).'/';
            }
        } else {
            $obj = new self($this->table,$this->primary);
            $obj->{$this->object_field} = $this->{$this->object_field};
            $obj->{$this->parent_field} = 0;
            $thread = $obj->count(TRUE).'/';
        }
        return $thread;
    }

    /**
     * Get childs of current item
     *
     * @return  array
     */
    public function getChilds(){
        $item = new self($this->table,$this->primary);
        cogear()->db->like($this->thread_field,str_replace('/','',$this->{$this->thread_field}),'after');
        if($result = $item->findAll()){
            return $result;
        }
        return NULL;
    }

}