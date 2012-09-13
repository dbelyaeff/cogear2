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
    protected $object;

    /**
     * Magic __get method
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
        return is_object($this->object) && isset($this->object()->$name) ? $this->object()->$name : parent::__get($name);
    }

    /**
     * Magic __set method
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value) {
        if ($this->object) {
            $this->object()->$name = $value;
        } else {
            $this->offsetSet($name, $value);
        }
    }

    /**
     * __isset magic method
     *
     * @param string $name
     */
    public function __isset($name) {
        return is_object($this->object) && isset($this->object()->$name);
    }

    /**
     * Magic __call method
     *
     * @param string $name
     * @param array $args
     * @return mixed
     */
    public function __call($name, $args) {
        if (is_object($this->object)) {
            $callback = new Callback(array($this->object, $name));
            if ($callback->check()) {
                return $callback->run($args);
            }
        }
        return parent::__call($name, $args);
    }
}