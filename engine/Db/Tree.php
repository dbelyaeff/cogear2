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

    protected $path_field = 'path';
    protected $level_field = 'level';
    protected $parent_field = 'pid';
    const DELIM = '.';

    /**
     * Find all
     */
    public function findAll() {
        $this->order($this->path_field, 'ASC');
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
            $this->{$this->path_field} = $this->formPath();
            $this->{$this->level_field} = sizeof(explode(self::DELIM, $this->{$this->path_field}));
            $this->save();
        }
        return $id;
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
            if ($parent->find()) {
                $path = $parent->{$this->path_field} . self::DELIM . $this->{$this->primary};
            }
        }
        isset($path) OR $path = str_pad($this->id, 15, ' ', STR_PAD_LEFT);
        return $path;
    }

}