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
class Callback extends Cogearable{

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
    public function __construct($callback) {
        $this->callback = self::prepare($callback);
    }
    /**
     * Call
     * 
     * Execute callback
     * 
     * @param   array   $args
     * @return  boolean
     */
    public function call(&$args = array()){
        if(!is_callable($this->callback)) return NULL;
        $args = array_merge_recursive($args,$this->args);
        return call_user_func_array($this->callback,$args);
    }
    /**
     * Set args
     * 
     * @param array $args 
     */
    public function setArgs(&$args) {
        $this->args =& $args;
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
        if (!is_callable($callback)) {
            if (is_string($callback)) {
                $callback = self::stringToAction($callback);
                $callback[0] = self::fetchObject($callback[0]);
                return is_callable($callback) ? $callback : NULL;
            } else {
                return NULL;
            }
        }
        return is_callable($callback) ? $callback : NULL;
    }

    /**
     * Prepare callback object
     *
     * @param   string  $class
     * @return  object
     */
    public static function fetchObject($class) {
        $element = ucfirst($class);
        $cogear = getInstance();
        if (strpos($class, '_Gear')) {
            $gear_name = strtolower(str_replace('_Gear', '', $class));
            if ($cogear->$gear_name) {
                return $cogear->$gear_name;
            }
            return new $class;
        } elseif (isset($cogear->$element)) {
            return $cogear->$element;
        } elseif (class_exists($class)) {
            $Reflection = new ReflectionClass($class);
            if ($Reflection->implementsInterface('Singleton')) {
                return call_user_func($class, 'getInstance');
            } else {
                return new $class;
            }
        }
        return NULL;
    }
    
    /**
     * Magic __toString method
     * 
     * @return string
     */
    public function __toString(){
        return serialize($this);
    }
}