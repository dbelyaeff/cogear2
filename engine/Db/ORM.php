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
     * Gather loaded items
     * 
     * @var type 
     */
    protected static $loaded_items = array();

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
    protected $primary = 'id';

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
        $fields = array_keys((array) $this->fields);
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
        return $this->object ? $this->object->$name : NULL;
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
        if (isset($this->object->$name)) {
            unset($this->object->$name);
        }
    }
    /**
     * Saving session
     */
    public function __sleep() {
        debug($this->fields);
        die('asdasd');
        return array();
    }
    /**
     * Restoring session
     */
    public function __wakeup() {
        die('wakeup');
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
        $primary = $this->primary;
        if ($this->object->$primary && isset(self::$loaded_items[$this->object->$primary])) {
            $this->object = self::$loaded_items[$this->object->$primary];
            return TRUE;
        } else
        if ($this->object) {
            $cogear->db->where($this->object->toArray());
        }
        if ($result = $cogear->db->get($this->table)->row()) {
            event('Db_ORM.find', $result);
            $this->object = $this->filter($result, self::FILTER_OUT);
            self::$loaded_items[$result->$primary] = $this->object;
            return TRUE;
        }
        return FALSE;
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
        if ($result = $cogear->db->get($this->table)->result()) {
            foreach ($result as &$element) {
                $element = $this->filter($element, self::FILTER_OUT);
            }
            $primary = $this->primary;
            self::$loaded_items[$result->$primary] = $result;
        }
        return $result;
    }

    /**
     * Count matched elements
     * 
     * @return  int
     */
    public function count() {
        return cogear()->db->count($this->table, $this->primary);
    }

    /**
     * Filter data
     * 
     * @param object $data
     * @param int $type
     * @return object
     */
    public function filter($data, $type = 0) {
        // Fullfill filters
        switch ($type) {
            case self::FILTER_IN:
                $filters = $this->filters_in;
                break;
            case self::FILTER_OUT:
            default:
                $filters = $this->filters_out;
        }
        // Seeking through the data
        foreach ($data as $field => $value) {
            // If filter isset for field
            if (isset($filters[$field])) {
                // Seeking through filters
                foreach ($filters[$field] as $callback) {
                    // Apply filter if it's callable
                    if (is_callable($callback)) {
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
        event('Db_ORM.save', $this);
        $data = $this->object->toArray();
        if (!$data) {
            return FALSE;
        } elseif (isset($data[$this->primary])) {
            event('Db_ORM.update', $this, $data);
            $data = $this->filter($data, self::FILTER_IN);
            $this->update($data);
            return TRUE;
        } else {
            event('Db_ORM.insert', $this, $data);
            $data = $this->filter($data, self::FILTER_IN);
            $this->object->{$this->primary} = $this->insert($data);
            return $this->object->{$this->primary};
        }
    }

    /**
     * Insert
     * 
     * @param   array   $data
     * @return 
     */
    public function insert($data = NULL) {
        $data OR $data = $this->object->toArray();
        if (!$data)
            return;
        $cogear = getInstance();
        event('Db_ORM.insert', $data);
        return $cogear->db->insert($this->table, $data);
    }

    /**
     * Simple update
     * 
     * @param   array   $data
     * 
     */
    public function update($data = NULL) {
        $data OR $data = $this->object->toArray();
        if (!$data OR !isset($data[$this->primary]))
            return;
        $cogear = getInstance();
        event('Db_ORM.update', $data);
        return $cogear->db->update($this->table, $data, array($this->primary => $data[$this->primary]));
    }

    /**
     * Delete
     * 
     * @return boolean
     */
    public function delete() {
        $cogear = getInstance();
        $primary = $this->primary;
        $data = $this->object->toArray();
        if (!$data) {
            return;
        } elseif (!isset($data[$primary])) {
            event('Db_ORM.delete.before', $this);
            return $cogear->db->delete($this->table, $data) ? TRUE : FALSE;
        } else {
            event('Db_ORM.delete.before', $this);
            return $cogear->db->delete($this->table, array($primary => $data[$primary])) ? TRUE : FALSE;
        }
        event('Db_ORM.delete.after', $this);
    }

    /**
     * Merge existing object with new data
     * 
     * @param array $data 
     */
    public function merge($data = array()) {
        $data && $this->object->mix($data);
    }
}