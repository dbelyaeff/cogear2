<?php

/**
 * Шестерёнка Роутер
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Router_Gear extends Gear {
    /**
     * Конструктор
     */
    public function __construct($config){
        parent::__construct($config);
        $this->object(new Router_Object());
    }
}