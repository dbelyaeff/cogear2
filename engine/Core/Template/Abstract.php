<?php

/**
 * Template
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Template_Abstract extends Options {

    protected $name = '';
    protected $path = '';
    protected $code = '';
    protected $vars = array();
    protected static $global_vars = array();

    /**
     * Constructor
     *
     * @param string $name
     */
    public function __construct($name) {
        $this->name = $name;
//        $this->path = Gear::preparePath($this->name, 'templates') . EXT;
//        if (file_exists($this->path)) {
//            $this->code = file_get_contents($this->path);
//        } else {
//            exit(t('Template <b>%s</b> is not found by path <u>%s</u>.', 'Errors', $this->name, $this->path));
//        }
    }

    /**
     * Magic __set method to assign vars
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value) {
        $this->set($name, $value);
    }

    /**
     * Set variable
     *
     * @param string $name
     * @param mixed $value
     */
    public function set($name, $value = NULL) {
        if (is_array($name) OR $name instanceof ArrayObject) {
            foreach ($name as $key => $value) {
                $this->set($key, $value);
            }
            return;
        }
        else
            $this->vars[$name] = $value;
    }
    
    /**
     * Reset vaiables
     */
    public function reset(){
        $this->vars = array();
    }

    /**
     * Set variable
     *
     * @param string $name
     * @param mixed $value
     */
    public function assign() {
        $args = func_get_args();
        call_user_func_array(array($this, 'set'), $args);
    }

    /**
     * Get variable
     *
     * @param   string  $name
     * @return mixed
     */
    public function __get($name) {
        return $name ? (isset($this->vars[$name]) ? $this->vars[$name] : NULL) : $this->vars;
    }

    /**
     * Magic isset method
     *
     * @param string $name
     * @return boolean
     */
    public function __isset($name) {
        return isset($this->vars[$name]);
    }

    /**
     * Get variable
     *
     * @param   string  $name
     * @return mixed
     */
    public function get($name = '') {
        return $name ? (isset($this->vars[$name]) ? $this->vars[$name] : NULL) : $this->vars;
    }

    /**
     * Bind variable
     *
     * @param string $name
     * @param mixed $value
     */
    public function bind($name, &$value = NULL) {
        if (is_array($name)) {
            foreach ($name as $key => $value) {
                $this->bind($key, $value);
            }
            return;
        } else {
            $this->vars[$name] = & $value;
        }
    }

    /**
     * Set global variable
     *
     * @param string $name
     * @param mixed $value
     */
    public static function setGlobal($name, $value = NULL) {
        if (is_array($name)) {
            foreach ($name as $key => $value) {
                $this->setGlobal($key, $value);
            }
            return;
        } else {
            self::$global_vars[$name] = $value;
        }
    }

    /**
     * Bind global variable
     *
     * @param string $name
     * @param mixed $value
     */
    public static function bindGlobal($name, &$value = NULL) {
        if (is_array($name)) {
            foreach ($name as $key => $value) {
                $this->bindGlobal($key, $value);
            }
            return;
        } else {
            self::$global_vars[$name] = & $value;
        }
    }

    /**
     * Get global
     *
     * @param   string  $name
     * @return mixed
     */
    public static function getGlobal($name = '') {
        return $name ? (isset(self::$global_vars[$name]) ? self::$global_vars[$name] : NULL) : self::$global_vars;
    }

    /**
     * Clear global vars
     */
    public static function clear() {
        self::$global_vars = array();
    }

    /**
     * Render template
     *
     * @return  string
     */
    public function render() {
        if(!file_exists($this->path)){
            exit(t('Template <b>%s</b> is not found by path <u>%s</u>.', 'Errors', $this->name, $this->path));
        }
        event('template.render', $this);
        ob_start();
        self::$global_vars && extract(self::$global_vars);
        $this->vars && extract($this->vars);
        include $this->path;
        $output = ob_get_clean();
        return $output;
    }
}
