<?php
/**
 *  Template handler
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Template extends Adapter{
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
     * Constants
     */
    const FILE = 0;
    const DB = 1;
    /**
     * Constructor
     *
     * @param   string  $name
     * @param   string  $handler
     */
    public function __construct($name,$handler = NULL){
        if(!self::$handler){
            $cogear = getInstance();
            self::$handler = $cogear->get('template.handler',self::FILE);
        }
        $this->name = $name;
        event('template.'.$name,$this);
        $handler OR $handler = self::$handler;
        switch($handler){
            case self::DB:
                $this->adapter = new Template_Db($this->name);
                break;
            case self::FILE:
            default:
                $this->adapter = new Template_File($this->name);
        }
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
        return call_user_func_array(array('Template_Abstract','setGlobal'),$args);
    }
    /**
     * Bind global variable
     */
    public static function bindGlobal($name,&$value) {
        return Template_Abstract::bindGlobal($name,$value);
    }
    /**
     * Clear template variables
     */
    public static function clear(){
        return Template_Abstract::clear();
    }
    /**
     * Get global
     */
    public static function getGlobal() {
        $args = func_get_args();
        return call_user_func_array(array('Template_Abstract','getGlobal'),$args);
    }
}
