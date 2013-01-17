<?php

/**
 * Специальный класс ORM, который кодирует и декодирует параметра options при записи в базу
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2013, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Db_ORM_Options extends Db_ORM {
    protected $filters_in = array(
        'options' => array('sleep'),
    );
    protected $filters_out = array(
        'options' => array('wake'),
    );

    /**
     * Сон
     *
     * @param Core_ArrayObject $options
     * @return Core_ArrayObject
     */
    public function sleep($options) {
        if ($options instanceof Core_ArrayObject) {
            return $options->serialize();
        }
        return serialize($options);
    }

    /**
     * Подъём
     *
     * @param string $options
     * @return type
     */
    public function wake($options) {
        $result = new Core_ArrayObject();
        $result->unserialize($options);
        return $result;
    }

}