<?php

/**
 * Menu Item
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Menu
 * @subpackage          
 * @version		$Id$
 */
class Menu_Item extends Core_ArrayObject{
    public $path;
    public $value;
    public $order = 0;
    protected $base_uri;
    protected $active;

    /**
     * Constructor
     * 
     * @param string $base_uri
     */
    public function __construct($path,$value = NULL,$order = 0,$base_uri = NULL) {
        $this->path = trim($path,'/');
        $base_uri && $this->path = str_replace(rtrim($base_uri,'/'),'',$this->path);
        $this->value = $value;
        $this->order = $order;
        $this->base_uri = $base_uri;
    }

    /**
     * Get uri
     * 
     * @return string
     */
    public function getUri() {
        return $this->getBaseUri() . $this->path;
    }
    
    /**
     * Get base uri
     * 
     * @return string 
     */
    public function getBaseUri(){
        return $this->base_uri ? $this->base_uri : Url::link();
    }

    /**
     * Make item active
     *  
     * @param   boolean $value
     * @return  boolean
     */
    public function active($value = NULL) {
        if($value){
            $this->active = TRUE;
            $this->class ? $this->class .= ' active' : $this->class = 'active';
        }
        return $this->active;
    }

}