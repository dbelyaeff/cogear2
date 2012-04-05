<?php
/**
 *  Cookie
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Cookie {
    /**
     * Set cookie
     *
     * @param string $name
     * @param string $value
     * @param int $expire
     * @param string $path
     */
    public static function set($name,$value,$expire = NULL,$path = NULL){
        // By default cookie lifetime is month
        $expire OR $expire = 2592000;
        $path OR $path = '/';
        setcookie($name, $value, time()+$expire, $path, '.'.SITE_URL);
    }
    /**
     * Get cookie
     *
     * @param string $name
     * @return string
     */
    public static function get($name){
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : NULL;
    }
    /**
     * Delete cookie
     *
     * @param string $name
     */
    public static function delete($name){
        self::set($name,NULL,-1);
    }
}
