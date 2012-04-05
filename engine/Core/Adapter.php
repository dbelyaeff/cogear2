<?php
/**
 * Adapter class
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Adapter {
    /**
     * Adapter
     *
     * @var object
     */
    protected $adapter;
    /**
     * Magic __get method
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name){
        return isset($this->adapter->$name) ? $this->adapter->$name : NULL;
    }
    /**
     * Magic __set method
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name,$value){
        $this->adapter->$name = $value;
    }
    /**
     * Magic __call method
     *
     * @param string $name
     * @param array $args
     * @return mixed
     */
    public function  __call($name, $args) {
        $callback = array($this->adapter,$name);
        return is_callable($callback) ? call_user_func_array($callback, $args) : NULL;
    }
}