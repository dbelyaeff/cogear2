<?php

/**
 * Событие
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Event extends Core_ArrayObject {

    private $name;
    private $result;
    private $results;
    public static $current;

    /**
     * Конструктор
     */
    public function __construct($name) {
        $this->name = $name;
        parent::__construct();
        $this->results = new Core_ArrayObject();
    }

    /**
     * Исполнение события
     *
     * @param   $args
     * @return  object
     */
    public function run($args) {
        self::$current = $this;
        foreach ($this as $callback) {
            if (FALSE === flash('event.' . $this->name)) {
                return FALSE;
            }
            $result = $callback->run($args);
            if (NULL !== $result) {
                $this->results->append($result);
            }
        }
        return $this;
    }

    /**
     * Получения результатов исполнения
     *
     * @return Core_ArrayObject
     */
    public function getResults() {
        return $this->results;
    }

    /**
     * Установка результата исполнения события
     *
     * @param mixed $data
     * @return mixed
     */
    public function result($data = NULL) {
        if ($data) {
            return $this->result = $data;
        }
        return $this->result;
    }

    /**
     * Проверка результата
     *
     * @return boolean
     */
    public function check() {
        foreach ($this->results as $result) {
            if ($result == FALSE) {
                return FALSE;
            }
        }
        return TRUE;
    }

}
