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
abstract class Cogearable extends Options{
    /**
     * Magic __get method
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name){
        $parent = parent::__get($name);
        return $parent ? $parent : cogear()->$name;
    }
    /**
     * Magic __call method
     *
     * @param   string  $name
     * @param   array   $array
     */
    public function __call($name,$args = array()){
        $callback = new Callback(array(cogear(),$name));
        if($callback->check()){
            return $callback->run($args);
        }
        return NULL;
    }
}