<?php

/**
 * Abstract Database Driver
 * 
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          Db
 * @version		$Id$
 */
abstract class Db_Driver_Abstract {

    /**
     * Query builder
     *
     * @var array
     */
    protected $_query = array(
        'select' => array(),
        'insert' => array(),
        'update' => array(),
        'delete' => NULL,
        'from' => array(),
        'join' => array(),
        'where' => array(),
        'or_where' => array(),
        'where_in' => array(),
        'group' => array(),
        'having' => array(),
        'order' => array(),
        'limit' => array(),
    );

    /**
     * Swap query
     * 
     * @var array
     */
    protected $_swap = array();

    /**
     * Compiled query
     *
     * @var string
     */
    protected $query;

    /**
     * Queries
     *
     * @var array
     */
    public static $queries = array();

    /**
     * Result
     *
     * @var object
     */
    protected $result;

    /**
     * Database fields
     *
     * @var array
     */
    protected $fields = array();

    /**
     * Output errors or not
     * @var boolean
     */
    protected $silent;

    /**
     * Errors
     * 
     * @var array
     */
    protected $errors = array();

    /**
     * Configuration
     *
     * @var array
     */
    protected $config = array(
        'host' => 'localhost',
        'port' => 3306,
        'user' => 'root',
        'pass' => NULL,
        'database' => 'cogear',
        'prefix' => '',
        'socket' => NULL,
    );

    /**
     * Database connection
     *
     * @var resource
     */
    protected $connection;

    /**
     * If this flag is off query elements will be saved after it's execution
     * 
     * Useful for count query rows
     * 
     * @var boolean 
     */
    protected $qr_flag = TRUE;

    /**
     * Constructor
     *
     * @param array $config
     */
    public function __construct(array $config) {
        $this->config = array_merge($this->config, $config);
        $this->_swap = $this->_query;
    }
    
    /**
     * Init driver
     */
    public function init(){
        return $this->open();
    }

    /**
     * Open database connection
     */
    public function open() {
        if (!$this->connect()) {
              error(t(Db_Gear::$error_codes[101],'Db.errors'));
              return FALSE;
        }
        return TRUE;
    }

    /**
     * Connect to database
     *
     * @return  resource
     */
    abstract protected function connect();
    /**
     * Desctructor
     */
    public function __destruct() {
        $this->connection && $this->close();
    }

    /**
     * Handle params
     *
     * @param string|arrray $data
     */
    protected function parse($data) {
        if (is_string($data)) {
            $data = preg_split('[\s,]', $data, -1, PREG_SPLIT_NO_EMPTY);
        }
        return $data;
    }

    /**
     * Add elements to query builder
     *
     * @param string $type
     * @param array $values
     * @param boolean   $escape
     */
    protected function addQuery($type, $values, $escape = TRUE) {
        $values = $this->parse($values);
        if (is_array($values)) {
            if ($escape) {
                foreach ($values as &$value) {
                    $value = $this->escape($value);
                }
            }
            return $this->_query[$type] = array_merge($this->_query[$type], $values);
        } else {
            $escape && $value = $this->escape($value);
            return $value;
        }
    }

    /**
     * Show errors or be silent
     *
     * @return Db_Driver_Abstract
     */
    public function silent() {
        $this->silent = $this->silent ? NULL : TRUE;
        return $this;
    }

    /**
     * Transform arguments array into string
     *
     * @param   array $args
     * @param   string  $glue
     * @return  string
     */
    protected function argsToString($args, $condition = ' = ', $glue = ' AND ') {
        $query = array();
        foreach ($args as $key => $value) {
            $query[] = $key . ' ' . trim($condition) . ' "' . $this->escape($value) . '" ';
        }
        return implode($glue, $query);
    }

    /**
     * Prepare table name
     *
     * @param string $name
     * @return string
     */
    protected function prepareTableName($name) {
        return $this->config['prefix'] . $name;
    }

    /**
     * Prepare values
     *
     * @param array $values
     * @return string
     */
    protected function prepareValues(array $values, $isolator='"') {
        $result = array();
        foreach ($values as $key => $value) {
            $result[] = is_numeric($key) ? $isolator . $value . $isolator : $key . ' = ' . $isolator . $value . $isolator;
        }
        return implode(', ', $result);
    }


    /**
     * SELECT subquery
     *
     * @param   string|array $fields
     * @param   boolean      $escape
     * @return object   Self intsance.
     */
    public function select($fields, $escape = FALSE) {
        $this->fields OR $this->fields = $this->getFields($table);
        $this->addQuery('select', $fields, $escape);
        return $this;
    }

    /**
     * FROM subquery
     *
     * @param   string  $table
     * @return object   Self intsance.
     */
    public function from($table) {
        $this->addQuery('from', $table);
        return $this;
    }

