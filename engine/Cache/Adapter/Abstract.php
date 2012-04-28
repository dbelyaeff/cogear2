<?php

/**
 * Abstract Cache class
 *
 *
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2010, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          Cache
 * @version		$Id$
 */
abstract class Cache_Adapter_Abstract extends Options {

    abstract public function read($name, $force=NULL);

    abstract public function write($name, $value, $tags=NULL, $ttl=NULL);

    abstract public function remove($name);

    abstract public function clear();
    /**
     * Remove cached tags
     *
     * @param string|array $name
     */
    public function removeTags($name) {
        if (is_array($name)) {
            foreach ($name as $tag) {
                $this->remove('tags/' . $tag);
            }
        } else {
            $this->remove('tags/' . $name);
        }
    }

    /**
     *  Prepare filaname for cache
     * @param string $name
     * @return string
     */
    protected function prepareKey($name) {
        $name = str_replace('/', '_', $name . EXT);
        return $name;
    }

}
