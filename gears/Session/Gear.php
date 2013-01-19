<?php

/**
 * Шестерёнка Сессии
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Session_Gear extends Gear {

    protected $hooks = array(
        //'dev.info' => 'trace',
    );

    /**
     * Init
     */
    public function __construct() {
        parent::__construct();
        $this->object(new Session(array('name' => 'session')));
    }

    /**
     * Trace session
     */
    public function trace($Stack){
        $Stack->append(template('Session/templates/trace')->render());
    }

}
/**
 * Shortcut for session
 *
 * @param type $name
 * @param type $value
 */
function session($name,$value = NULL){
    if($value !== NULL){
        cogear()->session->set($name,$value);
    }
    else {
        return cogear()->session->get($name);
    }
}