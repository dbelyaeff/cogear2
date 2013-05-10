<?php

/**
 * Объект кэга
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2010, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Cache_Object extends Core_Factory {

    public static $statistics;
    /**
     * Инициалиазация
     *
     * @param array $options
     */
    public function __construct($options = array()) {
        self::$statistics OR self::$statistics = new Core_ArrayObject();
        parent::__construct($options);
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