<?php

/**
 * Mysql Database Driver
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          Db
 * @version		$Id$
 */
class Db_Driver_Mysql extends Db_Driver_Abstract {
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
	/**
	 * Append a database prefix to all tables in a query.
	 *
	 * Queries sent to Drupal should wrap all table names in curly brackets. This
	 * function searches for this syntax and adds Drupal's table prefix to all
	 * tables, allowing Drupal to coexist with other systems in the same database if
	 * necessary.
	 *
	 * @param $sql
	 *   A string containing a partial or entire SQL query.
	 * @return
	 *   The properly-prefixed string.
	 */
	
	function db_prefix_tables($sql) {
	  $db_prefix = $this->config['prefix'];

	  if (is_array($db_prefix)) {
		if (array_key_exists('default', $db_prefix)) {
		  $tmp = $db_prefix;
		  unset($tmp['default']);
		  foreach ($tmp as $key => $val) {
			$sql = strtr($sql, array('{'. $key .'}' => $val . $key));
		  }
		  return strtr($sql, array('{' => $db_prefix['default'], '}' => ''));
		}
		else {
		  foreach ($db_prefix as $key => $val) {
			$sql = strtr($sql, array('{'. $key .'}' => $val . $key));
		  }
		  return strtr($sql, array('{' => '', '}' => ''));
		}
	  }
	  else {
		return strtr($sql, array('{' => $db_prefix, '}' => ''));
	  }
	}
	
	/**
	 * Create a new table from a Drupal table definition.
	 *
	 * @param $ret
	 *   Array to which query results will be added.
	 * @param $name
	 *   The name of the table to create.
	 * @param $table
	 *   A Schema API table definition array.
	 */
	
	public function db_create_table(&$ret, $name, $table) {
		print_r($table);
	  $statements = $this->db_create_table_sql($name, $table);
	  foreach ($statements as $statement) {
		$ret[] = $this->update_sql($statement);
	  }
	}
	
	/**
	 * Perform an SQL query and return success or failure.
	 *
	 * @param $sql
	 *   A string containing a complete SQL query.  %-substitution
	 *   parameters are not supported.
	 * @return
	 *   An array containing the keys:
	 *      success: a boolean indicating whether the query succeeded
	 *      query: the SQL query executed, passed through check_plain()
	 */

	public function update_sql($sql) {
	  //$result = $this->query($sql, true);
	  $result = $this->query($this->db_prefix_tables($sql), true);
	  return array('success' => $result !== FALSE, 'query' => HTML::check_plain($sql));
	}
	
	/**
	 * Generate SQL to create a new table from a Drupal schema definition.
	 *
	 * @param $name
	 *   The name of the table to create.
	 * @param $table
	 *   A Schema API table definition array.
	 * @return
	 *   An array of SQL statements to create the table.
	 */
	
	public function db_create_table_sql($name, $table) {

	  if (empty($table['mysql_suffix'])) {
		$table['mysql_suffix'] = '/*!40100 DEFAULT CHARACTER SET utf8';
		// By default, MySQL uses the default collation for new tables, which is
		// 'utf8_general_ci' for utf8. If an alternate collation has been set, it
		// needs to be explicitly specified.
		// @see db_connect()
		$collation = (!empty($table['collation']) ? $table['collation'] : (!empty($GLOBALS['db_collation']) ? $GLOBALS['db_collation'] : ''));
		if ($collation) {
		  $table['mysql_suffix'] .= ' COLLATE ' . $collation;
		}
		$table['mysql_suffix'] .= ' */';
	  }

	  $sql = "CREATE TABLE {". $name ."} (\n";

	  // Add the SQL statement for each field.
	  foreach ($table['fields'] as $field_name => $field) {
		$sql .= $this->_db_create_field_sql($field_name, $this->_db_process_field($field)) .", \n";
	  }

	  // Process keys & indexes.
	  $keys = $this->_db_create_keys_sql($table);
	  if (count($keys)) {
		$sql .= implode(", \n", $keys) .", \n";
	  }

	  // Remove the last comma and space.
	  $sql = substr($sql, 0, -3) ."\n) ";

	  $sql .= $table['mysql_suffix'];

	  return array($sql);
	}
	
