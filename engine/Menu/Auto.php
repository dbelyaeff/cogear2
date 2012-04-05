<?php
/**
 * Menu 
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          
 * @version		$Id$
 */
class Menu_Auto extends Menu_Object {
    /**
     * Constructor
     * 
     * @param string $name
     * @param string $base_url 
     */
    public function __construct($name,$template = NULL,$base_url = NULL){
        parent::__construct($name,$template,$base_url);
        $this->init();
    }
    /**
     * Init
     */
    public function init(){
        $cogear = getInstance();
        if(!$menu = $cogear->system_cache->get('menu/'.$this->name)){
            foreach($cogear->gears as $gear){
                if(method_exists($gear, 'menu')){
                    call_user_func_array(array($gear,'menu'), array($this->name,&$this));
                }
            }
            $cogear->system_cache->set('menu/'.$this->name,$this->toArray());
        }
        else $this->exchangeArray((array)$menu);
    }
}

