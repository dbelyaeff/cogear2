<?php

/**
 * Драйвер базы данных для работы через PDO
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 */
class Db_Driver_PDO extends Db_Driver_Abstract {

    protected $driver = 'mysql';
    protected $PDO;

    /**
     * Соединение с базой данных
     *
     * @return  boolean Удалость или нет установить соединение с базой данных
     */
    public function connect($database = '') {
        if (FALSE !== $database) {
            $database = ';dbname=' . ($database ? $database : $this->options->base);
        }
        try {
            $this->PDO = new PDO($this->driver . ':host=' . $this->options->host . $database, $this->options->user, $this->options->pass);
            $this->query('SET NAMES utf8;');
        } catch (PDOException $e) {
            $this->error($e->getMessage());
            return $this->is_connected = FALSE;
        }
        return $this->is_connected = TRUE;
    }

    /**
     * Рассоединение с базой данных
     */
    public function disconnect() {
// PDO рассоединяется с базой автоматически
    }

    /**
     * Прямое исполнение запроса
     *
     * @param string $query Запрос
     * @param boolean  $clear Очищать ли чепочку запроса
     * @return PDOStatement|bool
     */
    public function query($query) {
        $this->queries->push($query);
        $i = $this->queries->count();
        bench('db.query.' . $i . '.start');
        $this->result = $this->PDO->query($query);
        bench('db.query.' . $i . '.end');
        $this->autoclear && $this->clear();
        return $this->result ? $this->result : FALSE;
    }

    /**
     * Получение номера последней вставленной записи
     *
     * @return int
     */
    public function lastInsertId() {
        return $this->PDO->lastInsertId();
    }

    /**
     * Получние списка полей в таблице
     *
     * @param string  $table
     * @return object|NULL
     */
    public function getFieldsQuery($table) {
        return $this->query('SHOW COLUMNS FROM ' . $this->tableName($table, 'table')) ? $this->result() : NULL;
    }

    /**
     * Получение всего результата запроса
     *
     * @reutrn  object|NULL
     */
    public function result() {
        if ($this->result instanceof PDOStatement) {
            if ($result = $this->result->fetchAll(PDO::FETCH_ASSOC)) {
                return Core_ArrayObject::transform($result);
            }
            return FALSE;
        }
    }

    /**
     * Получение одной записи из результата запроса
     *
     * @return  object|NULL
     */
    public function row() {
        if ($this->result instanceof PDOStatement) {
            if ($result = $this->result->fetch(PDO::FETCH_ASSOC)) {
                return Core_ArrayObject::transform($result);
            }
            return FALSE;
        }
    }

    /**
     * Экранирование данных
     *
     * @param   mixed   $data
     */
    public function escape($data) {
        return $this->PDO->quote($data);
    }

    /**
     * Функция для экранизации переменных
     *
     * @param   string  $name
     * @return  string
     */
    public function addColon($name) {
        return ':' . $name;
    }

    /**
     * Вставка данных
     *
     * @param string $table Имя таблицы
     * @param array  $data Массив полей и значений
     * @param string $type  Тип вставки. INSERT или REPLACE
     * @return  int Номер вставленного элемента
     */
    public function insert($table, $data = array(), $type = 'INSERT') {
        $query = 'INSERT INTO ' . $this->tableName($table, 'table') . '(' . implode(',', array_keys($data)) . ') VALUES(' . implode(',', array_map(array($this, 'addColon'), array_keys($data))) . ');';
        $PDOStatement = $this->PDO->prepare($query);
        $exec_data = array();
        foreach ($data as $key => $value) {
            $exec_data[':' . $key] = $value;
        }
        $this->autoclear && $this->clear();
        try {
            $this->queries->push(str_replace(array_keys($exec_data), array_values($exec_data), (string) $PDOStatement->queryString));
            $i = $this->queries->count();
            bench('db.query.' . $i . '.start');
            if ($PDOStatement->execute($exec_data)) {
                bench('db.query.' . $i . '.end');
                return $this->PDO->lastInsertId();
            } else {
                return FALSE;
            }
        } catch (PDOException $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * Обновление данных
     *
     * @param string $table Имя таблицы
     * @param array  $ Массив полей и значений
     * @param string $where  Условия обновления
     */
    public function update($table, $data = array(), $where = array()) {
        $query = 'UPDATE ' . $this->tableName($table, 'table') . ' SET ';
        $it = $data instanceof Core_ArrayObject ? $data->getInnerIterator() : new ArrayIterator($data);
        $it = new CachingIterator($it);
        foreach ($it as $key => $value) {
            $query .= $key . ' = :' . $key;
            if ($it->hasNext())
                $query .= ', ';
        }
        if ($where) {
            $this->where($where);
            $query .= ' WHERE ' . $this->chain['WHERE'];
        }
        $this->autoclear && $this->clear();
        $PDOStatement = $this->PDO->prepare($query);
        $exec_data = array();
        foreach ($data as $key => $value) {
            $exec_data[':' . $key] = $value;
        }
        try {
            $this->queries->push(str_replace(array_keys($exec_data), array_values($exec_data), (string) $PDOStatement->queryString));
            $i = $this->queries->count();
            bench('db.query.' . $i . '.start');
            if ($PDOStatement->execute($exec_data)) {
                bench('db.query.' . $i . '.end');
                return $PDOStatement->rowCount();
            } else {
                return FALSE;
            }
        } catch (PDOException $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * Удаление данных
     *
     * @param   string  $table
     * @param   array   $where
     */
    public function delete($table, $where = array()) {
        $query = 'DELETE FROM ' . $this->tableName($table);
        if ($where) {
            $this->where($where);
            $query .= ' WHERE ' . $this->chain['WHERE'];
        }
        return $this->query($query);
    }

}