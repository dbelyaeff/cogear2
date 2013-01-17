<?php
/**
 * Профиль
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2013, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Profile_Object extends Object {
    /**
     * Конструктор
     *
     * @param Object $object
     * @param type $options
     */
    public function __construct(Object $object, $options = array()) {
        parent::__construct($options);
        $this->object($object);
    }

    /**
     * Отработка
     */
    public function render(){
        $tpl = new Template('Profile/templates/user');
        $tpl->user = $this->object();
        return $tpl->render();
    }


}