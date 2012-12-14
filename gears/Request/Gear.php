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
    public function __construct($config) {
        parent::__construct($config);
        $this->object(cogear()->request = new Request_Object());
    }
}