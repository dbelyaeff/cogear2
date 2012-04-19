<?php

/**
 * Sessions gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          
 * @version		$Id$
 */
class Session_Gear extends Gear {

    protected $name = 'Sessions';
    protected $description = 'Handle sessions.';
    protected $order = -1000;
    protected $hooks = array(
//        'dev.info' => 'trace',
    );

    /**
     * Init
     */
    public function init() {
        parent::init();
        $this->adapter = new Session_Object(array('name' => 'session'));
    }
    
    /**
     * Trace session
     */
    public function trace($Stack){
        $Stack->append(template('Session.trace')->render());
    }
    
    /**
     * Overload Cogearable __get
     * 
     * @param type $name 
     */
    public function __get($name){
        return $this->adapter->__get($name);
    }

}