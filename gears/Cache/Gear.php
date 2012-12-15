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
    public function __construct($config) {
        parent::__construct($config);
        $this->object(Cache::factory('normal',array(
            'driver' => Cache_Driver_Memcache::check() ? 'Cache_Driver_Memcache' : 'Cache_Driver_File',
            'path' => CACHE,
            'prefix' => 'normal',
        )));
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
function cache($name, $value = NULL, $tags = array(), $ttl = 3600) {
    if ($value !== NULL) {
        return cogear()->cache->write($name, $value, $tags, $ttl);
    } else {
        return cogear()->cache->read($name);
    }
}