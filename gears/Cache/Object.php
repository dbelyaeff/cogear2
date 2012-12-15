<?php

/**
 * Cache
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2010, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage

 */
class Cache_Object extends Object implements Interface_Factory {

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

    /**
     * Initiate cache
     *
     * @param array $options
     */
    public function __construct($options = array()) {
        parent::__construct($options);
        try {
            $this->object(new $this->options->driver($options));
        } catch (Exception $e) {
            error($e->getMessage());
        }
    }

}