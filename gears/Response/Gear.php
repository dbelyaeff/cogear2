<?php

/**
 * Шестеренка Ответ
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Response_Gear extends Gear {

    protected $hooks = array(
        'exit' => 'send',
    );
    /**
     * Конструктор
     */
    public function __construct($config) {
        parent::__construct($config);
        $this->object(Response::getInstance());
    }
}