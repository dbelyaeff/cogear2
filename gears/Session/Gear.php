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

    /**
     * Конструктор
     */
    public function __construct($config) {
        parent::__construct($config);
        $this->object(new Session(array('name' => 'session')));
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