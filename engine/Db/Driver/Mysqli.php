<?php
/**
 * MySQLi Database Driver
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          Db
 * @version		$Id$
 */
class Db_Driver_Mysqli extends Db_Driver_Mysql {
    /**
     * Connect to database
     *
     * @return boolean
     */
    public function connect() {
        $this->connection = mysqli_connect($this->config['host'] . ':' . $this->config['port'], $this->config['user'], $this->config['pass']);
        mysqli_select_db($this->connection,$this->config['database']);
        $this->query('SET NAMES utf8;');
        return $this->connection ? TRUE : FALSE;
    }

    /**
     * Disconnect from database
     *
     * @return boolean
     */
    public function disconnect() {
        return mysqli_close($this->connection);
    }

    /**
     * Execute query
     *
     * @param string $query
     * @return Db_Driver_Mysql
     */
    public function query($query = '') {
        if (!$query) {
            $query = $this->buildQuery();
        }
        self::start($query);
        if (!$this->result = mysqli_query($this->connection,$query)) {
            $this->silent OR $this->errors[] = mysqli_errno($this->connection);
        }
        $this->clear();
        self::stop($query);
        return $this->errors ? FALSE : $this;
    }

    /**
     * Get fields from table
     *
     * @param string $table
     * @return object
     */
    public function getFieldsQuery($table) {
        return  $this->query('SHOW COLUMNS FROM ' . $table) ? $this->result() : NULL;
    }


    /**
     * Result
     *
     * @return Core_ArrayObject|NULL
     */
    public function result() {
        $result = array();
        if ($this->result) {
            while ($row = mysqli_fetch_assoc($this->result)) {
                $result[] = $row;
            }
        }
        return $result ? Core_ArrayObject::transform($result) : NULL;
    }

    /**
     * Row
     *
     * @return Core_ArrayObject|NULL
     */
    public function row() {
        return $this->result ? Core_ArrayObject::transform(mysqli_fetch_assoc($this->result)) : NULL;
    }

    /**
     * Prepare variable for statement
     *
     * @param string $value
     * @return string
     */
    public function escape($value) {
        return mysqli_real_escape_string($this->connection,$value);
    }

    /**
     * Get last insert id
     *
     * @return int
     */
    public function getInsertId() {
        return mysqli_insert_id($this->connection);
    }

    /**
     * Start transaction
     */
    public function transaction() {
        $this->query('SET AUTOCOMMIT=0');
        $this->query('START TRANSACTION');
    }

    /**
     * Commit transaction
     */
    public function commit() {
        $this->query('COMMIT');
        $this->query('SET AUTOCOMMIT=1');
    }
}