    /**
     * WHERE subquery
     *
     * @param   string|array    $name
     * @param   string $value
     * @return object   Self intsance.
     */
    public function where($name, $value = NULL, $condition = ' = ') {
        if (is_array($name)) {
            $this->addQuery('where', $name, $condition);
        } else {
            $name .= ' ' . trim($condition);
            $this->addQuery('where', array($name => $value));
        }
        return $this;
    }

    /**
     * OR WHERE subquery
     *
     * @param   string|array    $name
     * @param   string $value
     * @return object   Self intsance.
     */
    public function or_where($name, $value = NULL, $condition = ' = ') {
        if (is_array($name)) {
            $this->addQuery('or_where', $name, $condition);
        } else {
            $name .= ' ' . trim($condition);
            $this->addQuery('or_where', array($name => $value));
        }
        return $this;
    }

    /**
     * WHERE IN subquery
     *
     * @param   string  $name
     * @param   array   $values
     * @return object   Self intsance.
     */
    public function whereIn($name, $values) {
        $this->addQuery('where_in', $values);
        return $this;
    }

    /**
     * GROUP BY subquery
     *
     * @param   string|array  $name
     * @return object   Self intsance.
     */
    public function group($name) {
        $this->addQuery('group', $name);
        return $this;
    }

    /**
     * ORDER subquery
     *
     * @param   string|array  $name
     * @return object   Self intsance.
     */
    public function order($name, $type = 'ASC') {
        $this->addQuery('order', $name . ' ' . $type);
        return $this;
    }

    /**
     * HAVING subquery
     *
     * @param   string|array    $name
     */
    public function having($name) {
        $this->addQuery('having', $name);
        return $this;
    }

    /**
     * JOIN subquery
     *
     * @param string $table
     * @param string|array $fields
     * @param string $type
     * @return object   Self intsance.
     */
    public function join($table, $fields, $type='') {
        $type = strtoupper($type);
        $query = $type . ' JOIN ' . $table . ' ON ' . $this->argsToString($fields);
        $this->addQuery('join', $query);
        return $this;
    }

    /**
     * LIKE subquery
     *
     * @param   string  $name
     * @param   string  $value
     * @return object   Self intsance.
     */
    public function like($name, $value, $type = 'before') {
        $value = $this->escape($value);
        switch ($type) {
            case 'both':
                $like = 'LIKE %' . $value . '%';
                break;
            case 'after':
                $like = 'LIKE ' . $value . '%';
                break;
            default:
            case 'before':
                $like = 'LIKE %' . $value;
        }
        $this->where($name, $like);
        return $this;
    }

    /**
     * LIMIT subquery
     *
     * @param   int $start
     * @param   int $offset
     * @return object   Self intsance.
     */
    public function limit($start = 0, $offset = 0) {
        $this->addQuery('limit', array($start, $offset));
        return $this;
    }

    /**
     * Execute query
     *
     * @param   string  $table
     * @param   int     $limit
     * @param   int     $offset
     * @return  object  Self instance.
     */
    public function get($table, $limit=0, $offset=0) {
        $this->from($table);
        $limit && $this->limit($limit, $offset);
        $this->buildQuery();
        $this->query($this->query);
        return $this;
    }

    /**
     * Get where
     *
     * @param string $table
     * @param array $where
     * @param int $limit
     * @param int $offset
     * @return object   Self instance.
     */
    public function get_where($table, $where, $limit=0, $offset=0) {
        $this->where($where);
        $this->get($table, $limit, $offset);
        return $this;
    }

    /**
     * Count rows
     * 
     * @param   string  $table
     * @param   string  $field
     */
    public function count($table, $field = '*', $reset = FALSE) {
        $this->swap('select');
        $this->qr_flag = $reset;
        $this->select('COUNT(' . $field . ') as count');
        $row = $this->get($table)->row();
        $this->swap('select');
        $this->qr_flag = TRUE;
        return $row->count;
    }

    /**
     * INSERT statement
     *
     * @param   string  $table
     * @param   array   $values
     * @return  int     Last insert id.
     */
    public function insert($table, $values) {
        $this->fields OR $this->fields = $this->getFields($table);
        $this->from($table);
        $this->addQuery('insert', $values);
        $this->query();
        return $this->getInsertId();
    }

    /**
     * Get last insert id
     *
     * @return  int
     */
    abstract public function getInsertId();

    /**
     * UPDATE statement
     *
     * @param   string  $table
     * @param   array   $values
     * @param   string|array   $where
     */
    public function update($table, array $values, $where) {
        $this->fields OR $this->fields = $this->getFields($table);
        $this->from($table);
        $this->addQuery('update', $values);
        $this->where($where);
        return $this->query();
    }

    /**
     * DELETE statement
     *
     * @param   string  $table
     * @param   string|array    $where
     */
    public function delete($table, $where = array()) {
        $this->_query['delete'] = TRUE;
        $this->from($table);
        $where && $this->where($where);
        return $this->query();
    }

    /**
     * Result
     *
     * @return  Core_ArrayObject    Result.
     */
    abstract public function result();

