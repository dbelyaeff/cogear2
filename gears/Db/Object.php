<?php

/**
 * Объект базы даных
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Database
 */
class Db_Object implements Interface_Factory {

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
        return isset(self::$_instances[$name]) ? self::$_instances[$name] : self::$_instances[$name] = new $class($options);
    }

}