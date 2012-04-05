<?php

/**
 * Database ORM
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Db_ORM extends Object {

    /**
     * Table name
     *
     * @var string
     */
    protected $table;
    /**
     * Primary field name
     *
     * @var primary
     */
    protected $primary;
    /**
     * Fields
     *
     * @var array
     */
    protected $fields = array();
    
    /**
     * Filters before save to DB
     * 
     * 'field' => array('filter1',…,'filterN'),
     * 
     * Pay attention that filter must be a real existing callback
     * 
     * @var array
     */
    protected $filters_in = array();
    /**
     * Filters after load from DB
     * 
     * 'field' => array('filter1',…,'filterN'),
     * 
     * Pay attention that filter must be a real existing callback
     * 
     * @var array
     */
    protected $filters_out = array();
    protected $reflection;
    const FILTER_IN = 0;
    const FILTER_OUT = 1;

    /**
     * Constructir
     *
     * @param string $table
     * @param string $primary
     */
    public function __construct($table = NULL, $primary = NULL) {
        parent::__construct();
        $this->clear();
        $table && $this->table = $table;
        $this->fields = cogear()->db->getFields($this->table);
        $this->reflection = new ReflectionClass($this);
        $fields = array_keys((array)$this->fields);
        $first = reset($fields);
        $this->primary = $primary ? $primary : $first;
    }

    /**
     * Magic __set method
     * 
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value) {
        $this->object->$name = $value;
    }

    /**
     * Magic __get method
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
        /*
         * Some unusual patch
         *
         * Sometimes object becomes NULL. Need to find this place and fix. PHP < 5.3 Only
         * After all it's possible to left just "return $this->object->$name;" over there.
         *
         */
       return is_null($this->object) ? NULL : $this->object->$name;
    }
    /**
     * Check object variable for existance
     * 
     * @param string $name 
     */
    public function __isset($name) {
        return isset($this->object->$name);
    }
    /**
     * Unset object param
     * 
     * @param string $name
     */
    public function __unset($name) {
        if(isset($this->object->$name)){
            unset($this->object->$name);
        }
    }
    /**
     * Magic __call method
     *
     * Simple adapter to database.
     * Example:
     * 
     * $user_orm = new Db_ORM('users');
     * $user = $user_orm->where('name','admin')->find();
     *
     * @param string $name
     * @param array $args
     * @return mixed
     */
    public function __call($name, $args) {
        $cogear = getInstance();
        if (method_exists($cogear->db, $name)) {
            return call_user_func_array(array($cogear->db, $name), $args);
        }
        return NULL;
    }

    /**
     * Find row
     *
     * @return  object/NULL
     */
    public function find() {
        $cogear = getInstance();
        if ($this->object) {
            $cogear->db->where($this->object->toArray());
        }
        if ($result = $cogear->db->get($this->table)->row()) {
            event('Db_ORM.find',$result);
            $this->object = $this->filter($result,self::FILTER_OUT);
        }
        return $result;
    }

    /**
     * Find all
     *
     * @return object/NULL
     */
    public function findAll() {
        $cogear = getInstance();
        if ($this->object) {
            $cogear->db->where($this->object->toArray());
        }
        if($result = $cogear->db->get($this->table)->result()){
            foreach($result as &$element){
                $element = $this->filter($element,self::FILTER_OUT);
            }
        }
        return $result;
    }
    
    /**
     * Count matched elements
     * 
     * @return  int
     */
    public function count(){
        $cogear = getInstance();
        return $cogear->db->count($this->table);
    }
    
    /**
     * Filter data
     * 
     * @param object $data
     * @param int $type
     * @return object
     */
    public function filter($data,$type = 0){
        // Fullfill filters
        switch($type){
            case self::FILTER_IN:
                $filters = $this->filters_in;
                break;
            case self::FILTER_OUT:
            default:
                $filters = $this->filters_out;
        }
        // Seeking through the data
        foreach($data as $field=>$value){
            // If filter isset for field
            if(isset($filters[$field])){
                // Seeking through filters
                foreach($filters[$field] as $callback){
                    // Apply filter if it's callable
                    if(is_callable($callback)){
                        $data[$field] = call_user_func_array($callback, array($value));
                    }
                }
            }
        }
        return $data;
    }
    /**
     * Save
     *
     * @return boolean|int|NULL  No object|Insert|Update
     */
    public function save() {
        $cogear = getInstance();
        if (!$data = $this->object->toArray()) {
            return FALSE;
        } elseif (!isset($data[$this->primary]) OR !$cogear->db->get_where($this->table, array($this->primary => $data[$this->primary]))->row()) {
            event('Db_ORM.insert.before',$this);
            $data = $this->filter($data,self::FILTER_IN);
            $this->object->{$this->primary} = $this->insert($data);
            event('Db_ORM.insert.after',$this);
            return $this->object->{$this->primary};
        } else {
            event('Db_ORM.update.before',$this);
            $data = $this->filter($data,self::FILTER_IN);
            $this->update($data);
            event('Db_ORM.update.after',$this);
            return NULL;
        }
    }
    /**
     * Insert
     * 
     * @param   array   $data
     * @return 
     */
    public function insert($data = NULL){
        $data OR $data = $this->object->toArray();
        if(!$data) return;
        $cogear = getInstance();
        event('Db_ORM.insert',$this);
        return $cogear->db->insert($this->table, $data);
    }
    
    /**
     * Simple update
     * 
     * @param   array   $data
     * 
     */
    public function update($data = NULL){
        $data OR $data = $this->object->toArray();
        if(!$data OR !isset($data[$this->primary])) return;
        $cogear = getInstance();
        event('Db_ORM.update',$this);
        if(isset($data[$this->primary])){
            $primary = $data[$this->primary];
            unset($data[$this->primary]);
        }
        return $cogear->db->update($this->table, $data, array($this->primary => $primary));
    }

    /**
     * Delete
     */
    public function delete() {
        $cogear = getInstance();
        $primary = $this->primary;
        $data = $this->object->toArray();
        if (!$data) {
            return;
        } elseif (!isset($data[$primary])) {
            event('Db_ORM.delete.before',$this);
            $cogear->db->delete($this->table, $data);
        } else {
            event('Db_ORM.delete.before',$this);
            $cogear->db->delete($this->table, array($primary => $data[$primary]));
        }
        event('Db_ORM.delete.after',$this);
    }
    /**
     * Merge existing object with new data
     * 
     * @param array $data 
     */
    public function merge($data = array()){
        $data && $this->object->mix($data);
    }
    /**
     * Clear current object
     */
    public function clear() {
        $this->object = new Core_ArrayObject();
    }

}