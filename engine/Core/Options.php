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
abstract class Options extends Core_ArrayObject {

    /**
     * Options
     * 
     * @var array 
     */
    protected $options = array();
    const SELF = 1;

    /**
     * Constructor
     *
     * @param array|ArrayObject $options
     * @param string $storage
     */
    public function __construct($options = array(), $place = NULL) {
        $this->options = new Core_ArrayObject($this->options);
        if ($place == self::SELF) {
            $options instanceof Core_ArrayObject OR $options = new Core_ArrayObject($options);
            foreach($options as $key=>$value){
                $this->$key = $value;
            }
        } else {
            $this->setOption($options);
        }
    }

    /**
     * Set options
     * 
     * @param array|ArrayObject $name
     * @param string $value
     */
    public function setOption($name, $value = NULL) {
        if (is_array($name) OR $name instanceof ArrayObject) {
            is_array($name) && $name = new Core_ArrayObject($name);
            foreach ($name as $key => $value) {
                $this->options->$key = $value;
            }
            return;
        }
        $this->options->$name = $value;
    }

    /**
     * Magic __get method
     * 
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
        return isset($this->$name) ? $this->$name : parent::__get($name);
    }

}