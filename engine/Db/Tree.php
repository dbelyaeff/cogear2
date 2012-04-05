<?php

class Db_Tree extends Db_Item implements Interface_Render {

    protected $path_field = 'path';
    protected $level_field = 'level';
    protected $parent_field = 'parent_id';
    const DELIM = '.';

    /**
     * Find all
     */
    public function findAll() {
        cogear()->db->order($this->path_field, 'asc');
        return parent::findAll();
    }

    /**
     * Insert data
     *
     * @param type $data 
     * @return  int
     */
    public function insert($data = NULL) {
        $this->{$this->primary} = parent::insert($data);
        $this->{$this->path_field} = $this->formPath();
        $this->{$this->level_field} = sizeof(explode(self::DELIM,$this->{$this->path_field}));
        $this->save();
        return $this->{$this->primary};
    }

    /**
     * Form materialized path
     * 
     * @return string
     */
    public function formPath() {
        if ($this->{$this->parent_field}) {
            $parent = new Db_ORM($this->table, $this->primary);
            $parent->{$this->primary} = $this->{$this->parent_field};
            if($parent->find()){
               $path = $parent->{$this->path_field}.self::DELIM.$this->{$this->primary}; 
            }
        }
        isset($path) OR $path = str_pad($this->id,15,' ',STR_PAD_LEFT);
        return $path;
    }

}