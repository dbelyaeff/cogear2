<?php

/**
 * Абстркатный класс драйвера баз данных
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 */
abstract class Db_Driver_Abstract extends Object {

    /**
     * Настройки подключения
     *
     * @var array
     */
    protected $options = array(
        'host' => 'localhost',
        'base' => 'cogear',
        'user' => 'root',
        'pass' => '',
        'port' => 3306,
        'prefix' => '',
    );

    /**
     * Звенья запроса
     *
     * @var array
     */
    protected $chain = array(
        'SELECT' => '*',
        'FROM' => '',
        'JOIN' => '',
        'WHERE' => '',
        'LIKE' => '',
        'GROUP BY' => '',
        'HAVING' => '',
        'ORDER BY' => '',
        'LIMIT' => '',
    );

    /**
     * Переменная для смены звеньев текущего запроса
     *
     * @var type
     */
    protected $swap_chain =  array(
        'SELECT' => '*',
        'FROM' => '',
        'JOIN' => '',
        'WHERE' => '',
        'LIKE' => '',
        'GROUP BY' => '',
        'HAVING' => '',
        'ORDER BY' => '',
        'LIMIT' => '',
    );

    /**
     * Схема таблиц в базе данных
     *
     * @var array
     */
    protected $tables = array();

    /**
     * Выполненные запросы
     *
     * @var array
     */
    public $queries;

    /**
     * Текущий запрос
     *
     * @var string
     */
    protected $query;

    /**
     * Результат запроса
     *
     * @var object
     */
    protected $result;
    /**
     * Флаг, который показывает есть ли соединение с базой
     *
     * @var boolean
     */
    protected $is_connected;
    /**
     * Автоматическая очистка цепочки запроса
     *
     * @var type
     */
    protected $autoclear = TRUE;

    /**
     * Констрктор
     *
     * @param array $options
     */
    public function __construct($options) {
        parent::__construct($options);
        $this->queries = new SplStack();
    }

    /**
     * Соединение с базой данных
     */
    abstract public function connect();

    /**
     * Рассоединение с базой данных
     */
    abstract public function disconnect();

    /**
     * Прямое исполнение запроса
     */
    abstract public function query($query);

    abstract public function lastInsertId();

    /**
     * Получние списка полей из таблицы
     *
     * @param type $table
     * @return type
     */
    abstract public function getFieldsQuery($table);

    /**
     * Экранирование данных
     *
     * @param   mixed   $data
     */
    abstract public function escape($data);

    /**
     *  Получение информации о полях в таблице
     *
     * @param string $table
     * @return array
     */
    public function getFields($table = NULL) {
        $table OR $table = $this->chain['FROM'];
        if (!$this->tables[$table] = cogear()->system_cache->read('database/' . $this->options->base . '/' . $table, TRUE)) {
            if ($fields = $this->getFieldsQuery($table)) {
                $this->tables[$table] = array();
                foreach ($fields as $field) {
                    $this->tables[$table][$field->Field] = $field->Type;
                }
                cogear()->system_cache->write('database/' . $this->options->base . '/' . $table, $this->tables[$table], array('db_fields'));
            }
        }
        return $this->tables[$table];
    }

