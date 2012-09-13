<?php

/**
 *  Callback
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Callback extends Cogearable {

    protected $callback;
    protected $args = array();

    /**
     * Default action for callback
     *
     * @var string
     */
    private static $default_action = 'index';
    /**
     * Delimiter for string callbacks
     * Some_Gear->method where -> is delim
     */
    const DELIM = '->';

    /**
     * Construct
     *
     * @param   string|callback $callback
     */
    public function __construct($callback,$args = array()) {
        $this->callback = self::prepare($callback);
        $args && $this->args = $args;
    }

    /**
     * Invoke method
     */
    public function __invoke() {
        return $this->callback;
    }

    /**
     * Check callback to be callable
     *
     * @return string
     */
    public function check() {
        return Callback::prepare($this->callback) ? TRUE : FALSE;
    }

    /**
     * Run
     *
     * Execute callback
     *
     * @param   array   $args
     * @return  boolean
     */
    public function run($args = array()) {
        if(!$this->callback) return NULL;
        $args = array_merge($args, $this->args);
        return call_user_func_array($this->callback, $args);
    }

    /**
     * Set args
     *
     * @param array $args
     */
    public function setArgs($args) {
        $this->args = $this->args ? array_merge($args,$this->args) : $args;
    }

    /**
     * Get args
     *
     * @return array
     */
    public function getArgs() {
        return $this->args;
    }

    /**
     * Transform string to action
     *
     * @param	string	$string
     * @return	callback
     */
    public static function stringToAction($string) {
        if (strpos($string, self::DELIM)) {
            return explode(self::DELIM, $string);
        }
        return array($string, self::$default_action);
    }

    /**
     * Prepare callback
     *
     * @param   mixed   $callback
     * @return  mixed
     */
    public static function prepare($callback) {
        $callback instanceof Core_ArrayObject && $callback = $callback->toArray();
        if (!is_callable($callback)) {
            if (is_string($callback)) {
                $callback = self::stringToAction($callback);
            }
            $callback[0] = self::fetchObject($callback[0]);
            return is_callable($callback) ? $callback : NULL;
        }
        return $callback;
    }

    /**
     * Prepare callback object
     *
     * @param   string  $class
     * @return  object
     */
    public static function fetchObject($class) {
        if(is_object($class)){
            return $class;
        }
        $cogear = getInstance();
        if (strpos($class, '_Gear')) {
            $gear_name = strtolower(str_replace('_Gear', '', $class));
            if ($cogear->$gear_name) {
                return $cogear->$gear_name;
            }
            return NULL;
        } elseif ($cogear->$class) {
            return $cogear->$class;
        }
        return NULL;
    }

    /**
     * Magic __toString method
     *
     * @return string
     */
    public function __toString() {
        return serialize($this);
    }

}