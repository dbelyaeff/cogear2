<?php

/**
 * Путь к базе данных
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2013, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Router_Route extends Db_ORM {

    protected $table = 'routes';
    protected $primary = 'route';

    const DELIM = '|';

    /**
     * Исполнение callback
     *
     * @return null
     */
    public function run() {
        if ($callback = self::decodeCallback($this->callback)) {
            return $callback->run();
        }
        return NULL;
    }

    /**
     * Кодирование Callback
     *
     * @param type $callback
     * @param type $args
     */
    public function encodeCallback($callback, $args = array()) {
        $data = array(get_class($callback[0]), $callback[1]);
        foreach ($args as $arg) {
            array_push($data, $arg);
        }
        return implode(self::DELIM, $data);
    }

    /**
     * Раскодировка Callback
     *
     * @param type $callback
     */
    public function decodeCallback($callback) {
        if ($data = explode(self::DELIM, $callback)) {
            $class = array_shift($data);
            $method = array_shift($data);
            $args = $data;
            $callback = array();
            if (!class_exists($class)) {
                return FALSE;
            }
            // Если это шестерёнка, то не надо её повторно создавать
            if (strpos($class, Gears::GEAR)) {
                $gear = substr($class, 0, strpos($class, '_' . Gears::GEAR));
                if (cogear()->$gear) {
                    $callback[0] = cogear()->$gear;
                }
            }
            // Если массив всё ещё пустой
            if (!$callback) {
                $callback[0] = new $class;
            }
            // Если метод не существует — останавливаем
            if (!method_exists($callback[0], $method)) {
                return FALSE;
            }
            else {
                $callback[1] = $method;
            }
            return new Callback($callback, $args);
        }
        return FALSE;
    }

    /**
     * Переопределение функции вставки информации
     * @param type $data
     */
    public function insert($data = NULL) {
        $this->object->created_date = time();
        parent::insert($data);
    }

}