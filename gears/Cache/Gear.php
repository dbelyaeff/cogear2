<?php

/**
 * Шестеренка кеширования
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Cache_Gear extends Gear {

    /**
     * Конструктор
     */
    public function __construct($xml) {
        parent::__construct($xml);
        $this->object(new Cache_Object());
    }

}

/**
 * Caching alias
 *
 * @param type $name
 * @param type $value
 * @param type $tags
 * @param type $ttl
 * @return type
 */
function cache($name, $value = '', $tags = array(), $ttl = 3600) {
    if ($value) {
        return cogear()->cache->write($name, $value, $tags, $ttl);
    } else {
        return cogear()->cache->read($name);
    }
}