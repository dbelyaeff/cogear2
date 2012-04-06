<?php

/**
 *  Template object
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Template_Object {

    protected $name = '';
    protected $path = '';
    protected $code = '';
    protected $vars = array();

    /**
     * Constructor
     * 
     * @param string $name
     */
    public function __construct($name) {
        $this->name = $name;
        $path = Gear::preparePath($this->name, 'templates') . EXT;
        if (file_exists($path)) {
            $this->path = $path;
        } else {
            $message = t('Template <b>%s</b> is not found by path <u>%s</u>.', 'Errors', $this->name, $this->path);
            exit($message);
        }
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
    public function reset() {
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
     *
     * @return type 
     */
    public function render(){
        event('template.render.before', $this);
        ob_start();
        extract($this->vars);
        include $this->path;
        event('template.render.after', $this);
        return ob_get_clean();;
    }

}