    /**
     * Импорт в базу данных
     *
     * @param type $file
     */
    public function import($file) {
        if (!file_exists($file)) {
            error(t("Файл <b>%s</b> не существует.", $file));
        }
        $data = file_get_contents($file);
        if ($result = $this->query($data)) {
            success(t("Дамп базы данных <b>%s</b> успешно импортирован!", basename($file)));
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Экспорт базы данных
     *
     * @param string $file
     * @param array $options
     */
    public function export($file, $options = array()) {
        // @todo Написать метод
    }

    /**
     * Имя таблицы с префиксом
     *
     * @param mixed $value
     * @param string $type
     * @return mixed
     */
    protected function tableName($value) {
        return $this->options->prefix . $value;
    }

    /**
     * Исполнение запроса в базу данных
     *
     * @param string $table
     * @return object
     */
    public function get($table = '', $limit = NULL, $offset = NULL) {
        $table && $this->from($table);
        $limit && $this->limit($limit, $offset);
        $this->query = $this->buildQuery();
        $this->query($this->query);
        return $this;
    }

    /**
     * Построение запроса
     *
     * @return string
     */
    protected function buildQuery() {
        $query = '';
        foreach ($this->chain as $key => $value) {
            if ($value) {
                $query .= $key . ' ' . $value . ' ';
            }
        }
        return $query;
    }

    /**
     * Выборка полей базы
     *
     * @param string|array $fields
     */
    public function select($fields = '*') {
        // Если подается массив полей, то он превращается в строку
        if (is_array($fields)) {
            $fields = implode(',', $fields);
        }
        $this->chain['SELECT'] = $fields;
        return $this;
    }

    /**
     * Выбор таблицы
     *
     * @param string $table
     * @return object
     */
    public function from($table) {
        $this->chain['FROM'] = $table;
        return $this;
    }

    /**
     * Кросс-выборка через JOIN
     *
     * @param string $table
     * @param string $on
     * @return object
     */
    public function join($table, $on = '', $type = 'INNER') {
        $this->chain['JOIN'] .= ' ' . $type . ' JOIN ' . $this->tableName($table) . ' ON ' . $on;
        return $this;
    }

    /**
     * Условие выборки WHERE с правилом И (AND)
     *
     * @param string|array $name
     * @param mixed $value
     * @param string $condition
     * @return object
     */
    public function where($name = array(), $value = 1, $condition = ' = ', $join = ' AND ') {
        if (is_array($name)) {
            foreach ($name as $key => $value) {
                $this->where($key, $value, $condition);
            }
        } else {
            if ($this->chain['WHERE']) {
                $this->chain['WHERE'] .= $join;
            }
            if(strpos($name,' ')){
                $where = explode(' ',$name,2);
                $name = $where[0];
                $condition = $where[1];
            }
            $this->chain['WHERE'] .= $name . ' ' . $condition . ' ' . $this->escape($value);
        }
        return $this;
    }

    /**
     * Условие выборки WHERE с правилом ИЛИ (OR)
     *
     * @param string|array $name
     * @param mixed $value
     * @param string $condition
     * @return object
     */
    public function or_where($name = array(), $value = TRUE, $condition = ' = ') {
        return $this->where($name, $value, $condition, 'OR');
    }

    /**
     * Условие выборки LIKE с правилом И (AND)
     *
     * @param string|array $name
     * @param mixed $value
     * @param string $condition // BOTH, LEFT или RIGHT
     * @return object
     */
    public function like($name = array(), $value = TRUE, $condition = 'BOTH', $join = 'AND', $action = ' LIKE ') {
        switch ($condition) {
            case 'LEFT':
                $value = '%' . $value;
                break;
            case 'RIGHT':
                $value .= '%';
                break;
            default:
                $value = '%' . $value . '%';
        }
        return $this->where($name, $value, $action, $join);
    }

    /**
     * Условие выборки LIKE с правилом ИЛИ (OR)
     *
     * @param string|array $name
     * @param mixed $value
     * @param string $condition // BOTH, LEFT или RIGHT
     * @return object
     */
    public function or_like($name = array(), $value = TRUE, $condition = 'BOTH') {
        return $this->like($name, $value, $condition, 'OR');
    }

    /**
     * Условие выборки NOT LIKE с правилом И (AND)
     *
     * @param string|array $name
     * @param mixed $value
     * @param string $condition // BOTH, LEFT или RIGHT
     * @return object
     */
    public function not_like($name = array(), $value = TRUE, $condition = 'BOTH') {
        return $this->like($name, $value, $condition, 'AND', ' NOT LIKE ');
    }

    /**
     * Порядок сортировки
     *
     * @param string $field
     * @param string $dir
     */
    public function order($field, $dir = 'ASC') {
        if ($this->chain['ORDER BY']){
            $this->chain['ORDER BY'] .= ',';
        }
        $this->chain['ORDER BY'] .= $field . ' ' . $dir;
    }

    /**
     * Группировка элементов
     *
     * @param string $field
     * @param string $dir
     */
    public function group($field, $dir = 'ASC') {
        if ($this->chain['GROUP BY'])
            $this->chain['GROUP BY'] .= ',';
        $this->chain['GROUP BY'] .= $field . ' ' . $dir;
    }

    /**
     * Лимитирование запрсоа
     *
     * @param int $limit
     * @param int $offset
     */
    public function limit($limit, $offset = NULL) {
        $this->chain['LIMIT'] = $offset ? $offset . ' ,' . $limit : $limit;
    }

    /**
     * Смена запроса
     *
     * Иногда возникает необходимость временно спрятать уже составленные звенья запроса
     *
     * @param string $type Можно менять местами какие-то определенные звенья запроса
     */
    public function swap($type = NULL) {
        if (NULL === $type) {
            $buffer = $this->chain;
            $this->chain = $this->swap_chain;
            $this->swap_chain = $buffer;
        } elseif (isset($this->chain[$type])) {
            $buffer = $this->chain[$type];
            $this->chain[$type] = $this->swap_chain[$type];
            $this->swap_chain[$type] = $buffer;
        }
    }

    /**
     * Получение количества результатов по запросу
     *
     * @param string $table Имя таблицы
     * @param string $field Поля для выборки
     * @param bool $reset Производить ли сброс цепочки
     * @return  int|NULL
     */
    public function countAll($table, $field = '*', $reset = FALSE) {
        $this->swap('SELECT');
        $this->select('COUNT(' . $field . ') as count');
        $this->autoclear = FALSE;
        $row = $this->get($table)->row();
        $this->autoclear = TRUE;
        $this->swap('SELECT');
        $reset && $this->clear();
        return $row ? $row->count : NULL;
    }

    /**
     * Возвращает последний исполненый запрос
     *
     * @return type
     */
    public function last() {
        return $this->queries->pop();
    }

    /**
     * Получение всего результата запроса
     *
     * @reutrn  object|NULL
     */
    abstract public function result();

    /**
     * Получение одной записи из результата запроса
     *
     * @return  object|NULL
     */
    abstract public function row();

    /**
     * Вставка данных
     *
     * @param string $table Имя таблицы
     * @param array  $data Массив полей и значений
     * @param string $type  Тип вставки. INSERT или REPLACE
     * @return  int Номер вставленного элемента
     */
    abstract public function insert($table, $data = array(), $type = 'INSERT');

    /**
     * Обновление данных
     *
     * @param string $table Имя таблицы
     * @param array  $data Массив полей и значений
     * @param string $where  Условия обновления
     */
    abstract public function update($table, $data = array(), $where = array());

    /**
     * Удаление данных
     *
     * @param   string  $table
     * @param   array  $where
     */
    abstract public function delete($table, $where = array());

    /**
     * Очищение звеньев запроса
     */
    public function clear() {
        $this->swap_chain = $this->chain = array(
            'SELECT' => '*',
            'FROM' => '',
            'JOIN' => '',
            'WHERE' => '',
            'LIKE' => '',
            'GROUP BY' => '',
            'HAVING' => '',
            'ORDER BY' => '',
            'LIMIT' => '',
        );
    }

}