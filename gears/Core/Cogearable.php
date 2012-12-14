<?php
/**
 * Simple class which allows to it ancessors to use Cogear variables and methods as their own
 *
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2010, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage

 */
abstract class Cogearable extends Errors {
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