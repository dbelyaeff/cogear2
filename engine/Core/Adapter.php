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
class Adapter extends Cogearable {

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
    public function __get($name) {
        return isset($this->adapter->$name) ? $this->adapter->$name : parent::__get($name);
    }

    /**
     * Magic __set method
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value) {
        if ($this->adapter) {
            $this->adapter->$name = $value;
        }
        else {
            $this->offsetSet($name,$value);
        }
    }
    
    /**
     * __isset magic method
     *
     * @param string $name 
     */
    public function __isset($name){
        return isset($this->adapter->$name); 
    }

    /**
     * Magic __call method
     *
     * @param string $name
     * @param array $args
     * @return mixed
     */
    public function __call($name, $args) {
        if(!$this->adapter) return NULL;
        $callback = new Callback(array($this->adapter, $name));
        return $callback->check() ? $callback->run($args) : parent::__call($name, $args);
    }

}