<?php
/**
 * Simple class which allows to it ancessors to use Cogear variables and methods as their own
 *
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2010, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
abstract class Cogearable {
    /**
     * Magic __get method
     * 
     * @param string $name
     * @return mixed 
     */
    public function __get($name){
        $cogear = getInstance();
        return $cogear->$name ? $cogear->$name : NULL;
    }
    /**
     * Magic __call method
     * 
     * @param   string  $name
     * @param   array   $array
     */
    public function __call($name,$args = array()){
        $cogear = getInstance();
        if(method_exists($cogear,$name)){
            return call_user_func_array(array($cogear,$name), $args);
        }
        return NULL;
    }
}