	/**
	 * Set database-engine specific properties for a field.
	 *
	 * @param $field
	 *   A field description array, as specified in the schema documentation.
	 */
	
	public function _db_process_field($field) {

	  if (!isset($field['size'])) {
		$field['size'] = 'normal';
	  }

	  // Set the correct database-engine specific datatype.
	  if (!isset($field['mysql_type'])) {
		$map = $this->db_type_map();
		$field['mysql_type'] = $map[$field['type'] .':'. $field['size']];
	  }

	  if ($field['type'] == 'serial') {
		$field['auto_increment'] = TRUE;
	  }

	  return $field;
	}
	
	/**
	 * This maps a generic data type in combination with its data size
	 * to the engine-specific data type.
	 */
	
	public function db_type_map() {
	  // Put :normal last so it gets preserved by array_flip.  This makes
	  // it much easier for modules (such as schema.module) to map
	  // database types back into schema types.
	  $map = array(
		'varchar:normal'  => 'VARCHAR',
		'char:normal'     => 'CHAR',

		'text:tiny'       => 'TINYTEXT',
		'text:small'      => 'TINYTEXT',
		'text:medium'     => 'MEDIUMTEXT',
		'text:big'        => 'LONGTEXT',
		'text:normal'     => 'TEXT',

		'serial:tiny'     => 'TINYINT',
		'serial:small'    => 'SMALLINT',
		'serial:medium'   => 'MEDIUMINT',
		'serial:big'      => 'BIGINT',
		'serial:normal'   => 'INT',

		'int:tiny'        => 'TINYINT',
		'int:small'       => 'SMALLINT',
		'int:medium'      => 'MEDIUMINT',
		'int:big'         => 'BIGINT',
		'int:normal'      => 'INT',

		'float:tiny'      => 'FLOAT',
		'float:small'     => 'FLOAT',
		'float:medium'    => 'FLOAT',
		'float:big'       => 'DOUBLE',
		'float:normal'    => 'FLOAT',

		'numeric:normal'  => 'DECIMAL',

		'blob:big'        => 'LONGBLOB',
		'blob:normal'     => 'BLOB',

		'datetime:normal' => 'DATETIME',
	  );
	  return $map;
	}
	
	/**
		 * Create an SQL string for a field to be used in table creation or alteration.
		 *
		 * Before passing a field out of a schema definition into this function it has
		 * to be processed by _db_process_field().
		 *
		 * @param $name
		 *    Name of the field.
		 * @param $spec
		 *    The field specification, as per the schema data structure format.
		 */
	
	function _db_create_field_sql($name, $spec) {
		$sql = "`". $name ."` ". $spec['mysql_type'];

		if (in_array($spec['type'], array('varchar', 'char', 'text')) && isset($spec['length'])) {
			$sql .= '('. $spec['length'] .')';
		}
		elseif (isset($spec['precision']) && isset($spec['scale'])) {
			$sql .= '('. $spec['precision'] .', '. $spec['scale'] .')';
		}

		if (!empty($spec['unsigned'])) {
			$sql .= ' unsigned';
		}

		if (!empty($spec['not null'])) {
			$sql .= ' NOT NULL';
		}

		if (!empty($spec['auto_increment'])) {
			$sql .= ' auto_increment';
		}

		if (isset($spec['default'])) {
			if (is_string($spec['default'])) {
			  $spec['default'] = "'". $spec['default'] ."'";
			}
			$sql .= ' DEFAULT '. $spec['default'];
		}

		if (empty($spec['not null']) && !isset($spec['default'])) {
			$sql .= ' DEFAULT NULL';
		}

		return $sql;
	}
	
	/**
		*
		 */
	
