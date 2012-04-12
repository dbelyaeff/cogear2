<?php
/**
 * Input gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          
 * @version		$Id$
 */
class Input_Gear extends Gear {

    protected $name = 'Input';
    protected $description = 'Catch user input';
    
    protected $get = array();
    protected $post = array();
    protected $cookies = array();


    /**
     * Init
     */
    public function init(){
        parent::init();
        $this->get = $_GET;
        $this->post = $_POST;
        $this->cookies = $_COOKIE;
    }
    
    /**
     * Get method
     *
     * @param string $name
     * @param mixed $default
     * @return mixed 
     */
    public function get($name = '',$default = NULL){
        if(!$name) return $this->get;
        return isset($this->get[$name]) ? $this->get[$name] : $default;
    }
    /**
     * Post method
     *
     * @param string $name
     * @param mixed $default
     * @return mixed 
     */
    public function post($name = '',$default = NULL){
        if(!$name) return $this->post;
        return isset($this->post[$name]) ? $this->post[$name] : $default;
    }
    /**
     * Cookie method
     *
     * @param string $name
     * @param mixed $default
     * @return mixed 
     */
    public function cookie($name = '',$default = NULL){
        if(!$name) return $this->cookie;
        return isset($this->cookie[$name]) ? $this->cookie[$name] : $default;
    }
}