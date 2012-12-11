<?php

/**
 * Шестерёнка Запрос
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Request_Gear extends Gear {

    /**
     * Конструктор
     */
    public function __construct($xml) {
        parent::__construct($xml);
        $this->object(new Request_Object());
        cogear()->request = $this;
    }
}