	public function _db_create_keys_sql($spec) {
	  $keys = array();

	  if (!empty($spec['primary key'])) {
		$keys[] = 'PRIMARY KEY ('. $this->_db_create_key_sql($spec['primary key']) .')';
	  }
	  if (!empty($spec['unique keys'])) {
		foreach ($spec['unique keys'] as $key => $fields) {
		  $keys[] = 'UNIQUE KEY '. $key .' ('. $this->_db_create_key_sql($fields) .')';
		}
	  }
	  if (!empty($spec['indexes'])) {
		foreach ($spec['indexes'] as $index => $fields) {
		  $keys[] = 'INDEX '. $index .' ('. $this->_db_create_key_sql($fields) .')';
		}
	  }

	  return $keys;
	}
	
	/**
		*
		 */
	
	public function _db_create_key_sql($fields) {
	  $ret = array();
	  foreach ($fields as $field) {
		if (is_array($field)) {
		  $ret[] = $field[0] .'('. $field[1] .')';
		}
		else {
		  $ret[] = $field;
		}
	  }
	  return implode(', ', $ret);
	}
	
	
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Connect to database
     *
     * @return boolean
     */
    public function connect() {
        $this->connection = mysql_connect($this->config['host'] . ':' . $this->config['port'], $this->config['user'], $this->config['pass']);
        if(!$database_exists = mysql_select_db($this->config['database'])){
            error(t('Database <b>%s</b> doesn\'t exists.','Db.errors',$this->config['database']));
            return FALSE;
        }
        $this->query('SET NAMES utf8;');
        return $this->connection ? TRUE : FALSE;
    }

    /**
     * Disconnect from database
     *
     * @return boolean
     */
    public function disconnect() {
        return mysql_close($this->connection);
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
        if (!$this->result = mysql_query($query, $this->connection)) {
            $this->silent OR $this->errors[] = mysql_errno();
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
        return $this->query('SHOW COLUMNS FROM ' . $table) ? $this->result() : NULL;
    }

    /**
     * Build query
     *
     * @return string
     */
    public function buildQuery() {
        $query = array();
        extract($this->_query);
        $from = $from[0];
        if ($insert) {
            $values = $this->filterFields($from, $insert);
            $into = array_keys($values);
            $values = array_values($values);
            $query[] = 'INSERT INTO ' . $this->prepareTableName($from) . ' (' . $this->prepareValues($into, '') . ') VALUES (' . $this->prepareValues($values) . ')';
        } elseif ($update) {
            $values = $this->filterFields($from, $update);
            $query[] = 'UPDATE ' . $this->prepareTableName($from) . ' SET ' . $this->prepareValues($values);
        } elseif ($delete) {
            $query[] = 'DELETE FROM ' . $this->prepareTableName($from);
        } else {
            $select = sizeof($select) < 1 ? '*' : implode(', ', $select);
            $query[] = 'SELECT ' . $select;
            $query[] = ' FROM ' . $this->prepareTableName($from);
        }
        $join && $query[] = implode(' ', $join);
        if ($where) {
            $where = $this->filterFields($from, $where);
            $where && $query[] = ' WHERE ' . $this->argsToString($where, ' = ');
        }
        if ($or_where) {
            $or_where = $this->filterFields($from, $or_where);
            $or_where && $query[] = 'OR ' . $this->argsToString($or_where, ' = ');
        }
        $group && $query[] = ' GROUP BY ' . implode(', ', $group);
        $having && $query[] = ' HAVING ' . implode(', ', $having);
        $order && $query[] = ' ORDER BY ' . implode(', ', $order);
        $limit && isset($limit[0]) && $query[] = ' LIMIT ' . $limit[0] . ($limit[1] ? ', ' . $limit[1] : '');
        return $this->query = implode($query);
    }

    /**
     * Result
     *
     * @return Core_ArrayObject|NULL
     */
    public function result() {
        $result = array();
        if ($this->result) {
            while ($row = mysql_fetch_assoc($this->result)) {
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
        return $this->result ? Core_ArrayObject::transform(mysql_fetch_assoc($this->result)) : NULL;
    }

    /**
     * Prepare variable for statement
     *
     * @param string $value
     * @return string
     */
    public function escape($value) {
        return mysql_real_escape_string($value);
    }

    /**
     * Get last insert id
     *
     * @return int
     */
    public function getInsertId() {
        return mysql_insert_id();
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