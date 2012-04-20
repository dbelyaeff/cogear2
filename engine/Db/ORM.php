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
        $this->object instanceof Core_ArrayObject && $this->object->$name = $value;
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
     * Filter current object with fields
     *
     * @return array
     */
    public function getData() {
        $data = array();
        if ($this->object->count()) {
            foreach ($this->fields as $key => $value) {
                isset($this->object->$key) && $data[$key] = $this->object->$key;
            }
        }
        return $data;
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
        if ($this->object->count()) {
            $cogear->db->where($this->getData());
            if ($result = $cogear->db->get($this->table)->row()) {
                event('Db_ORM.find', $this, $result);
                $this->object = $this->filterData($result, self::FILTER_OUT);
                self::$loaded_items[$result->$primary] = $this->object;
                return TRUE;
            }
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
            $cogear->db->where($this->getData());
        }
        if ($result = $cogear->db->get($this->table)->result()) {
            foreach ($result as &$element) {
                event('Db_ORM.findAll', $this, $result);
                $element = $this->filterData($element, self::FILTER_OUT);
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
    public function filterData($data, $type = 0) {
        // Fullfill filters
        switch ($type) {
            case self::FILTER_IN:
                $filters = $this->filters_in;
                break;
            case self::FILTER_OUT:
            default:
                $filters = $this->filters_out;
        }
        // Set scope to $this
        foreach ($filters as $field => $filter) {
            foreach ($filter as $key => $callback) {
                is_string($callback) && $filters[$field][$key] = array($this, $callback);
            }
        }
        // Seeking through the data
        foreach ($data as $field => $value) {
            // If filter isset for field
            if (isset($filters[$field])) {
                // Seeking through filters
                foreach ($filters[$field] as $callback) {
                    $callback = new Callback($callback);
                    // Apply filter if it's callable
                    if ($callback->check()) {
                        $data[$field] = $callback->run(array($value));
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
        $data = $this->getData();
        if (!$data) {
            return FALSE;
        } elseif (isset($data[$this->primary])) {
            $data = $this->filterData($data, self::FILTER_IN);
            if ($this->update($data)) {
                event('Db_ORM.update', $this);
                return TRUE;
            }
        } else {
            $data = $this->filterData($data, self::FILTER_IN);
            if ($this->insert($data)) {
                event('Db_ORM.insert', $this);
                return $this->object->{$this->primary};
            }
        }
    }

    /**
     * Insert
     *
     * @param   array   $data
     * @return
     */
    public function insert($data = NULL) {
        $data OR $data = $this->getData();
        if (!$data)
            return;
        $cogear = getInstance();
        event('Db_ORM.insert', $data);
        return $this->object->{$this->primary} = $cogear->db->insert($this->table, $data);
    }

    /**
     * Simple update
     *
     * @param   array   $data
     *
     */
    public function update($data = NULL) {
        $data OR $data = $this->getData();
        event('Db_ORM.update', $data);
        $primary = $this->primary;
        return cogear()->db->update($this->table, $data, array($this->primary => $this->$primary));
    }

    /**
     * Delete
     *
     * @return boolean
     */
    public function delete() {
        $cogear = getInstance();
        $primary = $this->primary;
        $data = $this->getData();
        $result = FALSE;
        if (!$data) {
            return FALSE;
        } elseif (!isset($data[$primary])) {
            event('Db_ORM.delete.before', $this);
            $result = $cogear->db->delete($this->table, $data) ? TRUE : FALSE;
        } else {
            event('Db_ORM.delete.before', $this);
            $result = $cogear->db->delete($this->table, array($primary => $data[$primary])) ? TRUE : FALSE;
        }
        return $result;
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