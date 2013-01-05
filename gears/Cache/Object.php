<?php

/**
 * Объект кэга
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2010, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Cache_Object extends Object implements Interface_Factory {

    public static $statistics;
    protected static $_instances = array();

    /**
     * Метод "фабрики", производящий эксземпляр объекта текущего класса
     *
     * @param string $name
     * @param array $options
     * @param string $class
     * @return object
     */
    public static function factory($name, $options = array(), $class = __CLASS__) {
        $options['name'] = $name;
        return isset(self::$_instances[$name]) ? self::$_instances[$name] : self::$_instances[$name] = new $class($options);
    }

    /**
     * Инициалиазация
     *
     * @param array $options
     */
    public function __construct($options = array()) {
        parent::__construct($options);
        self::$statistics OR self::$statistics = new Core_ArrayObject();
        try {
            $this->object(new $this->options->driver($options));
        } catch (Exception $e) {
            error($e->getMessage());
        }
    }

    /**
     * Magic __call method
     *
     * @param string $name
     * @param array $args
     * @return mixed
     */
    public function __call($name, $args) {
        isset(self::$statistics[$this->options->name]) OR self::$statistics[$this->options->name] = new Core_ArrayObject();
        isset(self::$statistics[$this->options->name]['read']) OR self::$statistics[$this->options->name]['read'] = 0;
        isset(self::$statistics[$this->options->name]) OR self::$statistics[$this->options->name] = new Core_ArrayObject();
        isset(self::$statistics[$this->options->name]['write']) OR self::$statistics[$this->options->name]['write'] = 0;
        if ($name == 'read') {
            self::$statistics[$this->options->name]['read']++;
        } else if ($name == 'write') {
            self::$statistics[$this->options->name]['write']++;
        }
        return parent::__call($name, $args);
    }

}