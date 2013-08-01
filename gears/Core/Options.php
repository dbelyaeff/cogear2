<?php

/**
 *  Опции
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011-2013, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Options extends Core_ArrayObject {

    /**
     * Options
     *
     * @var array
     */
    protected $options = array();

    const SELF = 1;

    /**
     * Конструктор
     *
     * @param array|ArrayObject $options
     * @param string $storage
     */
    public function __construct(

    $options = array(), $place = 0) {
    $this->setOptions($options, $place);
    }
    /**
     * Декодировка опций
     *
     * @param array $options
     */
    public static function decode($options) {
    !($options instanceof Core_ArrayObject) && $options = new Core_ArrayObject($options);
    $first_key = $options->getFirstKey();
    if($first_key[ 0

] !== '#') {
    return $options;
    }
    $results = new Core_ArrayObject ( );

    foreach ($options as $key => $value) {
        if ($key[0] == '#') {
            $real_key = substr($key, 1);
            $results->$real_key = $value;
        } else {
            $results->elements OR $results->elements = new Core_ArrayObject();
            if (is_array($value) OR $value instanceof Core_ArrayObject) {
                $value = self::decode($value);
            }
            $results->elements->$key = $value;
        }
    }
    return $results;
}

/**
 * Установка опций
 *
 * @param array $options
 * @param int $place
 */
public function setOptions($options, $place = 0) {
    $this->options = new Core_ArrayObject((array) $this->options);
    if (self::SELF == $place) {
        foreach ($options as $key => $value) {
            $this->$key = $value;
        }
    } else {
        $this->options->extend($options);
    }
}

/**
 * Возвращает опции
 *
 * @return array
 */
public function getOptions() {
    return $this->options;
}

/**
 * Magic __get method
 *
 * @param string $name
 * @return mixed
 */
public function __get($name) {
    if (isset($this->options->$name)) {
        return $this->options->$name;
    }
    return isset($this->$name) ? $this->$name : parent::__get($name);
}

/**
 * Isset
 *
 * @param type $name
 * @return type
 */
public function __isset($name) {
    return isset($this->options->$name) ? $this->options->$name : parent::__isset($name);
}

/**
 * Show
 */
public function show($region = NULL, $position = 0, $where = 0) {
    !$region && $region = $this->options && is_string($this->options->render) ? $this->options->render : 'content';
    $position ? inject($region, $this->render(), $position, $where) : append($region, $this->render());
}

}