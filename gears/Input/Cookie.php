<?php

/**
 *  Куки
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Input_Cookie {

    /**
     * Ставит куку
     *
     * @param string $name
     * @param string $value
     * @param int $expire
     * @param string $path
     */
    public static function set($name, $value, $expire = NULL, $path = NULL, $domain = NULL) {
        // By default cookie lifetime is month
        $expire OR $expire = 2592000;
        $path OR $path = '/';
        $domain OR $domain = self::getDomain();
        setcookie($name, $value, time() + $expire, $path, $domain);
    }
    /**
     * Волшебный метод для установки куки
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name,$value){
        return self::set($name,$value);
    }
    /**
     * Получает куку
     *
     * @param string $name
     * @return string
     */
    public static function get($name) {
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : NULL;
    }
    /**
     * Волшебный метод для получения куки
     *
     * @param type $name
     * @return type
     */
    public function __get($name){
        return self::get($name);
    }

    /**
     * Удаляет куку
     *
     * @param string $name
     */
    public static function delete($name) {
        self::set($name, NULL, -1);
    }

    /**
     * Возвращает домен для кукисов
     *
     * @return string
     */
    public static function getDomain() {
        return strpos(SITE_URL, '.') ? '.' . SITE_URL : '';
    }

}
