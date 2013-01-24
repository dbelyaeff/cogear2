<?php

/**
 * Database ORM
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011-2013, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Db_ORM extends Object {

    /**
     * Gather loaded items
     *
     * @var type
     */
    protected static $cached = array();
    protected $cache_path = '';

    /**
     * Флаг кеширования
     * @var type
     */
    protected $caching = TRUE;

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
     * Database
     *
     * @var Object
     */
    protected $db;

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
    public $reflection;
    protected $class;
    public static $skipClear = FALSE;

    const FILTER_IN = 0;
    const FILTER_OUT = 1;

    /**
     * Constructir
     *
     * @param string $table
     * @param string {$this->primary}
     */
    public function __construct($table = NULL, $primary = NULL, $db = NULL) {
        parent::__construct();
        if (self::$skipClear) {
            self::skipClear(FALSE);
        } else {
            $this->clear();
        }
        if (!self::$cached) {
            self::$cached = new Core_ArrayObject();
        }
        $table && $this->table = $table;
        $this->db = $db instanceof Db_Object ? $db : cogear()->Db->object();
        $this->fields = $this->db->getFields($this->table);
        $this->reflection = new ReflectionClass($this);
        $this->class = $this->reflection->getName();
        $fields = array_keys((array) $this->fields);
        $first = reset($fields);
        $this->primary OR $this->primary = $primary ? $primary : $first;
        $this->cache_path = $this->db->options->database . '.' . $this->table;
        $this->object(new Core_ArrayObject());
    }

    /**
     * Set skip db reset before object init
     */
    public static function skipClear($set = TRUE) {
        self::$skipClear = $set;
    }

    /**
     * Magic __set method
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value) {
        $this->object instanceof Core_ArrayObject && $this->object()->$name = $value;
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
         * After all it's possible to left just "return $this->object()->$name;" over there.
         *
         */
        return $this->object ? $this->object()->$name : NULL;
    }

    /**
     * Check object variable for existance
     *
     * @param string $name
     */
    public function __isset($name) {
        return isset($this->object()->$name);
    }

    /**
     * Unset object param
     *
     * @param string $name
     */
    public function __unset($name) {
        if (isset($this->object()->$name)) {
            unset($this->object()->$name);
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
        $callback = array($this->db, $name);
        if (!$args) {
            $args = array($this->table);
        }
        if (is_callable($callback)) {
            $result = call_user_func_array($callback, $args);
            if ($result instanceof Db_Driver_Abstract) {
                return $this;
            }
            return $result;
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
        if ($this->object()->count()) {
            foreach ($this->fields as $key => $value) {
                isset($this->object()->$key) && $data[$key] = $this->object()->$key;
            }
        }
        $data = $this->filterData($data, self::FILTER_IN);
        return $data;
    }

    /**
     * Кеширование объектов
     *
     * @param type $id
     * @param type $object
     */
    public function cache($id, $object = NULL, $tags = array(), $ttl = 3600) {
        // Возможность переключения кеширования
        if (is_bool($id)) {
            $this->caching = $id;
            return;
        }
        // Если кеширования выключено и происходит запись — возвращаем NULL
        if (!$this->caching && NULL === $object) {
            return NULL;
        }
        cogear()->cache->removeTags($this->table);
        $key = $this->table . '.' . $id;
        if ($object) {
            return cache($key, $object, $tags, $ttl);
        } else {
            return $object === FALSE ? cogear()->cache->remove($key) : cache($key);
        }
    }

    /**
     * Find row
     *
     * @return  object/NULL
     */
    public function find() {
        $primary = $this->primary;
        if ($object = $this->cache($this->object()->$primary)) {
            $this->object = $this->filterData($object, self::FILTER_OUT);
            $this->clear();
            return TRUE;
        }
        if ($this->object()->count()) {
            if ($data = $this->getData()) {
                $this->db->where($data);
            }
            if ($result = $this->db->get($this->table)->row()) {
                event('Db_ORM.find', $this, $result);
                $this->cache($result->{$this->primary}, $result);
                $this->object = $this->filterData($result, self::FILTER_OUT);
                $this->clear();
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
        if ($this->object) {
            $this->db->where($this->getData());
        }
        if ($result = $this->db->get($this->table)->result()) {
            foreach ($result as &$element) {
                event('Db_ORM.findAll', $this, $result);
                $this->cache($element->{$this->primary}, $element);
                $element = $this->filterData($element, self::FILTER_OUT);
            }
            $this->clear();
        }
        return $result;
    }

    /**
     * Count matched elements
     *
     * @return  int
     */
    public function count($reset = FALSE) {
        if ($data = $this->getData()) {
            $this->db->object()->where($data);
        }
        return $this->db->object()->countAll($this->table, $this->table . '.' . $this->primary, $reset);
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
                if (!is_array($callback) && method_exists($this, $callback)) {
                    $callback = new Callback(array($this, $callback));
                } else {
                    $callback = new Callback($callback);
                }
                if ($callback->check()) {
                    !empty($data[$field]) && $data[$field] = $callback->run(array($data[$field]));
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
            if ($result = $this->update($data)) {
                return $result;
            }
        } elseif ($id = $this->insert($data)) {
            return $id;
        }
        return FALSE;
    }

    /**
     * Insert
     *
     * @param   array   $data
     * @return
     */
    public function insert($data = NULL) {
        if ($data) {
            $this->object()->adopt($data);
        } else {
            $data = $this->getData();
        }
        event('Db_ORM.insert', $this, $data);
        if ($result = $this->db->insert($this->table, $data)) {
            $this->object()->{$this->primary} = $result;
            $this->cache($this->{$this->primary}, FALSE, array(), 0);
        }
        return $result;
    }

    /**
     * Simple update
     *
     * @param   array   $data
     *
     */
    public function update($data = NULL) {
        if ($data) {
            $this->object()->extend($data);
        } else {
            $data = $this->getData();
        }
        if (isset($data[$this->primary])) {
            unset($data[$this->primary]);
        }
        event('Db_ORM.update', $this, $data);
        if ($result = $this->db->update($this->table, $data, array($this->primary => $this->{$this->primary}))) {
            $this->cache($this->{$this->primary}, FALSE, array(), 0);
        }
        return $result;
    }

    /**
     * Delete
     *
     * @return boolean
     */
    public function delete() {
        $cogear = getInstance();
        $data = $this->getData();
        $result = FALSE;
        if (!isset($data[$this->primary]) && $data) {
            $result = $this->db->delete($this->table, $data) ? TRUE : FALSE;
            event('Db_ORM.delete', $this, $data, $result);
        } elseif ($data) {
            $result = $this->db->delete($this->table, array($this->primary => $data[$this->primary])) ? TRUE : FALSE;
            event('Db_ORM.delete', $this, $data, $result);
        } else {
            $result = $this->db->delete($this->table) ? TRUE : FALSE;
            event('Db_ORM.delete', $this, $data, $result);
        }
        // Чистим кеш
        $this->cache($this->{$this->primary}, FALSE, array(), 0);
        $this->clear();
        return $result;
    }

    /**
     * Merge existing object with new data
     *
     * @param array $data
     */
    public function merge($data = array()) {
        $data && $this->object()->extend($data);
    }

}