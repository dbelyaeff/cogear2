<?php

/**
 * Stack — enhanced Core_ArrayObject
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Stack extends Object {

    /**
     * Constructor
     *
     * @param type $options
     */
    public function __construct($options = NULL) {
        parent::__construct($options);
    }

    /**
     * Init
     */
    public function init() {
        event($this->options->name, $this);
    }

    /**
     * Magic __get method
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
        return $this->offsetExists($name) ? $this->offsetGet($name) : NULL;
    }

    /**
     * Magic __set method
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value) {
        $this->offsetSet($name, $value);
    }

    /**
     * Render stack
     *
     * @param string $glue
     * @return string
     */
    public function render($glue = ' ') {
        $this->init();
        return $this->toString($glue);
    }

}
