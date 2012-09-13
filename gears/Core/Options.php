<?php

/**
 *  Options
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Options extends Core_ArrayObject {

    /**
     * Options
     *
     * @var array
     */
    public $options = array();
    const SELF = 1;

    /**
     * Constructor
     *
     * @param array|ArrayObject $options
     * @param string $storage
     */
    public function __construct($options = array(), $place = 0) {
        $this->options = new Core_ArrayObject($this->options);
        if ($place) {
            parent::__construct($options);
        } else {
            $this->options->extend($options);
        }
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