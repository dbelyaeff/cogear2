<?php

/**
 * Сущность Когира
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
final class Cogear implements Interface_Singleton {

    /**
     * Сущность
     *
     * @var object
     */
    private static $_instance;

    /**
     * События
     */
    public $events;

    /**
     * Сушность шестерёнок
     *
     * @var Gears
     */
    public $gears;

    /**
     * Флаг для остановки события
     */
    public $stop_event = FALSE;

    /**
     * Settings and config
     */
    public $site;
    public $config;
    public $system_cache;

    /**
     * Конструктор
     */
    private function __construct() {
        $this->events = new Core_ArrayObject();
    }

    /**
     * Load Cogear
     */
    public function load() {
        $this->site = new Config(ROOT . DS . 'site' . EXT);
        $this->config = new Config(ROOT . DS . 'config' . EXT);
        $this->system_cache = Cache::factory('system', array(
                    'driver' => Cache_Driver_Memcache::check() ? 'Cache_Driver_Memcache' : 'Cache_Driver_File',
                    'prefix' => 'system',
                    'path' => CACHE . DS . 'system'
                ));
        defined('SITE_URL') OR define('SITE_URL', config('site.url'));
        if (strpos(SITE_URL, '/') && !defined('FOLDER')) {
            $array = explode('/', SITE_URL, 2);
            $folder = array_pop($array);
            define('FOLDER', $folder);
        }
        $core_gears = clone $this->site->gears;
        $this->gears = new Gears($core_gears->extend($this->config->gears));
        foreach ($this->gears as $gear) {
            $gear->init();
        }
    }

    /**
     * Clone
     */
    private function __clone() {

    }

    /**
     * Get instance
     *
     * @return Cogear
     */
    public static function getInstance() {
        return self::$_instance instanceof self ? self::$_instance : self::$_instance = new self();
    }

    /**
     * Register hooks for event
     *
     * @param   string  $event
     * @param   callback  $callback
     */
    public function hook($event, $callback, $position = NULL) {
        $this->events->$event OR $this->events->$event = new Event($event);
        $callback = new Callback($callback);
        $args = func_get_args();
        if (sizeof($args) > 3) {
            $callback->setArgs(array_slice($args, 3));
        }
        if ($position !== NULL) {
            $this->events->$event->inject($callback, $position);
        } else {
            $this->events->$event->append($callback);
        }
    }

    /**
     * Исполение события
     *
     * @param string $name
     * @param mixed $arg_1
     * …
     * @param mixed $arg_N
     * @return  boolean
     */
    public function event($name) {
        // Внешне может быть установлено прерывание события
        if(FALSE === flash('event.'.$name)){
            return FALSE;
        }
        $args = func_get_args();
        $args = array_slice($args, 1);
        return $this->events->$name ? $this->events->$name->run($args) : new Event($name);
    }

    /**
     * Magic get method
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
        if ($this->gears && $this->gears->$name) {
            return $this->gears->$name;
        }
        return NULL;
    }

    /**
     * Get config
     *
     * @param type $name
     * @param type $default
     * @return  mixed
     */
    public function get($name, $default = NULL) {
        $result = $this->site->get($name);
        if (NULL !== $result) {
            return $result;
        }
        $result = $this->config->get($name);
        if (NULL !== $result) {
            return $result;
        }
        return $default;
    }

    /**
     * Set config
     *
     * @param type $name
     * @param type $value
     * @return  mixed
     */
    public function set($name, $value) {
        return $this->config->set($name, $value);
    }

}

function getInstance() {
    return Cogear::getInstance();
}

function cogear() {
    return Cogear::getInstance();
}

function event() {
    $cogear = getInstance();
    $args = func_get_args();
    return call_user_func_array(array($cogear, 'event'), $args);
}

function hook() {
    $cogear = getInstance();
    $args = func_get_args();
    return call_user_func_array(array($cogear, 'hook'), $args);
}

function config($name = NULL, $default_value = NULL) {
    $cogear = getInstance();
    return $cogear->get($name, $default_value);
}
/**
 * Хранение одноразовых пеерменных
 *
 * @staticvar array $storage
 * @param string $key
 * @param mixed $value
 * @return mixed
 */
function flash($key,$value = NULL){
    static $storage = array();
    if(NULL !== $value){
        $storage[$key] = $value;
    }
    elseif(array_key_exists($key, $storage)){
        return $storage[$key];
    }
    return NULL;
}