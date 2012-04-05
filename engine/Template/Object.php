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
class Template_Object extends Adapter {

    /**
     * Default handler
     *
     * @var string
     */
    public static $handler;

    /**
     * Name
     * 
     * @var string
     */
    public $name;

    /**
     * Constructor
     *
     * @param   string  $name
     */
    public function __construct($name) {
        $this->name = $name;
        $this->adapter = new Template_File($this->name);
    }

    /*
     * We avoid usage of __callStatic method to have better compatibilty with PHP versions under 5.3.
     * That's because we need to make a couple of aliases ↓
     */

    /**
     * Set global variable
     */
    public static function setGlobal() {
        $args = func_get_args();
        return call_user_func_array(array('Template_Abstract', 'setGlobal'), $args);
    }

    /**
     * Bind global variable
     */
    public static function bindGlobal($name, &$value) {
        return Template_Abstract::bindGlobal($name, $value);
    }

    /**
     * Clear template variables
     */
    public static function clear() {
        return Template_Abstract::clear();
    }

    /**
     * Get global
     */
    public static function getGlobal() {
        $args = func_get_args();
        return call_user_func_array(array('Template_Abstract', 'getGlobal'), $args);
    }

}