    /**
     * Row
     *
     * @return  Core_ArrayObject    Row.
     */
    abstract public function row();

    /**
     * Execute query
     *
     * @param   string  $query
     * @return  object
     */
    abstract public function query($query = '');

    /**
     * Build query
     * 
     * @return  string
     */
    abstract public function buildQuery();

    /**
     * Start transaction
     */
    abstract public function transaction();

    /**
     * Commit transaction
     */
    abstract public function commit();

    /**
     * Escape value
     *
     * @param   string  $value
     * @return  string
     */
    abstract public function escape($value);

    /**
     * Grab table for fields
     * 
     * @param string $table
     * @return array 
     */
    public function getFields($table = '') {
        $table OR $table = reset($this->_query['from']);
        if (!$this->fields[$table] = cogear()->system_cache->read('database/' . $table, TRUE)) {
            if ($fields = $this->getFieldsQuery($table)) {
                $this->fields[$table] = array();
                foreach ($fields as $field) {
                    $this->fields[$table][$field->Field] = $field->Type;
                }
                cogear()->system_cache->write('database/' . $table, $this->fields[$table], array('db_fields'));
            }
        }
        return $this->fields[$table];
    }

    /**
     * Filter input assoc array corresponing to fields
     *
     * @param   string  $table
     * @param   array   $values
     */
    protected function filterFields($table, $values) {
        $result = array();
        $fields = isset($this->fields[$table]) ? $this->fields[$table] : $this->fields[$table] = $this->getFields($table);
        foreach ($values as $key => $value) {
            $key = preg_replace('/[^\w_-]/', '', $key);
            if (isset($fields[$key])) {
                $type = preg_replace('/[^a-z]/', '', $fields[$key]);
                switch ($type) {
                    case 'int':
                    case 'tinyint':
                    case 'smallint':
                    case 'mediumint':
                    case 'bigint':
                        $result[$key] = (int) $value;
                        break;
                    case 'float':
                        $result[$key] = (float) $value;
                        break;
                    case 'double':
                        $result[$key] = (double) $value;
                        break;
                    case 'date':
                        $result[$key] = date('Y-m-d', strtotime($value));
                        break;
                    case 'time':
                        $result[$key] = date('H:i:s', strtotime($value));
                        break;
                    case 'datetime':
                    case 'timestamp':
                        $result[$key] = date('Y-m-d H:i:s', strtotime($value));
                        break;
                    case 'year':
                        $result[$key] = date('Y', strtotime($value));
                        break;
                    case 'char':
                    case 'varchar':
                        $result[$key] = (string) $value;
                        break;
                    default:
                        $result[$key] = $value;
                }
            }
        }
        return $result;
    }

    /**
     * Get table fields query and result
     *
     * @return  Core_ArrayObject
     */
    abstract public function getFieldsQuery($table);

    /**
     * Disconnect from database
     */
    abstract protected function disconnect();

    /**
     * Close database connection
     */
    public function close() {
        $this->disconnect();
    }

    /**
     * Swap query
     * 
     * @param string $type 
     */
    public function swap($type = NULL) {
        if (!$type) {
            $buffer = $this->_query;
            $this->_query = $this->_swap;
            $this->_swap = $buffer;
        } elseif (isset($this->_query[$type])) {
            $buffer = $this->_query[$type];
            $this->_query[$type] = $this->_swap[$type];
            $this->_swap[$type] = $buffer;
        }
    }

    /**
     * Clear query
     */
    public function clear() {
        if ($this->qr_flag) {
            $this->_query = array(
                'select' => array(),
                'insert' => array(),
                'update' => array(),
                'delete' => NULL,
                'from' => array(),
                'join' => array(),
                'where' => array(),
                'or_where' => array(),
                'where_in' => array(),
                'group' => array(),
                'having' => array(),
                'order' => array(),
                'limit' => array(),
            );
        }
    }

    /**
     * Query time benchmark start
     *
     * @param string $query
     */
    public static function start($query) {
        self::$queries[$query]['start'] = microtime();
    }

    /**
     * Query time benchmark stop
     *
     * @param string $query
     */
    public static function stop($query) {
        self::$queries[$query]['stop'] = microtime();
    }

    /**
     * Get errors
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * Get last query
     *
     * @return string
     */
    public function getLastQuery() {
        $queries = $this->getQueries();
        return array_pop($queries);
    }

    /**
     * Get all executed queries
     *
     * @return array
     */
    public function getQueries() {
        return array_keys(self::$queries);
    }

    /**
     * Get queries
     *
     * @return array
     */
    public function getBenchmark() {
        $queries = array();
        foreach (self::$queries as $query => $time) {
            $queries[$query] = $time['stop'] - $time['start'];
        }
        return $queries;
    }

    public function createTable($table, $fields) {
    }

    public function dropTable($table, $if_exists) {
        
    }

    public function createFields($fields) {
        
    }

    public function alterTable($table, $fields) {
        
    }

    public function alterFields($fields) {
        
    }

}
