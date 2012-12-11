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
    public function __construct($xml) {
        parent::__construct($xml);
        cogear()->gears->Router OR $this->object(new Router_Object());
    }
}